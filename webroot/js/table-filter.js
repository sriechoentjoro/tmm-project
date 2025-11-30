/**
 * Table Search/Filter Functionality
 * Provides client-side search across all table columns
 */

(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Find all tables in index pages
        const tables = document.querySelectorAll('table');
        
        tables.forEach(function(table) {
            // Only add filter to tables with tbody
            const tbody = table.querySelector('tbody');
            const thead = table.querySelector('thead');
            
            if (!tbody || !thead) return;
            
            // Check if filter row already exists
            if (thead.querySelector('.filter-row')) return;
            
            // Get all header cells
            const headerRow = thead.querySelector('tr');
            if (!headerRow) return;
            
            const headers = Array.from(headerRow.querySelectorAll('th'));
            
            // Create filter row
            const filterRow = document.createElement('tr');
            filterRow.className = 'filter-row';
            
            headers.forEach(function(header, index) {
                const th = document.createElement('th');
                
                // Don't add input to Actions column
                if (header.classList.contains('actions') || header.textContent.trim() === 'Actions') {
                    const clearBtn = document.createElement('button');
                    clearBtn.type = 'button';
                    clearBtn.className = 'btn-clear-filter';
                    clearBtn.innerHTML = '<i class="fas fa-times"></i>';
                    clearBtn.title = 'Clear All Filters';
                    clearBtn.onclick = function() {
                        clearAllFilters(table);
                    };
                    th.appendChild(clearBtn);
                } else {
                    // Add search input
                    const input = document.createElement('input');
                    input.type = 'text';
                    // Find longest text in this column for better placeholder
                    var longestText = '';
                    var maxLength = 0;
                    var rows = tbody.querySelectorAll('tr');
                    rows.forEach(function(row) {
                        var cells = row.querySelectorAll('td');
                        if (cells[index]) {
                            var text = cells[index].textContent.trim();
                            if (text.length > maxLength && text.length <= 30) {
                                maxLength = text.length;
                                longestText = text;
                            }
                        }
                    });
                    input.placeholder = longestText || 'Filter...';
                    
                    // Set input width based on longest text
                    if (longestText) {
                        // Create temporary span to measure text width
                        var tempSpan = document.createElement('span');
                        tempSpan.style.visibility = 'hidden';
                        tempSpan.style.position = 'absolute';
                        tempSpan.style.whiteSpace = 'nowrap';
                        tempSpan.style.font = window.getComputedStyle(input).font;
                        tempSpan.textContent = longestText;
                        document.body.appendChild(tempSpan);
                        var textWidth = tempSpan.offsetWidth;
                        document.body.removeChild(tempSpan);
                        
                        // Set input width (add padding for comfort)
                        input.style.width = (textWidth + 30) + 'px';
                        input.style.minWidth = '80px';
                        input.style.maxWidth = '300px';
                    }
                    input.className = 'table-filter-input';
                    input.dataset.columnIndex = index;
                    
                    // Real-time filtering
                    input.addEventListener('keyup', function() {
                        filterTable(table);
                    });
                    
                    th.appendChild(input);
                }
                
                filterRow.appendChild(th);
            });
            
            // Insert filter row after header row
            thead.insertBefore(filterRow, headerRow.nextSibling);
        });
    });
    
    function filterTable(table) {
        const filterInputs = table.querySelectorAll('.table-filter-input');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Get all filter values
        const filters = Array.from(filterInputs).map(function(input) {
            return {
                index: parseInt(input.dataset.columnIndex),
                value: input.value.toLowerCase().trim()
            };
        });
        
        // Filter rows
        rows.forEach(function(row) {
            let show = true;
            const cells = Array.from(row.querySelectorAll('td'));
            
            filters.forEach(function(filter) {
                if (filter.value && cells[filter.index]) {
                    const cellText = cells[filter.index].textContent.toLowerCase().trim();
                    if (cellText.indexOf(filter.value) === -1) {
                        show = false;
                    }
                }
            });
            
            row.style.display = show ? '' : 'none';
        });
        
        // Update row count message
        updateRowCount(table);
    }
    
    function clearAllFilters(table) {
        const filterInputs = table.querySelectorAll('.table-filter-input');
        filterInputs.forEach(function(input) {
            input.value = '';
        });
        filterTable(table);
    }
    
    function updateRowCount(table) {
        const tbody = table.querySelector('tbody');
        const allRows = tbody.querySelectorAll('tr');
        const visibleRows = Array.from(allRows).filter(function(row) {
            return row.style.display !== 'none';
        });
        
        // Create or update count message
        let countMsg = table.parentElement.querySelector('.filter-count-message');
        if (!countMsg) {
            countMsg = document.createElement('div');
            countMsg.className = 'filter-count-message';
            table.parentElement.insertBefore(countMsg, table);
        }
        
        if (visibleRows.length < allRows.length) {
            countMsg.textContent = 'Showing ' + visibleRows.length + ' of ' + allRows.length + ' records';
            countMsg.style.display = 'block';
        } else {
            countMsg.style.display = 'none';
        }
    }
})();
