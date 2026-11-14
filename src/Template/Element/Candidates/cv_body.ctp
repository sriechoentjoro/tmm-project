<?php
/**
 * The curriculum vitae itself: the form a Japanese interviewer reads.
 *
 * This is the LPK's own printed form, reproduced rather than reinvented - the
 * same blocks in the same order, each label in Japanese with the Indonesian
 * underneath, because the two sides read it together across a table. The
 * document that came before it was a generic bilingual CV of our own design,
 * which meant the paper in the interviewer's hand and the page on the screen
 * were two different documents.
 *
 * Every field on the form exists on the candidates table; nothing here is
 * invented and nothing on the form is missing. Where a value is a reference
 * row the Japanese spelling is preferred - master_genders.title_jpg,
 * master_family_connections.title_kanji - and the Indonesian is shown when
 * there is no Japanese.
 *
 * It renders an entity, saved or not: the wizard builds one in memory from
 * what is in the session, so nothing here may assume an association was
 * loaded. Every one is reached through has().
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate $candidate
 * @var bool $showToolbar Print the update/export/print links. The wizard's
 *  preview has no record to export yet, so it leaves them off.
 */
$showToolbar = isset($showToolbar) ? $showToolbar : false;

/**
 * A date as a DateTime, whatever shape it arrives in. The wizard's entity
 * carries whatever was typed into the form; a saved record carries a date
 * object.
 */
$asDate = function ($value) {
    if (empty($value)) {
        return null;
    }
    if (is_object($value) && method_exists($value, 'format')) {
        return $value;
    }
    try {
        return new DateTime((string)$value);
    } catch (Exception $e) {
        return null;
    }
};

// 2007年9月29日 - the form writes dates the Japanese way and nothing else.
$jpDate = function ($value) use ($asDate) {
    $date = $asDate($value);

    return $date ? $date->format('Y') . '年' . $date->format('n') . '月' . $date->format('j') . '日' : '';
};
$jpMonth = function ($value) use ($asDate) {
    $date = $asDate($value);

    return $date ? $date->format('Y') . '年' . $date->format('n') . '月' : '';
};

$age = '';
$birth = $asDate($candidate->birth_date);
if ($birth) {
    $age = $birth->diff(new DateTime())->y;
}

/**
 * 有 / 無, and a blank for a flag nobody has answered yet.
 *
 * null is not "no". A candidate whose passport question was never asked must
 * not print as a candidate who has no passport.
 */
$yesNo = function ($flag, $yes = '有', $no = '無') {
    if ($flag === null || $flag === '') {
        return '';
    }

    return $flag ? $yes : $no;
};

$money = function ($amount) {
    if ($amount === null || $amount === '') {
        return '';
    }

    return 'Rp' . number_format((float)$amount, 0, ',', ',');
};

// 171, not 171.00; 60, not 6. body_height and body_weight are decimal
// columns holding whole numbers, and trimming trailing zeros off a string
// that has no decimal point eats the last digit of every round number.
$number = function ($value) {
    if ($value === null || $value === '') {
        return '';
    }
    $float = (float)$value;
    if ($float == (int)$float) {
        return (string)(int)$float;
    }

    return rtrim(rtrim(number_format($float, 2, '.', ''), '0'), '.');
};

/**
 * A reference row's label, Japanese first.
 *
 * @param \Cake\Datasource\EntityInterface|null $row The master row.
 * @param array $fields The columns to try, in order.
 * @return string
 */
$refLabel = function ($row, array $fields = ['title_jpg', 'title']) {
    if (!$row) {
        return '';
    }
    foreach ($fields as $field) {
        $value = $row->get($field);
        if ($value !== null && $value !== '') {
            return (string)$value;
        }
    }

    return '';
};

// An association the wizard's unsaved entity may simply not have.
$assoc = function ($entity, $property) {
    if (!$entity || !$entity->has($property)) {
        return null;
    }

    return $entity->get($property);
};

/**
 * Japanese and Indonesian on one line, as the form's own head block prints
 * them: 氏名 Nama, 生年月日 Tanggal Lahir, 出身地 Tempat Lahir.
 *
 * The stacked version below is for the blocks further down, where the label
 * column is narrow and two lines are what fits.
 */
