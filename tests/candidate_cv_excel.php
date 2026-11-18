<?php
/**
 * Export Excel, and whether it hands over the same CV the screen shows.
 *
 * It did not. The page became the LPK's own form - letterhead, 企業名, the
 * head block, 確認事項, 学歴, 職歴, 家族構成 - and the workbook went on
 * writing the CV this application drew before that: "Basic Information",
 * "Contact Information", "Additional Information", with none of 確認事項 in
 * it at all. Nobody saw it, because a download is the one thing nobody looks
 * at twice: it opens in another program, on somebody else's desk.
 *
 * A spreadsheet cannot render a template, so there will always be two
 * renderers. What there is no longer is two sets of labels and two ways of
 * formatting a value: App\View\CandidateCvForm holds both, and the page and
 * the workbook ask it. This holds them to that - every label the form
 * declares has to appear in the workbook AND in the rendered page, and the
 * values have to match down to 有 rather than 1 and Rp280,000,000 rather
 * than 280000000.
 */
require __DIR__ . '/lib/harness.php';

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

$db = sys_get_temp_dir() . '/tmm_candidate_cv_excel.sqlite';
@unlink($db);
$conn = sqliteConnections(['cms_lpk_candidates', 'cms_masters', 'cms_tmm_stakeholders',
    'cms_tmm_trainees', 'default'], $db);

// Spelled as the real tables spell them; checkFixtureColumns below holds them
// to the dumps the databases wrote.
$candidateColumns = ['id', 'name', 'name_katakana', 'identity_number', 'candidate_code',
    'birth_place', 'birth_place_katakana', 'birth_date', 'master_gender_id',
    'master_religion_id', 'master_marriage_status_id', 'master_blood_type_id',
    'body_height', 'body_weight', 'telephone_mobile', 'address', 'post_code',
    'image_photo', 'strengths', 'weaknesses', 'hobby', 'application_reasons',
    'last_salary_amount', 'saving_goal_amount', 'is_ever_went_to_japan',
    'is_holding_passport', 'is_wear_eye_glasses', 'is_color_blind', 'is_right_handed',
    'is_smoking', 'is_drinking_alcohol', 'is_tattooed', 'will_go_to_japan_after_finished',
    'expected_work_upon_returning_to_japan', 'acceptance_organization_id',
    'apprentice_order_id', 'master_propinsi_id', 'master_kabupaten_id',
    'master_kecamatan_id', 'master_kelurahan_id'];

$conn->execute('CREATE TABLE candidates (id INTEGER PRIMARY KEY, name VARCHAR(100),
    name_katakana VARCHAR(255), identity_number VARCHAR(50), candidate_code VARCHAR(10),
    birth_place VARCHAR(100), birth_place_katakana VARCHAR(100), birth_date DATE,
    master_gender_id INTEGER, master_religion_id INTEGER, master_marriage_status_id INTEGER,
    master_blood_type_id INTEGER, body_height DECIMAL, body_weight DECIMAL,
    telephone_mobile VARCHAR(20), address VARCHAR(256), post_code VARCHAR(6),
    image_photo VARCHAR(255), strengths TEXT, weaknesses TEXT, hobby TEXT,
    application_reasons TEXT, last_salary_amount INTEGER, saving_goal_amount INTEGER,
    is_ever_went_to_japan INTEGER, is_holding_passport INTEGER, is_wear_eye_glasses INTEGER,
    is_color_blind INTEGER, is_right_handed INTEGER, is_smoking INTEGER,
    is_drinking_alcohol INTEGER, is_tattooed INTEGER, will_go_to_japan_after_finished INTEGER,
    expected_work_upon_returning_to_japan VARCHAR(255), acceptance_organization_id INTEGER,
    apprentice_order_id INTEGER, master_propinsi_id INTEGER, master_kabupaten_id INTEGER,
    master_kecamatan_id INTEGER, master_kelurahan_id INTEGER,
    vocational_training_institution_id INTEGER, created DATETIME, modified DATETIME)');
