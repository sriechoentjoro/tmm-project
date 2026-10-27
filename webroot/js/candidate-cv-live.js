/**
 * The CV beside the registration form, kept in step with what is typed.
 *
 * It asks the server to render it rather than redrawing it here. That is the
 * whole point: /candidates/cv-preview renders the same element that
 * /candidates/cv/{id} renders, through the same field map the save uses, so
 * the preview is the document the candidate ends up with. A CV redrawn in
 * JavaScript would be a second one, free to drift from the first - and this
 * application has paid for that kind of second copy more than once.
 *
 * The panel is already filled when the page arrives, rendered from the
 * session. Without JavaScript it simply stops updating between steps, which
 * is a slower preview rather than a broken page.
 */
(function () {
    'use strict';

    var PANEL = 'cv-live-panel';
    var WAIT = 450;

    function ready() {
        var panel = document.getElementById(PANEL);
        if (!panel) {
            return;
        }
        var form = document.querySelector('form.cv-live-source')
            || document.querySelector('.wizard-form-column form');
        if (!form) {
            return;
        }

        var url = panel.dataset.previewUrl;
        if (!url) {
            return;
        }

        var timer = null;
        var inFlight = null;

        function csrf() {
            var meta = document.querySelector('meta[name="csrfToken"]');

            return meta ? meta.content : '';
        }

        function refresh() {
            var data = new FormData(form);
            var token = csrf();
            if (token && !data.has('_csrfToken')) {
                data.append('_csrfToken', token);
            }

            // One at a time: a fast typist would otherwise race several
            // answers and the last one to arrive need not be the newest.
            if (inFlight) {
                inFlight.abort();
            }
            var request = new XMLHttpRequest();
            inFlight = request;
            request.open('POST', url, true);
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            request.onload = function () {
                inFlight = null;
                if (request.status >= 200 && request.status < 300) {
                    panel.innerHTML = request.responseText;
                    panel.classList.remove('is-stale');
                } else {
                    // Say it rather than leave an old CV looking current.
                    panel.classList.add('is-stale');
                }
            };
            request.onerror = function () {
                inFlight = null;
                panel.classList.add('is-stale');
            };
            request.send(data);
        }

        function later() {
            window.clearTimeout(timer);
            timer = window.setTimeout(refresh, WAIT);
        }

        form.addEventListener('input', later);
        form.addEventListener('change', later);

        // Rows the wizard adds or removes are not typing, so they produce no
        // input event of their own.
        if (window.MutationObserver) {
            var watcher = new MutationObserver(later);
            var containers = form.querySelectorAll(
                '#education-entries, #experience-entries, #family-entries,'
                + ' #certification-entries, #course-entries'
            );
            for (var i = 0; i < containers.length; i++) {
                watcher.observe(containers[i], { childList: true, subtree: true });
            }
        }
    }

    document.addEventListener('DOMContentLoaded', ready);
})();
