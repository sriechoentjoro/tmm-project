#!/usr/bin/env bash
#
# Smoke test for POST /admin/lpk-registration/create.
#
# Logs in as an admin, submits a complete LPK registration, and reports what
# came back. Run it on the server, from the application directory:
#
#     bin/smoke-test-lpk-registration.sh
#
# Environment:
#   HOST_NAME   virtual host to test, default tmm-demo.widagdo.web.id
#   BASE_URL    default https://$HOST_NAME
#   RESOLVE_TO  pin the host to this address, default 127.0.0.1 (so the request
#               never leaves the box and SNI still matches). Set it empty to
#               resolve through DNS instead.
#   ADMIN_USER / ADMIN_PASS   prompted for if unset
#   LPK_EMAIL   default srikuncoro@yahoo.com
#   KEEP=1      keep the temporary directory for inspection
#
# It creates a real record. Delete it afterwards if this is not a test system.
#
set -u

HOST_NAME="${HOST_NAME:-tmm-demo.widagdo.web.id}"
BASE_URL="${BASE_URL:-https://$HOST_NAME}"
RESOLVE_TO="${RESOLVE_TO-127.0.0.1}"
LPK_EMAIL="${LPK_EMAIL:-srikuncoro@yahoo.com}"
SUFFIX="$(date +%H%M%S)"

if [ ! -f config/app_local.php ] || [ ! -f webroot/index.php ]; then
    echo "Run this from the application directory (config/app_local.php not found)." >&2
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

# Returns "<http_code> <final_url>" — the final URL is what tells us whether we
# were bounced back to the login page.
fetch() { "${CURL[@]}" -o "$2" -w '%{http_code} %{url_effective}' "$BASE_URL$1"; }

# CakePHP renders <input type="hidden" name="_csrfToken" ... value="...">;
# attribute order is not guaranteed, so match the tag then pull its value.
csrf_from() {
    grep -o '<input[^>]*_csrfToken[^>]*>' "$1" \
        | head -1 | grep -o 'value="[^"]*"' | head -1 \
        | sed 's/^value="//; s/"$//'
}

# Flash markup differs between the default template and the AdminLTE layout, so
# strip every tag and look for the messages this controller actually emits.
flash_from() {
    local text="$WORK/flash.txt"
    sed -e 's#<script[^>]*>#\n#g' -e 's#</script>#\n#g' -e 's/<[^>]*>/\n/g' "$1" \
        | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//' \
        | grep -vE '^$' > "$text"
    if grep -E 'LPK registered|Unable to register|could not be saved|verification email|check the form|error occurred' "$text" \
        | head -6 | sed 's/^/  /' | grep -q .; then
        grep -E 'LPK registered|Unable to register|could not be saved|verification email|check the form|error occurred' "$text" \
            | head -6 | sed 's/^/  /'
        return 0
    fi
    echo "  (no recognised message; first lines of the page:)"
    head -5 "$text" | sed 's/^/  /'
}

say "0. Target"
echo "  $BASE_URL${RESOLVE_TO:+  (pinned to $RESOLVE_TO)}"

say "1. Admin credentials"
if [ -z "${ADMIN_USER:-}" ]; then read -r -p "  admin username: " ADMIN_USER; fi
if [ -z "${ADMIN_PASS:-}" ]; then read -r -s -p "  admin password: " ADMIN_PASS; echo; fi
[ -n "$ADMIN_USER" ] && [ -n "$ADMIN_PASS" ] || fail "username and password are required"

say "2. Login"
read -r code url <<<"$(fetch /users/login "$WORK/login.html")"
echo "  GET /users/login -> $code  $url"
[ "$code" = 200 ] || fail "expected 200 after following redirects"
TOKEN="$(csrf_from "$WORK/login.html")"
[ -n "$TOKEN" ] || fail "no _csrfToken in the login form"

code="$("${CURL[@]}" -o "$WORK/after-login.html" -w '%{http_code}' \
    --data-urlencode "_csrfToken=$TOKEN" \
    --data-urlencode "username=$ADMIN_USER" \
    --data-urlencode "password=$ADMIN_PASS" \
    "$BASE_URL/users/login")"
echo "  POST /users/login -> $code"

