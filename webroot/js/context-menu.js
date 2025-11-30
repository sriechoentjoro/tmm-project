/**
 * Right-Click Context Menu for Table Rows
 * Allows users to View/Edit/Delete records by right-clicking on table rows
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Create context menu element
        const contextMenu = document.createElement('div');
        contextMenu.className = 'context-menu';
        contextMenu.id = 'table-context-menu';
        document.body.appendChild(contextMenu);

        let currentRow = null;
        let currentController = getControllerName();

        // Add right-click listener to all table rows
        document.addEventListener('click', function(e) {
            // Check if clicked on a table row
            const row = e.target.closest('tbody tr');
            
            if (row && row.querySelector('td.actions')) {
                // Left click - no need to preventDefault
                
                // Remove previous active state
                if (currentRow) {
                    currentRow.classList.remove('context-active');
                }
                
                // Set new active row
                currentRow = row;
                currentRow.classList.add('context-active');
                
                // Get action buttons from the row
                const actions = row.querySelector('td.actions');
                const viewBtn = actions.querySelector('.btn-view');
                const editBtn = actions.querySelector('.btn-edit');
                const deleteBtn = actions.querySelector('.btn-delete');
                
                // Build context menu
                let menuHTML = '';
                
                if (viewBtn) {
                    menuHTML += `
                        <a href="${viewBtn.href}" class="context-menu-item" data-action="view">
                            <i class="fas fa-eye"></i>
                            <span>View Details</span>
                        </a>
                    `;
                }
                
                if (editBtn) {
                    menuHTML += `
                        <a href="${editBtn.href}" class="context-menu-item" data-action="edit">
                            <i class="fas fa-edit"></i>
                            <span>Edit</span>
                        </a>
                    `;
                }
                
                if (deleteBtn) {
                    if (menuHTML) menuHTML += '<div class="context-menu-divider"></div>';
                    
                    // For delete, we need to trigger the form submission
                    const deleteForm = deleteBtn.closest('form');
                    if (deleteForm) {
                        menuHTML += `
                            <div class="context-menu-item" data-action="delete" data-form-id="${deleteForm.id || 'delete-form'}">
                                <i class="fas fa-trash"></i>
                                <span>Delete</span>
                            </div>
                        `;
                    }
                }
                
                contextMenu.innerHTML = menuHTML;
                
                // Position context menu
                const menuWidth = 180;
                const menuHeight = contextMenu.offsetHeight || 150;
                
                let posX = e.pageX;
                let posY = e.pageY;
                
                // Keep menu within viewport
                if (posX + menuWidth > window.innerWidth + window.scrollX) {
                    posX = e.pageX - menuWidth;
                }
                
                if (posY + menuHeight > window.innerHeight + window.scrollY) {
                    posY = e.pageY - menuHeight;
                }
                
                contextMenu.style.left = posX + 'px';
                contextMenu.style.top = posY + 'px';
                contextMenu.classList.add('active');
                
                // Add click handlers for menu items
                contextMenu.querySelectorAll('.context-menu-item').forEach(function(item) {
                    if (item.dataset.action === 'delete') {
                        item.addEventListener('click', function(e) {
                            // Left click - no need to preventDefault
                            const formId = this.dataset.formId;
                            const form = document.getElementById(formId) || deleteBtn.closest('form');
                            
                            if (form && confirm('Are you sure you want to delete this record?')) {
                                form.submit();
                            }
                            contextMenu.classList.remove('active');
                        });
                    }
                });
            }
        });

        // Close context menu on click outside
        document.addEventListener('click', function(e) {
            if (!contextMenu.contains(e.target)) {
                contextMenu.classList.remove('active');
                if (currentRow) {
                    currentRow.classList.remove('context-active');
                    currentRow = null;
                }
            }
        });

        // Close context menu on scroll
        document.addEventListener('scroll', function() {
            contextMenu.classList.remove('active');
            if (currentRow) {
                currentRow.classList.remove('context-active');
                currentRow = null;
            }
        });

        // Get controller name from URL
        function getControllerName() {
            const path = window.location.pathname;
            const parts = path.split('/');
            // Assuming URL pattern: /asahi_v3/controller-name/action
            return parts[2] || '';
        }
    });
})();
