<?php
/**
 * Administrator Dashboard — system-wide landing page.
 * All chrome is translatable via __(); record data is shown as-is.
 *
 * @var \App\View\AppView $this
 */
$fmt = function ($n) { return number_format((float)$n); };
$money = function ($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); };
// Safe display-name resolver for heterogeneous records.
$nameOf = function ($r, array $keys) {
    foreach ($keys as $k) {
        if (isset($r->$k) && trim((string)$r->$k) !== '') {
            return $r->$k;
        }
    }
    return isset($r->id) ? '#' . $r->id : '—';
};
$pipeLabels = [
    'candidate'  => __('Candidates'),
    'trainee'    => __('Trainees'),
    'apprentice' => __('Apprentices'),
];
?>
<div class="admin-dash">

    <div class="ad-head">
        <div>
            <h2><i class="fa fa-shield"></i> <?= __('Administrator Dashboard') ?></h2>
            <p class="ad-sub"><?= __('System-wide overview of the TMM Apprentice Management System') ?></p>
        </div>
        <div class="ad-head-meta">
            <span class="ad-date"><i class="fa fa-calendar"></i> <?= date('D, d M Y') ?></span>
        </div>
    </div>

    <!-- ── Primary KPIs ─────────────────────────────────────────── -->
    <div class="ad-kpi-grid">
        <div class="ad-kpi ad-kpi--indigo">
            <div class="ad-kpi-ico"><i class="fa fa-users"></i></div>
            <div class="ad-kpi-body">
                <div class="ad-kpi-num"><?= $fmt($stats['totalUsers']) ?></div>
                <div class="ad-kpi-lbl"><?= __('System Users') ?></div>
            </div>
        </div>
        <div class="ad-kpi ad-kpi--sky">
            <div class="ad-kpi-ico"><i class="fa fa-user-plus"></i></div>
            <div class="ad-kpi-body">
                <div class="ad-kpi-num"><?= $fmt($stats['totalCandidates']) ?></div>
                <div class="ad-kpi-lbl"><?= __('Candidates') ?></div>
            </div>
        </div>
        <div class="ad-kpi ad-kpi--teal">
            <div class="ad-kpi-ico"><i class="fa fa-graduation-cap"></i></div>
            <div class="ad-kpi-body">
                <div class="ad-kpi-num"><?= $fmt($stats['totalTrainees']) ?></div>
                <div class="ad-kpi-lbl"><?= __('Trainees') ?></div>
            </div>
        </div>
        <div class="ad-kpi ad-kpi--rose">
            <div class="ad-kpi-ico"><i class="fa fa-plane"></i></div>
            <div class="ad-kpi-body">
                <div class="ad-kpi-num"><?= $fmt($stats['totalApprentices']) ?></div>
                <div class="ad-kpi-lbl"><?= __('Apprentices in Japan') ?></div>
            </div>
        </div>
    </div>

    <div class="ad-cols">
        <!-- ── Pipeline funnel ──────────────────────────────────── -->
        <div class="ad-card ad-span2">
            <div class="ad-card-hd">
                <h4><i class="fa fa-filter"></i> <?= __('Recruitment Pipeline') ?></h4>
                <span class="ad-hint"><?= __('Timeline: Candidate → Trainee → Apprentice') ?></span>
            </div>
            <div class="ad-card-bd">
                <?php foreach ($pipeline as $stage): ?>
                    <?php $pct = round($stage['count'] / $pipelineMax * 100); ?>
                    <div class="ad-funnel-row">
                        <div class="ad-funnel-lbl"><?= h($pipeLabels[$stage['key']] ?? $stage['key']) ?></div>
                        <div class="ad-funnel-track">
                            <div class="ad-funnel-fill ad-fill--<?= h($stage['key']) ?>" style="width: <?= max(6, $pct) ?>%;">
                                <span><?= $fmt($stage['count']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="ad-conv">
                    <div class="ad-conv-item">
                        <span class="ad-conv-num"><?= $candidateToTrainee ?>%</span>
                        <span class="ad-conv-lbl"><?= __('Candidate → Trainee conversion') ?></span>
                    </div>
                    <div class="ad-conv-item">
                        <span class="ad-conv-num"><?= $traineeToApprentice ?>%</span>
                        <span class="ad-conv-lbl"><?= __('Trainee → Apprentice conversion') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Finance snapshot ─────────────────────────────────── -->
        <div class="ad-card">
            <div class="ad-card-hd">
                <h4><i class="fa fa-money"></i> <?= __('Finance Snapshot') ?></h4>
            </div>
            <div class="ad-card-bd">
                <div class="ad-fin-row">
                    <span class="ad-fin-lbl"><?= __('Contracted') ?></span>
                    <span class="ad-fin-val"><?= $money($stats['financeContracted']) ?></span>
                </div>
                <div class="ad-fin-row">
                    <span class="ad-fin-lbl"><?= __('Collected') ?></span>
                    <span class="ad-fin-val ad-pos"><?= $money($stats['financeCollected']) ?></span>
                </div>
                <div class="ad-fin-row">
                    <span class="ad-fin-lbl"><?= __('Outstanding') ?></span>
                    <span class="ad-fin-val ad-neg"><?= $money($stats['financeOutstanding']) ?></span>
                </div>
                <div class="ad-progress" title="<?= __('Collection Rate') ?>">
                    <div class="ad-progress-fill" style="width: <?= min(100, $collectionRate) ?>%;"></div>
                </div>
                <div class="ad-progress-cap">
                    <?= __('Collection Rate') ?>: <strong><?= $collectionRate ?>%</strong>
                    · <?= $fmt($stats['installmentsPaidOff']) ?> <?= __('paid off') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Stakeholder network ──────────────────────────────────── -->
    <div class="ad-card">
        <div class="ad-card-hd">
            <h4><i class="fa fa-sitemap"></i> <?= __('Stakeholder Network') ?></h4>
        </div>
        <div class="ad-card-bd">
            <div class="ad-mini-grid">
                <?= $this->Html->link(
                    '<span class="ad-mini-num">' . $fmt($stats['totalOrganizations']) . '</span>'
                    . '<span class="ad-mini-lbl"><i class="fa fa-building"></i> ' . __('Acceptance Organizations') . '</span>',
                    ['controller' => 'AcceptanceOrganizations', 'action' => 'index'],
                    ['escape' => false, 'class' => 'ad-mini']
                ) ?>
                <?= $this->Html->link(
                    '<span class="ad-mini-num">' . $fmt($stats['totalLpkInstitutions']) . '</span>'
                    . '<span class="ad-mini-lbl"><i class="fa fa-university"></i> ' . __('Vocational Training Institutions') . '</span>',
                    ['controller' => 'VocationalTrainingInstitutions', 'action' => 'index'],
                    ['escape' => false, 'class' => 'ad-mini']
                ) ?>
                <?= $this->Html->link(
                    '<span class="ad-mini-num">' . $fmt($stats['totalCoopAssociations']) . '</span>'
                    . '<span class="ad-mini-lbl"><i class="fa fa-handshake-o"></i> ' . __('Cooperative Associations') . '</span>',
                    ['controller' => 'CooperativeAssociations', 'action' => 'index'],
                    ['escape' => false, 'class' => 'ad-mini']
                ) ?>
                <?= $this->Html->link(
                    '<span class="ad-mini-num">' . $fmt($stats['totalSupportInstitutions']) . '</span>'
                    . '<span class="ad-mini-lbl"><i class="fa fa-life-ring"></i> ' . __('Support Institutions') . '</span>',
                    ['controller' => 'SpecialSkillSupportInstitutions', 'action' => 'index'],
                    ['escape' => false, 'class' => 'ad-mini']
                ) ?>
            </div>
        </div>
    </div>

    <!-- ── Operations & access control ──────────────────────────── -->
    <div class="ad-card">
        <div class="ad-card-hd">
            <h4><i class="fa fa-cogs"></i> <?= __('Operations & Access') ?></h4>
        </div>
        <div class="ad-card-bd">
            <div class="ad-mini-grid">
                <div class="ad-mini ad-mini--flat">
                    <span class="ad-mini-num"><?= $fmt($stats['totalTickets']) ?></span>
                    <span class="ad-mini-lbl"><i class="fa fa-ticket"></i> <?= __('Document Tickets') ?></span>
                </div>
                <div class="ad-mini ad-mini--flat">
                    <span class="ad-mini-num"><?= $fmt($stats['totalInstallments']) ?></span>
                    <span class="ad-mini-lbl"><i class="fa fa-credit-card"></i> <?= __('Installment Records') ?></span>
                </div>
                <div class="ad-mini ad-mini--flat">
                    <span class="ad-mini-num"><?= $fmt($stats['totalJournals']) ?></span>
                    <span class="ad-mini-lbl"><i class="fa fa-book"></i> <?= __('Journal Entries') ?></span>
                </div>
                <div class="ad-mini ad-mini--flat">
                    <span class="ad-mini-num"><?= $fmt($stats['totalRoles']) ?></span>
                    <span class="ad-mini-lbl"><i class="fa fa-shield"></i> <?= __('Roles') ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="ad-cols">
        <!-- ── Users by role ────────────────────────────────────── -->
        <?php if (!empty($usersByRole)): ?>
        <div class="ad-card">
            <div class="ad-card-hd">
                <h4><i class="fa fa-id-badge"></i> <?= __('Users by Role') ?></h4>
            </div>
            <div class="ad-card-bd">
                <table class="ad-table">
                    <?php foreach ($usersByRole as $r): ?>
                    <tr>
                        <td><?= h($r['name']) ?></td>
                        <td class="ad-num"><span class="ad-badge"><?= $fmt($r['count']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Quick actions ────────────────────────────────────── -->
        <div class="ad-card ad-span<?= empty($usersByRole) ? '2' : '' ?>">
            <div class="ad-card-hd">
                <h4><i class="fa fa-bolt"></i> <?= __('Quick Actions') ?></h4>
            </div>
            <div class="ad-card-bd">
                <div class="ad-actions">
                    <?= $this->Html->link('<i class="fa fa-users"></i> ' . __('Manage Users'),
                        ['controller' => 'Users', 'action' => 'index'], ['escape' => false, 'class' => 'ad-act']) ?>
                    <?= $this->Html->link('<i class="fa fa-shield"></i> ' . __('Manage Roles'),
                        ['controller' => 'Roles', 'action' => 'index'], ['escape' => false, 'class' => 'ad-act']) ?>
                    <?= $this->Html->link('<i class="fa fa-user-plus"></i> ' . __('Candidates'),
                        ['controller' => 'Candidates', 'action' => 'index'], ['escape' => false, 'class' => 'ad-act']) ?>
                    <?= $this->Html->link('<i class="fa fa-graduation-cap"></i> ' . __('Trainees'),
                        ['controller' => 'Trainees', 'action' => 'index'], ['escape' => false, 'class' => 'ad-act']) ?>
                    <?= $this->Html->link('<i class="fa fa-money"></i> ' . __('Installment Records'),
                        ['controller' => 'TraineeInstallments', 'action' => 'index'], ['escape' => false, 'class' => 'ad-act']) ?>
                    <?= $this->Html->link('<i class="fa fa-book"></i> ' . __('Journal Entries'),
                        ['controller' => 'Journals', 'action' => 'index'], ['escape' => false, 'class' => 'ad-act']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Recent activity ──────────────────────────────────────── -->
    <div class="ad-cols ad-cols3">
        <div class="ad-card">
            <div class="ad-card-hd"><h4><i class="fa fa-user"></i> <?= __('Recent Users') ?></h4></div>
            <div class="ad-card-bd">
                <?php if (empty($recentUsers)): ?>
                    <p class="ad-empty"><?= __('No records yet.') ?></p>
                <?php else: foreach ($recentUsers as $u): ?>
                    <div class="ad-feed">
                        <span class="ad-feed-name"><?= h($nameOf($u, ['username', 'name', 'email'])) ?></span>
                        <?php if (isset($u->created) && $u->created): ?>
                            <span class="ad-feed-date"><?= h($u->created->format('d M Y')) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
        <div class="ad-card">
            <div class="ad-card-hd"><h4><i class="fa fa-user-plus"></i> <?= __('Recent Candidates') ?></h4></div>
            <div class="ad-card-bd">
                <?php if (empty($recentCandidates)): ?>
                    <p class="ad-empty"><?= __('No records yet.') ?></p>
                <?php else: foreach ($recentCandidates as $c): ?>
                    <div class="ad-feed">
                        <span class="ad-feed-name"><?= h($nameOf($c, ['name', 'full_name', 'candidate_code', 'applicant_code'])) ?></span>
                        <?php if (isset($c->created) && $c->created): ?>
                            <span class="ad-feed-date"><?= h($c->created->format('d M Y')) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
        <div class="ad-card">
            <div class="ad-card-hd"><h4><i class="fa fa-graduation-cap"></i> <?= __('Recent Trainees') ?></h4></div>
            <div class="ad-card-bd">
                <?php if (empty($recentTrainees)): ?>
                    <p class="ad-empty"><?= __('No records yet.') ?></p>
                <?php else: foreach ($recentTrainees as $t): ?>
                    <div class="ad-feed">
                        <span class="ad-feed-name"><?= h($nameOf($t, ['name', 'full_name', 'tmm_code'])) ?></span>
                        <?php if (isset($t->created) && $t->created): ?>
                            <span class="ad-feed-date"><?= h($t->created->format('d M Y')) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

</div>

<style>
.admin-dash { max-width: 1280px; margin: 0 auto; padding: 4px 2px 40px; color: #1f2937; }
.ad-head { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 12px;
    padding: 18px 22px; margin-bottom: 20px; border-radius: 14px;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #fff; box-shadow: 0 8px 24px rgba(79,70,229,.28); }
.ad-head h2 { margin: 0; font-size: 24px; font-weight: 700; }
.ad-sub { margin: 6px 0 0; opacity: .9; font-size: 13px; }
.ad-date { background: rgba(255,255,255,.18); padding: 6px 12px; border-radius: 20px; font-size: 13px; white-space: nowrap; }

.ad-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 18px; }
.ad-kpi { display: flex; align-items: center; gap: 14px; padding: 18px 20px; border-radius: 14px; color: #fff;
    position: relative; overflow: hidden; box-shadow: 0 6px 18px rgba(0,0,0,.12); }
.ad-kpi--indigo { background: linear-gradient(135deg,#6366f1,#4338ca); }
.ad-kpi--sky    { background: linear-gradient(135deg,#0ea5e9,#0369a1); }
.ad-kpi--teal   { background: linear-gradient(135deg,#14b8a6,#0f766e); }
.ad-kpi--rose   { background: linear-gradient(135deg,#f43f5e,#be123c); }
.ad-kpi-ico { font-size: 30px; opacity: .85; width: 52px; height: 52px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.16); }
.ad-kpi-num { font-size: 30px; font-weight: 800; line-height: 1; }
.ad-kpi-lbl { font-size: 13px; opacity: .92; margin-top: 6px; }

.ad-cols { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 18px; }
.ad-cols3 { grid-template-columns: repeat(3, 1fr); }
.ad-span2 { grid-column: span 2; }

.ad-card { background: #fff; border: 1px solid #eef0f4; border-radius: 14px; box-shadow: 0 2px 12px rgba(17,24,39,.05); overflow: hidden; }
.ad-card-hd { display: flex; align-items: center; justify-content: space-between; gap: 10px;
    padding: 13px 18px; border-bottom: 1px solid #f0f1f5; background: #fbfbfd; }
.ad-card-hd h4 { margin: 0; font-size: 15px; font-weight: 700; color: #374151; }
.ad-card-hd h4 i { color: #6366f1; margin-right: 6px; }
.ad-hint { font-size: 11.5px; color: #9ca3af; }
.ad-card-bd { padding: 16px 18px; }

.ad-funnel-row { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.ad-funnel-lbl { width: 110px; font-size: 13px; font-weight: 600; color: #4b5563; flex-shrink: 0; }
.ad-funnel-track { flex: 1; background: #f1f3f7; border-radius: 8px; height: 30px; overflow: hidden; }
.ad-funnel-fill { height: 100%; display: flex; align-items: center; justify-content: flex-end;
    padding-right: 10px; color: #fff; font-weight: 700; font-size: 13px; border-radius: 8px; transition: width .5s ease; }
.ad-fill--candidate  { background: linear-gradient(90deg,#38bdf8,#0284c7); }
.ad-fill--trainee    { background: linear-gradient(90deg,#2dd4bf,#0d9488); }
.ad-fill--apprentice { background: linear-gradient(90deg,#fb7185,#e11d48); }
.ad-conv { display: flex; gap: 24px; margin-top: 14px; padding-top: 14px; border-top: 1px dashed #e5e7eb; }
.ad-conv-num { font-size: 22px; font-weight: 800; color: #4f46e5; }
.ad-conv-lbl { display: block; font-size: 12px; color: #6b7280; margin-top: 2px; }

.ad-fin-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
.ad-fin-lbl { color: #6b7280; }
.ad-fin-val { font-weight: 700; font-variant-numeric: tabular-nums; }
.ad-pos { color: #059669; } .ad-neg { color: #dc2626; }
.ad-progress { height: 10px; background: #eef0f4; border-radius: 6px; overflow: hidden; margin: 14px 0 6px; }
.ad-progress-fill { height: 100%; background: linear-gradient(90deg,#34d399,#059669); border-radius: 6px; }
.ad-progress-cap { font-size: 12px; color: #6b7280; }

.ad-mini-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.ad-mini { display: flex; flex-direction: column; gap: 6px; padding: 16px; border-radius: 12px; text-align: center;
    text-decoration: none; color: #1f2937; background: #f8fafc; border: 1px solid #eef0f4; transition: .15s; }
.ad-mini:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,.08); color: #4f46e5; }
.ad-mini--flat:hover { transform: none; box-shadow: none; color: #1f2937; }
.ad-mini-num { font-size: 26px; font-weight: 800; color: #4f46e5; }
.ad-mini-lbl { font-size: 12px; color: #6b7280; }
.ad-mini-lbl i { margin-right: 4px; }

.ad-table { width: 100%; border-collapse: collapse; }
.ad-table td { padding: 9px 4px; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
.ad-table td:first-child { text-transform: capitalize; color: #374151; }
.ad-num { text-align: right; }
.ad-badge { background: #eef2ff; color: #4338ca; font-weight: 700; padding: 2px 10px; border-radius: 20px; font-size: 12px; }

.ad-actions { display: flex; flex-direction: column; gap: 8px; }
.ad-act { display: block; padding: 10px 14px; border-radius: 10px; text-decoration: none; font-size: 13.5px; font-weight: 600;
    color: #374151; background: #f8fafc; border: 1px solid #eef0f4; transition: .15s; }
.ad-act:hover { background: #4f46e5; color: #fff; border-color: #4f46e5; }
.ad-act i { width: 20px; text-align: center; margin-right: 6px; }

.ad-feed { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
.ad-feed:last-child { border-bottom: none; }
.ad-feed-name { font-size: 13.5px; font-weight: 600; color: #374151; }
.ad-feed-date { font-size: 11.5px; color: #9ca3af; }
.ad-empty { color: #9ca3af; font-size: 13px; text-align: center; padding: 14px 0; }

@media (max-width: 1024px) {
    .ad-kpi-grid { grid-template-columns: repeat(2, 1fr); }
    .ad-cols, .ad-cols3 { grid-template-columns: 1fr; }
    .ad-span2 { grid-column: auto; }
    .ad-mini-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
