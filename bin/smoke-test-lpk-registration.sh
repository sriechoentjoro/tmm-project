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
#   BASE_URL   default http://127.0.0.1  (Host header is set from it)
#   HOST_NAME  virtual host to send, default tmm-demo.widagdo.web.id
#   ADMIN_USER / ADMIN_PASS  prompted for if unset
#   LPK_EMAIL  default srikuncoro@yahoo.com
#   KEEP       set to 1 to keep the temporary directory for inspection
#
# It creates a real record. Delete it afterwards if this is not a test system.
#
set -u

BASE_URL="${BASE_URL:-http://127.0.0.1}"
HOST_NAME="${HOST_NAME:-tmm-demo.widagdo.web.id}"
LPK_EMAIL="${LPK_EMAIL:-srikuncoro@yahoo.com}"
SUFFIX="$(date +%H%M%S)"

if [ ! -f config/app_local.php ] || [ ! -f webroot/index.php ]; then
    echo "Run this from the application directory (config/app_local.php not found)." >&2
    exit 1
fi

WORK="$(mktemp -d)"
trap '[ "${KEEP:-0}" = 1 ] || rm -rf "$WORK"' EXIT
JAR="$WORK/cookies.txt"

say() { printf '\n\033[1m%s\033[0m\n' "$*"; }
fail() { printf '  FAIL: %s\n' "$*"; exit 1; }

# CakePHP renders <input type="hidden" name="_csrfToken" ... value="...">;
# attribute order is not guaranteed, so match the tag then pull its value.
csrf_from() {
    grep -o '<input[^>]*_csrfToken[^>]*>' "$1" \
        | head -1 \
        | grep -o 'value="[^"]*"' \
        | head -1 \
        | sed 's/^value="//; s/"$//'
}

# Flash markup differs between the default template and the AdminLTE layout,
# so strip every tag and look for the messages this controller actually emits
# rather than trying to match a wrapper element.
flash_from() {
    local text="$WORK/flash.txt"
    sed -e 's#<script[^>]*>#\n#g' -e 's#</script>#\n#g' -e 's/<[^>]*>/\n/g' "$1" \
        | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//' \
        | grep -vE '^$' > "$text"
    if grep -nE 'LPK registered|Unable to register|could not be saved|verification email|check the form|error occurred' "$text" \
        | sed 's/^[0-9]*://' | sed 's/^/  /' | head -6 | grep .; then
        return 0
    fi
    echo "  (no recognised message; first lines of the page:)"
    head -5 "$text" | sed 's/^/  /'
}

get()  { curl -sS -k -c "$JAR" -b "$JAR" -H "Host: $HOST_NAME" -o "$2" -w '%{http_code}' "$BASE_URL$1"; }

say "1. Admin credentials"
if [ -z "${ADMIN_USER:-}" ]; then read -r -p "  admin username: " ADMIN_USER; fi
if [ -z "${ADMIN_PASS:-}" ]; then read -r -s -p "  admin password: " ADMIN_PASS; echo; fi
[ -n "$ADMIN_USER" ] && [ -n "$ADMIN_PASS" ] || fail "username and password are required"

say "2. Login"
code="$(get /users/login "$WORK/login.html")"
echo "  GET /users/login -> $code"
[ "$code" = 200 ] || fail "expected 200"
TOKEN="$(csrf_from "$WORK/login.html")"
[ -n "$TOKEN" ] || fail "no _csrfToken in the login form"

code="$(curl -sS -k -c "$JAR" -b "$JAR" -H "Host: $HOST_NAME" -L \
    -o "$WORK/after-login.html" -w '%{http_code}' \
    --data-urlencode "_csrfToken=$TOKEN" \
    --data-urlencode "username=$ADMIN_USER" \
    --data-urlencode "password=$ADMIN_PASS" \
    "$BASE_URL/users/login")"
echo "  POST /users/login -> $code"
if grep -qi 'invalid\|incorrect\|salah' "$WORK/after-login.html" 2>/dev/null; then
    flash_from "$WORK/after-login.html"
    fail "login rejected"
fi

say "3. Pick a valid province / city / subdistrict / village chain"
# existsIn rules reject unknown ids, so read a real chain straight from the DB.
CHAIN="$(php -r '
$c = include "config/app_local.php";
$d = $c["Datasources"]["default"];
$pdo = new PDO("mysql:host={$d["host"]};dbname=cms_masters;charset=utf8mb4", $d["username"], $d["password"]);
$sql = "SELECT p.id p, k.id k, c.id c, l.id l
        FROM master_kelurahans l
        JOIN master_kecamatans c ON c.id = l.kecamatan_id
        JOIN master_kabupatens k ON k.id = c.kabupaten_id
        JOIN master_propinsis  p ON p.id = k.propinsi_id
        LIMIT 1";
$row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
echo $row ? "{$row["p"]} {$row["k"]} {$row["c"]} {$row["l"]}" : "";
' 2>"$WORK/db-error.txt")"
if [ -z "$CHAIN" ]; then
    cat "$WORK/db-error.txt" >&2
    fail "could not read a location chain from cms_masters"
fi
read -r PROP KAB KEC KEL <<<"$CHAIN"
echo "  propinsi=$PROP kabupaten=$KAB kecamatan=$KEC kelurahan=$KEL"

say "4. Open the create form"
code="$(get /admin/lpk-registration/create "$WORK/create.html")"
echo "  GET /admin/lpk-registration/create -> $code"
[ "$code" = 200 ] || fail "expected 200 (still on the login page? check the admin account's role)"
for field in username mou_file director post_code; do
    grep -q "name=\"$field\"" "$WORK/create.html" \
        || fail "the form has no '$field' input — the deployed code is older than PR #11"
done
echo "  form has username, mou_file, director, post_code — deployed code is current"
TOKEN="$(csrf_from "$WORK/create.html")"
[ -n "$TOKEN" ] || fail "no _csrfToken in the create form"

say "5. Submit"
printf '%%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%%%EOF\n' > "$WORK/mou.pdf"
code="$(curl -sS -k -c "$JAR" -b "$JAR" -H "Host: $HOST_NAME" -L \
    -o "$WORK/result.html" -w '%{http_code}' \
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
    echo "  HTTP 500 — the last lines of logs/error.log:"
    tail -20 logs/error.log 2>/dev/null | sed 's/^/    /'
fi

say "7. Was the row written?"
LPK_EMAIL="$LPK_EMAIL" php -r '
$c = include "config/app_local.php";
$d = $c["Datasources"]["default"];
$pdo = new PDO("mysql:host={$d["host"]};dbname=cms_tmm_stakeholders;charset=utf8mb4", $d["username"], $d["password"]);
$st = $pdo->prepare("SELECT id, name, username, email, director, post_code, mou_file
                     FROM vocational_training_institutions WHERE email = ? ORDER BY id DESC LIMIT 3");
$st->execute([getenv("LPK_EMAIL") ?: "srikuncoro@yahoo.com"]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) { echo "  no row with that email\n"; exit; }
foreach ($rows as $r) {
    echo "  ---\n";
    foreach ($r as $k => $v) { printf("  %-12s %s\n", $k, var_export($v, true)); }
}
' 2>&1 | sed 's/^/  /'

say "8. Registration log"
grep -a "lpk_registration" logs/*.log 2>/dev/null | tail -5 | sed 's/^/  /' || echo "  (nothing logged)"

echo
echo "Done. The record created above is real — delete it if this is not a test system."
