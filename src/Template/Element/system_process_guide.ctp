<?php
/**
 * The TMM process, end to end. One document, two front doors.
 *
 * It used to be two documents. Users/guide.ctp was the public one, reachable
 * from the login page; Dashboard/process_flow.ctp was the sysdoc behind the
 * leftmost menu tab. They described the same pipeline in different words, and
 * drifted: the guide already promised "Create Apprentice Order & Share to LPK"
 * while no such code existed, and the sysdoc knew nothing about how an LPK is
 * onboarded. Two descriptions of one process is one description too many.
 *
 * What each page keeps is what actually differs:
 *
 *   Users/guide            public, no login, standalone layout - so no role
 *                          highlighting, because nobody is signed in
 *   Dashboard/processFlow  inside the application menu, and $userRoles marks
 *                          the phases the reader actually works in
 *
 * Both hosts supply their own mermaid loader: the process_flow layout has one,
 * and the elegant layout gets it from the process_flow_assets element. Putting
 * it here as well would load it twice.
 *
 * Text goes through __() rather than a trilingual array literal. The array was
 * easier to read three languages at once, but nothing could see it -
 * bin/i18n-coverage.php reads __() and reports what is missing, and content it
 * cannot see is content nobody notices has gone untranslated.
 *
 * @var \App\View\AppView $this
 * @var array $userRoles Empty when nobody is signed in.
 */
$userRoles = isset($userRoles) ? (array)$userRoles : [];

$colour = [
    'recruitment'   => '#4c5bd4',
    'lpk'           => '#00838f',
    'training'      => '#e67e22',
    'documentation' => '#8e44ad',
    'accounting'    => '#16a085',
    'management'    => '#576574',
];

