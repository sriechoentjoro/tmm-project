<?php
/**
 * Batch name card print — all trainees in one batch, 8 per A4 page
 * @var array $batch     batch info
 * @var array $trainees  trainees in this batch
 */
?>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { background:#fff; font-family:Arial,sans-serif; padding:10px; }

.batch-sheet { display:flex; flex-wrap:wrap; gap:6mm; justify-content:flex-start; max-width:210mm; margin:0 auto; }

/* CR80: 85.6 × 54mm */
.id-card {
    width:85.6mm; height:54mm;
    border-radius:6px; overflow:hidden;
    display:flex; flex-direction:column;
    background:#fff; border:0.5pt solid #ccc;
    page-break-inside:avoid;
    position:relative;
}

.card-stripe {
    height:14mm; flex-shrink:0;
    padding:0 4mm;
    display:flex; align-items:center; justify-content:space-between;
    position:relative; overflow:hidden;
}
.card-stripe::before { content:''; position:absolute; right:-7mm; top:-7mm; width:20mm; height:20mm; border-radius:50%; background:rgba(255,255,255,0.10); }
.s-name { font-size:6.5pt; font-weight:900; color:#fff; letter-spacing:0.5px; text-transform:uppercase; line-height:1.2; }
.s-sub  { font-size:4.5pt; color:rgba(255,255,255,0.82); margin-top:1px; }
.s-type { background:rgba(255,255,255,0.2); color:#fff; border-radius:3px; padding:1px 4px; font-size:4.5pt; font-weight:800; letter-spacing:0.8px; text-transform:uppercase; z-index:1; flex-shrink:0; }

.card-body { flex:1; display:flex; align-items:center; gap:3mm; padding:2mm 4mm; }
.card-photo {
    width:15mm; height:19mm; border-radius:3px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:16pt; font-weight:900; color:#fff;
    position:relative; overflow:hidden;
}
.card-photo::after { content:''; position:absolute; top:0; left:0; right:0; height:35%; background:linear-gradient(to bottom,rgba(255,255,255,0.12),transparent); }
.info { flex:1; min-width:0; }
.info-name  { font-size:7pt; font-weight:800; color:#1a202c; line-height:1.3; text-transform:uppercase; word-break:break-word; }
.info-code  { font-size:5.5pt; font-weight:700; padding:1px 5px; border-radius:3px; display:inline-block; margin-top:1.5mm; }
.info-batch { font-size:5pt; color:#64748b; margin-top:1mm; }
.info-nik   { font-size:5pt; color:#64748b; margin-top:0.5mm; }
.info-nik span { font-family:'Courier New',monospace; color:#374151; font-weight:700; }
.info-gen   { font-size:5pt; color:#94a3b8; margin-top:1mm; }

.card-footer { height:10mm; flex-shrink:0; background:#f8fafc; padding:0 4mm; display:flex; align-items:center; justify-content:center; }
.footer-org  { font-size:5pt; font-weight:800; text-align:center; text-transform:uppercase; letter-spacing:0.8px; }
.footer-sub  { font-size:4pt; color:#94a3b8; text-align:center; margin-top:1px; }

@page { size:A4 portrait; margin:1cm; }
@media print {
    .no-print { display:none !important; }
    .card-stripe, .card-photo, .info-code, .card-footer {
        -webkit-print-color-adjust:exact !important;
        print-color-adjust:exact !important;
    }
}
</style>

<!-- Print controls -->
<div class="no-print" style="max-width:210mm;margin:0 auto 18px;background:#fff;border-radius:10px;padding:14px 20px;box-shadow:0 2px 10px rgba(0,0,0,0.08);display:flex;align-items:center;justify-content:space-between;gap:14px;">
    <div>
        <div style="font-size:15px;font-weight:700;color:#1a202c;"><?= h($batch['batch_name']) ?></div>
        <div style="font-size:12px;color:#64748b;margin-top:2px;"><?= count($trainees) ?> <?= __('trainees') ?> · 8 <?= __('cards per page') ?></div>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="javascript:history.back()" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">← <?= __('Back') ?></a>
        <button onclick="window.print()" style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;background:linear-gradient(135deg,#6949BC,#9C27B0);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">🖨 <?= __('Print / Save PDF') ?></button>
    </div>
</div>

<div class="batch-sheet">
<?php foreach ($trainees as $t):
    $name   = $t['name']            ?? '—';
    $code   = $t['tmm_code']        ?? '';
    $init   = mb_strtoupper(mb_substr($name, 0, 1));
    $gender = (int)($t['master_gender_id'] ?? 1);
    $idNo   = $t['identity_number'] ?? null;
    $bname  = $t['batch_name']      ?? null;

    $grad   = $gender === 2
        ? 'linear-gradient(135deg,#C2185B,#E91E63)'
        : 'linear-gradient(135deg,#01579B,#0288D1)';
    $accent = $gender === 2 ? '#C2185B' : '#0277BD';
    $codeBg = $gender === 2 ? 'background:#fce4ec;color:#C2185B;' : 'background:#e3f2fd;color:#0277BD;';
?>
<div class="id-card" style="border-color:<?= $accent ?>;">
    <div class="card-stripe" style="background:<?= $grad ?>;">
        <div>
            <div class="s-name">TMM Training</div>
            <div class="s-sub">Trainee Management Programme</div>
        </div>
        <div class="s-type">Trainee</div>
    </div>
    <div class="card-body">
        <div class="card-photo" style="background:<?= $grad ?>;"><?= h($init) ?></div>
        <div class="info">
            <div class="info-name"><?= h($name) ?></div>
            <div class="info-code" style="<?= $codeBg ?>"><?= h($code) ?></div>
            <?php if ($bname): ?><div class="info-batch"><?= h($bname) ?></div><?php endif; ?>
            <?php if ($idNo): ?><div class="info-nik">NIK <span><?= h($idNo) ?></span></div><?php endif; ?>
            <div class="info-gen"><?= $gender === 2 ? '♀ Female' : '♂ Male' ?></div>
        </div>
    </div>
    <div class="card-footer" style="border-top:1.5px solid <?= $accent ?>;">
        <div>
            <div class="footer-org" style="color:<?= $accent ?>;">Trainee Name Card</div>
            <div class="footer-sub">TMM · Trainee Management System</div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php if (empty($trainees)): ?>
<div style="padding:40px;color:#94a3b8;font-size:13pt;">No trainees in this batch.</div>
<?php endif; ?>
</div>
