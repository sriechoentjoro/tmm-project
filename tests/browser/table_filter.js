/**
 * The filter row the index templates write, driven in a real browser.
 *
 * It was inert. table-filter.js saw a .filter-row already in the thead and
 * returned, and nothing else bound those boxes: webroot/js/table-enhanced.js is
 * loaded by no page, and no controller read the filter_* parameters it would
 * have sent. So on seventy-six index screens you typed, you chose, and the
 * table sat there.
 *
 * It now asks the server where the screen paginates, and narrows what is on the
 * page where it does not.
 *
 * Needs node and a global playwright install:
 *     npm install -g playwright && npx playwright install chromium
 * tests/run.php names this as skipped where they are missing rather than
 * leaving it out of the count.
 */
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..', '..');
const script = fs.readFileSync(path.join(root, 'webroot/js/table-filter.js'), 'utf8');

const ROWS = `
  <tr><td>e</td><td>1</td><td>Budi</td><td>Universitas Indonesia</td></tr>
  <tr><td>e</td><td>5</td><td>Budiman</td><td>Institut Teknologi Bandung</td></tr>
  <tr><td>e</td><td>12</td><td>Nur</td><td>Universitas Gadjah Mada</td></tr>
  <tr><td>e</td><td>30</td><td>Budi</td><td>Politeknik Negeri Bandung</td></tr>`;

/** The shape the index templates write: operators, a range box, a select. */
function page(serverSide, rows) {
    return `<!doctype html><html><head><meta charset="utf-8"></head><body>
<script>window.serverSideFilter = ${serverSide};<\/script>
<table class="table">
<thead>
  <tr><th>Actions</th><th>ID</th><th>Candidate</th><th>College</th></tr>
  <tr class="filter-row">
    <td><button type="button" class="btn-clear-filter">x</button></td>
    <td>
      <select class="filter-operator" data-column="id">
        <option value="=">=</option><option value="!=">!=</option>
        <option value="&lt;">&lt;</option><option value="&gt;">&gt;</option>
        <option value="&lt;=">&le;</option><option value="&gt;=">&ge;</option>
        <option value="between">Between</option>
      </select>
      <input type="number" class="filter-input" data-column="id">
      <input type="number" class="filter-input-range" data-column="id" style="display:none">
    </td>
    <td>
      <select class="filter-input" data-column="candidate_id" data-type="select">
        <option value="">All Candidates</option>
        <option value="1">Budi</option>
        <option value="2">Budiman</option>
        <option value="3">Nur</option>
      </select>
    </td>
    <td>
      <select class="filter-operator" data-column="college">
        <option value="like">LIKE</option><option value="not_like">NOT LIKE</option>
        <option value="=">=</option><option value="!=">!=</option>
        <option value="starts_with">Starts With</option><option value="ends_with">Ends With</option>
      </select>
      <input type="text" class="filter-input" data-column="college">
    </td>
  </tr>
</thead>
<tbody>${rows}</tbody>
</table>
<ul class="pagination"><li>1</li><li>2</li></ul>
<script>${script}<\/script>
</body></html>`;
}

let failures = 0;
let checks = 0;
function check(label, got, want) {
    checks++;
    const ok = JSON.stringify(got) === JSON.stringify(want);
    console.log('  ' + label.padEnd(62) + ' ' + (ok ? 'ok' : 'FAIL'));
    if (!ok) {
        console.log('      got  ' + JSON.stringify(got));
        console.log('      want ' + JSON.stringify(want));
        failures++;
    }
}

/** Chromium, wherever this machine keeps it. */
function chromiumPath() {
    const candidates = [
        '/opt/pw-browsers/chromium-1194/chrome-linux/chrome',
        '/opt/pw-browsers/chromium/chrome-linux/chrome',
    ];
    for (const candidate of candidates) {
        if (fs.existsSync(candidate)) {
            return candidate;
        }
    }
    const found = fs.existsSync('/opt/pw-browsers')
        ? fs.readdirSync('/opt/pw-browsers')
            .filter(d => d.startsWith('chromium'))
            .map(d => `/opt/pw-browsers/${d}/chrome-linux/chrome`)
            .find(p => fs.existsSync(p))
        : null;

    return found || undefined;    // undefined lets playwright find its own
}