$conn->execute('CREATE TABLE candidate_educations (id INTEGER PRIMARY KEY, candidate_id INTEGER,
    master_strata_id INTEGER, master_propinsi_id INTEGER, master_kabupaten_id INTEGER,
    college_entry_date DATE, college_graduate_date DATE, college_name VARCHAR(255),
    college_major VARCHAR(255))');
$conn->execute('CREATE TABLE candidate_experiences (id INTEGER PRIMARY KEY, candidate_id INTEGER,
    employment_start_year INTEGER, employment_end_year INTEGER, company_name VARCHAR(255),
    title VARCHAR(255), master_employee_status_id INTEGER, employment_awards TEXT,
    last_salary_amount INTEGER, job_detail TEXT, termination_reasons TEXT)');
$conn->execute('CREATE TABLE candidate_families (id INTEGER PRIMARY KEY, candidate_id INTEGER,
    master_family_connection_id INTEGER, name VARCHAR(255), age INTEGER,
    master_occupation_id INTEGER, detail TEXT)');
$conn->execute('CREATE TABLE candidate_certifications (id INTEGER PRIMARY KEY,
    candidate_id INTEGER, title VARCHAR(255), institution_name VARCHAR(255),
    certification_date DATE, detail TEXT)');
$conn->execute('CREATE TABLE candidate_courses (id INTEGER PRIMARY KEY, candidate_id INTEGER,
    vocational_training_institution_id INTEGER, master_course_major_id INTEGER,
    course_year INTEGER, title VARCHAR(255), detail TEXT)');
$conn->execute('CREATE TABLE apprentice_orders (id INTEGER PRIMARY KEY,
    cooperative_association_id INTEGER, acceptance_organization_id INTEGER,
    title VARCHAR(256), master_job_category_id INTEGER, departure_year INTEGER,
    departure_month VARCHAR(100), is_practical_test_required INTEGER)');
$conn->execute('CREATE TABLE acceptance_organizations (id INTEGER PRIMARY KEY,
    title VARCHAR(255), master_japan_prefecture_id INTEGER, post_code VARCHAR(10),
    address VARCHAR(255), director VARCHAR(255), director_hiragana VARCHAR(255))');
foreach (['master_genders', 'master_religions', 'master_marriage_statuses'] as $table) {
    $conn->execute("CREATE TABLE $table (id INTEGER PRIMARY KEY, title VARCHAR(100),
        title_jpg VARCHAR(100))");
}
foreach (['master_blood_types', 'master_propinsis', 'master_kabupatens',
    'master_kecamatans', 'master_kelurahans', 'master_occupations'] as $table) {
    $conn->execute("CREATE TABLE $table (id INTEGER PRIMARY KEY, title VARCHAR(100))");
}
$conn->execute('CREATE TABLE master_stratas (id INTEGER PRIMARY KEY, title VARCHAR(100),
    title_hiragana VARCHAR(100))');
$conn->execute('CREATE TABLE master_family_connections (id INTEGER PRIMARY KEY,
    title VARCHAR(100), title_eng VARCHAR(100), title_kanji VARCHAR(100))');

$conn->insert('master_genders', ['id' => 1, 'title' => 'Male', 'title_jpg' => '男']);
$conn->insert('master_religions', ['id' => 2, 'title' => 'Islam', 'title_jpg' => 'イスラム']);
$conn->insert('master_marriage_statuses', ['id' => 1, 'title' => 'Single', 'title_jpg' => '未婚']);
$conn->insert('master_blood_types', ['id' => 2, 'title' => 'B']);
$conn->insert('master_propinsis', ['id' => 9, 'title' => 'JAWA BARAT']);
$conn->insert('master_kabupatens', ['id' => 7, 'title' => 'KOTA BEKASI']);
$conn->insert('master_kecamatans', ['id' => 5, 'title' => 'BEKASI TIMUR']);
$conn->insert('master_kelurahans', ['id' => 4, 'title' => 'BEKASI JAYA']);
$conn->insert('master_stratas', ['id' => 3, 'title' => 'SMA', 'title_hiragana' => '高等学校']);
$conn->insert('master_family_connections',
    ['id' => 2, 'title' => 'Father', 'title_eng' => 'Father', 'title_kanji' => '父']);
