#!/usr/bin/env bash
#
# Check that the five help guides really switch language on the live site.
#
# Run it on the server, from the application directory, after deploying:
#
#     bin/verify-help-i18n.sh
#
# It logs in, switches the interface language through the same route the
# switcher uses (/users/change-language/{ind,eng,jpn}), fetches each help page
# and counts what came back: Japanese characters, Indonesian function words and
# English function words. A page that switched correctly shows one of those and
# not the other two.
#
# This counts rendered output, not catalogue entries. A .po file can be perfect
# and the page still English because the translation cache is stale, so this is
# the check that actually settles it.
#
# Environment:
#   HOST_NAME   virtual host to test, default tmm-demo.widagdo.web.id
#   BASE_URL    default https://$HOST_NAME
#   RESOLVE_TO  pin the host to this address, default 127.0.0.1 (so the request
#               never leaves the box and SNI still matches). Set it empty to
#               resolve through DNS instead.
#   ADMIN_USER / ADMIN_PASS   prompted for if unset
#   KEEP=1      keep the temporary directory, so the fetched pages can be read
#
set -u

HOST_NAME="${HOST_NAME:-tmm-demo.widagdo.web.id}"
BASE_URL="${BASE_URL:-https://$HOST_NAME}"
RESOLVE_TO="${RESOLVE_TO-127.0.0.1}"

PAGES="users stakeholders candidates candidate-documents vocational-training-institutions"

if [ ! -f webroot/index.php ]; then
    echo "Run this from the application directory (webroot/index.php not found)." >&2
    exit 1
fi

WORK="$(mktemp -d)"
trap '[ "${KEEP:-0}" = 1 ] || rm -rf "$WORK"' EXIT
JAR="$WORK/cookies.txt"

say()  { printf '\n\033[1m%s\033[0m\n' "$*"; }
fail() { printf '  FAIL: %s\n' "$*"; exit 1; }

# -L throughout: nginx answers 301 on http and CakePHP redirects after login,
# so a bare request reports a redirect rather than the page being tested.
CURL=(curl -sS -k -L -c "$JAR" -b "$JAR")
if [ -n "$RESOLVE_TO" ]; then
    CURL+=(--resolve "$HOST_NAME:443:$RESOLVE_TO" --resolve "$HOST_NAME:80:$RESOLVE_TO")
fi

fetch() { "${CURL[@]}" -o "$2" -w '%{http_code} %{url_effective}' "$BASE_URL$1"; }

csrf_from() {
    grep -o '<input[^>]*_csrfToken[^>]*>' "$1" \
        | head -1 | grep -o 'value="[^"]*"' | head -1 \
        | sed 's/^value="//; s/"$//'
}

# Counts are taken from the visible page body only.
#
# strip_tags() removes the tags but keeps what is between them, so the contents
# of <script> and <style> survive it, and so do HTML comments. The layouts carry
# roughly 50 English words in JavaScript and CSS comments alone, which showed up
# as a constant English count on every Indonesian and Japanese page - noise that
# would hide a real regression behind a baseline. Those three are cut first.
counts_for() {
    php -r '
    $html = file_get_contents($argv[1]);
    $body = preg_replace(
        ["#<script\b[^>]*>.*?</script>#is", "#<style\b[^>]*>.*?</style>#is", "#<!--.*?-->#s"],
        " ", $html);
    $text = html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, "UTF-8");
    $jp  = preg_match_all("/[\x{3040}-\x{30ff}\x{4e00}-\x{9fff}]/u", $text);
    $id  = preg_match_all("/\b(yang|untuk|dengan|adalah|Klik|Pastikan|Buka)\b/u", $text);
    $en  = preg_match_all("/\b(the|Click|Ensure|Verify|Guide|Select)\b/u", $text);
    printf("%d %d %d %d\n", strlen($html), $jp, $id, $en);
    ' "$1"
}

say "0. Target"
echo "  $BASE_URL${RESOLVE_TO:+  (pinned to $RESOLVE_TO)}"
echo "  commit: $(git rev-parse --short HEAD 2>/dev/null || echo '(not a git checkout)')"

say "1. Admin credentials"
if [ -z "${ADMIN_USER:-}" ]; then read -r -p "  username: " ADMIN_USER; fi
if [ -z "${ADMIN_PASS:-}" ]; then read -r -s -p "  password: " ADMIN_PASS; echo; fi
[ -n "$ADMIN_USER" ] && [ -n "$ADMIN_PASS" ] || fail "username and password are required"

