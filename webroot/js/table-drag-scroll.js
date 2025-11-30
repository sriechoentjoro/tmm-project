/**
 * Table Drag to Scroll
 * Enables left-click drag to scroll tables horizontally
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Find all scrollable table containers
        const scrollableContainers = document.querySelectorAll('.table-responsive, .index.content');

        scrollableContainers.forEach(function(container) {
            let isDown = false;
            let startX;
            let scrollLeft;

            function shouldPreventDrag(target) {
                return target.tagName === 'A' ||
                    target.tagName === 'BUTTON' ||
                    target.tagName === 'INPUT' ||
                    target.closest('a') ||
                    target.closest('button');
            }

            // Mouse down - start dragging
            container.addEventListener('mousedown', function(e) {
                if (shouldPreventDrag(e.target)) return;

                isDown = true;
                container.classList.add('dragging');
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
                container.style.cursor = 'grabbing';
                container.style.userSelect = 'none';
            });

            // Touch start - start dragging (mobile)
            container.addEventListener('touchstart', function(e) {
                if (shouldPreventDrag(e.target)) return;

                isDown = true;
                container.classList.add('dragging');
                startX = e.touches[0].pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
                container.style.userSelect = 'none';
            }, { passive: true });

            // Mouse leave - stop dragging
            container.addEventListener('mouseleave', function() {
                isDown = false;
                container.classList.remove('dragging');
                container.style.cursor = 'grab';
            });

            // Mouse up - stop dragging
            container.addEventListener('mouseup', function() {
                isDown = false;
                container.classList.remove('dragging');
                container.style.cursor = 'grab';
                container.style.userSelect = 'auto';
            });

            // Touch end - stop dragging (mobile)
            container.addEventListener('touchend', function() {
                isDown = false;
                container.classList.remove('dragging');
                container.style.userSelect = 'auto';
            });

            // Mouse move - do the scrolling
            container.addEventListener('mousemove', function(e) {
                if (!isDown) return;
                e.preventDefault();

                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2; // Scroll speed multiplier
                container.scrollLeft = scrollLeft - walk;
            });

            // Touch move - do the scrolling (mobile)
            container.addEventListener('touchmove', function(e) {
                if (!isDown) return;

                const x = e.touches[0].pageX - container.offsetLeft;
                const walk = (x - startX) * 2; // Scroll speed multiplier
                container.scrollLeft = scrollLeft - walk;
            }, { passive: true });

            // Set initial cursor
            container.style.cursor = 'grab';
        });        // Also enable for the main menu if it's scrollable
        const menu = document.querySelector('.elegant-menu');
        if (menu) {
            let isDown = false;
            let startX;
            let scrollLeft;

            function shouldPreventDrag(target) {
                return target.tagName === 'A' || target.closest('a');
            }

            menu.addEventListener('mousedown', function(e) {
                if (shouldPreventDrag(e.target)) return;

                isDown = true;
                menu.classList.add('dragging');
                startX = e.pageX - menu.offsetLeft;
                scrollLeft = menu.scrollLeft;
                menu.style.cursor = 'grabbing';
                menu.style.userSelect = 'none';
            });

            menu.addEventListener('touchstart', function(e) {
                if (shouldPreventDrag(e.target)) return;

                isDown = true;
                menu.classList.add('dragging');
                startX = e.touches[0].pageX - menu.offsetLeft;
                scrollLeft = menu.scrollLeft;
                menu.style.userSelect = 'none';
            }, { passive: true });

            menu.addEventListener('mouseleave', function() {
                isDown = false;
                menu.classList.remove('dragging');
                menu.style.cursor = 'grab';
            });

            menu.addEventListener('mouseup', function() {
                isDown = false;
                menu.classList.remove('dragging');
                menu.style.cursor = 'grab';
                menu.style.userSelect = 'auto';
            });

            menu.addEventListener('touchend', function() {
                isDown = false;
                menu.classList.remove('dragging');
                menu.style.userSelect = 'auto';
            });

            menu.addEventListener('mousemove', function(e) {
                if (!isDown) return;
                e.preventDefault();

                const x = e.pageX - menu.offsetLeft;
                const walk = (x - startX) * 2;
                menu.scrollLeft = scrollLeft - walk;
            });

            menu.addEventListener('touchmove', function(e) {
                if (!isDown) return;

                const x = e.touches[0].pageX - menu.offsetLeft;
                const walk = (x - startX) * 2;
                menu.scrollLeft = scrollLeft - walk;
            }, { passive: true });

            menu.style.cursor = 'grab';
        }
    });
})();
