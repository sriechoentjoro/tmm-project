<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate $candidate
 *
 * Rendered in the elegant layout (/candidates/cv/{id}) and in the print
 * layout (/candidates/export-cv-pdf/{id}), so all styling is self-contained.
 */

$fmtDate = function ($value, $format = 'Y-m-d') {
    if (empty($value)) {
        return '';
    }
    return is_object($value) ? $value->format($format) : (string)$value;
};

$age = '';
if (!empty($candidate->birth_date)) {
    try {
        $birth = new DateTime($fmtDate($candidate->birth_date));
        $age = $birth->diff(new DateTime())->y;
    } catch (Exception $e) {
        $age = '';
    }
}

// Bilingual label helper: English (or current locale) on top, Japanese underneath,
// so a Japanese interviewer can read the CV without a translator.
$ja = [
    'Curriculum Vitae'      => '履歴書',
    'Basic Information'     => '基本情報',
    'Identity Number'       => '身分証明書番号',
    'Candidate Code'        => '候補者コード',
    'Place / Date of Birth' => '出生地・生年月日',
    'years'                 => '歳',
    'Gender'                => '性別',
    'Religion'               => '宗教',
    'Marriage Status'       => '婚姻状況',
    'Blood Type'            => '血液型',
    'Height / Weight'       => '身長・体重',
    'Contact Information'   => '連絡先情報',
    'Mobile Phone'          => '携帯電話番号',
    'Emergency Contact'     => '緊急連絡先',
    'Email'                 => 'メールアドレス',
    'Address'                => '住所',
    'Education History'     => '学歴',
    'No.'                   => '番号',
    'School / College'      => '学校名',
    'Major'                  => '専攻',
    'Entry'                  => '入学',
    'Graduated'              => '卒業',
    'Work Experience'       => '職歴',
    'Company'                => '会社名',
    'Position'               => '役職',
    'Period'                 => '期間',
    'Job Detail'             => '業務内容',
    'Certifications'        => '資格',
    'Certification'         => '資格名',
    'Institution'            => '取得機関',
    'Date'                   => '取得日',
    'Detail'                 => '詳細',
    'Courses & Training'    => '研修・コース',
    'Course'                 => 'コース名',
    'Year'                   => '年',
    'Family Information'    => '家族構成',
    'Name'                   => '氏名',
    'Age'                    => '年齢',
    'Additional Information' => 'その他の情報',
    'Strengths'              => '長所',
    'Weaknesses'             => '短所',
    'Hobby'                  => '趣味',
    'Application Reasons'   => '志望動機',
];
$bl = function ($label) use ($ja) {
    $jaText = $ja[$label] ?? '';
    $en = __($label);
    if ($jaText === '') {
        return h($en);
    }
    return h($en) . '<br><span class="cvdoc-ja">' . h($jaText) . '</span>';
};
?>
<style>
.cvdoc { max-width: 900px; margin: 0 auto; background: #fff; color: #2c3e50; font-size: 11pt; }
.cvdoc-toolbar { text-align: right; margin-bottom: 14px; }
.cvdoc-toolbar a {
    display: inline-block;
    background: #00BCD4;
    color: #fff;
    border-radius: 6px;
    padding: 8px 18px;
    margin-left: 8px;
    font-size: 10.5pt;
    font-weight: 700;
    text-decoration: none;
}
.cvdoc-toolbar a.cvdoc-btn-light { background: #6c757d; }
.cvdoc-toolbar a.cvdoc-btn-update { background: #fb8c00; }
.cvdoc-toolbar a.cvdoc-btn-update i { margin-right: 4px; }
.cvdoc-header {
    display: flex;
    gap: 24px;
    align-items: center;
    border-bottom: 3px solid #00BCD4;
    padding-bottom: 18px;
    margin-bottom: 20px;
}
.cvdoc-header img, .cvdoc-photo-placeholder {
    width: 120px;
    height: 150px;
    object-fit: cover;
    border: 1px solid #cfd8dc;
}
.cvdoc-photo-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #b0bec5;
    font-size: 36pt;
    background: #f5f7f8;
}
.cvdoc-header h1 { margin: 0; font-size: 20pt; letter-spacing: .5px; }
.cvdoc-header .cvdoc-katakana { font-size: 12pt; color: #607d8b; margin-top: 2px; }
.cvdoc-header .cvdoc-subtitle { font-size: 10pt; color: #90a4ae; margin-top: 8px; text-transform: uppercase; letter-spacing: 1.5px; }
.cvdoc h2 {
    font-size: 12.5pt;
    color: #0097A7;
    border-bottom: 2px solid #e0f2f4;
    padding-bottom: 5px;
    margin: 22px 0 10px 0;
}
.cvdoc table.cvdoc-table { width: 100%; border-collapse: collapse; table-layout: fixed; margin: 0; }
.cvdoc table.cvdoc-table th {
    background: #f1fafb;
    color: #455a64;
    border: 1px solid #dde7ea;
    padding: 7px 10px;
    text-align: left;
    font-size: 9.5pt;
    font-weight: 700;
}
.cvdoc table.cvdoc-table td {
    border: 1px solid #e4ecef;
    padding: 7px 10px;
    font-size: 10pt;
    background: #fff;
    word-wrap: break-word;
}
.cvdoc table.cvdoc-kv th { width: 30%; }
.cvdoc-ja { display: block; font-size: 8.5pt; color: #78909c; font-weight: 500; margin-top: 1px; }
.cvdoc h2 .cvdoc-ja { display: inline; font-size: 10pt; color: #4dd0e1; margin: 0 0 0 8px; }
@media print {
    .cvdoc-toolbar { display: none !important; }
    .cvdoc h2 { page-break-after: avoid; }
    .cvdoc table { page-break-inside: auto; }
}
</style>

<div class="cvdoc content">
    <div class="cvdoc-toolbar no-print">
        <a class="cvdoc-btn-update" href="<?= $this->Url->build(['action' => 'edit', $candidate->id]) ?>"><i class="fas fa-pen"></i> <?= __('Update') ?></a>
        <a href="<?= $this->Url->build(['action' => 'exportCvExcel', $candidate->id]) ?>"><?= __('Export Excel') ?></a>
        <a href="<?= $this->Url->build(['action' => 'exportCvPdf', $candidate->id]) ?>" target="_blank"><?= __('Print / PDF') ?></a>
        <a class="cvdoc-btn-light" href="<?= $this->Url->build(['action' => 'view', $candidate->id]) ?>"><?= __('Back') ?></a>
    </div>

    <!-- Header -->
    <div class="cvdoc-header">
        <?php
        // Only render the photo when the file really exists (some records
        // reference photos that were never uploaded to this server)
        $photoExists = !empty($candidate->image_photo) && file_exists(WWW_ROOT . $candidate->image_photo);
        ?>
        <?php if ($photoExists): ?>
            <img src="<?= $this->Url->build('/' . h($candidate->image_photo)) ?>" alt="<?= h($candidate->name) ?>">
        <?php else: ?>
            <div class="cvdoc-photo-placeholder">&#9786;</div>
        <?php endif; ?>
        <div>
            <div class="cvdoc-subtitle"><?= __('Curriculum Vitae') ?> / <?= $ja['Curriculum Vitae'] ?></div>
            <h1><?= h($candidate->name) ?></h1>
            <?php if (!empty($candidate->name_katakana)): ?>
                <div class="cvdoc-katakana"><?= h($candidate->name_katakana) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Basic Information -->
    <h2><?= __('Basic Information') ?> <span class="cvdoc-ja">/ <?php echo $ja["Basic Information"]; ?></span></h2>
    <table class="cvdoc-table cvdoc-kv">
        <tr><th><?= $bl('Identity Number') ?></th><td><?= h($candidate->identity_number) ?></td></tr>
        <?php if (!empty($candidate->candidate_code)): ?>
        <tr><th><?= $bl('Candidate Code') ?></th><td><?= h($candidate->candidate_code) ?></td></tr>
        <?php endif; ?>
        <tr><th><?= $bl('Place / Date of Birth') ?></th><td><?= h($candidate->birth_place) ?>, <?= h($fmtDate($candidate->birth_date)) ?><?= $age !== '' ? ' (' . $age . ' ' . __('years') . ')' : '' ?></td></tr>
        <tr><th><?= $bl('Gender') ?></th><td><?= !empty($candidate->master_gender) ? h($candidate->master_gender->title) : '' ?></td></tr>
        <tr><th><?= $bl('Religion') ?></th><td><?= !empty($candidate->master_religion) ? h($candidate->master_religion->title) : '' ?></td></tr>
        <tr><th><?= $bl('Marriage Status') ?></th><td><?= !empty($candidate->master_marriage_status) ? h($candidate->master_marriage_status->title) : '' ?></td></tr>
        <?php if (!empty($candidate->master_blood_type)): ?>
        <tr><th><?= $bl('Blood Type') ?></th><td><?= h($candidate->master_blood_type->title) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($candidate->body_height) || !empty($candidate->body_weight)): ?>
        <tr><th><?= $bl('Height / Weight') ?></th><td><?= !empty($candidate->body_height) ? h($candidate->body_height) . ' cm' : '-' ?> / <?= !empty($candidate->body_weight) ? h($candidate->body_weight) . ' kg' : '-' ?></td></tr>
        <?php endif; ?>
    </table>

    <!-- Contact Information -->
    <h2><?= __('Contact Information') ?> <span class="cvdoc-ja">/ <?php echo $ja["Contact Information"]; ?></span></h2>
    <table class="cvdoc-table cvdoc-kv">
        <?php if (!empty($candidate->telephone_mobile)): ?>
        <tr><th><?= $bl('Mobile Phone') ?></th><td><?= h($candidate->telephone_mobile) ?></td></tr>
        <?php endif; ?>
        <tr><th><?= $bl('Emergency Contact') ?></th><td><?= h($candidate->telephone_emergency) ?></td></tr>
        <?php if (!empty($candidate->email)): ?>
        <tr><th><?= $bl('Email') ?></th><td><?= h($candidate->email) ?></td></tr>
        <?php endif; ?>
        <tr><th><?= $bl('Address') ?></th><td><?= h($candidate->address) ?><?= !empty($candidate->post_code) ? ' (' . h($candidate->post_code) . ')' : '' ?></td></tr>
    </table>

    <!-- Education History -->
    <?php if (!empty($candidate->candidate_educations)): ?>
    <h2><?= __('Education History') ?> <span class="cvdoc-ja">/ <?php echo $ja["Education History"]; ?></span></h2>
    <table class="cvdoc-table">
        <thead>
            <tr>
                <th style="width: 5%;"><?= $bl('No.') ?></th>
                <th style="width: 35%;"><?= $bl('School / College') ?></th>
                <th style="width: 25%;"><?= $bl('Major') ?></th>
                <th style="width: 17%;"><?= $bl('Entry') ?></th>
                <th style="width: 18%;"><?= $bl('Graduated') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidate->candidate_educations as $index => $edu): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= h($edu->college_name) ?></td>
                <td><?= h($edu->college_major) ?></td>
                <td><?= h($fmtDate($edu->college_entry_date)) ?></td>
                <td><?= h($fmtDate($edu->college_graduate_date)) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Work Experience -->
    <?php if (!empty($candidate->candidate_experiences)): ?>
    <h2><?= __('Work Experience') ?> <span class="cvdoc-ja">/ <?php echo $ja["Work Experience"]; ?></span></h2>
    <table class="cvdoc-table">
        <thead>
            <tr>
                <th style="width: 5%;"><?= $bl('No.') ?></th>
                <th style="width: 30%;"><?= $bl('Company') ?></th>
                <th style="width: 22%;"><?= $bl('Position') ?></th>
                <th style="width: 15%;"><?= $bl('Period') ?></th>
                <th style="width: 28%;"><?= $bl('Job Detail') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidate->candidate_experiences as $index => $exp): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= h($exp->company_name) ?></td>
                <td><?= h($exp->title) ?></td>
                <td><?= h($exp->employment_start_year) ?><?= ($exp->employment_start_year || $exp->employment_end_year) ? ' - ' : '' ?><?= h($exp->employment_end_year) ?></td>
                <td><?= h($exp->job_detail) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Certifications -->
    <?php if (!empty($candidate->candidate_certifications)): ?>
    <h2><?= __('Certifications') ?> <span class="cvdoc-ja">/ <?php echo $ja["Certifications"]; ?></span></h2>
    <table class="cvdoc-table">
        <thead>
            <tr>
                <th style="width: 5%;"><?= $bl('No.') ?></th>
                <th style="width: 35%;"><?= $bl('Certification') ?></th>
                <th style="width: 30%;"><?= $bl('Institution') ?></th>
                <th style="width: 15%;"><?= $bl('Date') ?></th>
                <th style="width: 15%;"><?= $bl('Detail') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidate->candidate_certifications as $index => $cert): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= h($cert->title) ?></td>
                <td><?= h($cert->institution_name) ?></td>
                <td><?= h($fmtDate($cert->certification_date)) ?></td>
                <td><?= h($cert->detail) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Courses & Training -->
    <?php if (!empty($candidate->candidate_courses)): ?>
    <h2><?= __('Courses & Training') ?> <span class="cvdoc-ja">/ <?php echo $ja["Courses & Training"]; ?></span></h2>
    <table class="cvdoc-table">
        <thead>
            <tr>
                <th style="width: 5%;"><?= $bl('No.') ?></th>
                <th style="width: 40%;"><?= $bl('Course') ?></th>
                <th style="width: 15%;"><?= $bl('Year') ?></th>
                <th style="width: 40%;"><?= $bl('Detail') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidate->candidate_courses as $index => $course): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= h($course->title) ?></td>
                <td><?= h($course->course_year) ?></td>
                <td><?= h($course->detail) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Family Information -->
    <?php if (!empty($candidate->candidate_families)): ?>
    <h2><?= __('Family Information') ?> <span class="cvdoc-ja">/ <?php echo $ja["Family Information"]; ?></span></h2>
    <table class="cvdoc-table">
        <thead>
            <tr>
                <th style="width: 5%;"><?= $bl('No.') ?></th>
                <th style="width: 35%;"><?= $bl('Name') ?></th>
                <th style="width: 10%;"><?= $bl('Age') ?></th>
                <th style="width: 50%;"><?= $bl('Detail') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidate->candidate_families as $index => $fam): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= h($fam->name) ?></td>
                <td><?= h($fam->age) ?></td>
                <td><?= h($fam->detail) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Additional Information -->
    <?php if (!empty($candidate->strengths) || !empty($candidate->weaknesses) || !empty($candidate->hobby) || !empty($candidate->application_reasons)): ?>
    <h2><?= __('Additional Information') ?> <span class="cvdoc-ja">/ <?php echo $ja["Additional Information"]; ?></span></h2>
    <table class="cvdoc-table cvdoc-kv">
        <?php if (!empty($candidate->strengths)): ?>
        <tr><th><?= $bl('Strengths') ?></th><td><?= h($candidate->strengths) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($candidate->weaknesses)): ?>
        <tr><th><?= $bl('Weaknesses') ?></th><td><?= h($candidate->weaknesses) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($candidate->hobby)): ?>
        <tr><th><?= $bl('Hobby') ?></th><td><?= h($candidate->hobby) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($candidate->application_reasons)): ?>
        <tr><th><?= $bl('Application Reasons') ?></th><td><?= h($candidate->application_reasons) ?></td></tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>
</div>
