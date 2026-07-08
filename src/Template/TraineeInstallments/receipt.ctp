<?php
/**
 * Installment Receipt / Payment Voucher (print layout)
 *
 * Paid off  -> RECEIPT with a PAID OFF / LUNAS stamp.
 * Not paid  -> PAYMENT VOUCHER showing the remaining balance.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeInstallment $installment
 * @var \App\Model\Entity\Trainee|null $trainee
 */
$isPaidOff = (bool)$installment->is_paid_off;
$docTitle = $isPaidOff ? 'RECEIPT' : 'PAYMENT VOUCHER';
$this->assign('title', $docTitle . ' - ' . ($trainee ? $trainee->name : ('#' . $installment->id)));

$currency = $installment->has('master_currency') ? $installment->master_currency->title : 'IDR';
$money = function ($amount) use ($currency) {
    $prefix = ($currency === 'IDR') ? 'Rp ' : $currency . ' ';
    return $prefix . number_format((int)$amount, 0, ',', '.');
};
$formatDate = function ($value) {
    if ($value instanceof \DateTimeInterface || $value instanceof \Cake\I18n\Date) {
        return $value->format('d F Y');
    }
    $timestamp = $value ? strtotime((string)$value) : false;
    return $timestamp ? date('d F Y', $timestamp) : '-';
};

$receiptNo = sprintf('%s-%05d', $isPaidOff ? 'RCPT' : 'PV', $installment->id);
$category = $installment->has('master_transaction_category') ? $installment->master_transaction_category->title : '-';
?>

<div class="receipt-doc">
    <div class="receipt-head">
        <div class="brand">
            <div class="brand-mark"><i class="fa fa-graduation-cap"></i></div>
            <div>
                <div class="brand-name">TMM Apprentice Management System</div>
                <div class="brand-sub">Trainee Installment Payment</div>
            </div>
        </div>
        <div class="doc-meta">
            <div class="doc-title"><?= $docTitle ?></div>
            <div class="doc-no"><?= h($receiptNo) ?></div>
            <div class="doc-date"><?= h($formatDate($installment->payment_date)) ?></div>
        </div>
    </div>

    <?php if ($isPaidOff): ?>
        <div class="stamp stamp-paid">LUNAS &middot; PAID OFF</div>
    <?php else: ?>
        <div class="stamp stamp-partial">INSTALLMENT</div>
    <?php endif; ?>

    <table class="party">
        <tr>
            <td class="party-label"><?= __('Received From') ?></td>
            <td class="party-value">
                <strong><?= $trainee ? h($trainee->name) : ('Trainee #' . h($installment->trainee_id)) ?></strong>
                <?php if ($trainee && $trainee->tmm_code): ?>
                    <span class="tmm-code"><?= h($trainee->tmm_code) ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="party-label"><?= __('Transaction Type') ?></td>
            <td class="party-value"><?= h($category) ?></td>
        </tr>
    </table>

    <table class="figures">
        <tbody>
            <tr>
                <td><?= __('This Payment') ?></td>
                <td class="fig-amount fig-highlight"><?= $money($installment->payment_amount) ?></td>
            </tr>
            <tr>
                <td><?= __('Total Owing Cost') ?></td>
                <td class="fig-amount"><?= $money($installment->full_payment_amount) ?></td>
            </tr>
            <tr>
                <td><?= __('Total Paid to Date') ?></td>
                <td class="fig-amount"><?= $money($installment->payment_accummulated) ?></td>
            </tr>
            <tr class="fig-remaining">
                <td><?= __('Remaining Balance') ?></td>
                <td class="fig-amount">
                    <?php if ($isPaidOff): ?>
                        <strong><?= $money(0) ?> &mdash; <?= __('SETTLED') ?></strong>
                    <?php else: ?>
                        <strong><?= $money($installment->unpaid_amount) ?></strong>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="amount-words">
        <span class="aw-label"><?= __('Amount received') ?>:</span>
        <strong><?= $money($installment->payment_amount) ?></strong>
        <?php if ($isPaidOff): ?>
            <div class="settle-note"><i class="fa fa-check-circle"></i>
                <?= __('This payment settled the trainee\'s full balance. The account is now closed.') ?></div>
        <?php else: ?>
            <div class="settle-note settle-note-open">
                <?= __('Outstanding balance after this payment') ?>: <strong><?= $money($installment->unpaid_amount) ?></strong>.
            </div>
        <?php endif; ?>
    </div>

    <div class="signatures">
        <div class="sign-box">
            <div class="sign-line"></div>
            <div class="sign-label"><?= __('Received by (Accounting)') ?></div>
        </div>
        <div class="sign-box">
            <div class="sign-line"></div>
            <div class="sign-label"><?= __('Trainee / Payer') ?></div>
        </div>
    </div>

    <div class="receipt-footer">
        <strong>TMM Apprentice Management System</strong>
        <div><?= __('Printed on') ?>: <?= date('l, d F Y H:i:s') ?></div>
        <div class="footer-note"><?= __('This document is a system-generated payment record.') ?></div>
    </div>

    <div class="print-actions no-print">
        <button onclick="window.print()"><i class="fa fa-print"></i> <?= __('Print') ?></button>
    </div>
