<?php
/**
 * The stakeholder dashboard's two "waiting for you" panels.
 *
 * Both used to read tables nothing writes to. Pending Email Verifications read
 * users.verification_token, which nothing sets - the real tokens live in
 * email_verification_tokens, and there is no user row to carry them anyway until
 * the director sets a password at the end of the flow. Pending Approvals read
 * admin_approval_queue, which nothing writes to either, and no registration flow
 * has an approval step at all.
 *
 * So both were empty whatever the state of the system, while the alert at the top
 * counted institutions genuinely stuck at pending_verification and linked down to
 * a panel that always said "No pending verifications" under a green tick.
 */
require __DIR__ . '/lib/harness.php';

use Cake\I18n\Time;

/** Stands in for the ResultSet the controller hands over. */
class DashboardSet implements IteratorAggregate, Countable
{
    private $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->rows);
    }

    public function count()
    {
        return count($this->rows);
    }
}

$statistics = [
    'lpk' => ['total' => 6, 'active' => 3, 'pending_verification' => 3, 'suspended' => 0],
    'special_skill' => ['total' => 4, 'active' => 3, 'pending_verification' => 1, 'suspended' => 0],
    'acceptance_org' => ['total' => 2, 'active' => 2, 'suspended' => 0],
    'cooperative_assoc' => ['total' => 1, 'active' => 1, 'suspended' => 0],
    'overall' => ['total_stakeholders' => 13, 'total_pending_verifications' => 4,
        'total_active' => 9, 'total_suspended' => 0],
];
$charts = [
    'stakeholder_distribution' => ['labels' => ['LPK'], 'data' => [6], 'colors' => ['#667eea']],
    'status_distribution' => ['labels' => ['Active', 'Pending', 'Suspended'],
        'lpk' => [3, 3, 0], 'special_skill' => [3, 1, 0],
        'acceptance_org' => [2, 0, 0], 'cooperative_assoc' => [1, 0, 0]],
    'monthly_trend' => ['labels' => ['Apr 2026'], 'lpk' => [1],
        'special_skill' => [0], 'acceptance_org' => [0], 'cooperative_assoc' => [0]],
];

/**
 * One row of the waiting list, as _waitingRegistrations() builds it.
 *
 * @param string $type lpk or special_skill.
 * @param int $id Institution id.
 * @param string $name Institution name.
 * @param string $contact Who to talk to.
 * @param string $email Where the link went.
 * @param string $link live, expired, used, none or unknown.
 * @param string|null $expires When the link runs out.
 * @return array
 */
function waitingRow($type, $id, $name, $contact, $email, $link, $expires = null)
{
    return ['type' => $type, 'id' => $id, 'name' => $name, 'contact' => $contact,
        'email' => $email, 'since' => new Time('-6 days'), 'link' => $link,
        'expires' => $expires === null ? null : new Time($expires)];
}

$rows = [
    waitingRow('lpk', 1, 'LPK Sakura', 'Budi', 'sakura@example.com', 'live', '+10 hours'),
    waitingRow('lpk', 2, 'LPK Melati', 'Nur', 'melati@example.com', 'expired', '-10 hours'),
    waitingRow('lpk', 3, 'LPK Anggrek', 'Sri', 'anggrek@example.com', 'none'),
    waitingRow('special_skill', 1, 'PT Kirin', 'Dewi', 'kirin@example.com', 'used', '+10 hours'),
];

/**
 * Render the dashboard in one state.
 *
 * @param string $label What state this is.
 * @param array $vars waiting, pendingApprovals, approvalProblem.
 * @return string
 */
function dashboard($label, array $vars)
{
    global $statistics, $charts;

    return renderClean($label, 'index', $vars + ['statistics' => $statistics,
        'chartData' => $charts, 'recentActivities' => new DashboardSet([])],
        ['controller' => 'StakeholderDashboard',
         'templatePath' => 'Admin' . DIRECTORY_SEPARATOR . 'StakeholderDashboard',
         'url' => '/admin/stakeholder-dashboard',
         'params' => ['prefix' => 'admin', 'controller' => 'StakeholderDashboard',
            'action' => 'index']]);
}

echo "  registrations that have not finished\n";
$html = dashboard('renders', ['waiting' => ['rows' => $rows, 'statusKnown' => true,
    'tokenProblem' => null], 'pendingApprovals' => [], 'approvalProblem' => null]);
checkTrue('the panel is headed by what it lists',
    strpos($html, 'Registrations Not Finished') !== false);
checkTrue('the expired link is marked', strpos($html, 'badge-danger">Expired') !== false);
checkTrue('and said to be the one that cannot finish alone',
    strpos($html, 'cannot finish without a new link') !== false);
checkTrue('a link already followed is distinguished from one still live',
    strpos($html, 'Already used') !== false && strpos($html, 'Live') !== false);
checkTrue('an institution never sent one says so', strpos($html, 'None sent') !== false);
checkTrue('the stuck institution is named', strpos($html, 'LPK Melati') !== false);
checkTrue('with its contact', strpos($html, 'Nur') !== false);

echo "  what an admin can do about it\n";
checkTrue('an LPK row offers the link again',
    strpos($html, 'resend-verification') !== false);
// LpkRegistration::resendVerification() loads VocationalTrainingInstitutions and
// would fetch the wrong institution if handed a special skill id - both tables
// number from 1, so it would mail the wrong address rather than fail.
checkTrue('and a special skill row says there is no such screen',
    strpos($html, 'no resend screen') !== false);

echo "  an earned all-clear\n";
$html = dashboard('renders', ['waiting' => ['rows' => [], 'statusKnown' => true,
    'tokenProblem' => null], 'pendingApprovals' => [], 'approvalProblem' => null]);
checkTrue('is shown when there is nothing waiting',
    strpos($html, 'Every registration has been finished') !== false);

echo "  a state that cannot be established\n";
$html = dashboard('renders', ['waiting' => ['rows' => [], 'statusKnown' => false,
    'tokenProblem' => null], 'pendingApprovals' => [], 'approvalProblem' => null]);
checkTrue('a missing status column is said out loud',
    strpos($html, 'registration status column is not on this installation') !== false);
checkTrue('and no all-clear is claimed',
    strpos($html, 'Every registration has been finished') === false);

$html = dashboard('renders', ['waiting' => ['rows' => [$rows[0]], 'statusKnown' => true,
    'tokenProblem' => "Table 'cms_masters.email_verification_tokens' doesn't exist"],
    'pendingApprovals' => [], 'approvalProblem' => null]);
checkTrue('an unreadable token table is said out loud',
    strpos($html, 'verification links could not be read') !== false);
checkTrue('and names the error', strpos($html, 'email_verification_tokens') !== false);
checkTrue('while the institutions are still listed',
    strpos($html, 'LPK Sakura') !== false);

echo "  the approval queue\n";
$html = dashboard('renders', ['waiting' => ['rows' => [], 'statusKnown' => true,
    'tokenProblem' => null], 'pendingApprovals' => [], 'approvalProblem' => null]);
checkTrue('says nothing files into it', strpos($html, 'Nothing files into this queue.') !== false);
checkTrue('and the old green tick is gone', strpos($html, 'No pending approvals') === false);

$html = dashboard('renders', ['waiting' => ['rows' => [], 'statusKnown' => true,
    'tokenProblem' => null], 'pendingApprovals' => [],
    'approvalProblem' => "Table 'admin_approval_queue' doesn't exist"]);
checkTrue('an unreadable queue is reported rather than shown as empty',
    strpos($html, 'approval queue could not be read') !== false);

finish();
