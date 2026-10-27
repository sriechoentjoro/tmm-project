<?php
/**
 * The registration wizard, what it saves, and the CV beside it.
 *
 * Two things were wrong, and the second hid the first.
 *
 * The wizard's forms use names of their own - educations[i][institution_name],
 * families[i][full_name], certifications[i][certification_name] - and
 * saveEducations() and its siblings handed those rows straight to
 * newEntity(). Not one of those names is a column: candidate_educations holds
 * college_name and college_major, candidate_families holds name,
 * candidate_certifications holds title. An entity drops what is not a column
 * without a word, so every education, experience, family, certificate and
 * course the wizard collected was written as an empty row carrying nothing
 * but candidate_id. Nobody saw it, because nothing errored and nothing in the
 * application showed those rows back.
 *
 * And there was no preview: cv.ctp has existed all along with nothing linking
 * to it. Had there been one, the blank rows would have been obvious on the
 * first registration.
 *
 * So the map and the preview are checked together: the preview renders the
 * same element /candidates/cv/{id} renders, through the same map the save
 * uses, which is the only arrangement where what somebody sees while typing
 * is what they get afterwards.
 */
require __DIR__ . '/lib/harness.php';

use Cake\ORM\TableRegistry;

$db = sys_get_temp_dir() . '/tmm_candidate_wizard.sqlite';
@unlink($db);
$conn = sqliteConnections(['cms_lpk_candidates', 'cms_masters', 'default'], $db);
$conn->execute('CREATE TABLE candidates (id INTEGER PRIMARY KEY, name VARCHAR(100),
    name_katakana VARCHAR(255), identity_number VARCHAR(50), candidate_code VARCHAR(10),
    birth_place VARCHAR(100), birth_date DATE, master_gender_id INTEGER,
    master_religion_id INTEGER, master_marriage_status_id INTEGER, blood_type_id INTEGER,
    body_height INTEGER, body_weight INTEGER, telephone_mobile VARCHAR(20),
    telephone_emergency VARCHAR(256), email VARCHAR(100), address VARCHAR(256),
    post_code VARCHAR(6), image_photo VARCHAR(255), strengths TEXT, weaknesses TEXT,
    hobby TEXT, application_reasons TEXT, master_propinsi_id INTEGER,
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
$conn->execute('CREATE TABLE candidate_certifications (id INTEGER PRIMARY KEY, candidate_id INTEGER,
    title VARCHAR(255), institution_name VARCHAR(255), certification_date DATE, detail TEXT)');
$conn->execute('CREATE TABLE candidate_courses (id INTEGER PRIMARY KEY, candidate_id INTEGER,
    vocational_training_institution_id INTEGER, master_course_major_id INTEGER,
    course_year INTEGER, title VARCHAR(255), detail TEXT)');
foreach (['master_genders', 'master_religions', 'master_marriage_statuses',
    'master_blood_types', 'master_propinsis', 'master_stratas',
    'master_family_connections', 'master_occupations'] as $table) {
    $conn->execute("CREATE TABLE $table (id INTEGER PRIMARY KEY, title VARCHAR(100))");
}
$conn->insert('master_genders', ['id' => 1, 'title' => 'Male']);
$conn->insert('master_stratas', ['id' => 3, 'title' => 'SMA']);
$conn->insert('master_family_connections', ['id' => 2, 'title' => 'Father']);

// What the wizard's own forms post, spelled exactly as
// webroot/js/wizard-dynamic-forms.js spells them.
$wizardData = [
    'identity_number' => '3374010101990001',
    'name' => 'KIKIWAHYUDDIN',
    'birth_place' => 'KARAWANG',
    'birth_date' => '1998-02-23',
    'master_gender_id' => 1,
    'telephone_emergency' => '085624277298',
    'address' => 'Jalan Satu',
    'educations' => [[
        'institution_name' => 'SMA Negeri 1 Karawang',
        'level' => 'SMA',
        'field_of_study' => 'IPA',
        'year_graduated' => '2016',
    ]],
    'experiences' => [[
        'company_name' => 'PT Satu',
        'position' => 'Operator',
        'start_date' => '2017-03-01',
        'end_date' => '2019-08-01',
        'job_description' => 'Perakitan',
    ]],
    'families' => [[
        'full_name' => 'Bapak Ahmad',
        'relationship' => 'Father',
        'age' => '55',
        'occupation' => 'Petani',
        'contact_number' => '08123456789',
    ]],
    'certifications' => [[
        'certification_name' => 'Las Dasar',
        'issuing_organization' => 'BLK Karawang',
        'issue_date' => '2017-05-20',
        'expiry_date' => '2027-05-20',
    ]],
    'courses' => [[
        'course_name' => 'Bahasa Jepang N5',
        'training_institution' => 'LPK Sejahtera',
        'start_date' => '2020-01-15',
    ]],
    // A row somebody opened and left alone.
    'educations_blank' => [],
];

list($controller, $rows) = reachInto('App\Controller\CandidatesController', 'wizardRows');

/**
 * Map one kind of row the way the save does.
 *
 * @param string $kind educations, experiences, and so on.
 * @return array The first mapped row, or [].
 */
function mapped($kind)
{
    global $controller, $rows, $wizardData;
    $out = $rows->invoke($controller, $kind, $wizardData[$kind]);

    return $out ? $out[0] : [];
}

echo "  an education, as the column names really are\n";
$edu = mapped('educations');
check('the school goes in college_name', $edu['college_name'], 'SMA Negeri 1 Karawang');
check('the subject in college_major', $edu['college_major'], 'IPA');
check('the year it ended becomes a date the column can hold',
    $edu['college_graduate_date'], '2016-01-01');
check('and the level becomes the id of that level',
    $edu['master_strata_id'], 3);
checkTrue('none of the form\'s own names survive into the row',
    !isset($edu['institution_name']) && !isset($edu['field_of_study'])
    && !isset($edu['level']) && !isset($edu['year_graduated']));

echo "  a work experience\n";
$exp = mapped('experiences');
check('the role goes in title, which is what that column is called',
    $exp['title'], 'Operator');
check('the dates become the years the columns hold',
    $exp['employment_start_year'] . '-' . $exp['employment_end_year'], '2017-2019');
check('and the description in job_detail', $exp['job_detail'], 'Perakitan');

echo "  a family member\n";
$fam = mapped('families');
check('the name goes in name', $fam['name'], 'Bapak Ahmad');
check('the age is a number', $fam['age'], 55);
check('the relationship becomes its id', $fam['master_family_connection_id'], 2);
checkTrue('and what has no column of its own is kept rather than dropped',
    strpos($fam['detail'], 'Petani') !== false
    && strpos($fam['detail'], '08123456789') !== false);

echo "  a certificate and a course\n";
$cert = mapped('certifications');
check('the certificate name goes in title', $cert['title'], 'Las Dasar');
check('the issuer in institution_name', $cert['institution_name'], 'BLK Karawang');
check('the issue date is a date', $cert['certification_date'], '2017-05-20');
checkTrue('the expiry is kept in detail, having no column',
    strpos($cert['detail'], '2027-05-20') !== false);
$course = mapped('courses');
check('the course name goes in title', $course['title'], 'Bahasa Jepang N5');
check('and the year is taken from the date', $course['course_year'], 2020);

echo "  rows nobody filled in\n";
$empty = $rows->invoke($controller, 'educations', [[
    'institution_name' => '', 'level' => '', 'field_of_study' => '', 'year_graduated' => '',
]]);
check('are not written as records', count($empty), 0);

echo "  every mapped name is a column that exists\n";
// The fault being fixed, stated as a rule: anything the map produces has to
// be a column, or it is dropped exactly as the form's own names were.
$columns = [
    'educations' => 'candidate_educations',
    'experiences' => 'candidate_experiences',
    'families' => 'candidate_families',
    'certifications' => 'candidate_certifications',
    'courses' => 'candidate_courses',
];
foreach ($columns as $kind => $table) {
    $schema = TableRegistry::getTableLocator()
        ->get(\Cake\Utility\Inflector::camelize($table))->getSchema();
    $stray = [];
    foreach (array_keys(mapped($kind)) as $column) {
        if (!$schema->hasColumn($column)) {
            $stray[] = $column;
        }
    }
    check($kind . ' map writes only real columns', implode(', ', $stray), '');
}

echo "  the CV beside the form\n";
list($maker, $build) = reachInto('App\Controller\CandidatesController', 'wizardCandidateEntity');
$candidate = $build->invoke($maker, $wizardData);
check('carries the name that was typed', $candidate->name, 'KIKIWAHYUDDIN');
check('and the education that was added', count($candidate->candidate_educations), 1);
check('under the property the CV reads',
    $candidate->candidate_educations[0]->college_name, 'SMA Negeri 1 Karawang');

$html = renderClean('the CV renders from it', 'cv_preview',
    ['cvCandidate' => $candidate], [
        'controller' => 'Candidates', 'templatePath' => 'Candidates',
        'url' => '/candidates/cv-preview',
        'params' => ['controller' => 'Candidates', 'action' => 'cvPreview'],
    ]);
checkTrue('showing the school just entered',
    strpos($html, 'SMA Negeri 1 Karawang') !== false);
checkTrue('the company', strpos($html, 'PT Satu') !== false);
checkTrue('the family member', strpos($html, 'Bapak Ahmad') !== false);
checkTrue('and the certificate', strpos($html, 'Las Dasar') !== false);
// The markup, not the stylesheet: the CSS rule for the toolbar is in the
// element's <style> block either way, and matching that would be matching
// the styling rather than the buttons.
check('with no toolbar, there being nothing yet to export',
    strpos($html, 'class="cvdoc-toolbar no-print"'), false);

echo "  one CV, not two\n";
$cv = file_get_contents(TMM_ROOT . '/src/Template/Candidates/cv.ctp');
$preview = file_get_contents(TMM_ROOT . '/src/Template/Candidates/cv_preview.ctp');
checkTrue('the saved CV renders the shared element',
    strpos($cv, "element('Candidates/cv_body'") !== false);
checkTrue('and so does the preview',
    strpos($preview, "element('Candidates/cv_body'") !== false);
$wizard = file_get_contents(TMM_ROOT . '/src/Template/Candidates/wizard.ctp');
checkTrue('the wizard shows it beside the form',
    strpos($wizard, 'cv-live-panel') !== false
    && strpos($wizard, "element('Candidates/cv_body'") !== false);

echo "  how it is reached, and by whom\n";
$index = file_get_contents(TMM_ROOT . '/src/Template/Candidates/index.ctp');
$view = file_get_contents(TMM_ROOT . '/src/Template/Candidates/view.ctp');
checkTrue('a CV button on every row of the list',
    strpos($index, "'action' => 'cv'") !== false);
checkTrue('and on the candidate\'s own page',
    strpos($view, "'action' => 'cv'") !== false);
$source = file_get_contents(TMM_ROOT . '/src/Controller/CandidatesController.php');
checkTrue('the CV is granted where view is granted',
    strpos($source, "\$action === 'cv' || \$action === 'preview'") !== false);
checkTrue('and the live preview where add is granted',
    strpos($source, "\$action === 'wizard' || \$action === 'cvPreview'") !== false);
checkTrue('the preview writes nothing',
    strpos($source, "public function cvPreview()") !== false
    && strpos($source, "allowMethod(['post'])") !== false);

echo "  the way in\n";
// The wizard had none. Menu 24, "Register New Candidate", goes to
// /candidates/add - the plain form, no preview - and the only links to the
// wizard were a button on the candidates dashboard and one on the help page.
// The screen was reachable by typing the address and no other way.
$migration = TMM_ROOT . '/database/migrations/add_candidate_wizard_menu.sql';
checkTrue('there is a migration that puts it in the menu', is_file($migration));
$sql = is_file($migration) ? file_get_contents($migration) : '';
checkTrue('pointing at the wizard', strpos($sql, "'/candidates/wizard'") !== false);
checkTrue('under the same Recruitment parent as the plain form',
    strpos($sql, 'parent_id = 23') !== false && strpos($sql, 'SELECT 23,') !== false);
checkTrue('and it leaves the plain form where it is',
    strpos($sql, 'UPDATE menus') !== false && strpos($sql, 'DELETE') === false);
checkTrue('every role that has the plain form gets this one too',
    strpos($sql, 'WHERE rm.menu_id = 24') !== false);
checkTrue('running it twice changes nothing the second time',
    strpos($sql, '@already') !== false && substr_count($sql, '@already = 0') >= 2);

echo "  and the title it will show\n";
foreach (['id', 'ja'] as $locale) {
    $po = file_get_contents(TMM_ROOT . '/src/Locale/' . $locale . '/default.po');
    // elegant_menu.ctp renders __($menu->title), so a menu title with no
    // translation shows in English on an Indonesian screen.
    checkTrue($locale . ' translates the new menu title',
        strpos($po, 'msgid "Register Candidate with CV Preview"') !== false);
}

echo "  what that menu grants, and what it does not\n";
// granted_actions = '*' expands to [the menu's own action, index, view]. For
// this row that is [wizard, index, view] - which does not contain add. Read
// against add alone, the wizard would have refused itself to the one role
// whose menu is the wizard.
class WizardGate extends \App\Controller\CandidatesController
{
    public $permissions = [];

    protected function getMenuRolePermissions()
    {
        return $this->permissions;
    }

    protected function handleUnauthorizedAccess($action = 'access', $reason = null)
    {
        return null;
    }
}

/**
 * Ask the gate about one action for a role holding the given permissions.
 *
 * @param string $action The action attempted.
 * @param array $permissions controller => granted actions.
 * @return bool
 */
function wizardGate($action, array $permissions)
{
    $user = ['id' => 5, 'username' => 'lpk', 'role_names' => ['lpk-penyangga']];
    $session = new \Cake\Http\Session();
    $session->write('Auth.User', $user);
    $request = new \Cake\Http\ServerRequest([
        'url' => '/candidates/' . $action,
        'session' => $session,
        'params' => ['controller' => 'Candidates', 'action' => $action,
            'plugin' => null, 'pass' => []],
    ]);
    $controller = new WizardGate($request, new \Cake\Http\Response());
    $controller->permissions = $permissions;

    return (bool)$controller->isAuthorized($user);
}

$fromPlainForm = ['Candidates' => ['add', 'index', 'view']];
$fromWizardMenu = ['Candidates' => ['wizard', 'index', 'view']];
foreach (['wizard', 'cvPreview'] as $action) {
    checkTrue('a role that may use the plain form may ' . $action,
        wizardGate($action, $fromPlainForm));
    checkTrue('and so may one whose own menu is the wizard: ' . $action,
        wizardGate($action, $fromWizardMenu));
    checkTrue('a role granted neither may not ' . $action,
        !wizardGate($action, ['Candidates' => ['index', 'view']]));
}
checkTrue('the wizard menu does not quietly grant the plain form as well',
    !wizardGate('add', $fromWizardMenu));

finish($db);