</div>

<style>
    .receipt-doc {
        max-width: 720px;
        margin: 0 auto;
        position: relative;
        font-family: Arial, sans-serif;
        color: #263238;
    }
    .receipt-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 3px solid #667eea;
        padding-bottom: 15px;
        margin-bottom: 10px;
    }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand-mark {
        width: 46px; height: 46px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
    }
    .brand-name { font-size: 15pt; font-weight: bold; color: #667eea; }
    .brand-sub { font-size: 9pt; color: #78909c; }
    .doc-meta { text-align: right; }
    .doc-title { font-size: 16pt; font-weight: bold; letter-spacing: 1px; }
    .doc-no { font-size: 11pt; font-weight: bold; color: #455a64; }
    .doc-date { font-size: 9pt; color: #78909c; }
    .stamp {
        position: absolute;
        top: 90px; right: 30px;
        transform: rotate(-12deg);
        border: 3px solid;
        border-radius: 8px;
        padding: 6px 18px;
        font-size: 15pt;
        font-weight: bold;
        letter-spacing: 1px;
        opacity: 0.85;
    }
    .stamp-paid { color: #2e7d32; border-color: #2e7d32; }
    .stamp-partial { color: #e67e22; border-color: #e67e22; }
    .party { width: 100%; margin: 20px 0 15px; border-collapse: collapse; }
    .party td { padding: 6px 8px; vertical-align: top; }
    .party-label { width: 180px; color: #78909c; font-size: 10pt; }
    .party-value { font-size: 11pt; }
    .tmm-code {
        display: inline-block;
        margin-left: 8px;
        background: #eef1ff;
        color: #3b4890;
        padding: 1px 8px;
        border-radius: 4px;
        font-size: 9pt;
        font-weight: bold;
    }
    .figures { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .figures td { padding: 10px 12px; border-bottom: 1px solid #e9ecef; }
    .figures td:first-child { color: #455a64; }
    .fig-amount { text-align: right; white-space: nowrap; font-size: 11pt; }
    .fig-highlight { font-size: 14pt; font-weight: bold; color: #1a237e; }
    .fig-remaining td { border-top: 2px solid #667eea; border-bottom: none; font-size: 12pt; }
    .amount-words {
        background: #f8f9ff;
        border: 1px solid #e4e8fb;
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 20px;
    }
    .aw-label { color: #78909c; font-size: 10pt; }
    .settle-note { margin-top: 8px; color: #2e7d32; font-size: 10pt; }
    .settle-note-open { color: #b26a00; }
    .signatures { display: flex; justify-content: space-between; gap: 40px; margin: 40px 0 25px; }
    .sign-box { flex: 1; text-align: center; }
    .sign-line { border-bottom: 1px solid #90a4ae; margin-bottom: 6px; height: 40px; }
    .sign-label { font-size: 9pt; color: #78909c; }
    .receipt-footer {
        border-top: 2px solid #e0e0e0;
        padding-top: 12px;
        text-align: center;
        font-size: 9pt;
        color: #78909c;
    }
    .receipt-footer strong { color: #455a64; }
    .footer-note { font-style: italic; margin-top: 3px; }
    .print-actions { text-align: center; margin-top: 20px; }
    .print-actions button {
        background: #667eea; color: #fff; border: none;
        padding: 10px 24px; border-radius: 8px; font-size: 11pt; cursor: pointer;
    }
    @media print { .no-print { display: none !important; } }
</style>
