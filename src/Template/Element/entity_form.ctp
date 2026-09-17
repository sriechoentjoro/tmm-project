<?php
/**
 * Shared add/edit form shell.
 *
 * The scaffolded forms in this application were all the same shape: a heading,
 * one unlabelled <fieldset>, a column of inputs in whatever order the table
 * happens to store them, and a bare Save button. Nothing said which fields
 * mattered, which were optional, or what belonged in them, and a foreign key
 * appeared as a number box asking the user to know that trainee 47 is the one
 * they mean.
 *
 * This element renders such a form as grouped sections with an icon, a
 * two-column grid, required markers, and a sentence under any field that needs
 * explaining. It writes the same class names that webroot/js/pretty-form.js
 * builds - .cand-section, .icon-chip, .cand-form-actions - so a page using this
 * element and a page the script rewrote look the same, and both are styled by
 * webroot/css/pretty-form.css.
 *
 * Usage:
 *
 *   echo $this->element('entity_form', [
 *       'entity'   => $traineeCertificate,
 *       'title'    => __('Add Certificate'),
 *       'subtitle' => __('Issue a certificate to a trainee who has finished training.'),
 *       'icon'     => 'fa-certificate',
 *       'sections' => [[
 *           'title'  => __('Certificate'),
 *           'icon'   => 'fa-id-card',
 *           'fields' => [
 *               'trainee_id' => [
 *                   'label' => __('Trainee'), 'type' => 'select',
 *                   'options' => $trainees, 'empty' => __('- choose -'),
 *                   'required' => true, 'width' => 6,
 *                   'help' => __('Who the certificate is for.'),
 *               ],
 *           ],
 *       ]],
 *   ]);
 *
 * Field options are passed to FormHelper::control() untouched except for the
 * four this element consumes itself: 'help', 'width', 'required' and 'tile'.
 * They are removed before the call on purpose - an option FormHelper does not
 * recognise is not ignored, it is printed as an HTML attribute.
 *
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $entity
 * @var string $title
 * @var array $sections
 */

// Font Awesome 5.15.4 is what webroot/css/fontawesome-all.min.css ships, so
// the icon names here are the FA5 ones: fa-save, not fa-floppy-disk.
$icon        = $icon        ?? 'fa-edit';
$subtitle    = $subtitle    ?? null;
$backUrl     = $backUrl     ?? ['action' => 'index'];
$backLabel   = $backLabel   ?? __('Back to List');
$submitLabel = $submitLabel ?? __('Save');
$note        = $note        ?? __('Fields marked * are required.');
$formOptions = $formOptions ?? [];
$sections    = $sections    ?? [];

/**
 * The id FormHelper would give this field, so a hand-written <label for> points
 * at the input the helper generated.
 */
$domId = function ($field) {
    return strtolower(str_replace(['.', '_', '[', ']'], ['-', '-', '-', ''], $field));
};
?>
<?= $this->Html->css('datepicker-fix.css') ?>

<div class="cand-form">

    <div class="cand-page-header">
        <div class="cand-page-icon"><i class="fas <?= h($icon) ?>"></i></div>
        <div>
            <h1><?= h($title) ?></h1>
            <?php if ($subtitle) : ?>
                <p><?= h($subtitle) ?></p>
            <?php endif; ?>
        </div>
        <div class="cand-page-spacer"></div>
        <?= $this->Html->link(
            '<i class="fas fa-arrow-left"></i> ' . h($backLabel),
            $backUrl,
            ['class' => 'btn-cand-cancel', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->create($entity, $formOptions) ?>

    <?php foreach ($sections as $section) : ?>
        <?php
        $fields = $section['fields'] ?? [];
        if (!$fields) {
            continue;
        }
        ?>
        <div class="cand-section">
            <?php if (!empty($section['title'])) : ?>
                <div class="cand-section-header">
                    <div class="icon-chip"><i class="fas <?= h($section['icon'] ?? 'fa-sliders-h') ?>"></i></div>
                    <div>
                        <h5><?= h($section['title']) ?></h5>
                        <?php if (!empty($section['subtitle'])) : ?>
                            <p><?= h($section['subtitle']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="cand-section-body">
                <div class="row">
                    <?php foreach ($fields as $name => $options) : ?>
                        <?php
                        $options = $options ?: [];
                        $help     = $options['help'] ?? null;
                        $width    = $options['width'] ?? 6;
                        $required = !empty($options['required']);
                        $isTile   = ($options['type'] ?? null) === 'checkbox' || !empty($options['tile']);
                        unset($options['help'], $options['width'], $options['tile']);

                        $label = $options['label'] ?? null;
                        if ($required && is_string($label)) {
                            $options['label'] = ['text' => $label, 'class' => 'cand-required'];
                        }

                        if (!$isTile && !isset($options['class'])) {
                            $options['class'] = 'form-control';
                        }
                        ?>
                        <div class="col-md-<?= (int)$width ?>">
                            <?php if ($isTile) : ?>
                                <?php
                                $id = $options['id'] ?? $domId($name);
                                $tileLabel = is_array($label) ? ($label['text'] ?? $name) : ($label ?: $name);
                                ?>
                                <div class="cand-field">
                                    <div class="form-check form-switch cand-switch-tile">
                                        <?= $this->Form->checkbox($name, [
                                            'class' => 'form-check-input',
                                            'role' => 'switch',
                                            'id' => $id,
                                        ]) ?>
                                        <label class="form-check-label" for="<?= h($id) ?>"><?= h($tileLabel) ?></label>
                                    </div>
                                    <?php if ($help) : ?>
                                        <span class="cand-help"><?= h($help) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php else : ?>
                                <div class="cand-field">
                                    <?= $this->Form->control($name, $options) ?>
                                    <?php if ($help) : ?>
                                        <span class="cand-help"><?= h($help) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="cand-form-actions">
        <?php
        // FormHelper::button() already defaults to 'escape' => false, so the
        // icon markup survives; the label is escaped here instead. There is no
        // 'escapeTitle' option on button() - it would be printed as an
        // attribute on the <button> tag.
        ?>
        <?= $this->Form->button('<i class="fas fa-save"></i> ' . h($submitLabel), [
            'class' => 'btn-cand-save',
        ]) ?>
        <?= $this->Html->link(__('Cancel'), $backUrl, ['class' => 'btn-cand-cancel']) ?>
        <?php if ($note) : ?>
            <span class="cand-actions-note"><?= h($note) ?></span>
        <?php endif; ?>
    </div>

    <?= $this->Form->end() ?>
</div>
