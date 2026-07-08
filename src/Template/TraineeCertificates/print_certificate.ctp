<?php
/**
 * @var \App\View\AppView $this
 * @var array $row       certificate + joined trainee/batch/score data
 * @var array $scoreRows per-competency scores
 */

function pcGrade($avg) {
    if ($avg === null) return null;
    if ($avg >= 81) return ['A', 'Sangat Baik'];
    if ($avg >= 61) return ['B', 'Baik'];
    if ($avg >= 41) return ['C', 'Cukup'];
    if ($avg >= 21) return ['D', 'Kurang'];
    return               ['E', 'Sangat Kurang'];
}

$name    = $row['trainee_name'] ?? '—';
$code    = $row['trainee_code'] ?? '';
$certNo  = $row['certificate_no'] ?? '—';
$batch   = $row['batch_code']   ?? null;
$issueD  = $row['issue_date']   ? date('d F Y', strtotime($row['issue_date'])) : '—';
$avg     = $row['avg_score'] !== null ? (float)$row['avg_score'] : null;
$tests   = (int)$row['test_count'];
$passed  = (int)($row['passed'] ?? 0);
$failed  = (int)($row['failed'] ?? 0);
$gradeInfo = pcGrade($avg);
?>
<style>
/* ── Override print layout defaults for certificate ───────────── */
body { padding: 0 !important; background: #fff !important; }

/* ── Certificate shell ────────────────────────────────────────── */
.cert-page {
    width: 210mm;
    min-height: 148mm; /* A5 landscape feel on A4 page */
    margin: 0 auto;
    background: #fff;
    position: relative;
    page-break-after: always;
}

/* ── Outer border frame ───────────────────────────────────────── */
.cert-frame {
    border: 8px solid #6949BC;
    border-radius: 8px;
    padding: 8px;
    margin: 0;
}
.cert-inner {
    border: 2px solid #b39ddb;
    border-radius: 4px;
    padding: 28px 36px 22px;
    position: relative;
    background:
        radial-gradient(ellipse at top left, rgba(105,73,188,0.04) 0%, transparent 60%),
        radial-gradient(ellipse at bottom right, rgba(105,73,188,0.04) 0%, transparent 60%),
        #fff;
}

/* ── Corner ornaments ─────────────────────────────────────────── */
.corner {
    position: absolute; width: 28px; height: 28px;
    border-color: #6949BC; border-style: solid;
}
.corner-tl { top: 6px; left: 6px;  border-width: 3px 0 0 3px; }
.corner-tr { top: 6px; right: 6px; border-width: 3px 3px 0 0; }
.corner-bl { bottom: 6px; left: 6px;  border-width: 0 0 3px 3px; }
.corner-br { bottom: 6px; right: 6px; border-width: 0 3px 3px 0; }

/* ── Organisation header ──────────────────────────────────────── */
.cert-org {
    text-align: center; margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1.5px solid #e0d6f5;
}
.cert-org .org-name {
    font-size: 17pt; font-weight: 900; color: #6949BC;
    letter-spacing: 2px; text-transform: uppercase; line-height: 1.2;
}
.cert-org .org-sub {
    font-size: 8pt; color: #64748b; letter-spacing: 1.5px;
    text-transform: uppercase; margin-top: 2px;
}

/* ── Certificate title ────────────────────────────────────────── */
.cert-title-block { text-align: center; margin-bottom: 20px; }
.cert-title-block .cert-of {
    font-size: 9pt; font-weight: 600; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 3px; margin-bottom: 4px;
}
.cert-title-block .cert-label {
    font-size: 22pt; font-weight: 900; color: #1a202c;
    letter-spacing: 1px; line-height: 1.1;
}
.cert-title-block .cert-label span { color: #6949BC; }

/* ── Divider line ─────────────────────────────────────────────── */
.cert-divider {
    display: flex; align-items: center; gap: 10px; margin: 0 0 20px;
}
.cert-divider-line { flex: 1; height: 1px; background: #e0d6f5; }
.cert-divider-diamond {
    width: 8px; height: 8px; background: #6949BC;
    transform: rotate(45deg); flex-shrink: 0;
}

/* ── Recipient block ──────────────────────────────────────────── */
.cert-recipient { text-align: center; margin-bottom: 20px; }
.cert-recipient .presented-to {
    font-size: 9pt; color: #94a3b8; font-style: italic; margin-bottom: 4px;
}
.cert-recipient .trainee-name {
    font-size: 19pt; font-weight: 800; color: #1a202c;
    letter-spacing: 0.5px; border-bottom: 2px solid #6949BC;
    display: inline-block; padding-bottom: 3px; margin-bottom: 6px;
}
.cert-recipient .trainee-meta {
    font-size: 9pt; color: #64748b;
}

/* ── Body text ────────────────────────────────────────────────── */
.cert-body-text {
    text-align: center; font-size: 9.5pt; color: #374151;
    line-height: 1.7; margin-bottom: 18px;
}
.cert-body-text strong { color: #1a202c; }

/* ── Score / grade highlight ──────────────────────────────────── */
.cert-score-row {
    display: flex; justify-content: center; gap: 32px;
    margin-bottom: 20px;
}
.cert-score-box {
    text-align: center;
    background: #f5f3ff; border: 1.5px solid #c4b5fd;
    border-radius: 8px; padding: 10px 24px;
}
.cert-score-box .sv { font-size: 20pt; font-weight: 900; color: #6949BC; line-height: 1; }
.cert-score-box .sl { font-size: 7.5pt; color: #7c3aed; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin-top: 3px; }

/* ── Score table (second page) ────────────────────────────────── */
.score-page { width: 210mm; margin: 0 auto; padding: 20px 28px; page-break-before: always; }
.score-page h2 { font-size: 13pt; font-weight: 800; color: #6949BC; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 2px solid #e0d6f5; }
.score-tbl { width: 100%; border-collapse: collapse; font-size: 9pt; }
.score-tbl thead tr { background: #6949BC; }
.score-tbl thead th { padding: 7px 10px; color: #fff; font-weight: 700; text-align: left; }
.score-tbl tbody tr:nth-child(even) { background: #f5f3ff; }
.score-tbl tbody td { padding: 6px 10px; color: #374151; border-bottom: 1px solid #e0d6f5; }
.pass { color: #15803d; font-weight: 700; }
.fail { color: #b91c1c; font-weight: 700; }

/* ── Footer signature ─────────────────────────────────────────── */
.cert-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 4px;
    padding-top: 14px;
    border-top: 1px solid #e0d6f5;
}
.cert-no-block { font-size: 8pt; color: #94a3b8; }
.cert-no-block span { display: block; font-family: 'Courier New', monospace; color: #6949BC; font-weight: 700; font-size: 9pt; }
.sig-block { text-align: center; min-width: 140px; }
.sig-line { border-top: 1.5px solid #1a202c; margin-top: 36px; padding-top: 4px; font-size: 8pt; color: #374151; }
.cert-date-block { font-size: 8pt; color: #94a3b8; text-align: right; }
.cert-date-block span { display: block; font-weight: 700; color: #374151; font-size: 9pt; }

/* ── Print overrides ──────────────────────────────────────────── */
@page { size: A4 portrait; margin: 1cm; }
@media print {
    .no-print { display: none !important; }
    .cert-frame { border-color: #6949BC !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .cert-score-box { background: #f5f3ff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .score-tbl thead tr { background: #6949BC !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
}
</style>

<!-- ── PAGE 1: Certificate ──────────────────────────────────────────── -->
<div class="cert-page">
    <div class="cert-frame">
        <div class="cert-inner">
            <!-- Corner ornaments -->
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>

            <!-- Organisation header -->
            <div class="cert-org">
                <div class="org-name">TMM — Trainee Management</div>
                <div class="org-sub">Training Programme · Official Certificate</div>
            </div>

            <!-- Certificate title -->
            <div class="cert-title-block">
                <div class="cert-of"><?= __('This is to certify that') ?></div>
                <div class="cert-label"><?= __('Certificate of') ?> <span><?= __('Completion') ?></span></div>
            </div>

            <div class="cert-divider">
                <div class="cert-divider-line"></div>
                <div class="cert-divider-diamond"></div>
                <div class="cert-divider-line"></div>
            </div>

            <!-- Recipient -->
            <div class="cert-recipient">
                <div class="presented-to"><?= __('has been awarded to') ?></div>
                <div class="trainee-name"><?= h($name) ?></div>
                <?php if ($code): ?>
                <div class="trainee-meta"><?= __('Trainee Code') ?>: <strong><?= h($code) ?></strong></div>
                <?php endif; ?>
                <?php if ($batch): ?>
                <div class="trainee-meta" style="margin-top:2px;"><?= __('Batch') ?>: <strong><?= h($batch) ?></strong></div>
                <?php endif; ?>
            </div>

            <!-- Body text -->
            <div class="cert-body-text">
                <?= __('for successfully completing the') ?>
                <strong><?= __('Trainee Training Programme') ?></strong><br>
                <?php if (!empty($scoreRows)): ?>
                <?= __('comprising') ?> <strong><?= count($scoreRows) ?></strong> <?= __('competency test(s)') ?>
                <?php if ($avg !== null): ?>, <?= __('achieving an overall average score of') ?> <strong><?= number_format($avg, 1) ?></strong><?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Score / Grade boxes -->
            <?php if ($avg !== null): ?>
            <div class="cert-score-row">
                <div class="cert-score-box">
                    <div class="sv"><?= number_format($avg, 1) ?></div>
                    <div class="sl"><?= __('Average Score') ?></div>
                </div>
                <div class="cert-score-box">
                    <div class="sv"><?= $tests ?></div>
                    <div class="sl"><?= __('Tests Taken') ?></div>
                </div>
                <?php if ($passed > 0): ?>
                <div class="cert-score-box" style="background:#f0fdf4;border-color:#86efac;">
                    <div class="sv" style="color:#15803d;"><?= $passed ?></div>
                    <div class="sl" style="color:#15803d;"><?= __('Passed') ?></div>
                </div>
                <?php endif; ?>
                <?php if ($gradeInfo): [$gl, $gd] = $gradeInfo; ?>
                <div class="cert-score-box">
                    <div class="sv" style="font-size:24pt;"><?= h($gl) ?></div>
                    <div class="sl"><?= h($gd) ?></div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="cert-footer">
                <div class="cert-no-block">
                    <?= __('Certificate No.') ?><br>
                    <span><?= h($certNo) ?></span>
                </div>
                <div class="sig-block">
                    <div class="sig-line">
                        <?= __('Authorised Signatory') ?>
                    </div>
                </div>
                <div class="cert-date-block">
                    <?= __('Issue Date') ?><br>
                    <span><?= h($issueD) ?></span>
                </div>
            </div>

        </div><!-- /.cert-inner -->
    </div><!-- /.cert-frame -->
</div><!-- /.cert-page -->

<!-- ── PAGE 2: Score detail (only if there are scores) ─────────────── -->
<?php if (!empty($scoreRows)): ?>
<div class="score-page">
    <h2><i class="fas fa-chart-bar" style="margin-right:8px;"></i><?= __('Test Score Detail') ?> — <?= h($name) ?></h2>
    <p style="font-size:8.5pt;color:#64748b;margin-bottom:12px;">
        <?= __('Certificate No.') ?>: <strong><?= h($certNo) ?></strong>
        &nbsp;·&nbsp; <?= __('Batch') ?>: <strong><?= h($batch ?? '—') ?></strong>
        &nbsp;·&nbsp; <?= __('Issue Date') ?>: <strong><?= h($issueD) ?></strong>
    </p>
    <table class="score-tbl">
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th><?= __('Competency') ?></th>
                <th style="width:80px;"><?= __('Test Date') ?></th>
                <th style="width:60px;text-align:center;"><?= __('Score') ?></th>
                <th style="width:50px;text-align:center;"><?= __('Grade') ?></th>
                <th style="width:60px;text-align:center;"><?= __('Status') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($scoreRows as $i => $s):
            $sc = (int)$s['score'];
            $status = $sc >= 60 ? 'pass' : 'fail';
            $statusLabel = $sc >= 60 ? __('Pass') : __('Fail');
        ?>
        <tr>
            <td style="color:#94a3b8;"><?= $i + 1 ?></td>
            <td><?= h($s['competency'] ?? '—') ?></td>
            <td style="white-space:nowrap;"><?= $s['test_date'] ? date('d M Y', strtotime($s['test_date'])) : '—' ?></td>
            <td style="text-align:center;font-weight:700;"><?= $sc ?></td>
            <td style="text-align:center;font-weight:800;"><?= h($s['grade'] ?? '?') ?></td>
            <td style="text-align:center;" class="<?= $status ?>"><?= $statusLabel ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <?php if ($avg !== null): ?>
        <tfoot>
            <tr style="background:#f5f3ff;font-weight:700;">
                <td colspan="3" style="text-align:right;padding:7px 10px;color:#6949BC;"><?= __('Overall Average') ?></td>
                <td style="text-align:center;color:#6949BC;font-size:11pt;"><?= number_format($avg, 1) ?></td>
                <td style="text-align:center;color:#6949BC;"><?= $gradeInfo ? h($gradeInfo[0]) : '' ?></td>
                <td></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>

    <div style="margin-top:24px;display:flex;justify-content:space-between;font-size:8pt;color:#94a3b8;">
        <span><?= __('Generated') ?>: <?= date('d M Y H:i') ?></span>
        <span><?= __('TMM Trainee Management System') ?></span>
    </div>
</div>
<?php endif; ?>