$conn->insert('master_occupations', ['id' => 11, 'title' => 'Petani']);
$conn->insert('acceptance_organizations', ['id' => 6, 'title' => 'サン工業株式会社']);
$conn->insert('acceptance_organizations', ['id' => 8, 'title' => '北海道水産株式会社']);
$conn->insert('apprentice_orders', ['id' => 3, 'acceptance_organization_id' => 8,
    'title' => 'Order 2026']);

$conn->insert('candidates', [
    'id' => 1,
    'name' => 'BINTANG RAMADHAN',
    'name_katakana' => 'ビンタン ラマダン',
    'identity_number' => '3275010101070001',
    'candidate_code' => 'C-001',
    'birth_place' => 'BEKASI',
    'birth_place_katakana' => 'ベカシ',
    'birth_date' => '2007-09-29',
    'master_gender_id' => 1,
    'master_religion_id' => 2,
    'master_marriage_status_id' => 1,
    'master_blood_type_id' => 2,
    'body_height' => 171,
    'body_weight' => 60,
    'telephone_mobile' => '0857 1452 4064',
    'address' => 'JL.KH. AGUS SALIM NO.34',
    'post_code' => '17112',
    'strengths' => '責任感がある',
    'weaknesses' => '緊張しやすい',
    'hobby' => '水泳',
    'application_reasons' => 'ビジネス資本を増やします',
    'last_salary_amount' => 700000,
    'saving_goal_amount' => 280000000,
    'is_ever_went_to_japan' => 0,
    'is_holding_passport' => 0,
    'is_wear_eye_glasses' => 1,
    'is_color_blind' => 0,
    'is_right_handed' => 1,
    'is_smoking' => 0,
    'is_drinking_alcohol' => 0,
    'is_tattooed' => 0,
    'will_go_to_japan_after_finished' => 1,
    'expected_work_upon_returning_to_japan' => '3号（SANGO）',
    'acceptance_organization_id' => 6,
    'apprentice_order_id' => 3,
    'master_propinsi_id' => 9,
    'master_kabupaten_id' => 7,
    'master_kecamatan_id' => 5,
    'master_kelurahan_id' => 4,
]);
$conn->insert('candidate_educations', ['id' => 1, 'candidate_id' => 1, 'master_strata_id' => 3,
    'college_graduate_date' => '2025-06-01', 'college_name' => 'SMKS KARYA GUNA 2 BEKASI',
    'college_major' => 'TEKNIK KOMPUTER JARINGAN']);
$conn->insert('candidate_experiences', ['id' => 1, 'candidate_id' => 1,
    'employment_start_year' => 2025, 'employment_end_year' => 2026,
    'company_name' => 'CV JAYA ABADI TEKNICAL', 'title' => 'OPERATOR PELAPIS CAT CHROME']);
$conn->insert('candidate_families', ['id' => 1, 'candidate_id' => 1,
    'master_family_connection_id' => 2, 'name' => 'EMRIKO ROZALI', 'age' => 52,
    'master_occupation_id' => 11]);
$conn->insert('candidate_certifications', ['id' => 1, 'candidate_id' => 1,
    'title' => 'Las Dasar', 'institution_name' => 'BLK Karawang',
    'certification_date' => '2024-05-20']);

/**
 * The controller, built as a request builds it.
 *
 * @param string $action The action being run.
 * @return \App\Controller\CandidatesController
 */
