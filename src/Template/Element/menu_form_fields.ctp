<?php
/**
 * Shared form fields for Menus add/edit
 * @var \App\View\AppView $this
 * @var array $parentMenus
 * @var array $categories
 */
?>
<fieldset>
    <legend><?= __('Menu Details') ?></legend>
    <div class="row">
        <div class="col-md-12">
            <?php echo $this->Form->control('title', [
                'class' => 'form-control',
                'label' => ['class' => 'form-label', 'text' => __('Title')],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?php echo $this->Form->control('category', [
                'type'    => 'select',
                'options' => array_combine($categories, $categories),
                'empty'   => __('(choose category)'),
                'class'   => 'form-control',
                'label'   => ['class' => 'form-label', 'text' => __('Category')],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?php echo $this->Form->control('new_category_hint', [
                'type'     => 'text',
                'label'    => ['class' => 'form-label', 'text' => __('...or type a new category')],
                'name'     => 'category_new',
                'required' => false,
                'value'    => '',
                'class'    => 'form-control',
                'placeholder' => __('Overrides the select above'),
            ]) ?>
        </div>
        <div class="col-md-6">
            <?php echo $this->Form->control('parent_id', [
                'options' => $parentMenus,
                'empty'   => __('(top level)'),
                'class'   => 'form-control',
                'label'   => ['class' => 'form-label', 'text' => __('Parent Menu')],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?php echo $this->Form->control('sort_order', [
                'type'  => 'number',
                'class' => 'form-control',
                'label' => ['class' => 'form-label', 'text' => __('Sort Order')],
            ]) ?>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?= __('URL &amp; Access Control') ?></legend>
    <div class="row">
        <div class="col-md-12">
            <?php echo $this->Form->control('url', [
                'label'       => ['class' => 'form-label', 'text' => __('URL (e.g. /candidates or # for parent)')],
                'id'          => 'menu-url',
                'class'       => 'form-control',
                'placeholder' => '/example-page',
            ]) ?>
        </div>
        <div class="col-md-5">
            <?php echo $this->Form->control('controller', [
                'label'       => ['class' => 'form-label', 'text' => __('Controller')],
                'placeholder' => __('Auto-filled from URL'),
                'required'    => false,
                'id'          => 'menu-controller',
                'class'       => 'form-control',
            ]) ?>
        </div>
        <div class="col-md-5">
            <?php echo $this->Form->control('action', [
                'label'       => ['class' => 'form-label', 'text' => __('Action')],
                'placeholder' => __('Auto-filled from URL'),
                'required'    => false,
                'id'          => 'menu-action',
                'class'       => 'form-control',
            ]) ?>
        </div>
        <div class="col-md-2 d-flex align-items-end pb-3">
            <button type="button" id="btn-derive" class="btn btn-outline-primary w-100"
                title="<?= __('Re-derive Controller &amp; Action from URL') ?>">
                <i class="fas fa-sync-alt"></i> <?= __('Derive') ?>
            </button>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?= __('Appearance &amp; Settings') ?></legend>
    <div class="row">
        <div class="col-md-6">
            <?php echo $this->Form->control('icon', [
                'label'       => ['class' => 'form-label', 'text' => __('Icon class (e.g. fas fa-users)')],
                'class'       => 'form-control',
                'placeholder' => 'fas fa-circle',
            ]) ?>
        </div>
        <div class="col-md-6">
            <?php echo $this->Form->control('target', [
                'type'    => 'select',
                'options' => ['_self' => '_self', '_blank' => '_blank'],
                'class'   => 'form-control',
                'label'   => ['class' => 'form-label', 'text' => __('Link Target')],
            ]) ?>
        </div>
        <div class="col-md-12">
            <?php echo $this->Form->control('is_active', [
                'type'  => 'checkbox',
                'label' => __('Active'),
            ]) ?>
        </div>
    </div>
</fieldset>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var urlInput        = document.getElementById('menu-url');
    var controllerInput = document.getElementById('menu-controller');
    var actionInput     = document.getElementById('menu-action');
    var deriveBtn       = document.getElementById('btn-derive');

    function kebabToCamel(str, upperFirst) {
        return str.split('-').map(function (p, i) {
            if (!p) return '';
            return (i === 0 && !upperFirst) ? p.toLowerCase() : p.charAt(0).toUpperCase() + p.slice(1).toLowerCase();
        }).join('');
    }

    function deriveFromUrl() {
        var url = (urlInput ? urlInput.value : '').trim();
        if (!url || url === '#' || url.indexOf('http') === 0 || url.indexOf('javascript:') === 0) return;
        var parts = url.replace(/^\/|\/$/g, '').split('/').filter(Boolean);
        if (parts.length && (parts[0] === 'admin' || parts[0] === 'tmm')) parts.shift();
        if (!parts.length) return;
        var ctrl   = kebabToCamel(parts[0], true);
        var action = parts[1] ? kebabToCamel(parts[1], false) : 'index';
        if (controllerInput) controllerInput.value = ctrl;
        if (actionInput)     actionInput.value     = action;
    }

    if (urlInput) {
        urlInput.addEventListener('change', function () {
            if (controllerInput && controllerInput.value.trim() === '' &&
                actionInput     && actionInput.value.trim()     === '') {
                deriveFromUrl();
            }
        });
    }

    if (deriveBtn) deriveBtn.addEventListener('click', deriveFromUrl);

    var form = document.querySelector('form[method="post"]');
    if (!form) return;
    form.addEventListener('submit', function () {
        var newCat = form.querySelector('input[name="category_new"]');
        var select = form.querySelector('select[name="category"]');
        if (newCat && select && newCat.value.trim() !== '') {
            var opt = document.createElement('option');
            opt.value = newCat.value.trim();
            opt.selected = true;
            select.appendChild(opt);
        }
    });
});
</script>