/** Which roles work in which phase, for the "your role" marker. */
$phaseRoles = [
    'stakeholders'  => ['tmm-recruitment'],
    'order'         => ['tmm-recruitment'],
    'recruitment'   => ['lpk-penyangga', 'tmm-recruitment'],
    'training'      => ['tmm-training'],
    'documentation' => ['tmm-documentation'],
    'apprenticeship' => ['tmm-documentation'],
    'accounting'    => ['accounting'],
    'administration' => ['administrator'],
];
$mine = function ($key) use ($phaseRoles, $userRoles) {
    if (!$userRoles || in_array('administrator', $userRoles, true)) {
        // An administrator works everywhere, so marking every phase would mark
        // nothing. Signed out, there is no role to mark either.
        return false;
    }
    foreach ($phaseRoles[$key] ?? [] as $role) {
        if (in_array($role, $userRoles, true)) {
            return true;
        }
    }

    return false;
};
?>
<style>
.spg-intro { background: linear-gradient(135deg,#e3f2fd,#f3e5f5); border-radius: 14px; padding: 20px 24px; margin-bottom: 24px; border-left: 5px solid #667eea; }
.spg-intro h2 { margin: 0 0 6px; color: #3a2b6e; font-size: 20px; }
.spg-intro p { margin: 0; color: #555; line-height: 1.7; }
.spg-legend { display: flex; flex-wrap: wrap; gap: 10px; margin: 18px 0 26px; }
.spg-role { display: inline-flex; align-items: center; gap: 7px; padding: 6px 13px; border-radius: 20px; color: #fff; font-size: 12.5px; font-weight: 700; box-shadow: 0 2px 5px rgba(0,0,0,.12); }
.spg-phase-head { display: flex; align-items: center; gap: 14px; margin: 34px 0 18px; }
.spg-num { flex-shrink: 0; width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 20px; font-weight: 800; box-shadow: 0 3px 8px rgba(0,0,0,.18); }
.spg-phase-head h3 { margin: 0; font-size: 21px; color: #2c2150; }
.spg-phase-head .sub { font-size: 13px; color: #8a8a9a; font-weight: 500; margin-top: 2px; }
.spg-yours { display: inline-block; margin-left: 10px; padding: 2px 10px; border-radius: 12px; background: #e8f5e9; color: #2e7d32; font-size: 11.5px; font-weight: 700; vertical-align: middle; }
.spg-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 14px; }
.spg-step { background: #fff; border: 1px solid #e7e9f0; border-radius: 12px; padding: 16px 18px; box-shadow: 0 1px 4px rgba(0,0,0,.05); position: relative; }
.spg-step.mine { border-color: #a5d6a7; box-shadow: 0 2px 10px rgba(46,125,50,.12); }
.spg-step-n { position: absolute; top: 14px; right: 16px; font-size: 12px; font-weight: 700; color: #b9bed0; }
.spg-step-top { display: flex; align-items: center; gap: 11px; margin-bottom: 9px; }
.spg-ico { flex-shrink: 0; width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 15px; }
.spg-step h4 { margin: 0; font-size: 15.5px; color: #2c2150; line-height: 1.35; }
.spg-step h4 small { display: block; font-size: 11.5px; color: #98a0b3; font-weight: 500; margin-top: 2px; }
.spg-step p { margin: 0 0 10px; color: #5a5f70; font-size: 13.5px; line-height: 1.65; }
.spg-menu { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; margin: 0 6px 6px 0; border: 1px solid #dfe3ec; border-radius: 16px; font-size: 12px; color: #4c5bd4; text-decoration: none; background: #f8f9fc; }
.spg-menu:hover { background: #4c5bd4; color: #fff; border-color: #4c5bd4; text-decoration: none; }
.spg-sub { margin: 8px 0 0; padding-left: 18px; color: #6b7280; font-size: 12.5px; line-height: 1.8; }
.spg-arrow { text-align: center; color: #c7cbd8; font-size: 19px; margin: 4px 0; }
.spg-note { background: #fff8e6; border-left: 4px solid #f0b429; border-radius: 8px; padding: 12px 16px; margin: 14px 0 0; color: #7a5c12; font-size: 13px; line-height: 1.7; }
</style>

<div class="spg-intro">
    <h2><i class="fas fa-sitemap"></i> <?= __('How the TMM System Works') ?></h2>
    <p><?= __('From registering a partner institution, through recruiting and training candidates, to an apprentice working in Japan and coming home. Every phase below links to the screens that do the work.') ?></p>
</div>

<div class="spg-legend">
    <span class="spg-role" style="background:<?= $colour['recruitment'] ?>"><i class="fas fa-user-tie"></i> TMM-Recruitment</span>
    <span class="spg-role" style="background:<?= $colour['lpk'] ?>"><i class="fas fa-school"></i> LPK (lpk-penyangga)</span>
    <span class="spg-role" style="background:<?= $colour['training'] ?>"><i class="fas fa-chalkboard-teacher"></i> TMM-Training</span>
    <span class="spg-role" style="background:<?= $colour['documentation'] ?>"><i class="fas fa-passport"></i> TMM-Documentation</span>
    <span class="spg-role" style="background:<?= $colour['accounting'] ?>"><i class="fas fa-coins"></i> TMM-Accounting</span>
    <span class="spg-role" style="background:<?= $colour['management'] ?>"><i class="fas fa-chart-line"></i> Management</span>
</div>

<div class="flow-section">
    <h2><i class="fas fa-project-diagram"></i> <?= __('Main Process Pipeline') ?></h2>
    <div class="mermaid">
graph LR
    A["<?= __('1. Candidate Recruitment') ?>"] --> B["<?= __('2. Trainee Training') ?>"]
    B --> C["<?= __('3. Documents & Departure') ?>"]
    C --> D["<?= __('4. Apprenticeship in Japan') ?>"]
    D --> E["<?= __('Post-Apprentice') ?>"]
    S["<?= __('Stakeholders') ?>"] -.-> A
    S -.-> D
    K["<?= __('Accounting') ?>"] -.-> B
    K -.-> C
    O["<?= __('Apprentice Order shared with LPK') ?>"] --> A
    D --> O
    style A fill:#e8ecfd,stroke:#667eea
    style B fill:#e0f2f7,stroke:#2193b0
    style C fill:#e0f5f1,stroke:#11998e
    style D fill:#fdf0dd,stroke:#f7971e
    style E fill:#eeeeee,stroke:#616161
    style O fill:#fff3cd,stroke:#f7971e,stroke-dasharray: 4 3
    </div>
    <p style="color:#666; font-size:13px; margin-top:10px;">
        <?= __('The pipeline is not a straight line. An apprentice order from an accepting organization is shared with partner LPKs, and that is where the next round of candidate recruitment begins - so the last phase feeds the first.') ?>
    </p>
</div>

<?php
/**
 * The six phases, as data. Each step names the screens that do its work, so a
 * reader can go from "what happens next" to the page that does it without
 * hunting through the menu.
 *
 * The Japanese terms under each heading are left as they are: 送り出し機関 is
 * the term the industry uses in every language, and translating it would make
 * the document harder to match against the paperwork, not easier.
 */
$phases = [
    [
        'key' => 'stakeholders', 'num' => 1, 'colour' => $colour['recruitment'],
        'title' => __('Set Up Stakeholders'),
        'sub' => __('Register every partner organisation before any order is placed.'),
        'steps' => [
            [
                'n' => '1a', 'icon' => 'fa-school', 'colour' => $colour['lpk'],
                'title' => __('LPK - Vocational Training Institution'), 'jp' => '送り出し機関',
                'text' => __('The Indonesian institution that finds and trains candidates. An admin registers it; the institution then activates its own account.'),
                'links' => [
                    ['/admin/lpk-registration', __('LPK Registration Queue')],
                    ['/admin/lpk-registration/create', __('Register a New LPK')],
                    ['/vocational-training-institutions/verify', __('Verification Status')],
                ],
                'bullets' => [
                    __('An admin enters the institution and the login name it will use.'),
                    __('Its director clicks the verification link emailed to them, valid 24 hours.'),
                    __('The director sets a password - only then is the account active.'),
                    __('If the link expires or never arrives, Resend Verification Email sends a fresh one.'),
                ],
            ],
            [
                'n' => '1b', 'icon' => 'fa-building', 'colour' => '#00a8a8',
                'title' => __('AO - Acceptance Organization'), 'jp' => '受入機関',
                'text' => __('The company in Japan that will accept the apprentices. Record its profile and the work it needs done.'),
                'links' => [['/acceptance-organizations', __('Acceptance Organizations')]],
            ],
            [
                'n' => '1c', 'icon' => 'fa-handshake', 'colour' => '#c0392b',
                'title' => __('Kumiai - Cooperative Association'), 'jp' => '協同組合',
                'text' => __('The supervising cooperative that mediates between the accepting organization and the sending side.'),
                'links' => [['/cooperative-associations', __('Cooperative Associations')]],
            ],
            [
                'n' => '1d', 'icon' => 'fa-user-cog', 'colour' => '#7f8c8d',
                'title' => __('Special Skill Support Institution'), 'jp' => '登録支援機関',
                'text' => __('For the Specified Skilled Worker route - the registered support organisation.'),
                'links' => [['/special-skill-support-institutions', __('Special Skill Support Institutions')]],
            ],
        ],
    ],
    [
        'key' => 'order', 'num' => 2, 'colour' => $colour['recruitment'],
        'title' => __('Create an Apprentice Order and Share It'),
        'sub' => __('A vacancy in Japan, offered to the institutions that can fill it.'),
        'steps' => [
            [
                'n' => '2a', 'icon' => 'fa-file-signature', 'colour' => $colour['recruitment'],
                'title' => __('Create Apprentice Order'), 'jp' => '求人',
                'text' => __('How many people are needed, which job category, which departure month, and which cooperative and accepting organization it belongs to.'),
                'links' => [
                    ['/apprentice-orders/add', __('New Order')],
                    ['/apprentice-orders', __('Apprentice Orders')],
                    ['/apprentice-orders/statistics', __('Order Statistics')],
                ],
            ],
            [
                'n' => '2b', 'icon' => 'fa-share-alt', 'colour' => $colour['lpk'],
                'title' => __('Share the Order with LPKs'), 'jp' => 'オーダー共有',
                'text' => __('Offer the order to one or more institutions so they can start recruiting against it. Each one is emailed the order details, and withdrawing it later emails them again.'),
                'links' => [['/apprentice-orders', __('Open an order, then Shared with Institutions')]],
                'bullets' => [
                    __('The order page lists who has it, whether the email reached them, and who shared it.'),
                    __('Withdrawing an order from an institution tells them; the record of the offer stays.'),
                ],
            ],
        ],
        'note' => __('Only tmm-recruitment and administrator may create, edit, delete, share or withdraw an order. Other roles can see an order and who it was shared with, but cannot change it.'),
    ],
    [
        'key' => 'recruitment', 'num' => 3, 'colour' => $colour['lpk'],
        'title' => __('Recruitment and Selection'),
        'sub' => __('The institution finds candidates and puts them through selection.'),
        'steps' => [
            [
                'n' => '3a', 'icon' => 'fa-users', 'colour' => $colour['lpk'],
                'title' => __('Collect Candidates'), 'jp' => '候補者',
                'text' => __('Enter each applicant with personal data, family, education and experience, then upload their documents.'),
                'links' => [
                    ['/candidates', __('Candidates')],
                    ['/candidate-documents', __('Candidate Documents')],
                ],
            ],
            [
                'n' => '3b', 'icon' => 'fa-clipboard-check', 'colour' => $colour['lpk'],
                'title' => __('Selection'), 'jp' => '選考',
                'text' => __('Physical tests, scoring, interviews and a medical check-up together give a readiness view of each candidate.'),
                'links' => [
                    ['/lpk-physical-tests', __('Physical Tests')],
                    ['/lpk-candidate-scoring', __('Candidate Scoring')],
                    ['/candidate-record-interviews', __('Interviews')],
                    ['/candidate-record-medical-check-ups', __('Medical Check-Ups')],
                ],
                'bullets' => [
                    __('A candidate who passes is promoted and becomes a trainee.'),
                ],
            ],
        ],
        'links' => [['/candidates/promotion-history', __('Promotion History')]],
    ],
    [
        'key' => 'training', 'num' => 4, 'colour' => $colour['training'],
        'title' => __('Training'),
        'sub' => __('Japanese language, work competencies and culture, in batches.'),
        'steps' => [
            [
                'n' => '4a', 'icon' => 'fa-chalkboard-teacher', 'colour' => $colour['training'],
                'title' => __('Training Batches and Modules'), 'jp' => '研修',
                'text' => __('Group trainees into batches and enrol them in the training modules.'),
                'links' => [
                    ['/trainees', __('Trainees')],
                    ['/trainee-training-batches/index', __('Training Batches')],
                ],
            ],
            [
                'n' => '4b', 'icon' => 'fa-award', 'colour' => $colour['training'],
                'title' => __('Scores and Certificates'), 'jp' => '成績・修了証',
                'text' => __('Daily and test scores are recorded, certificates issued, and a passing trainee is promoted to apprentice.'),
                'links' => [
                    ['/trainee-training-test-scores/index', __('Test Scores')],
                    ['/trainee-certificates/index', __('Certificates')],
                    ['/trainees/promotion-checklist', __('Promotion Checklist')],
                ],
            ],
        ],
    ],
    [
        'key' => 'documentation', 'num' => 5, 'colour' => $colour['documentation'],
        'title' => __('Documents and Departure'),
        'sub' => __('Everything that has to exist on paper before a flight is booked.'),
        'steps' => [
            [
                'n' => '5a', 'icon' => 'fa-folder-open', 'colour' => $colour['documentation'],
                'title' => __('Submission Documents'), 'jp' => '提出書類',
                'text' => __('The document set each trainee must complete, tracked against a checklist.'),
                'links' => [
                    ['/trainee-submission-documents/index', __('Submission Documents')],
                    ['/trainee-submission-documents/checklist', __('Document Checklist')],
                ],
            ],
            [
                'n' => '5b', 'icon' => 'fa-passport', 'colour' => $colour['documentation'],
                'title' => __('Passport, Medical, COE and Visa'), 'jp' => '在留資格・ビザ',
                'text' => __('Passport records, the pre-departure medical check-up, then the certificate of eligibility and the visa.'),
                'links' => [
                    ['/trainee-record-pasports', __('Passports')],
                    ['/trainee-record-medical-check-ups', __('Medical Check-Ups')],
                    ['/trainee-record-coe-visas', __('COE / Visa')],
                ],
            ],
            [
                'n' => '5c', 'icon' => 'fa-plane-departure', 'colour' => $colour['documentation'],
                'title' => __('Tickets and Departure'), 'jp' => '出発',
                'text' => __('Flights are booked and the departure document pack is assembled.'),
                'links' => [
                    ['/tickets', __('Tickets')],
                    ['/tickets/departures', __('Departures')],
                    ['/trainee-documents/departure', __('Departure Documents')],
                ],
            ],
        ],
    ],
    [
        'key' => 'apprenticeship', 'num' => 6, 'colour' => '#f7971e',
        'title' => __('Apprenticeship in Japan, and Coming Home'),
        'sub' => __('Work at the accepting organization, tracked to completion.'),
        'steps' => [
            [
                'n' => '6a', 'icon' => 'fa-industry', 'colour' => '#f7971e',
                'title' => __('Apprentice in Japan'), 'jp' => '技能実習生',
                'text' => __('The apprentice works at the accepting organization according to the order. Documents, certifications and progress are recorded throughout.'),
                'links' => [
                    ['/apprentices/index', __('Apprentices')],
                    ['/apprentice-documents/index', __('Apprentice Documents')],
                ],
            ],
            [
                'n' => '6b', 'icon' => 'fa-house-user', 'colour' => '#616161',
                'title' => __('Post-Apprentice'), 'jp' => '修了者',
                'text' => __('Once the placement ends, the alumnus is managed as a post-apprentice.'),
                'links' => [['/post-apprentices/index', __('Post-Apprentices')]],
            ],
        ],
    ],
];
?>

<?php foreach ($phases as $i => $phase): ?>
    <?php $isMine = $mine($phase['key']); ?>
    <div class="spg-phase-head">
        <div class="spg-num" style="background:<?= $phase['colour'] ?>"><?= (int)$phase['num'] ?></div>
        <div>
            <h3>
                <?= h($phase['title']) ?>
                <?php if ($isMine): ?>
                    <span class="spg-yours"><i class="fas fa-user-check"></i> <?= __('Your role') ?></span>
                <?php endif; ?>
            </h3>
            <div class="sub"><?= h($phase['sub']) ?></div>
        </div>
    </div>

    <div class="spg-steps">
        <?php foreach ($phase['steps'] as $step): ?>
            <div class="spg-step <?= $isMine ? 'mine' : '' ?>">
                <span class="spg-step-n"><?= h($step['n']) ?></span>
                <div class="spg-step-top">
                    <div class="spg-ico" style="background:<?= $step['colour'] ?>"><i class="fas <?= h($step['icon']) ?>"></i></div>
                    <h4><?= h($step['title']) ?><small><?= h($step['jp']) ?></small></h4>
                </div>
                <p><?= h($step['text']) ?></p>
                <div>
                    <?php foreach ($step['links'] as $link): ?>
                        <a class="spg-menu" href="<?= h($link[0]) ?>"><i class="fas fa-arrow-right"></i><?= h($link[1]) ?></a>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($step['bullets'])): ?>
                    <ul class="spg-sub">
                        <?php foreach ($step['bullets'] as $bullet): ?>
                            <li><?= h($bullet) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($phase['links'])): ?>
        <div style="margin-top:10px;">
            <?php foreach ($phase['links'] as $link): ?>
                <a class="spg-menu" href="<?= h($link[0]) ?>"><i class="fas fa-arrow-right"></i><?= h($link[1]) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($phase['note'])): ?>
        <p class="spg-note"><i class="fas fa-user-shield"></i> <?= h($phase['note']) ?></p>
    <?php endif; ?>

    <?php if ($i < count($phases) - 1): ?>
        <div class="spg-arrow"><i class="fas fa-chevron-down"></i></div>
    <?php endif; ?>
<?php endforeach; ?>

<div class="spg-arrow"><i class="fas fa-ellipsis-h"></i></div>

<div class="flow-section">
    <h2><i class="fas fa-layer-group"></i> <?= __('Supporting Areas') ?></h2>
    <div class="spg-steps">
        <div class="spg-step <?= $mine('accounting') ? 'mine' : '' ?>">
            <div class="spg-step-top">
                <div class="spg-ico" style="background:<?= $colour['accounting'] ?>"><i class="fas fa-coins"></i></div>
                <h4><?= __('Accounting') ?><small><?= __('Runs alongside training and departure') ?></small></h4>
            </div>
            <p><?= __('Trainee installments, journals, the chart of accounts, and the financial reports.') ?></p>
            <div>
                <a class="spg-menu" href="/trainee-installments"><i class="fas fa-arrow-right"></i><?= __('Installments') ?></a>
                <a class="spg-menu" href="/journals"><i class="fas fa-arrow-right"></i><?= __('Journals') ?></a>
                <a class="spg-menu" href="/chart-of-accounts"><i class="fas fa-arrow-right"></i><?= __('Chart of Accounts') ?></a>
                <a class="spg-menu" href="/reports/balance-sheet"><i class="fas fa-arrow-right"></i><?= __('Balance Sheet') ?></a>
                <a class="spg-menu" href="/reports/income-statement"><i class="fas fa-arrow-right"></i><?= __('Income Statement') ?></a>
                <a class="spg-menu" href="/reports/cash-flow"><i class="fas fa-arrow-right"></i><?= __('Cash Flow') ?></a>
            </div>
        </div>
        <div class="spg-step <?= $mine('administration') ? 'mine' : '' ?>">
            <div class="spg-step-top">
                <div class="spg-ico" style="background:#616161"><i class="fas fa-users-cog"></i></div>
                <h4><?= __('System Administration') ?><small><?= __('Who exists, and what they may do') ?></small></h4>
            </div>
            <p><?= __('Users, roles, menus, access permissions and the audit log.') ?></p>
            <div>
                <a class="spg-menu" href="/users"><i class="fas fa-arrow-right"></i><?= __('Users') ?></a>
                <a class="spg-menu" href="/roles"><i class="fas fa-arrow-right"></i><?= __('Roles') ?></a>
                <a class="spg-menu" href="/menus"><i class="fas fa-arrow-right"></i><?= __('Menus') ?></a>
                <a class="spg-menu" href="/permissions"><i class="fas fa-arrow-right"></i><?= __('Permissions') ?></a>
                <a class="spg-menu" href="/audit"><i class="fas fa-arrow-right"></i><?= __('Audit Logs') ?></a>
            </div>
        </div>
    </div>
</div>

<div class="flow-section">
    <h2><i class="fas fa-user-shield"></i> <?= __('Who May Do What') ?></h2>
    <ul style="color:#555; line-height:1.9;">
        <li><?= __('<strong>Apprentice orders</strong> - only <em>tmm-recruitment</em> and <em>administrator</em> may create, edit, delete, share with an LPK, or withdraw a share. Other roles can still see an order and who it has been shared with; they simply cannot change it.') ?></li>
        <li><?= __('<strong>LPK sign-in details</strong> - an institution\'s login name is visible only to an administrator and to that institution itself. The password is never shown anywhere; it is stored hashed and cannot be read back.') ?></li>
        <li><?= __('Everything else follows your role\'s permissions. When a page refuses access, the message names your account and your role - pass that on to an administrator.') ?></li>
    </ul>
</div>

<div class="flow-section">
    <h2><i class="fas fa-question-circle"></i> <?= __('How to Use the System') ?></h2>
    <ul style="color:#555; line-height:1.9;">
        <li><?= __('Use the menu at the top of the screen to open each module, or click the links on the phases above.') ?></li>
        <li><?= __('Every list page has column filters, search and export buttons.') ?></li>
        <li><?= __('The Process Flow help button on each module explains that module\'s workflow in detail.') ?></li>
        <li><?= __('The language can be changed at any time from the switcher; this page and the whole interface follow it.') ?></li>
    </ul>
</div>
