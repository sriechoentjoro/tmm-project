<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeStory $apprenticeStory
 * @var array $apprenticeOptions
 */
?>
<?= $this->Html->css('datepicker-fix.css') ?>
<?= $this->Html->script('form-confirm.js?v=2.0') ?>

<style>
.story-form-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(0,188,212,0.10);
    overflow: hidden;
    margin-bottom: 30px;
}
.story-form-header {
    background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
    padding: 22px 30px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.story-form-header i {
    font-size: 26px;
    color: #fff;
    opacity: 0.9;
}
.story-form-header h3 {
    margin: 0;
    color: #fff;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 0.3px;
}
.story-form-header p {
    margin: 2px 0 0;
    color: rgba(255,255,255,0.78);
    font-size: 13px;
}
.story-form-body {
    padding: 30px;
}
.story-section {
    border-left: 3px solid #00BCD4;
    padding-left: 16px;
    margin-bottom: 28px;
}
.story-section-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #00838F;
    margin-bottom: 14px;
}
.form-label {
    font-weight: 600;
    font-size: 13px;
    color: #374151;
    margin-bottom: 5px;
    display: block;
}
.form-label .req {
    color: #e53e3e;
    margin-left: 3px;
}
.form-control, select.form-control {
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 9px 14px;
    font-size: 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
    width: 100%;
    background: #f8fafc;
    color: #1a202c;
}
.form-control:focus, select.form-control:focus {
    border-color: #00BCD4;
    box-shadow: 0 0 0 3px rgba(0,188,212,0.12);
    background: #fff;
    outline: none;
}
textarea.form-control {
    min-height: 110px;
    resize: vertical;
}
.image-upload-zone {
    border: 2px dashed #b2ebf2;
    border-radius: 10px;
    padding: 22px;
    text-align: center;
    background: #f0fdff;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    position: relative;
}
.image-upload-zone:hover {
    border-color: #00BCD4;
    background: #e0f7fa;
}
.image-upload-zone i {
    font-size: 32px;
    color: #00BCD4;
    margin-bottom: 8px;
    display: block;
}
.image-upload-zone p {
    margin: 0;
    font-size: 13px;
    color: #4a5568;
}
.image-upload-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
}
#imagePreview {
    display: none;
    margin-top: 12px;
    border-radius: 8px;
    max-height: 200px;
    object-fit: contain;
    border: 1px solid #e2e8f0;
}
.form-actions {
    display: flex;
    gap: 12px;
    padding: 20px 30px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 14px 14px;
}
.btn-save {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 24px;
    background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(0,188,212,0.25);
}
.btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(0,188,212,0.35);
    color: #fff;
    text-decoration: none;
}
.btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 20px;
    background: #fff;
    color: #4a5568;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-cancel:hover {
    background: #f1f5f9;
    color: #1a202c;
    text-decoration: none;
}
.char-count {
    font-size: 11px;
    color: #94a3b8;
    text-align: right;
    margin-top: 3px;
}
.char-count.near-limit { color: #f59e0b; }
.char-count.at-limit   { color: #ef4444; }
</style>

<!-- Actions Sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List Stories'), ['action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-users"></i> ' . __('List Apprentices'), ['controller' => 'Apprentices', 'action' => 'index'], ['escape' => false]) ?></li>
    </ul>
</nav>

<div class="apprenticeStories form content">

    <?= $this->Form->create($apprenticeStory, [
        'data-confirm' => 'true',
        'id'           => 'apprenticeStoryForm',
        'enctype'      => 'multipart/form-data',
    ]) ?>

    <div class="story-form-card">

        <!-- Header -->
        <div class="story-form-header">
            <i class="fas fa-book-open"></i>
            <div>
                <h3><?= __('Add Apprentice Story') ?></h3>
                <p><?= __('Document a learning experience, challenge, or insight from an apprentice') ?></p>
            </div>
        </div>

        <div class="story-form-body">

            <!-- Section 1: Basic Info -->
            <div class="story-section">
                <div class="story-section-title"><i class="fas fa-info-circle"></i> <?= __('Basic Information') ?></div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?= __('Apprentice') ?> <span class="req">*</span></label>
                        <?= $this->Form->control('apprentice_id', [
                            'options'  => $apprenticeOptions,
                            'class'    => 'form-control',
                            'label'    => false,
                            'empty'    => __('-- Select Apprentice --'),
                            'required' => true,
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?= __('Date of Occurrence') ?> <span class="req">*</span></label>
                        <?= $this->Form->control('date_occurrence', [
                            'type'         => 'text',
                            'class'        => 'form-control datepicker',
                            'label'        => false,
                            'placeholder'  => 'YYYY-MM-DD',
                            'autocomplete' => 'off',
                            'required'     => true,
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Story Title') ?> <span class="req">*</span></label>
                        <?= $this->Form->control('title', [
                            'class'       => 'form-control',
                            'label'       => false,
                            'placeholder' => __('Enter a clear, descriptive title for this story'),
                            'required'    => true,
                            'maxlength'   => 256,
                            'id'          => 'StoryTitle',
                        ]) ?>
                        <div class="char-count" id="titleCount">0 / 256</div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Problem Detail -->
            <div class="story-section">
                <div class="story-section-title"><i class="fas fa-exclamation-triangle"></i> <?= __('Problem Detail') ?></div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?= __('Problem Classification') ?> <span class="req">*</span></label>
                        <?= $this->Form->control('problem_classification', [
                            'class'       => 'form-control',
                            'label'       => false,
                            'placeholder' => __('e.g. Technical, Attitude, Communication'),
                            'required'    => true,
                            'maxlength'   => 256,
                        ]) ?>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label"><?= __('Problem Contents') ?> <span class="req">*</span></label>
                        <?= $this->Form->control('problem_contents', [
                            'type'        => 'textarea',
                            'class'       => 'form-control',
                            'label'       => false,
                            'placeholder' => __('Describe the problem or situation in detail…'),
                            'required'    => true,
                            'rows'        => 5,
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Section 3: Resolution -->
            <div class="story-section">
                <div class="story-section-title"><i class="fas fa-lightbulb"></i> <?= __('Resolution & Learning') ?></div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?= __('Solution') ?> <span class="req">*</span></label>
                        <?= $this->Form->control('problem_solution', [
                            'type'        => 'textarea',
                            'class'       => 'form-control',
                            'label'       => false,
                            'placeholder' => __('What action was taken to resolve the problem?'),
                            'required'    => true,
                            'rows'        => 4,
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?= __('Inference / Lesson Learned') ?> <span class="req">*</span></label>
                        <?= $this->Form->control('problem_inference', [
                            'type'        => 'textarea',
                            'class'       => 'form-control',
                            'label'       => false,
                            'placeholder' => __('What lesson or insight was gained?'),
                            'required'    => true,
                            'rows'        => 4,
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Section 4: Supporting Image -->
            <div class="story-section">
                <div class="story-section-title"><i class="fas fa-image"></i> <?= __('Supporting Image') ?> <span style="font-weight:400;color:#94a3b8;">(<?= __('optional') ?>)</span></div>
                <div class="image-upload-zone" id="uploadZone">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p><?= __('Click or drag an image here to upload') ?></p>
                    <p style="color:#94a3b8;font-size:12px;margin-top:4px;"><?= __('JPG, PNG, GIF — max 5 MB') ?></p>
                    <input type="file" name="image_upload" id="imageUpload" accept="image/*">
                </div>
                <img id="imagePreview" src="" alt="Preview">
                <?= $this->Form->control('image_path', [
                    'type'  => 'hidden',
                    'label' => false,
                    'id'    => 'ImagePath',
                ]) ?>
            </div>

        </div><!-- /.story-form-body -->

        <!-- Footer actions -->
        <div class="form-actions">
            <?= $this->Form->button('<i class="fas fa-save"></i> ' . __('Save Story'), [
                'type'   => 'submit',
                'class'  => 'btn-save',
                'id'     => 'submitBtn',
                'escape' => false,
            ]) ?>
            <?= $this->Html->link(
                '<i class="fas fa-times"></i> ' . __('Cancel'),
                ['action' => 'index'],
                ['class' => 'btn-cancel', 'escape' => false]
            ) ?>
        </div>

    </div><!-- /.story-form-card -->

    <?= $this->Form->end() ?>
</div>

<?php $this->append('script'); ?>
<script>
$(document).ready(function () {

    // ── Datepicker ────────────────────────────────────────────────────────
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        container: 'body',
        zIndexOffset: 1050
    });

    // ── Character counter for title ───────────────────────────────────────
    var $title = $('#StoryTitle');
    var $count = $('#titleCount');
    function updateCount() {
        var len = $title.val().length;
        var max = 256;
        $count.text(len + ' / ' + max);
        $count.removeClass('near-limit at-limit');
        if (len >= max)       $count.addClass('at-limit');
        else if (len >= 220)  $count.addClass('near-limit');
    }
    $title.on('input', updateCount);
    updateCount();

    // ── Image preview ─────────────────────────────────────────────────────
    $('#imageUpload').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        if (file.size > 5 * 1024 * 1024) {
            alert('<?= __('Image must be smaller than 5 MB.') ?>');
            this.value = '';
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imagePreview').attr('src', e.target.result).show();
            $('#ImagePath').val(file.name);
        };
        reader.readAsDataURL(file);
    });

    // Drag-over style
    var zone = document.getElementById('uploadZone');
    zone.addEventListener('dragover',  function(e){ e.preventDefault(); zone.style.borderColor='#00BCD4'; });
    zone.addEventListener('dragleave', function()  { zone.style.borderColor=''; });
    zone.addEventListener('drop',      function(e) {
        e.preventDefault();
        zone.style.borderColor='';
        var files = e.dataTransfer.files;
        if (files.length) {
            document.getElementById('imageUpload').files = files;
            $(document.getElementById('imageUpload')).trigger('change');
        }
    });

});
</script>
<?php $this->end(); ?>