say "2. Login"
read -r code url <<<"$(fetch /users/login "$WORK/login.html")"
echo "  GET /users/login -> $code  $url"
[ "$code" = 200 ] || fail "expected 200 after following redirects"

TOKEN="$(csrf_from "$WORK/login.html")"
POST_TOKEN=()
if [ -n "$TOKEN" ]; then
    POST_TOKEN=(--data-urlencode "_csrfToken=$TOKEN")
    echo "  CSRF token present"
else
    echo "  no CSRF token in the form"
fi

code="$("${CURL[@]}" -o "$WORK/after-login.html" -w '%{http_code}' \
    "${POST_TOKEN[@]+"${POST_TOKEN[@]}"}" \
    --data-urlencode "username=$ADMIN_USER" \
    --data-urlencode "password=$ADMIN_PASS" \
    "$BASE_URL/users/login")"
echo "  POST /users/login -> $code"

read -r code url <<<"$(fetch /users/help "$WORK/probe.html")"
case "$url" in
    *"/users/login"*) fail "bounced back to the login page — wrong credentials, or this account cannot open /users/help" ;;
esac
[ "$code" = 200 ] || fail "GET /users/help returned $code"
echo "  session works"

say "3. Help pages per language"
printf '  %-34s %-5s %7s %7s %7s %7s\n' page lang bytes japanese indo english
bad=0
for page in $PAGES; do
    for lang in ind eng jpn; do
        read -r code url <<<"$(fetch "/users/change-language/$lang" "$WORK/switch.html")"
        [ "$code" = 200 ] || { printf '  %-34s %-5s switch failed (%s)\n' "$page" "$lang" "$code"; bad=1; continue; }

        read -r code url <<<"$(fetch "/$page/help" "$WORK/$page-$lang.html")"
        if [ "$code" != 200 ]; then
            printf '  %-34s %-5s HTTP %s\n' "$page" "$lang" "$code"; bad=1; continue
        fi
        read -r bytes jp id en <<<"$(counts_for "$WORK/$page-$lang.html")"
        printf '  %-34s %-5s %7s %7s %7s %7s' "$page" "$lang" "$bytes" "$jp" "$id" "$en"

        # The layout contributes a little of every language (the switcher shows
        # all three names), so the test is which one dominates, not purity.
        case "$lang" in
            ind) [ "$id" -gt 10 ] && [ "$jp" -lt 50 ] || { printf '  <- expected Indonesian'; bad=1; } ;;
            eng) [ "$en" -gt 10 ] && [ "$jp" -lt 50 ] || { printf '  <- expected English'; bad=1; } ;;
            jpn) [ "$jp" -gt 300 ] || { printf '  <- expected Japanese'; bad=1; } ;;
        esac
        printf '\n'
    done
done

# The three diagrams on the LPK process-flow page carry their own copy, and a
# mermaid block that fails to parse renders as nothing at all, silently — so it
# is worth looking inside the <div class="mermaid"> rather than at the page as a
# whole, where a few hundred translated words elsewhere would mask it.
say "4. Diagrams on the LPK process-flow page"
printf '  %-34s %-5s %7s %7s %7s %7s\n' block lang blocks japanese indo english
for lang in ind eng jpn; do
    read -r code url <<<"$(fetch "/users/change-language/$lang" "$WORK/switch.html")"
    [ "$code" = 200 ] || { printf '  %-34s %-5s switch failed (%s)\n' "(mermaid)" "$lang" "$code"; bad=1; continue; }

    read -r code url <<<"$(fetch "/admin/lpk-registration/process-flow" "$WORK/flow-$lang.html")"
    if [ "$code" != 200 ]; then
        printf '  %-34s %-5s HTTP %s\n' "(mermaid)" "$lang" "$code"; bad=1; continue
    fi

    read -r blocks jp id en <<<"$(php -r '
    $html = file_get_contents($argv[1]);
    preg_match_all("#<div class=\"mermaid\">(.*?)</div>#s", $html, $m);
    $src = implode("\n", $m[1]);
    printf("%d %d %d %d\n", count($m[1]),
        preg_match_all("/[\x{3040}-\x{30ff}\x{4e00}-\x{9fff}]/u", $src),
        preg_match_all("/\b(Simpan|Buat|Kirim|Menunggu|Tandai|Wajib|Perbarui|Arahkan)\b/u", $src),
        preg_match_all("/\b(Save|Generate|Send|Wait|Mark|Required|Update|Redirect)\b/u", $src));
    ' "$WORK/flow-$lang.html")"
    printf '  %-34s %-5s %7s %7s %7s %7s' "3 mermaid blocks" "$lang" "$blocks" "$jp" "$id" "$en"

    [ "$blocks" = 3 ] || { printf '  <- expected 3 blocks'; bad=1; printf '\n'; continue; }
    case "$lang" in
        ind) [ "$id" -gt 3 ] || { printf '  <- expected Indonesian'; bad=1; } ;;
        eng) [ "$en" -gt 3 ] || { printf '  <- expected English'; bad=1; } ;;
        jpn) [ "$jp" -gt 50 ] || { printf '  <- expected Japanese'; bad=1; } ;;
    esac
    printf '\n'