say "3. Open the create form"
read -r code url <<<"$(fetch /admin/lpk-registration/create "$WORK/create.html")"
echo "  GET /admin/lpk-registration/create -> $code  $url"
case "$url" in
    *"/users/login"*)
        flash_from "$WORK/after-login.html"
        fail "bounced back to the login page — wrong credentials, or this account has no admin access"
        ;;
esac
[ "$code" = 200 ] || fail "expected 200"

for field in username mou_file director post_code; do
    grep -q "name=\"$field\"" "$WORK/create.html" \
        || fail "the form has no '$field' input — the deployed code predates the LPK registration fix"
done
echo "  form has username, mou_file, director, post_code — deployed code is current"
TOKEN="$(csrf_from "$WORK/create.html")"
[ -n "$TOKEN" ] || fail "no _csrfToken in the create form"

say "4. Pick a valid province / city / subdistrict / village chain"
# buildRules() has existsIn on all four, so read a real chain from cms_masters.
CHAIN="$(php -r '
$c = include "config/app_local.php";
$d = $c["Datasources"]["default"];
$pdo = new PDO("mysql:host={$d["host"]};dbname=cms_masters;charset=utf8mb4", $d["username"], $d["password"]);
$row = $pdo->query("SELECT p.id p, k.id k, c.id c, l.id l
                    FROM master_kelurahans l
                    JOIN master_kecamatans c ON c.id = l.kecamatan_id
                    JOIN master_kabupatens k ON k.id = c.kabupaten_id
                    JOIN master_propinsis  p ON p.id = k.propinsi_id
                    LIMIT 1")->fetch(PDO::FETCH_ASSOC);
echo $row ? "{$row["p"]} {$row["k"]} {$row["c"]} {$row["l"]}" : "";
' 2>"$WORK/db-error.txt")"
if [ -z "$CHAIN" ]; then
    sed 's/^/  /' "$WORK/db-error.txt" >&2
    fail "could not read a location chain from cms_masters"
fi
read -r PROP KAB KEC KEL <<<"$CHAIN"
echo "  propinsi=$PROP kabupaten=$KAB kecamatan=$KEC kelurahan=$KEL"

say "5. Submit"
printf '%%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%%%EOF\n' > "$WORK/mou.pdf"
code="$("${CURL[@]}" -o "$WORK/result.html" -w '%{http_code}' \
    -F "_csrfToken=$TOKEN" \
    -F "name=LPK Smoke Test $SUFFIX" \
    -F "abbreviation=SMK$SUFFIX" \
    -F "username=smoketest$SUFFIX" \
    -F "email=$LPK_EMAIL" \
    -F "director=Sri Kuncoro" \
    -F "director_katakana=" \
    -F "is_special_skill_support_institution=0" \
    -F "address=Jalan Uji Coba No. 1" \
    -F "post_code=50123" \
    -F "master_propinsi_id=$PROP" \
    -F "master_kabupaten_id=$KAB" \
    -F "master_kecamatan_id=$KEC" \
    -F "master_kelurahan_id=$KEL" \
    -F "mou_file=@$WORK/mou.pdf;type=application/pdf" \
    "$BASE_URL/admin/lpk-registration/create")"
echo "  POST -> $code"

say "6. Result"
flash_from "$WORK/result.html"
if [ "$code" = 500 ]; then
    echo
    echo "  HTTP 500 — last lines of logs/error.log:"
    tail -20 logs/error.log 2>/dev/null | sed 's/^/    /'
fi

say "7. Was the row written?"
LPK_EMAIL="$LPK_EMAIL" php -r '
$c = include "config/app_local.php";
$d = $c["Datasources"]["default"];
$pdo = new PDO("mysql:host={$d["host"]};dbname=cms_tmm_stakeholders;charset=utf8mb4", $d["username"], $d["password"]);
$st = $pdo->prepare("SELECT id, name, username, email, director, post_code, mou_file
                     FROM vocational_training_institutions WHERE email = ? ORDER BY id DESC LIMIT 3");
$st->execute([getenv("LPK_EMAIL")]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) { echo "no row with that email\n"; exit; }
foreach ($rows as $r) {
    echo "---\n";
    foreach ($r as $k => $v) { printf("%-12s %s\n", $k, var_export($v, true)); }
}
' 2>&1 | sed 's/^/  /'

say "8. Registration log"
grep -a "lpk_registration" logs/*.log 2>/dev/null | tail -5 | sed 's/^/  /' || echo "  (nothing logged)"

echo
echo "Done. The record created above is real — delete it if this is not a test system."
