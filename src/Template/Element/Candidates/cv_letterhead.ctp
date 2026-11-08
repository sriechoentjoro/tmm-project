<?php
/**
 * The letterhead at the top of a candidate's CV.
 *
 * Deliberately the operator's own, not the candidate's institution. A CV goes
 * to a receiving company in Japan from the sending organisation that holds the
 * licence, and that is this one whichever LPK proposed the candidate. A
 * supporting LPK's own name and address belong on its own paperwork, not on
 * the document the Japanese side files.
 *
 * So it is written here rather than read from vocational_training_institutions,
 * and this file is the one place to change it. The table could not supply it
 * anyway: it has name, address and email but no telephone column and nowhere
 * to keep a logo.
 *
 * @var \App\View\AppView $this
 */
?>
<div class="cvdoc-letterhead">
    <img class="cvdoc-letterhead-logo"
         src="<?= $this->Url->build('/img/lpk-trimitra-logo.png') ?>"
         alt="LPK Trimitra Matahari Mandiri">
    <div class="cvdoc-letterhead-text">
        <div class="cvdoc-letterhead-name">LPK TRIMITRA MATAHARI MANDIRI</div>
        <div>Ruko Kingswood Unit 1 - 3</div>
        <div>Jl. Raya Jatibening No. 40 &ndash; Bekasi Jawa Barat 17412</div>
        <div>Telp : (021) 84988942</div>
        <div>Email: lpktrimitramandiri@yahoo.com</div>
    </div>
</div>
<div class="cvdoc-rule"></div>