$labelInline = function ($ja, $id) {
    $out = [];
    if ($ja !== '') {
        $out[] = '<span class="cvdoc-ja-inline">' . h($ja) . '</span>';
    }
    if ($id !== '') {
        $out[] = '<span class="cvdoc-id-inline">' . h(__($id)) . '</span>';
    }

    return implode(' ', $out);
};

// Japanese label above, Indonesian below, as the form prints them.
$label = function ($ja, $id) {
    $out = '';
    if ($ja !== '') {
        $out .= '<span class="cvdoc-ja">' . h($ja) . '</span>';
    }
    if ($id !== '') {
        $out .= '<span class="cvdoc-id">' . h(__($id)) . '</span>';
    }

    return $out;
};

$rows = function ($value) {
    return is_array($value) ? $value : [];
};
$educations = $rows($assoc($candidate, 'candidate_educations'));
$experiences = $rows($assoc($candidate, 'candidate_experiences'));
$families = $rows($assoc($candidate, 'candidate_families'));
// The printed form has no row for these two, and the wizard collects them.
// Printed after the form rather than dropped, and only when there is
// something to print, so a candidate with neither gets exactly the form.
$certifications = $rows($assoc($candidate, 'candidate_certifications'));
$courses = $rows($assoc($candidate, 'candidate_courses'));

// JL.KH. AGUS SALIM NO.34 ... KOTA BEKASI JAWA BARAT 〒17112
$addressParts = [];
foreach (['address'] as $field) {
    if (!empty($candidate->get($field))) {
        $addressParts[] = $candidate->get($field);
    }
}
foreach (['master_kelurahan', 'master_kecamatan', 'master_kabupaten', 'master_propinsi'] as $property) {
    $region = $assoc($candidate, $property);
    if ($region && $region->get('title')) {
        $addressParts[] = $region->get('title');
    }
}
$address = implode(' ', $addressParts);
if (!empty($candidate->post_code)) {
    $address = trim($address . ' 〒' . $candidate->post_code);
}

$company = $assoc($candidate, 'acceptance_organization');
?>
<style>
.cvdoc { max-width: 900px; margin: 0 auto; background: #fff; color: #000; font-size: 9.5pt;
    font-family: "Noto Sans JP", "Yu Gothic", "Meiryo", Arial, sans-serif; }