function candidatesController($action)
{
    $session = new Session();
    $session->write('Auth.User', ['id' => 1, 'username' => 'admin',
        'role_names' => ['administrator']]);
    $request = new ServerRequest([
        'url' => '/candidates/' . $action,
        'session' => $session,
        'params' => ['controller' => 'Candidates', 'action' => $action,
            'plugin' => null, 'pass' => []],
    ]);
    Router::reload();
    Router::connect('/:controller/:action/*', [],
        ['routeClass' => 'Cake\Routing\Route\DashedRoute']);
    Router::setRequestInfo($request);

    return new \App\Controller\CandidatesController($request, new Response());
}

echo "  the download itself\n";
$controller = candidatesController('exportCvExcel');
$response = $controller->exportCvExcel(1);
$body = (string)$response->getBody();

checkTrue('Export Excel answers with something', $body !== '');
// PK is the first two bytes of every zip, and an .xlsx is a zip. A CSV
// renamed .xlsx is the failure this catches.
check('and it really is a spreadsheet, not a CSV in disguise', substr($body, 0, 2), 'PK');
checkTrue('named for the candidate',
    strpos($response->getHeaderLine('Content-Disposition'), '.xlsx') !== false);

$file = sys_get_temp_dir() . '/tmm_cv_export.xlsx';
file_put_contents($file, $body);
$book = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx')->load($file);
$sheet = $book->getActiveSheet();
check('on a sheet named for what it holds', $sheet->getTitle(), 'CV');

$cells = [];
foreach ($sheet->toArray(null, true, false, false) as $row) {
    foreach ($row as $cell) {
        if ($cell !== null && trim((string)$cell) !== '') {
            $cells[] = trim((string)$cell);
        }
    }
}
$workbook = implode("\n", $cells);
checkTrue('with the form written into it (' . count($cells) . ' filled cells)',
    count($cells) > 60);

echo "  the same form the page prints\n";
$candidate = TableRegistry::getTableLocator()->get('Candidates')->get(1, [
    'contain' => [
        'CandidateEducations' => ['MasterStratas'],
        'CandidateExperiences',
        'CandidateFamilies' => ['MasterFamilyConnections', 'MasterOccupations'],
        'CandidateCertifications', 'CandidateCourses',
        'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterBloodTypes',
        'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans',
        'AcceptanceOrganizations', 'ApprenticeOrders' => ['AcceptanceOrganizations'],
    ],
]);
$page = renderClean('the page renders from the same record', 'cv_preview',
    ['cvCandidate' => $candidate], [
        'controller' => 'Candidates', 'templatePath' => 'Candidates',
        'url' => '/candidates/cv-preview',
        'params' => ['controller' => 'Candidates', 'action' => 'cvPreview'],
    ]);

