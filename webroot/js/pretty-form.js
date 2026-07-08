/**
 * Pretty Form - progressive enhancement for scaffolded add/edit forms.
 *
 * Runs on any page with div.form.content that has form > fieldset inside.
 * Skips pages that already have cand-form class (applied on first run).
 *
 * What it does:
 *  - Wraps each <fieldset> in a cand-section card with icon header
 *  - Two-column grid for simple fields
 *  - Switch tiles for checkboxes
 *  - Bootstrap-ifies plain CakePHP inputs (adds form-control class)
 *  - Upgrades form-actions bar to cand-form-actions style
 */
$(function () {
    var $content = $('div.form.content').first();
    if (!$content.length || $content.hasClass('cand-form')) return;

    var $fieldsets = $content.find('form fieldset');
    if (!$fieldsets.length) return;

    $content.addClass('cand-form');

    // Bootstrap-ify plain CakePHP inputs (those without form-control)
    $content.find('form input:not([type=hidden]):not([type=submit]):not([type=checkbox]):not([type=radio]), form select, form textarea').each(function () {
        if (!$(this).hasClass('form-control')) $(this).addClass('form-control');
    });
    $content.find('form label:not(.form-check-label):not(.form-label)').addClass('form-label');

    // Wrap each fieldset in a cand-section card
    $fieldsets.each(function () {
        var $fs = $(this);
        if ($fs.closest('.cand-section').length) return;

        var $legend = $fs.children('legend').first();
        var legendText = $legend.length ? $legend.text().trim() : '';

        var $section = $('<div class="cand-section"></div>');
        $fs.before($section);

        if (legendText) {
            $section.append(
                '<div class="cand-section-header">' +
                '<div class="icon-chip"><i class="fas fa-sliders-h"></i></div>' +
                '<div><h5>' + $('<div>').text(legendText).html() + '</h5></div>' +
                '</div>'
            );
            $legend.hide();
        }

        $('<div class="cand-section-body"></div>').appendTo($section).append($fs);
    });

    // Two-column grid: .row > .col-12 inside fieldsets (bake template pattern)
    $content.find('form fieldset').each(function () {
        $(this).children('.row').children('.col-12').each(function () {
            var $col = $(this);
            if ($col.find('textarea, input[type=file], .card, .form-check, table').length) return;
            $col.removeClass('col-12').addClass('col-md-6');
        });
    });

    // Two-column grid: plain CakePHP .input wrappers inside fieldsets
    $content.find('form fieldset .input').each(function () {
        var $div = $(this);
        if ($div.find('textarea, input[type=file], select[multiple], .form-check').length) return;
        if ($div.hasClass('col-md-6') || $div.hasClass('col-md-4')) return;
        $div.addClass('col-md-6');
    });
    // Wrap consecutive .input cols in a .row if needed
    $content.find('form fieldset').each(function () {
        var $fs = $(this);
        var pending = [];
        $fs.children('.input.col-md-6').each(function () {
            pending.push(this);
        });
        if (pending.length > 1) {
            var $row = $('<div class="row"></div>');
            $(pending[0]).before($row);
            $(pending).each(function () { $row.append(this); });
        }
    });

    // Checkboxes → switch tiles
    $content.find('form fieldset .form-check').each(function () {
        var $check = $(this);
        var $input = $check.find('input[type=checkbox]');
        if (!$input.length) return;
        $check.addClass('form-switch');
        $input.addClass('form-check-input').attr('role', 'switch');
        if (!$check.parent().hasClass('cand-switch-tile')) {
            $check.wrap('<div class="cand-switch-tile"></div>');
        }
        // Only resize col-12 → col-md-6; leave already-sized cols (col-md-6 etc.) alone
        var $col = $check.closest('[class*="col-"]');
        if ($col.length && $col.hasClass('col-12')) {
            $col.removeClass('col-12').addClass('col-md-6');
        }
    });

    // Also handle CakePHP default checkbox wrappers (div.input.boolean)
    $content.find('form fieldset .input.boolean, form fieldset .input.checkbox').each(function () {
        var $div = $(this);
        var $input = $div.find('input[type=checkbox]');
        if (!$input.length || $div.find('.cand-switch-tile').length) return;
        var $label = $div.find('label');
        var $switchWrap = $('<div class="form-check form-switch cand-switch-tile"></div>');
        $input.addClass('form-check-input').attr('role', 'switch');
        $label.addClass('form-check-label');
        $switchWrap.append($input).append($label);
        $div.empty().append($switchWrap);
        // Only resize col-12 → col-md-6; leave already-sized cols alone
        if ($div.hasClass('col-12')) {
            $div.removeClass('col-12').addClass('col-md-6');
        }
    });

    // Upgrade form-actions bar
    $content.find('form .form-actions').each(function () {
        var $fa = $(this);
        if ($fa.hasClass('cand-form-actions')) return;
        $fa.addClass('cand-form-actions');
        // Style the submit button
        $fa.find('button[type="submit"]').not('.btn-cand-save').each(function () {
            $(this).addClass('btn-cand-save').removeClass('btn-export-light');
        });
        // Style cancel/back links
        $fa.find('a').not('.btn-cand-cancel, .btn-cand-save').each(function () {
            $(this).addClass('btn-cand-cancel').removeClass('btn-export-light btn btn-secondary btn-sm');
        });
    });

    // Wrap bare submit button that isn't inside form-actions
    $content.find('form').each(function () {
        var $form = $(this);
        var $bare = $form.children('button[type="submit"]');
        if ($bare.length && !$form.find('.form-actions, .cand-form-actions').length) {
            var $wrap = $('<div class="cand-form-actions"></div>');
            $bare.before($wrap);
            $wrap.append($bare.addClass('btn-cand-save'));
        }
    });
});