.cvdoc-toolbar { text-align: right; margin-bottom: 14px; }
.cvdoc-toolbar a {
    display: inline-block; background: #0071bc; color: #fff; border-radius: 6px;
    padding: 8px 18px; margin-left: 8px; font-size: 10pt; font-weight: 700; text-decoration: none;
}
.cvdoc-toolbar a.cvdoc-btn-light { background: #6c757d; }
.cvdoc-toolbar a.cvdoc-btn-update { background: #f58220; }
.cvdoc-toolbar a.cvdoc-btn-update i { margin-right: 4px; }

.cvdoc-letterhead { display: flex; align-items: center; gap: 18px; padding: 4px 0 10px 0; }
.cvdoc-letterhead-logo { width: 110px; height: auto; flex: 0 0 auto; }
.cvdoc-letterhead-text { flex: 1 1 auto; text-align: center; line-height: 1.35; font-size: 9pt; }
.cvdoc-letterhead-name { font-size: 15pt; font-weight: 700; letter-spacing: .5px; }
.cvdoc-rule { height: 3px; background: #f58220; border-bottom: 2px solid #0071bc; margin-bottom: 10px; }

.cvdoc-company { margin: 6px 0 10px 0; line-height: 1.4; }
.cvdoc-company .cvdoc-company-ja { font-weight: 700; }

.cvdoc table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 10px; }
.cvdoc th, .cvdoc td { border: 1px solid #000; padding: 3px 5px; vertical-align: middle;
    word-wrap: break-word; }
/* The label cells carry the form's own shading, so a reader's eye runs down
   the questions and across to the answers. Without it every cell is white
   and the page is a grid rather than a form. */
.cvdoc th { background: #ebebeb; font-weight: 400; text-align: left; }
.cvdoc .cvdoc-ja { display: block; font-size: 9pt; }
.cvdoc .cvdoc-id { display: block; font-size: 7.5pt; color: #333; }
.cvdoc .cvdoc-value { font-weight: 700; }
.cvdoc .cvdoc-sub { display: block; font-weight: 400; font-size: 8.5pt; }
.cvdoc .cvdoc-section { background: #d9d9d9; text-align: center; font-weight: 700; font-size: 10pt; }
/* The head block's left column: the label on its own line to the left, the
   value on the line under it, centred - as the form has it. */
.cvdoc .cvdoc-ja-inline { font-size: 9pt; }
.cvdoc .cvdoc-id-inline { font-size: 9pt; font-weight: 700; }
.cvdoc .cvdoc-block-label { text-align: left; }
.cvdoc .cvdoc-block-value { text-align: center; font-weight: 700; background: #fff; }
.cvdoc .cvdoc-unit { width: 34px; text-align: center; }
.cvdoc .cvdoc-centre { text-align: center; }
.cvdoc-photo { width: 118px; text-align: center; padding: 4px; }
.cvdoc-photo img { width: 108px; height: 140px; object-fit: cover; }
.cvdoc-photo-empty { width: 108px; height: 140px; background: #f2f2f2; display: inline-block; }
.cvdoc-no { font-size: 15pt; font-weight: 700; text-align: center; }
.cvdoc-empty-row td { height: 20px; }

@page { size: A4 portrait; margin: 8mm; }

@media print {
    .cvdoc-toolbar, .no-print { display: none !important; }

    /* Browsers drop background colours when printing unless told otherwise,
       and the shading on the labels is the form, not decoration. */
    .cvdoc, .cvdoc * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* The form is one A4 page on paper, so it is one A4 page here. Everything
       below is the amount of tightening that takes and no more. */
    .cvdoc { max-width: none; font-size: 7.4pt; }
    .cvdoc th, .cvdoc td { padding: 1px 4px; }
    .cvdoc .cvdoc-ja { font-size: 7.2pt; }
    .cvdoc .cvdoc-ja-inline, .cvdoc .cvdoc-id-inline { font-size: 7.2pt; }
    .cvdoc .cvdoc-id { font-size: 6pt; }
    .cvdoc .cvdoc-sub { font-size: 6.5pt; }
    .cvdoc .cvdoc-section { font-size: 8pt; }
    .cvdoc table { margin-bottom: 5px; }
    .cvdoc-letterhead { padding: 0 0 5px 0; gap: 12px; }
    .cvdoc-letterhead-logo { width: 78px; }
    .cvdoc-letterhead-text { font-size: 7pt; line-height: 1.2; }
    .cvdoc-letterhead-name { font-size: 12pt; }
    .cvdoc-rule { margin-bottom: 5px; }
    .cvdoc-company { margin: 3px 0 5px 0; font-size: 7.4pt; }
    .cvdoc-photo img, .cvdoc-photo-empty { width: 86px; height: 112px; }
    .cvdoc-no { font-size: 12pt; }
    /* The blank lines are there so a paper form can be written on. Printed
       from a record that is already filled they are only pushing the page
       count up, so one is kept to show the block can take more and the rest
       go. That is what lifts a short appendix back onto the first page. */
    .cvdoc-empty-row td { height: 13px; }
    .cvdoc-empty-row ~ .cvdoc-empty-row { display: none; }

    /* A block that will not fit moves whole to the next page rather than
       leaving its heading at the foot of this one. The heading travels with
       the table it heads. */
    .cvdoc table { page-break-inside: avoid; }
    .cvdoc tr { page-break-inside: avoid; }
    .cvdoc-letterhead, .cvdoc-rule, .cvdoc-company { page-break-after: avoid; }
}
</style>

<div class="cvdoc content">
<?php if ($showToolbar): ?>
    <div class="cvdoc-toolbar no-print">
        <a class="cvdoc-btn-update" href="<?= $this->Url->build(['action' => 'edit', $candidate->id]) ?>"><i class="fas fa-pen"></i> <?= __('Update') ?></a>
        <a href="<?= $this->Url->build(['action' => 'exportCvExcel', $candidate->id]) ?>"><?= __('Export Excel') ?></a>
        <a href="<?= $this->Url->build(['action' => 'exportCvPdf', $candidate->id]) ?>" target="_blank"><?= __('Print / PDF') ?></a>
        <a class="cvdoc-btn-light" href="<?= $this->Url->build(['action' => 'view', $candidate->id]) ?>"><?= __('Back') ?></a>
    </div>
<?php endif; ?>

    <?= $this->element('Candidates/cv_letterhead') ?>

    <div class="cvdoc-company">
        <span class="cvdoc-company-ja">企業名：<?= h($refLabel($company, ['title'])) ?></span><br>
        <?= __('Nama Calon Perusahaan') ?>: <?= h($refLabel($company, ['title'])) ?>
    </div>

    <!-- Identity: photo, and the two columns of the form's head block -->
    <table class="cvdoc-identity">
        <colgroup>
            <col style="width: 118px"><col style="width: 13%"><col>
            <col style="width: 11%"><col style="width: 18%"><col style="width: 14%"><col style="width: 30px">
        </colgroup>
        <tr>
            <td class="cvdoc-photo" rowspan="9">
                <?php
                // Only when the file is really on this server: several records
                // name a photo that was never uploaded.
                $photo = !empty($candidate->image_photo) && file_exists(WWW_ROOT . $candidate->image_photo)
                    ? $candidate->image_photo : null;
                ?>
                <?php if ($photo): ?>
                    <img src="<?= $this->Url->build('/' . h($photo)) ?>" alt="<?= h($candidate->name) ?>">
                <?php else: ?>
                    <span class="cvdoc-photo-empty"></span>
                <?php endif; ?>
            </td>
            <th><?= $labelInline('研修生No.', '') ?></th>
            <?php
            // Left empty on purpose. The trainee number is given by the
            // receiving side once the candidate is accepted, so printing our
            // own candidate_code here would put our number in their box.
            ?>
            <td class="cvdoc-no"></td>
            <th><?= $labelInline('性別', '') ?></th>
            <th><?= $labelInline('', 'Jenis Kelamin') ?></th>
            <td class="cvdoc-centre cvdoc-value" colspan="2"><?= h($refLabel($assoc($candidate, 'master_gender'))) ?></td>
        </tr>
        <tr>
            <th class="cvdoc-block-label" colspan="2"><?= $labelInline('氏名', 'Nama') ?></th>
            <th><?= $labelInline('年齢', '') ?></th>
            <th><?= $labelInline('', 'Usia') ?></th>
            <td class="cvdoc-centre cvdoc-value"><?= h($age) ?></td>
            <td class="cvdoc-unit">歳</td>
        </tr>
        <tr>
            <td class="cvdoc-block-value" colspan="2"><?= h($candidate->name) ?></td>
            <th><?= $labelInline('身長', '') ?></th>
            <th><?= $labelInline('', 'Tinggi Badan') ?></th>
            <td class="cvdoc-centre cvdoc-value"><?= h($number($candidate->body_height)) ?></td>
            <td class="cvdoc-unit">CM</td>
        </tr>
        <tr>
            <td class="cvdoc-block-value" colspan="2"><?= h($candidate->name_katakana) ?></td>
            <th><?= $labelInline('体重', '') ?></th>
            <th><?= $labelInline('', 'Berat Badan') ?></th>
            <td class="cvdoc-centre cvdoc-value"><?= h($number($candidate->body_weight)) ?></td>
            <td class="cvdoc-unit">KG</td>
        </tr>
        <tr>
            <th class="cvdoc-block-label" colspan="2"><?= $labelInline('生年月日', 'Tanggal Lahir') ?></th>
            <th><?= $labelInline('血液型', '') ?></th>
            <th><?= $labelInline('', 'Golongan Darah') ?></th>
            <td class="cvdoc-centre cvdoc-value"><?= h($refLabel($assoc($candidate, 'master_blood_type'))) ?></td>
            <td class="cvdoc-unit">型</td>
        </tr>
        <tr>
            <td class="cvdoc-block-value" colspan="2"><?= h($jpDate($candidate->birth_date)) ?></td>
            <th><?= $labelInline('配偶者', '') ?></th>
            <th><?= $labelInline('', 'Status Pernikahan') ?></th>
            <td class="cvdoc-centre cvdoc-value" colspan="2"><?= h($refLabel($assoc($candidate, 'master_marriage_status'))) ?></td>
        </tr>
        <tr>
            <th class="cvdoc-block-label" colspan="2"><?= $labelInline('出身地', 'Tempat Lahir') ?></th>
            <th><?= $labelInline('宗教', '') ?></th>
            <th><?= $labelInline('', 'Agama') ?></th>
            <td class="cvdoc-centre cvdoc-value" colspan="2"><?= h($refLabel($assoc($candidate, 'master_religion'))) ?></td>
        </tr>
        <tr>
            <td class="cvdoc-block-value" colspan="2"><?= h($candidate->birth_place) ?></td>
            <th><?= $labelInline('訪日経験', '') ?></th>
            <th><?= $labelInline('', 'Pernah ke Jepang') ?></th>
            <td class="cvdoc-centre cvdoc-value" colspan="2"><?= h($yesNo($candidate->is_ever_went_to_japan)) ?></td>
        </tr>
        <tr>
            <td class="cvdoc-block-value" colspan="2"><?= h($candidate->birth_place_katakana) ?></td>
            <th><?= $labelInline('旅券の有無', '') ?></th>
            <th><?= $labelInline('', 'Pernah Memiliki Paspor') ?></th>
            <td class="cvdoc-centre cvdoc-value" colspan="2"><?= h($yesNo($candidate->is_holding_passport)) ?></td>
        </tr>
    </table>

    <table>
        <colgroup><col style="width: 20%"><col></colgroup>
        <tr><td class="cvdoc-section" colspan="2"><?= $labelInline('現住所', 'Alamat Rumah') ?></td></tr>
        <tr><td class="cvdoc-centre" colspan="2"><?= h($address) ?></td></tr>
        <tr>
            <th><?= $labelInline('No.HP（携帯電話番号）', '') ?></th>
            <td class="cvdoc-centre"><?= h($candidate->telephone_mobile) ?></td>
        </tr>
    </table>

    <!-- 確認事項 -->
    <table>
        <colgroup><col style="width: 22%"><col style="width: 28%"><col style="width: 22%"><col></colgroup>
        <tr><td class="cvdoc-section" colspan="4"><?= $label('確認事項', 'Poin konfirmasi') ?></td></tr>
        <tr>
            <th><?= $label('長所', 'Kelebihan') ?></th>
            <td class="cvdoc-centre"><?= h($candidate->strengths) ?></td>
            <th><?= $label('短所', 'Kekurangan') ?></th>
            <td class="cvdoc-centre"><?= h($candidate->weaknesses) ?></td>
        </tr>
        <tr>
            <th><?= $label('趣味', 'Hobi') ?></th>
            <td class="cvdoc-centre"><?= h($candidate->hobby) ?></td>
            <th><?= $label('視力', 'Penglihatan (minus di)') ?></th>
            <td class="cvdoc-centre">
                <?= h($yesNo($candidate->is_wear_eye_glasses)) ?>
                <?php if (!empty($candidate->explain_eye_condition)): ?>
                    <span class="cvdoc-sub"><?= h($candidate->explain_eye_condition) ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><?= $label('色覚異常', 'Buta warna/tidak') ?></th>
            <td class="cvdoc-centre">
                <?= h($yesNo($candidate->is_color_blind)) ?>
                <?php if (!empty($candidate->explain_color_blind)): ?>
                    <span class="cvdoc-sub"><?= h($candidate->explain_color_blind) ?></span>
                <?php endif; ?>
            </td>
            <th><?= $label('利き手', 'Tangan yang dominan') ?></th>
            <td class="cvdoc-centre"><?= h($yesNo($candidate->is_right_handed, '右', '左')) ?></td>
        </tr>
        <tr>
            <th><?= $label('喫煙', 'Merokok/tidak') ?></th>
            <td class="cvdoc-centre"><?= h($yesNo($candidate->is_smoking, '喫煙', '禁煙')) ?></td>
            <th><?= $label('刺青', 'Bertato/tidak') ?></th>
            <td class="cvdoc-centre"><?= h($yesNo($candidate->is_tattooed)) ?></td>
        </tr>
        <tr>
            <th><?= $label('飲酒', 'Minum alkohol/tidak') ?></th>
            <td class="cvdoc-centre"><?= h($yesNo($candidate->is_drinking_alcohol)) ?></td>
            <th><?= $label('現在の収入', 'Jumlah pendapatan terakhir') ?></th>
            <td class="cvdoc-centre"><?= h($money($candidate->last_salary_amount)) ?></td>
        </tr>
        <tr>
            <th colspan="2"><?= $label('3年間日本での貯金目標', 'Target tabungan 3 thn di Jepang') ?></th>
            <td class="cvdoc-centre" colspan="2"><?= h($money($candidate->saving_goal_amount)) ?></td>
        </tr>
        <tr>
            <th colspan="2"><?= $label('技能実習に行く目的', 'Tujuan magang ke Jepang') ?></th>
            <td colspan="2"><?= h($candidate->application_reasons) ?></td>
        </tr>
        <tr>
            <th><?= $label('実習3年間終了後日本に戻って、働く希望', 'Selesai 3 tahun magang, ada/tidak keinginan kembali bekerja ke Jepang') ?></th>
            <td class="cvdoc-centre"><?= h($yesNo($candidate->will_go_to_japan_after_finished)) ?></td>
            <th><?= $label('「有」の場合、何をしたい？', 'Jika jawab "Ya", sebagai apa?') ?></th>
            <td class="cvdoc-centre"><?= h($candidate->expected_work_upon_returning_to_japan) ?></td>
        </tr>
    </table>

    <!-- 学歴 -->
    <table>
        <colgroup><col style="width: 30%"><col style="width: 35%"><col></colgroup>
        <tr><td class="cvdoc-section" colspan="3"><?= $label('学歴', 'Pendidikan') ?></td></tr>
        <tr>
            <th class="cvdoc-centre"><?= $label('期間年', 'Tahun') ?></th>
            <th class="cvdoc-centre"><?= $label('学校名', 'Nama Sekolah') ?></th>
            <th class="cvdoc-centre"><?= $label('専攻', 'Jurusan') ?></th>
        </tr>
        <?php foreach ($educations as $education): ?>
            <tr>
                <td class="cvdoc-centre">
                    <?php
                    // The wizard asks only when they finished, so half of
                    // these have one end. A lone dash in front of a year
                    // reads as a mistake rather than as an unknown.
                    $period = array_filter([
                        $jpMonth($education->college_entry_date),
                        $jpMonth($education->college_graduate_date),
                    ]);
                    ?>
                    <?= h(implode(' - ', $period)) ?>
                </td>
                <td><?= h($education->college_name) ?></td>
                <td>
                    <?= h($education->college_major) ?>
                    <?php $strata = $assoc($education, 'master_strata'); ?>
                    <?php if ($strata): ?>
                        <span class="cvdoc-sub"><?= h($refLabel($strata, ['title_hiragana', 'title'])) ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php for ($i = count($educations); $i < 3; $i++): ?>
            <tr class="cvdoc-empty-row"><td></td><td></td><td></td></tr>
        <?php endfor; ?>
    </table>

    <!-- 職歴 -->
    <table>
        <colgroup><col style="width: 30%"><col style="width: 35%"><col></colgroup>
        <tr><td class="cvdoc-section" colspan="3"><?= $label('職歴', 'Pengalaman kerja') ?></td></tr>
        <tr>
            <th class="cvdoc-centre"><?= $label('期間', 'Tahun') ?></th>
            <th class="cvdoc-centre"><?= $label('会社名', 'Nama Perusahaan') ?></th>
            <th class="cvdoc-centre"><?= $label('職種', 'Jenis kerja') ?></th>
        </tr>
        <?php foreach ($experiences as $experience): ?>
            <tr>
                <td class="cvdoc-centre">
                    <?php
                    // year(4) columns: the table keeps the year and not the
                    // month, so the month the printed form shows is not ours
                    // to write.
                    $period = array_filter([
                        $experience->employment_start_year ? $experience->employment_start_year . '年' : '',
                        $experience->employment_end_year ? $experience->employment_end_year . '年' : '',
                    ]);
                    ?>
                    <?= h(implode(' - ', $period)) ?>
                </td>
                <td><?= h($experience->company_name) ?></td>
                <td>
                    <?= h($experience->title) ?>
                    <?php if (!empty($experience->job_detail)): ?>
                        <span class="cvdoc-sub"><?= h($experience->job_detail) ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php for ($i = count($experiences); $i < 2; $i++): ?>
            <tr class="cvdoc-empty-row"><td></td><td></td><td></td></tr>
        <?php endfor; ?>
    </table>

    <!-- 家族構成 -->
    <table>
        <colgroup><col style="width: 18%"><col style="width: 32%"><col style="width: 12%"><col></colgroup>
        <tr><td class="cvdoc-section" colspan="4"><?= $label('家族構成', 'Susunan Keluarga Kandung') ?></td></tr>
        <tr>
            <th class="cvdoc-centre"><?= $label('続柄', 'Urutan Keluarga') ?></th>
            <th class="cvdoc-centre"><?= $label('氏名', 'Nama Anggota Keluarga') ?></th>
            <th class="cvdoc-centre"><?= $label('年齢', 'Usia') ?></th>
            <th class="cvdoc-centre"><?= $label('職業', 'Pekerjaan') ?></th>
        </tr>
        <?php foreach ($families as $family): ?>
            <tr>
                <td class="cvdoc-centre cvdoc-value">
                    <?= h($refLabel($assoc($family, 'master_family_connection'), ['title_kanji', 'title'])) ?>
                </td>
                <td><?= h($family->name) ?></td>
                <td class="cvdoc-centre"><?= h($family->age !== null && $family->age !== '' ? $family->age . '歳' : '') ?></td>
                <td>
                    <?php
                    // The occupation row when it resolved, and the free text
                    // only when it did not. Printing both repeats the
                    // occupation, because the wizard writes it into the
                    // detail line as well for the installations whose
                    // master_occupations does not carry what was typed.
                    $occupation = $refLabel($assoc($family, 'master_occupation'), ['title']);
                    ?>
                    <?= h($occupation !== '' ? $occupation : (string)$family->detail) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php for ($i = count($families); $i < 3; $i++): ?>
            <tr class="cvdoc-empty-row"><td></td><td></td><td></td><td></td></tr>
        <?php endfor; ?>
    </table>

<?php if ($certifications || $courses): ?>
    <!-- 資格・研修: not on the printed form, and not thrown away either -->
    <table>
        <colgroup><col style="width: 18%"><col style="width: 32%"><col></colgroup>
        <tr><td class="cvdoc-section" colspan="3"><?= $label('資格・研修', 'Sertifikat & Pelatihan') ?></td></tr>
        <tr>
            <th class="cvdoc-centre"><?= $label('取得日', 'Tanggal') ?></th>
            <th class="cvdoc-centre"><?= $label('資格名・コース名', 'Nama Sertifikat / Kursus') ?></th>
            <th class="cvdoc-centre"><?= $label('取得機関', 'Lembaga') ?></th>
        </tr>
        <?php foreach ($certifications as $certification): ?>
            <tr>
                <td class="cvdoc-centre"><?= h($jpMonth($certification->certification_date)) ?></td>
                <td>
                    <?= h($certification->title) ?>
                    <?php if (!empty($certification->detail)): ?>
                        <span class="cvdoc-sub"><?= h($certification->detail) ?></span>
                    <?php endif; ?>
                </td>
                <td><?= h($certification->institution_name) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($courses as $course): ?>
            <tr>
                <td class="cvdoc-centre"><?= h($course->course_year ? $course->course_year . '年' : '') ?></td>
                <td>
                    <?= h($course->title) ?>
                    <?php if (!empty($course->detail)): ?>
                        <span class="cvdoc-sub"><?= h($course->detail) ?></span>
                    <?php endif; ?>
                </td>
                <td></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</div>