// Every label the form declares, in both. This is the whole point: a label
// changed in one and not the other is the drift that put "Basic Information"
// in a download of a document that has no such heading.
// Compared against the page's text rather than its markup: a label holding
// a quote or an ampersand reaches the browser as &quot; and &amp;, and
// matching the raw label against the escaped markup would report drift that
// is not there.
$readable = html_entity_decode($page, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$form = new \App\View\CandidateCvForm($candidate);
$missingFromBook = [];
$missingFromPage = [];
foreach ($form->labels() as $key => $pair) {
    foreach ($pair as $half) {
        if ($half === '') {
            continue;
        }
        $text = $half === $pair[1] ? __($half) : $half;
        if (strpos($workbook, $text) === false) {
            $missingFromBook[] = $key . ': ' . $text;
        }
        if (strpos($readable, $text) === false) {
            $missingFromPage[] = $key . ': ' . $text;
        }
    }
}
check('every label on the form is in the workbook', implode(', ', $missingFromBook), '');
check('and every one of them is on the page', implode(', ', $missingFromPage), '');

echo "  and the same values\n";
$missingValues = [];
foreach ($form->values() as $key => $value) {
    if ($value === '') {
        continue;
    }
    if (strpos($workbook, $value) === false) {
        $missingValues[] = $key . ': ' . $value;
    }
}
check('every value the form holds is in the workbook', implode(', ', $missingValues), '');

// Formatted, not raw. These are the four the page was already checked for,
// asked of the download instead.
checkTrue('a flag reads 有 and 無, not 1 and 0',
    strpos($workbook, '有') !== false && strpos($workbook, '無') !== false
    && strpos($workbook, '是') === false);
checkTrue('money carries its separators', strpos($workbook, 'Rp280,000,000') !== false);
checkTrue('the date is written the Japanese way', strpos($workbook, '2007年9月29日') !== false);
checkTrue('the address is assembled from its four region rows',
    strpos($workbook, 'BEKASI JAYA BEKASI TIMUR KOTA BEKASI JAWA BARAT 〒17112') !== false);

echo "  what the form has and the old workbook had not\n";
// 確認事項 was absent from the download altogether.
foreach (['確認事項', '長所', '短所', '視力', '色覚異常', '利き手', '喫煙', '刺青', '飲酒'] as $ja) {
    checkTrue($ja . ' is in the download', strpos($workbook, $ja) !== false);
}
// And the headings of the workbook it used to be are gone.
foreach (['Basic Information', 'Contact Information', 'Additional Information'] as $old) {
    check('"' . $old . '" is not a heading on this form', strpos($workbook, $old), false);
}

echo "  the interviewer's own column\n";
// Blank on purpose: it is where whoever runs the interview writes on the
// sheet. What is checked is that it is there, headed, on the right of the
// head block, and that it stops above 確認事項 rather than running on.
checkTrue('Catatan is on the download', strpos($workbook, 'Catatan') !== false);
$merged = array_keys($sheet->getMergeCells());
$notes = array_values(array_filter($merged, function ($range) {
    return strpos($range, 'H') === 0;
}));
check('as one tall cell in the last column', count($notes), 1);
if ($notes) {
    list($top, $foot) = explode(':', $notes[0]);
    $topRow = (int)preg_replace('/\D/', '', $top);
    $footRow = (int)preg_replace('/\D/', '', $foot);
    // Nine rows of head block and three of address: the same twelve the page
    // spans.
    check('spanning the head block and the address under it', $footRow - $topRow + 1, 12);
    checkTrue('and the confirmation points start below it',
        strpos($sheet->getCell('A' . ($footRow + 2))->getValue(), '確認事項') === 0);
}

echo "  the rows that hang off the candidate\n";
checkTrue('the school', strpos($workbook, 'SMKS KARYA GUNA 2 BEKASI') !== false);
checkTrue('the employer worked for', strpos($workbook, 'CV JAYA ABADI TEKNICAL') !== false);
checkTrue('the family member', strpos($workbook, 'EMRIKO ROZALI') !== false);
checkTrue('their tie, in kanji', strpos($workbook, '父') !== false);
checkTrue('their occupation from its own column', strpos($workbook, 'Petani') !== false);
checkTrue('the certificate the form has no row for', strpos($workbook, 'Las Dasar') !== false);

echo "  the employer, read and never written\n";
checkTrue('the candidate\'s own employer is the one printed',
    strpos($workbook, 'サン工業株式会社') !== false);
check('not the order\'s, which is only the fallback',
    strpos($workbook, '北海道水産株式会社'), false);
$source = file_get_contents(TMM_ROOT . '/src/Controller/CandidatesController.php');
check('and no company name is written into the exporter',
    preg_match('/サン工業|Sun Kogyo/u', $source), 0);

echo "  the fixture against the real tables\n";
checkFixtureColumns('candidates', $candidateColumns);
checkFixtureColumns('apprentice_orders', ['id', 'acceptance_organization_id', 'title']);
checkFixtureColumns('acceptance_organizations', ['id', 'title']);

@unlink($file);
finish($db);