done
echo "  a block that renders blank in the browser still counts here, so open the"
echo "  page once to confirm the diagrams actually draw:"
echo "      $BASE_URL/admin/lpk-registration/process-flow"

# Flash messages come from controllers, not templates. They were missing from
# the catalogues entirely until recently, and nothing above would notice: every
# page here could be perfectly translated while every message the application
# speaks stayed English.
#
# The probe is a GET that changes nothing. CandidatesController::view() rejects
# a non-numeric id with a flash and a redirect, so following the redirect lands
# on a page carrying the message - no record is written, updated or deleted.
say "5. Flash message from a controller"
printf '  %-34s %-5s %s\n' probe lang message
for lang in ind eng jpn; do
    read -r code url <<<"$(fetch "/users/change-language/$lang" "$WORK/switch.html")"
    [ "$code" = 200 ] || { printf '  %-34s %-5s switch failed (%s)\n' "(flash)" "$lang" "$code"; bad=1; continue; }

    read -r code url <<<"$(fetch "/candidates/view/not-a-number" "$WORK/flash-$lang.html")"
    if [ "$code" != 200 ]; then
        printf '  %-34s %-5s HTTP %s\n' "(flash)" "$lang" "$code"; bad=1; continue
    fi

    message="$(grep -o '<div class="message flash-message error"[^>]*>[^<]*</div>' "$WORK/flash-$lang.html" \
        | head -1 | sed -e 's/^[^>]*>//' -e 's#</div>$##')"
    printf '  %-34s %-5s %s' "invalid candidate id" "$lang" "${message:-(none)}"

    case "$lang" in
        ind) expect='ID kandidat tidak valid.' ;;
        eng) expect='Invalid candidate ID.' ;;
        jpn) expect='候補者IDが不正です。' ;;
    esac
    [ "$message" = "$expect" ] || { printf '  <- expected: %s' "$expect"; bad=1; }
    printf '\n'
done

# The process-flow pages used to render in a standalone layout with no
# application menu. They now use 'elegant' like the rest of the interface, and
# take the styling the old layout supplied from the shared
# process_flow_assets element instead. Two things can go wrong silently: the
# menu does not load (AppController skips it for some layouts), or the element
# is not included and the page comes back unstyled with blank diagrams.
say "6. Process-flow pages inside the application container"
printf '  %-34s %5s %5s %5s\n' page menu styling mermaid
for path in /master-genders/process-flow /candidates/process-flow /admin/lpk-registration/process-flow; do
    read -r code url <<<"$(fetch "$path" "$WORK/container.html")"
    if [ "$code" != 200 ]; then
        printf '  %-34s HTTP %s\n' "$path" "$code"; bad=1; continue
    fi

    menu=no;  grep -q 'elegant-menu-wrapper' "$WORK/container.html" && menu=yes
    style=no; grep -q 'step-description' "$WORK/container.html" && style=yes
    mmd=no;   grep -q 'mermaid.min.js'    "$WORK/container.html" && mmd=yes
    printf '  %-34s %5s %5s %5s' "$path" "$menu" "$style" "$mmd"

    [ "$menu" = yes ] && [ "$style" = yes ] && [ "$mmd" = yes ] \
        || { printf '  <- expected all three'; bad=1; }
    printf '\n'
done

say "7. Result"
if [ "$bad" = 0 ]; then
    echo "  all 15 page/language combinations rendered in the expected language,"
    echo "  all 3 diagrams carry their translated labels in all 3 languages,"
    echo "  and the controller's flash message came back translated too"
    echo "  the process-flow pages render inside the application menu, styled,"
    echo "  with the mermaid loader present"
else
    echo "  some combinations did not switch — see the lines marked above"
    echo "  if every page is English, the translation cache is stale:"
    echo "      rm -rf tmp/cache/persistent/* tmp/cache/models/*"
fi
[ "${KEEP:-0}" = 1 ] && echo "  pages kept in $WORK"
exit "$bad"
