<?php
/**
 * TMM — Panduan Alur Proses Sistem (End-to-End)
 * Ditampilkan dari halaman login (tanpa perlu autentikasi).
 * Layout: process_flow (menyediakan container + language switcher + tombol kembali).
 *
 * @var \App\View\AppView $this
 */
$this->assign('title', __('System Process Guide'));

/** Warna per-role untuk konsistensi visual */
$roleColor = [
    'recruitment'   => '#4c5bd4',
    'lpk'           => '#00838f',
    'training'      => '#e67e22',
    'documentation' => '#8e44ad',
    'accounting'    => '#16a085',
    'management'    => '#576574',
];
?>
<style>
.tmg-intro{background:linear-gradient(135deg,#e3f2fd,#f3e5f5);border-radius:14px;padding:20px 24px;margin-bottom:28px;border-left:5px solid #667eea}
.tmg-intro h2{margin:0 0 6px;color:#3a2b6e;font-size:20px}
.tmg-intro p{margin:0;color:#555;line-height:1.7}

/* Legenda role */
.tmg-legend{display:flex;flex-wrap:wrap;gap:10px;margin:18px 0 30px}
.tmg-role{display:inline-flex;align-items:center;gap:7px;padding:6px 13px;border-radius:20px;color:#fff;font-size:12.5px;font-weight:700;box-shadow:0 2px 5px rgba(0,0,0,.12)}

/* Fase */
.tmg-phase{margin-bottom:14px}
.tmg-phase-head{display:flex;align-items:center;gap:14px;margin:34px 0 18px}
.tmg-phase-num{flex-shrink:0;width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:800;box-shadow:0 3px 8px rgba(0,0,0,.18)}
.tmg-phase-head h3{margin:0;font-size:21px;color:#2c2150}
.tmg-phase-head .sub{font-size:13px;color:#8a8a9a;font-weight:500;margin-top:2px}

/* Kartu langkah */
.tmg-steps{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;position:relative}
.tmg-step{background:#fff;border:1px solid #eceef3;border-radius:13px;padding:18px;box-shadow:0 2px 10px rgba(0,0,0,.05);position:relative;transition:transform .15s,box-shadow .15s}
.tmg-step:hover{transform:translateY(-4px);box-shadow:0 8px 22px rgba(102,126,234,.16)}
.tmg-step-top{display:flex;align-items:center;gap:12px;margin-bottom:10px}
.tmg-step-ico{flex-shrink:0;width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:19px}
.tmg-step-n{position:absolute;top:-9px;right:14px;background:#2c2150;color:#fff;font-size:11px;font-weight:800;padding:2px 9px;border-radius:10px}
.tmg-step h4{margin:0;font-size:15.5px;color:#22223b;line-height:1.25}
.tmg-step h4 small{display:block;font-weight:500;color:#9a9aa8;font-size:11.5px;margin-top:2px}
.tmg-step p{margin:6px 0 12px;color:#5a5a6a;font-size:13.3px;line-height:1.6}
.tmg-step .meta{display:flex;flex-wrap:wrap;gap:6px;align-items:center}
.tmg-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:8px;font-size:11px;font-weight:700;color:#fff}
.tmg-menu{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:8px;font-size:11px;background:#f1f3f9;color:#4c5bd4;font-weight:600;text-decoration:none}
.tmg-menu:hover{background:#e4e8f7;text-decoration:none;color:#3a48b0}
.tmg-sub{margin:10px 0 0;padding-left:4px}
.tmg-sub li{font-size:12.5px;color:#666;line-height:1.7;list-style:none;position:relative;padding-left:20px}
.tmg-sub li::before{content:"\f0da";font-family:"Font Awesome 5 Free";font-weight:900;color:#c3b6e8;position:absolute;left:4px;top:1px;font-size:10px}

/* Konektor panah antar fase */
.tmg-arrow{text-align:center;color:#b7a8e0;font-size:26px;margin:6px 0 2px}

/* Pipeline seleksi mini */
.tmg-pipe{display:flex;flex-wrap:wrap;align-items:stretch;gap:0;margin:8px 0 4px;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.06)}
.tmg-pipe-item{flex:1;min-width:120px;background:#fff;padding:14px 12px;text-align:center;border-right:3px solid #f0f0f5;position:relative}
.tmg-pipe-item:last-child{border-right:none}
.tmg-pipe-item i{font-size:22px;display:block;margin-bottom:6px}
.tmg-pipe-item .t{font-size:12.5px;font-weight:700;color:#333}
.tmg-pipe-item .s{font-size:11px;color:#999;margin-top:2px}
.tmg-pipe-item .ar{position:absolute;right:-11px;top:50%;transform:translateY(-50%);color:#ddd;font-size:16px;z-index:2}

.tmg-note{background:#fff8e1;border-left:4px solid #ffb300;border-radius:10px;padding:14px 18px;margin:26px 0;color:#6b5300;font-size:13px;line-height:1.7}
.tmg-note b{color:#5a4500}
.tmg-cta{text-align:center;margin:30px 0 6px}
.tmg-cta a{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:13px 30px;border-radius:30px;font-weight:700;text-decoration:none;box-shadow:0 4px 14px rgba(102,126,234,.35)}
</style>

<div class="tmg-intro">
    <h2><i class="fas fa-route"></i> <?= __('End-to-End Process Flow') ?></h2>
    <p><?= __('This guide walks through the entire TMM (Technical Intern & Specified Skilled Worker) journey — from setting up stakeholders and apprentice orders, through candidate recruitment & selection at the LPK, training, documentation & departure to Japan, and finally the return home. Each step shows the responsible role, the menu path, and how the data connects.') ?></p>
</div>

<!-- LEGENDA ROLE -->
<div class="tmg-legend">
    <span class="tmg-role" style="background:<?= $roleColor['recruitment'] ?>"><i class="fas fa-user-tie"></i> TMM-Recruitment</span>
    <span class="tmg-role" style="background:<?= $roleColor['lpk'] ?>"><i class="fas fa-school"></i> LPK (lpk-penyangga)</span>
    <span class="tmg-role" style="background:<?= $roleColor['training'] ?>"><i class="fas fa-chalkboard-teacher"></i> TMM-Training</span>
    <span class="tmg-role" style="background:<?= $roleColor['documentation'] ?>"><i class="fas fa-passport"></i> TMM-Documentation</span>
    <span class="tmg-role" style="background:<?= $roleColor['accounting'] ?>"><i class="fas fa-coins"></i> TMM-Accounting</span>
    <span class="tmg-role" style="background:<?= $roleColor['management'] ?>"><i class="fas fa-chart-line"></i> Management</span>
</div>

<!-- ============ FASE 1 : SETUP STAKEHOLDER ============ -->
<div class="tmg-phase">
    <div class="tmg-phase-head">
        <div class="tmg-phase-num" style="background:<?= $roleColor['recruitment'] ?>">1</div>
        <div><h3><?= __('Set Up Stakeholders') ?></h3>
            <div class="sub"><?= __('Role') ?>: <b>TMM-Recruitment</b> · <?= __('Register every partner organisation before any order') ?></div></div>
    </div>
    <div class="tmg-steps">
        <div class="tmg-step">
            <span class="tmg-step-n">1a</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:<?= $roleColor['lpk'] ?>"><i class="fas fa-school"></i></div>
                <h4><?= __('LPK — Vocational Training Institution') ?><small>送り出し機関 / Sending Institution</small></h4>
            </div>
            <p><?= __('Register each LPK partner, then create its login user and verify the email so the LPK can operate independently.') ?></p>
            <div class="meta">
                <a class="tmg-menu" href="/vocational-training-institutions"><i class="fas fa-list"></i> Vocational Training Institutions</a>
                <a class="tmg-menu" href="/vocational-training-institutions/verify"><i class="fas fa-clipboard-check"></i> Verify</a>
            </div>
            <ul class="tmg-sub">
                <li><?= __('Create institution record (name, address, contacts)') ?></li>
                <li><?= __('Create LPK user account & assign role') ?> <b>lpk-penyangga</b></li>
                <li><?= __('Email verification → LPK activates & sets password') ?></li>
            </ul>
        </div>
        <div class="tmg-step">
            <span class="tmg-step-n">1b</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:#00a8a8"><i class="fas fa-building"></i></div>
                <h4><?= __('AO — Acceptance Organization') ?><small>受入機関 / Uke-ire Kikan</small></h4>
            </div>
            <p><?= __('The company in Japan that will accept the interns. Record its profile and job needs.') ?></p>
            <div class="meta"><a class="tmg-menu" href="/acceptance-organizations"><i class="fas fa-list"></i> Acceptance Organizations</a></div>
        </div>
        <div class="tmg-step">
            <span class="tmg-step-n">1c</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:#c0392b"><i class="fas fa-handshake"></i></div>
                <h4><?= __('Kumiai — Cooperative Association') ?><small>協同組合 / Kyōdō Kumiai</small></h4>
            </div>
            <p><?= __('The supervising cooperative that mediates between the AO and the sending side.') ?></p>
            <div class="meta"><a class="tmg-menu" href="/cooperative-associations"><i class="fas fa-list"></i> Cooperative Associations</a></div>
        </div>
        <div class="tmg-step">
            <span class="tmg-step-n">1d</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:#7f8c8d"><i class="fas fa-user-cog"></i></div>
                <h4><?= __('Special Skill Support Institution') ?><small>登録支援機関 / Registered Support Org</small></h4>
            </div>
            <p><?= __('For the Specified Skilled Worker (SSW) route — the registered support organisation.') ?></p>
            <div class="meta"><a class="tmg-menu" href="/special-skill-support-institutions"><i class="fas fa-list"></i> Special Skill Support Institutions</a></div>
        </div>
    </div>
</div>

<div class="tmg-arrow"><i class="fas fa-chevron-down"></i></div>

<!-- ============ FASE 2 : ORDER & SHARE ============ -->
<div class="tmg-phase">
    <div class="tmg-phase-head">
        <div class="tmg-phase-num" style="background:<?= $roleColor['recruitment'] ?>">2</div>
        <div><h3><?= __('Create Apprentice Order & Share to LPK') ?></h3>
            <div class="sub"><?= __('Role') ?>: <b>TMM-Recruitment</b></div></div>
    </div>
    <div class="tmg-steps">
        <div class="tmg-step">
            <span class="tmg-step-n">2a</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:<?= $roleColor['recruitment'] ?>"><i class="fas fa-file-signature"></i></div>
                <h4><?= __('Create Apprentice Order') ?><small>求人 / Job Order</small></h4>
            </div>
            <p><?= __('Define how many interns are needed (male/female), the job category, and link the Kumiai + Acceptance Organization.') ?></p>
            <div class="meta">
                <a class="tmg-menu" href="/apprentice-orders/add"><i class="fas fa-plus"></i> New Order</a>
                <a class="tmg-menu" href="/apprentice-orders/statistics"><i class="fas fa-chart-pie"></i> Statistics</a>
            </div>
        </div>
        <div class="tmg-step">
            <span class="tmg-step-n">2b</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:<?= $roleColor['lpk'] ?>"><i class="fas fa-share-nodes"></i></div>
                <h4><?= __('Share Order to LPK') ?><small>Order Sharing</small></h4>
            </div>
            <p><?= __('Distribute the order to one or more LPK so they can start recruiting candidates against it.') ?></p>
            <div class="meta"><a class="tmg-menu" href="/apprentice-orders"><i class="fas fa-share"></i> Apprentice Orders → Share</a></div>
        </div>
    </div>
</div>

<div class="tmg-arrow"><i class="fas fa-chevron-down"></i></div>

<!-- ============ FASE 3 : REKRUTMEN & SELEKSI LPK ============ -->
<div class="tmg-phase">
    <div class="tmg-phase-head">
        <div class="tmg-phase-num" style="background:<?= $roleColor['lpk'] ?>">3</div>
        <div><h3><?= __('Recruitment & Selection Pipeline') ?></h3>
            <div class="sub"><?= __('Role') ?>: <b>LPK (lpk-penyangga)</b> · <?= __('LPK only sees its own candidates') ?></div></div>
    </div>
    <div class="tmg-steps" style="margin-bottom:14px">
        <div class="tmg-step">
            <span class="tmg-step-n">3a</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:<?= $roleColor['lpk'] ?>"><i class="fas fa-user-plus"></i></div>
                <h4><?= __('Collect Candidates') ?><small>候補者 / Calon Magang</small></h4>
            </div>
            <p><?= __('Enter each applicant with personal data, family, education & experience, then upload their documents.') ?></p>
            <div class="meta">
                <a class="tmg-menu" href="/candidates"><i class="fas fa-users"></i> Candidates</a>
                <a class="tmg-menu" href="/candidate-documents"><i class="fas fa-folder-open"></i> Documents</a>
            </div>
        </div>
    </div>

    <!-- Pipeline seleksi -->
    <div style="font-size:13px;color:#8a8a9a;font-weight:600;margin:6px 2px 8px"><i class="fas fa-filter"></i> <?= __('Selection stages (each recorded per candidate, then combined into an integrated score):') ?></div>
    <div class="tmg-pipe">
        <div class="tmg-pipe-item"><i class="fas fa-dumbbell" style="color:#00838f"></i><div class="t"><?= __('Physical Test') ?></div><div class="s">/lpk-physical-tests</div><span class="ar"><i class="fas fa-chevron-right"></i></span></div>
        <div class="tmg-pipe-item"><i class="fas fa-comments" style="color:#4c5bd4"></i><div class="t"><?= __('Interview') ?></div><div class="s">/candidate-record-interviews</div><span class="ar"><i class="fas fa-chevron-right"></i></span></div>
        <div class="tmg-pipe-item"><i class="fas fa-notes-medical" style="color:#c0392b"></i><div class="t"><?= __('Medical Check-Up') ?></div><div class="s">/candidate-record-medical-check-ups</div><span class="ar"><i class="fas fa-chevron-right"></i></span></div>
        <div class="tmg-pipe-item"><i class="fas fa-clipboard-check" style="color:#8e44ad"></i><div class="t"><?= __('Integrated Scoring') ?></div><div class="s">/lpk-candidate-scoring</div><span class="ar"><i class="fas fa-chevron-right"></i></span></div>
        <div class="tmg-pipe-item"><i class="fas fa-user-graduate" style="color:#27ae60"></i><div class="t"><?= __('Promote → Trainee') ?></div><div class="s">/candidates/promote-to-trainee</div></div>
    </div>
    <ul class="tmg-sub" style="margin-top:14px">
        <li><?= __('Integrated Scoring combines placement test + interview + MCU into a readiness view.') ?></li>
        <li><?= __('When a candidate passes, promote them — they become a Trainee and move to the training phase.') ?> <a class="tmg-menu" href="/candidates/promotion-history"><i class="fas fa-history"></i> Promotion History</a></li>
    </ul>
</div>

<div class="tmg-arrow"><i class="fas fa-chevron-down"></i></div>

<!-- ============ FASE 4 : PELATIHAN ============ -->
<div class="tmg-phase">
    <div class="tmg-phase-head">
        <div class="tmg-phase-num" style="background:<?= $roleColor['training'] ?>">4</div>
        <div><h3><?= __('Training') ?></h3>
            <div class="sub"><?= __('Role') ?>: <b>TMM-Training</b></div></div>
    </div>
    <div class="tmg-steps">
        <div class="tmg-step">
            <span class="tmg-step-n">4a</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:<?= $roleColor['training'] ?>"><i class="fas fa-layer-group"></i></div>
                <h4><?= __('Training Batches & Modules') ?></h4>
            </div>
            <p><?= __('Group trainees into batches and enrol them in training modules (language, skills, culture).') ?></p>
            <div class="meta"><a class="tmg-menu" href="/trainee-training-batches/index"><i class="fas fa-users-line"></i> Batches</a></div>
        </div>
        <div class="tmg-step">
            <span class="tmg-step-n">4b</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:#d35400"><i class="fas fa-chart-bar"></i></div>
                <h4><?= __('Daily & Test Scores') ?></h4>
            </div>
            <p><?= __('Record daily scores and periodic competency test results to track each trainee\'s progress.') ?></p>
            <div class="meta">
                <a class="tmg-menu" href="/trainee-training-test-scores/daily"><i class="fas fa-calendar-day"></i> Daily</a>
                <a class="tmg-menu" href="/trainee-training-test-scores/report"><i class="fas fa-file-alt"></i> Report</a>
            </div>
        </div>
        <div class="tmg-step">
            <span class="tmg-step-n">4c</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:#27ae60"><i class="fas fa-user-tie"></i></div>
                <h4><?= __('Promote → Apprentice') ?></h4>
            </div>
            <p><?= __('Trainees who pass candidate + training checks are promoted to Apprentice, ready for departure.') ?></p>
            <div class="meta">
                <a class="tmg-menu" href="/trainees/promotion-checklist"><i class="fas fa-clipboard-list"></i> Checklist</a>
                <a class="tmg-menu" href="/trainees/promotion-history"><i class="fas fa-history"></i> History</a>
            </div>
        </div>
    </div>
</div>

<div class="tmg-arrow"><i class="fas fa-chevron-down"></i></div>

<!-- ============ FASE 5 : DOKUMEN & KEBERANGKATAN ============ -->
<div class="tmg-phase">
    <div class="tmg-phase-head">
        <div class="tmg-phase-num" style="background:<?= $roleColor['documentation'] ?>">5</div>
        <div><h3><?= __('Documentation & Departure to Japan') ?></h3>
            <div class="sub"><?= __('Role') ?>: <b>TMM-Documentation</b> + <b>TMM-Accounting</b></div></div>
    </div>
    <div class="tmg-steps">
        <div class="tmg-step">
            <span class="tmg-step-n">5a</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:<?= $roleColor['documentation'] ?>"><i class="fas fa-passport"></i></div>
                <h4><?= __('Departure Documents') ?><small>COE · Visa · Passport · MCU</small></h4>
            </div>
            <p><?= __('Prepare Certificate of Eligibility, visa, passport and pre-departure medical records for each apprentice.') ?></p>
            <div class="meta">
                <a class="tmg-menu" href="/trainee-record-coe-visas"><i class="fas fa-id-card"></i> COE / Visa</a>
                <a class="tmg-menu" href="/trainee-record-pasports"><i class="fas fa-passport"></i> Passport</a>
                <a class="tmg-menu" href="/trainee-submission-documents/index"><i class="fas fa-folder"></i> Submission Docs</a>
            </div>
        </div>
        <div class="tmg-step">
            <span class="tmg-step-n">5b</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:#2980b9"><i class="fas fa-plane-departure"></i></div>
                <h4><?= __('Ticketing & Flights') ?></h4>
            </div>
            <p><?= __('Arrange flights, transit legs and departure schedules.') ?></p>
            <div class="meta">
                <a class="tmg-menu" href="/tickets"><i class="fas fa-ticket"></i> Tickets</a>
                <a class="tmg-menu" href="/tickets/transit"><i class="fas fa-route"></i> Transit</a>
            </div>
        </div>
        <div class="tmg-step">
            <span class="tmg-step-n">5c</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:<?= $roleColor['accounting'] ?>"><i class="fas fa-coins"></i></div>
                <h4><?= __('Costs & Installments') ?></h4>
            </div>
            <p><?= __('Record document costs and manage trainee installment payments and receipts.') ?></p>
            <div class="meta">
                <a class="tmg-menu" href="/trainee-submission-documents/costs"><i class="fas fa-file-invoice-dollar"></i> Doc Costs</a>
                <a class="tmg-menu" href="/trainee-installments/tracking"><i class="fas fa-wallet"></i> Installments</a>
            </div>
        </div>
    </div>
</div>

<div class="tmg-arrow"><i class="fas fa-chevron-down"></i></div>

<!-- ============ FASE 6 : MAGANG & PULANG ============ -->
<div class="tmg-phase">
    <div class="tmg-phase-head">
        <div class="tmg-phase-num" style="background:#27ae60">6</div>
        <div><h3><?= __('Apprenticeship in Japan → Return Home') ?></h3>
            <div class="sub"><?= __('Roles') ?>: <b>TMM-Documentation</b> · <b>Management</b></div></div>
    </div>
    <div class="tmg-steps">
        <div class="tmg-step">
            <span class="tmg-step-n">6a</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:#16a085"><i class="fas fa-user-tie"></i></div>
                <h4><?= __('Apprentice Management') ?></h4>
            </div>
            <p><?= __('Track apprentices during their placement in Japan, including kaizen / problem-solving stories.') ?></p>
            <div class="meta">
                <a class="tmg-menu" href="/apprentices/index"><i class="fas fa-users"></i> Apprentices</a>
                <a class="tmg-menu" href="/apprentice-stories/index"><i class="fas fa-lightbulb"></i> Stories</a>
            </div>
        </div>
        <div class="tmg-step">
            <span class="tmg-step-n">6b</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:#e67e22"><i class="fas fa-plane-arrival"></i></div>
                <h4><?= __('Post-Apprentice — Return to Indonesia') ?></h4>
            </div>
            <p><?= __('When the programme ends and the apprentice returns home, track their alumni status (employed, self-employed, continuing study…).') ?></p>
            <div class="meta"><a class="tmg-menu" href="/post-apprentices/index"><i class="fas fa-user-clock"></i> Post Apprentices</a></div>
        </div>
        <div class="tmg-step">
            <span class="tmg-step-n">6c</span>
            <div class="tmg-step-top">
                <div class="tmg-step-ico" style="background:<?= $roleColor['management'] ?>"><i class="fas fa-chart-line"></i></div>
                <h4><?= __('Dashboards & Reports') ?></h4>
            </div>
            <p><?= __('Management monitors the whole pipeline through department dashboards and reports at any time.') ?></p>
            <div class="meta">
                <a class="tmg-menu" href="/dashboard/executive"><i class="fas fa-gauge-high"></i> Executive</a>
                <a class="tmg-menu" href="/reports/candidate-pipeline"><i class="fas fa-diagram-project"></i> Pipeline Report</a>
            </div>
        </div>
    </div>
</div>

<!-- CATATAN untuk user & debugger -->
<div class="tmg-note">
    <b><i class="fas fa-database"></i> <?= __('For developers / debuggers') ?>:</b>
    <?= __('The pipeline spans 15 dedicated databases (cms_*). Stakeholders live in') ?> <code>cms_tmm_stakeholders</code>,
    <?= __('candidates in') ?> <code>cms_lpk_candidates</code>, <?= __('trainees in') ?> <code>cms_tmm_trainees</code>,
    <?= __('apprentices in') ?> <code>cms_tmm_apprentices</code>, <?= __('finance in') ?> <code>cms_tmm_trainee_accountings</code>.
    <?= __('A person keeps the same identity as they flow Candidate → Trainee → Apprentice → Post-Apprentice via linking IDs (candidate_id, trainee_id, apprenticeship_order_id).') ?>
    <?= __('Access is role-filtered: LPK users only see their own institution\'s candidates.') ?>
</div>

<!-- Ringkasan alur satu baris -->
<div style="text-align:center;margin:24px 0;font-size:13px;color:#6a6a7a;line-height:2.4">
    <span class="tmg-badge" style="background:<?= $roleColor['recruitment'] ?>"><?= __('Stakeholders') ?></span> →
    <span class="tmg-badge" style="background:<?= $roleColor['recruitment'] ?>"><?= __('Order') ?></span> →
    <span class="tmg-badge" style="background:<?= $roleColor['lpk'] ?>"><?= __('Candidate') ?></span> →
    <span class="tmg-badge" style="background:<?= $roleColor['lpk'] ?>"><?= __('Selection') ?></span> →
    <span class="tmg-badge" style="background:<?= $roleColor['training'] ?>"><?= __('Trainee') ?></span> →
    <span class="tmg-badge" style="background:#27ae60"><?= __('Apprentice') ?></span> →
    <span class="tmg-badge" style="background:<?= $roleColor['documentation'] ?>"><?= __('Departure') ?></span> →
    <span class="tmg-badge" style="background:#e67e22"><?= __('Return Home') ?></span>
</div>

<div class="tmg-cta">
    <a href="/users/login"><i class="fas fa-right-to-bracket"></i> <?= __('Back to Login') ?></a>
</div>
