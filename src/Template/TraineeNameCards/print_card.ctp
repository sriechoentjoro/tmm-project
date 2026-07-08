<?php
/**
 * Single trainee name card — no DB record, just trainee data
 * @var array $row  trainee fields
 */
$name   = $row['name']             ?? '—';
$code   = $row['tmm_code']         ?? '';
$init   = mb_strtoupper(mb_substr($name, 0, 1));
$gender = (int)($row['master_gender_id'] ?? 1);
$idNo   = $row['identity_number']  ?? null;
$batch  = $row['batch_name']       ?? null;

$headerGrad  = $gender === 2
    ? 'linear-gradient(135deg,#C2185B 0%,#E91E63 60%,#F06292 100%)'
    : 'linear-gradient(135deg,#01579B 0%,#0277BD 60%,#039BE5 100%)';
$avatarGrad  = $gender === 2
    ? 'linear-gradient(135deg,#AD1457,#E91E63)'
    : 'linear-gradient(135deg,#01579B,#0288D1)';
$accent      = $gender === 2 ? '#C2185B' : '#0277BD';
$codeBg      = $gender === 2 ? '#fce4ec' : '#e3f2fd';
?>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { background:#e8e8e8; font-family:Arial,sans-serif; padding:30px; }

.print-controls {
    max-width:210mm; margin:0 auto 24px;
    background:#fff; border-radius:10px; padding:16px 22px;
    box-shadow:0 2px 12px rgba(0,0,0,0.1);
    display:flex; align-items:center; justify-content:space-between; gap:16px;
}
.print-controls h3 { margin:0; font-size:15px; color:#1a202c; }
.print-controls p  { margin:4px 0 0; font-size:12px; color:#64748b; }
.btn-print { display:inline-flex; align-items:center; gap:7px; padding:10px 22px; background:<?= $accent ?>; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; }
.btn-back  { display:inline-flex; align-items:center; gap:6px; padding:10px 16px; background:#fff; color:#64748b; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; }

.card-sheet { display:flex; justify-content:center; max-width:210mm; margin:0 auto; }

/* CR80: 85.6 × 54mm */
.id-card {
    width:85.6mm; height:54mm;
    border-radius:8px; overflow:hidden;
    box-shadow:0 6px 24px rgba(0,0,0,0.25);
    display:flex; flex-direction:column;
    background:#fff;
}
.card-stripe {
    height:15mm; flex-shrink:0;
    background:<?= $headerGrad ?>;
    padding:0 5mm;
    display:flex; align-items:center; justify-content:space-between;
    position:relative; overflow:hidden;
}
.card-stripe::before { content:''; position:absolute; right:-8mm; top:-8mm; width:22mm; height:22mm; border-radius:50%; background:rgba(255,255,255,0.10); }
.card-stripe::after  { content:''; position:absolute; right:6mm; top:-4mm; width:12mm; height:12mm; border-radius:50%; background:rgba(255,255,255,0.08); }
.s-org .s-name { font-size:7pt; font-weight:900; color:#fff; letter-spacing:0.8px; text-transform:uppercase; line-height:1.2; }
.s-org .s-sub  { font-size:4.5pt; color:rgba(255,255,255,0.82); letter-spacing:0.5px; margin-top:1px; }
.s-type { background:rgba(255,255,255,0.22); color:#fff; border-radius:3px; padding:2px 5px; font-size:5pt; font-weight:800; letter-spacing:1px; text-transform:uppercase; z-index:1; flex-shrink:0; }

.card-body { flex:1; display:flex; align-items:center; gap:4mm; padding:3mm 5mm 2mm; }
.card-photo {
    width:17mm; height:21mm; border-radius:4px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:20pt; font-weight:900; color:#fff;
    background:<?= $avatarGrad ?>;
    box-shadow:0 2px 8px rgba(0,0,0,0.2);
    position:relative; overflow:hidden;
}
.card-photo::after { content:''; position:absolute; top:0; left:0; right:0; height:40%; background:linear-gradient(to bottom,rgba(255,255,255,0.15),transparent); }

.info-name { font-size:7.5pt; font-weight:800; color:#1a202c; line-height:1.35; text-transform:uppercase; word-break:break-word; }
.info-code { display:inline-block; margin-top:2mm; font-size:6pt; font-weight:700; color:<?= $accent ?>; background:<?= $codeBg ?>; padding:1px 6px; border-radius:4px; }
.info-batch { margin-top:1.5mm; font-size:5.5pt; color:#64748b; }
.info-nik   { margin-top:1mm; font-size:5pt; color:#64748b; }
.info-nik span { font-family:'Courier New',monospace; color:#374151; font-weight:700; }
.info-gen   { margin-top:1.5mm; font-size:5.5pt; color:#94a3b8; }

.card-footer {
    height:11mm; flex-shrink:0;
    background:#f8fafc; border-top:1.5px solid <?= $accent ?>;
    padding:0 5mm; display:flex; align-items:center; justify-content:center;
}
.footer-org { font-size:5.5pt; font-weight:800; color:<?= $accent ?>; letter-spacing:1px; text-transform:uppercase; text-align:center; }
.footer-tagline { font-size:4.5pt; color:#94a3b8; margin-top:1.5px; text-align:center; }

@page { size:A4 portrait; margin:2cm; }
@media print {
    body { background:#fff !important; padding:0 !important; }
    .no-print { display:none !important; }
    .id-card { box-shadow:0 0 0 0.5pt #bbb !important; }
    .card-stripe, .card-photo, .info-code, .card-footer {
        -webkit-print-color-adjust:exact !important;
        print-color-adjust:exact !important;
    }
}
</style>

<div class="print-controls no-print">
    <div>
        <h3><?= h($name) ?></h3>
        <p><?= h($code) ?> · <?= $gender === 2 ? 'Female' : 'Male' ?><?= $batch ? ' · ' . h($batch) : '' ?></p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="javascript:history.back()" class="btn-back">← <?= __('Back') ?></a>
        <button onclick="window.print()" class="btn-print">🖨 <?= __('Print / Save PDF') ?></button>
    </div>
</div>

<div class="card-sheet">
    <div class="id-card">
        <div class="card-stripe">
            <div class="s-org">
                <div class="s-name">TMM Training</div>
                <div class="s-sub">Trainee Management Programme</div>
            </div>
            <div class="s-type">Trainee</div>
        </div>
        <div class="card-body">
            <div class="card-photo"><?= h($init) ?></div>
            <div>
                <div class="info-name"><?= h($name) ?></div>
                <div class="info-code"><?= h($code) ?></div>
                <?php if ($batch): ?><div class="info-batch"><i class="fas fa-layer-group" style="font-size:4pt;"></i> <?= h($batch) ?></div><?php endif; ?>
                <?php if ($idNo): ?><div class="info-nik">NIK <span><?= h($idNo) ?></span></div><?php endif; ?>
                <div class="info-gen"><?= $gender === 2 ? '♀ Female' : '♂ Male' ?></div>
            </div>
        </div>
        <div class="card-footer">
            <div>
                <div class="footer-org">Trainee Name Card</div>
                <div class="footer-tagline">TMM · Trainee Management System</div>
            </div>
        </div>
    </div>
</div>
