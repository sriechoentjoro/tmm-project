/**
 * Global date picker initialisation.
 *
 * bootstrap-datepicker is loaded on every page by Layout/elegant.ctp, but each
 * template used to run its own $('.datepicker').datepicker(...) block. Any
 * template that forgot it left its date fields as CakePHP's default three
 * <select> dropdowns (year / month / day). Initialising once here means a date
 * field only needs class="datepicker" to work.
 *
 * Scoped to input.datepicker deliberately: bootstrap-datepicker gives the
 * dropdown it creates the class "datepicker" as well, so a bare $('.datepicker')
 * selector can match the widget it just built.
 */
(function ($) {
    'use strict';

    if (!$ || !$.fn || !$.fn.datepicker) {
        return;
    }

    var OPTIONS = {
        format: 'yyyy-mm-dd', // MySQL date format
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        container: 'body',
        showOnFocus: true,
        zIndexOffset: 1050
    };

    function init(root) {
        $('input.datepicker', root || document).each(function () {
            // Templates may still carry their own init; don't bind twice.
            if ($(this).data('datepicker')) {
                return;
            }
            $(this).datepicker(OPTIONS);
        });
    }

    $(function () {
        init(document);
    });

    // Date fields inside AJAX tabs or dynamically added rows are not in the DOM
    // on ready; re-run init for them.
    $(document).on('tmm:content-loaded', function (event) {
        init(event.target);
    });

    window.tmmInitDatepickers = init;
})(window.jQuery);