(async () => {
    const browser = await chromium.launch({ executablePath: chromiumPath() });
    const p = await browser.newPage();

    // The server this page thinks it is talking to: every request comes back as
    // the same table, so a filter's effect shows in the URL it asks for rather
    // than in rows we would have had to fake.
    let serverSide = true;
    await p.route('**/*', route => route.fulfill({
        status: 200, contentType: 'text/html', body: page(serverSide, ROWS) }));

    const params = () => new URL(p.url()).searchParams;
    const q = name => params().get(name);

    console.log('  a screen that paginates');
    await p.goto('http://tmm.test/widgets?sort=id&direction=asc&page=3');
    check('the row is adopted, not rebuilt', await p.$$eval('.filter-row', r => r.length), 1);

    await p.selectOption('select[data-column="candidate_id"]', '1');
    await p.waitForURL(/filter_candidate_id/);
    check('a select asks the server by id, which is what the column holds',
        q('filter_candidate_id'), '1');
    check('the sort is kept', [q('sort'), q('direction')], ['id', 'asc']);
    check('and a narrowed list starts at its own first page', q('page'), null);
    check('the select comes back showing the choice',
        await p.$eval('select[data-column="candidate_id"]', e => e.value), '1');

    console.log('  text, with its operator');
    await p.fill('input[data-column="college"]', 'bandung');
    await p.waitForURL(/filter_college=bandung/);
    check('the box asks by what was typed', q('filter_college'), 'bandung');
    check('with the operator beside it', q('filter_college_operator'), 'like');
    check('and the earlier filter is still there', q('filter_candidate_id'), '1');
    check('the box comes back filled',
        await p.$eval('input[data-column="college"]', e => e.value), 'bandung');

    await p.selectOption('select[data-column="college"]', 'starts_with');
    await p.waitForURL(/starts_with/);
    check('changing the operator asks again', q('filter_college_operator'), 'starts_with');
    check('and it comes back showing',
        await p.$eval('select[data-column="college"]', e => e.value), 'starts_with');

    console.log('  the range box, written hidden and never shown');
    check('starts hidden', await p.$eval('.filter-input-range', e => e.style.display), 'none');
    await p.fill('input[data-column="id"].filter-input', '5');
    await p.waitForURL(/filter_id=5/);
    await p.selectOption('select[data-column="id"]', 'between');
    await p.waitForURL(/filter_id_operator=between/);
    check('choosing between reveals it',
        await p.$eval('.filter-input-range', e => e.style.display), '');
    await p.fill('.filter-input-range', '12');
    await p.waitForURL(/filter_id_to=12/);
    check('and its value is sent as the far end', q('filter_id_to'), '12');
    check('it comes back filled and shown',
        await p.$eval('.filter-input-range', e => [e.value, e.style.display]), ['12', '']);

    console.log('  and it does not ask for nothing');
    const before = p.url();
    await p.fill('input[data-column="college"]', '');
    await p.waitForURL(u => u.toString() !== before);
    const quiet = p.url();
    await p.selectOption('select[data-column="college"]', 'ends_with');
    await p.waitForTimeout(700);
    check('an operator over an empty box does not reload the page', p.url(), quiet);

    await p.click('.btn-clear-filter');
    await p.waitForURL(u => !u.toString().includes('filter_'));
    check('clear drops every filter',
        [...params().keys()].filter(k => k.startsWith('filter_')), []);
    check('and keeps the sort', [q('sort'), q('direction')], ['id', 'asc']);

    console.log('  a screen that does not paginate');
    // A few index actions build their rows by hand in raw SQL. Sending filters
    // to one of those would put words in the address bar and change nothing.
    serverSide = false;
    await p.goto('http://tmm.test/certificates');
    const visible = () => p.$$eval('tbody tr',
        rows => rows.filter(r => r.style.display !== 'none').map(r => r.cells[1].textContent));

    const url = p.url();
    await p.fill('input[data-column="college"]', 'bandung');
    await p.waitForTimeout(700);
    check('nothing is asked of a screen that cannot answer', p.url(), url);
    check('the rows on the page are narrowed instead', await visible(), ['5', '30']);
    await p.selectOption('select[data-column="candidate_id"]', '1');
    check('a select there matches the name, not the id', await visible(), ['30']);
    check('and matches it whole, not as a prefix', (await visible()).includes('5'), false);
    await p.click('.btn-clear-filter');
    check('clear brings every row back', await visible(), ['1', '5', '12', '30']);

    console.log('  a table with no filter row of its own');
    await p.route('**/plain', route => route.fulfill({ status: 200, contentType: 'text/html',
        body: `<table class="table"><thead><tr><th>Name</th></tr></thead>
        <tbody><tr><td>Budi</td></tr><tr><td>Nur</td></tr></tbody></table>
        <script>${script}<\/script>` }));
    await p.goto('http://tmm.test/plain');
    check('still has one built for it', await p.$$eval('.filter-row input', r => r.length), 1);
    await p.fill('.table-filter-input', 'nur');
    check('and it filters', await p.$$eval('tbody tr',
        rows => rows.filter(r => r.style.display !== 'none').map(r => r.cells[0].textContent)),
        ['Nur']);

    console.log('  the two ways a template says "do not build one"');
    // Both were written and neither was ever honoured: the layout read the flag,
    // logged a line, selected some tables and did nothing with them.
    await p.route('**/noauto', route => route.fulfill({ status: 200, contentType: 'text/html',
        body: `<table class="table no-auto-filter"><thead><tr><th>Name</th></tr></thead>
        <tbody><tr><td>Budi</td></tr></tbody></table><script>${script}<\/script>` }));
    await p.goto('http://tmm.test/noauto');
    check('no-auto-filter is honoured', await p.$$eval('.filter-row', r => r.length), 0);

    await p.route('**/skip', route => route.fulfill({ status: 200, contentType: 'text/html',
        body: `<script>window.skipAutoFilter = true;<\/script>
        <table class="table"><thead><tr><th>Name</th></tr></thead>
        <tbody><tr><td>Budi</td></tr></tbody></table><script>${script}<\/script>` }));
    await p.goto('http://tmm.test/skip');
    check('and so is the flag the baked index sets',
        await p.$$eval('.filter-row', r => r.length), 0);

    // ...but neither means "ignore the row I wrote myself".
    await p.route('**/both', route => route.fulfill({ status: 200, contentType: 'text/html',
        body: `<script>window.skipAutoFilter = true;<\/script>
        <table class="table no-auto-filter"><thead>
        <tr><th>Name</th></tr>
        <tr class="filter-row"><td><input class="filter-input" data-column="name"></td></tr>
        </thead><tbody><tr><td>Budi</td></tr><tr><td>Nur</td></tr></tbody></table>
        <script>${script}<\/script>` }));
    await p.goto('http://tmm.test/both');
    await p.fill('.filter-input', 'nur');
    check('a row the template wrote is still bound', await p.$$eval('tbody tr',
        rows => rows.filter(r => r.style.display !== 'none').map(r => r.cells[0].textContent)),
        ['Nur']);

    await browser.close();
    console.log('\n  ' + checks + ' checks, ' + (failures ? failures + ' FAILED' : 'all good'));
    process.exit(failures ? 1 : 0);
})();
