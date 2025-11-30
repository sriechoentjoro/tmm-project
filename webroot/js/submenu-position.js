/**
 * Smart Submenu Positioning
 * Detects screen edges and positions submenus to prevent cut-off
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Find all menu items with submenus
        const menuItems = document.querySelectorAll('.elegant-menu-item, [data-submenu]');

        menuItems.forEach(function(item) {
            item.addEventListener('mouseenter', function() {
                positionSubmenu(this);
            });
        });

        function positionSubmenu(menuItem) {
            const submenu = menuItem.querySelector('.elegant-submenu');
            if (!submenu) return;

            // Reset positioning
            submenu.style.left = '';
            submenu.style.right = '';
            submenu.classList.remove('position-left', 'position-right');

            // Get viewport width
            const viewportWidth = window.innerWidth;

            // Get menu item position
            const rect = menuItem.getBoundingClientRect();
            const submenuWidth = submenu.offsetWidth || 250; // Default width if not visible

            // Calculate space on right and left
            const spaceOnRight = viewportWidth - rect.right;
            const spaceOnLeft = rect.left;

            // Desktop positioning logic
            if (window.innerWidth > 768) {
                // Try right first
                if (spaceOnRight >= submenuWidth) {
                    // Enough space on right
                    submenu.style.left = '100%';
                    submenu.style.right = 'auto';
                    submenu.classList.add('position-right');
                } else if (spaceOnLeft >= submenuWidth) {
                    // Not enough space on right, try left
                    submenu.style.left = 'auto';
                    submenu.style.right = '100%';
                    submenu.classList.add('position-left');
                } else {
                    // Not enough space on either side, use right and allow scroll
                    submenu.style.left = '100%';
                    submenu.style.right = 'auto';
                    submenu.classList.add('position-right');
                }
            } else {
                // Mobile: Always below
                submenu.style.position = 'relative';
                submenu.style.left = '0';
                submenu.style.right = 'auto';
            }
        }

        // Reposition on window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Re-position any visible submenus
                const visibleSubmenus = document.querySelectorAll('.elegant-submenu:hover, .elegant-menu-item:hover .elegant-submenu');
                visibleSubmenus.forEach(function(submenu) {
                    const menuItem = submenu.closest('.elegant-menu-item, [data-submenu]');
                    if (menuItem) {
                        positionSubmenu(menuItem);
                    }
                });
            }, 100);
        });
    });
})();
