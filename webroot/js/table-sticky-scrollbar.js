/**
 * A horizontal scrollbar for wide tables that stays where you can reach it.
 *
 * The wide listings already scroll sideways, and table-enhanced.css already
 * styles the bar. The trouble is where that bar sits: on the bottom edge of
 * the table. On a listing of forty rows that is several screens below
 * whatever you are looking at, so moving a column into view meant scrolling
 * the page to the very bottom, dragging, and scrolling back up.
 *
 * This puts a second bar along the bottom of the window while any part of the
 * table is on screen, and keeps the two in step. It appears only when it is
 * needed - when the table really is wider than its container, and when its own
 * bar is not already visible - because a permanent bar on a table that fits is
 * just another line across the screen.
 *
 * It only reads scrollLeft and writes scrollLeft. Dragging, filtering and the
 * scroll indicators belong to table-drag-scroll.js and table-filter.js, which
 * bind their own selectors; nothing here touches them.
 */
(function () {
    'use strict';

    var CONTAINERS = '.table-scroll-wrapper, .table-responsive';

    /**
     * Give one container a bar of its own.
     *
     * @param {Element} container The thing that scrolls sideways.
     * @return {void}
     */
    function attach(container) {
        if (container.dataset.stickyScrollbar === 'on') {
            return;
        }
        container.dataset.stickyScrollbar = 'on';

        var bar = document.createElement('div');
        bar.className = 'table-sticky-scrollbar';
        bar.setAttribute('aria-hidden', 'true');

        var inner = document.createElement('div');
        inner.className = 'table-sticky-scrollbar-inner';
        bar.appendChild(inner);
        document.body.appendChild(bar);

        var syncing = false;

        function mirrorWidths() {
            inner.style.width = container.scrollWidth + 'px';
        }

        function place() {
            var box = container.getBoundingClientRect();
            var viewport = window.innerHeight || document.documentElement.clientHeight;

            // Needed only while the table is on screen, is wider than its
            // container, and its own bar is still below the fold.
            var wider = container.scrollWidth > container.clientWidth + 1;
            var onScreen = box.top < viewport && box.bottom > 0;
            var ownBarBelowFold = box.bottom > viewport;

            if (!wider || !onScreen || !ownBarBelowFold) {
                bar.classList.remove('is-visible');

                return;
            }

            bar.style.left = box.left + 'px';
            bar.style.width = box.width + 'px';
            bar.classList.add('is-visible');
            mirrorWidths();
        }

        // Both directions, with a flag so each does not call the other back.
        container.addEventListener('scroll', function () {
            if (syncing) {
                syncing = false;

                return;
            }
            syncing = true;
            bar.scrollLeft = container.scrollLeft;
        });

        bar.addEventListener('scroll', function () {
            if (syncing) {
                syncing = false;

                return;
            }
            syncing = true;
            container.scrollLeft = bar.scrollLeft;
        });

        var pending = false;
        function onViewChange() {
            if (pending) {
                return;
            }
            pending = true;
            window.requestAnimationFrame(function () {
                pending = false;
                place();
            });
        }

        window.addEventListener('scroll', onViewChange, { passive: true });
        window.addEventListener('resize', onViewChange);

        // A table whose rows arrive later - a filter, an AJAX tab - changes
        // width without anybody scrolling.
        if (window.ResizeObserver) {
            new ResizeObserver(onViewChange).observe(container);
        }

        place();
    }

    function attachAll() {
        var containers = document.querySelectorAll(CONTAINERS);
        for (var i = 0; i < containers.length; i++) {
            attach(containers[i]);
        }
    }

    document.addEventListener('DOMContentLoaded', attachAll);

    // Tables inside an AJAX tab are not in the document on ready.
    document.addEventListener('tmm:content-loaded', attachAll);

    window.tmmStickyScrollbars = attachAll;
})();
