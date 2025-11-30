/**
 * AJAX Table Filter - Server-side filtering via Controller
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find all tables that need AJAX filters
    const tables = document.querySelectorAll('table.table, table.github-table, table[class*="inventories"], table[class*="data"]');
    
    tables.forEach(table => {
        const thead = table.querySelector('thead');
        if (!thead) return;
        
        const headerRow = thead.querySelector('tr');
        if (!headerRow) return;
        
        // Check if filter row already exists
        if (thead.querySelector('.ajax-filter-row')) return;
        
        // Create filter row
        const filterRow = document.createElement('tr');
        filterRow.className = 'ajax-filter-row';
        filterRow.style.backgroundColor = '#f8f9fa';
        filterRow.style.borderBottom = '2px solid #dee2e6';
        
        // Get column names from headers
        const headers = headerRow.querySelectorAll('th');
        const columnNames = [];
        
        headers.forEach((th, index) => {
            const filterCell = document.createElement('th');
            filterCell.style.padding = '8px 12px';
            
            // Get column name from header link or text
            const sortLink = th.querySelector('a');
            let columnName = '';
            
            if (sortLink) {
                // Extract column name from sort URL
                const href = sortLink.getAttribute('href');
                const match = href ? href.match(/sort=([^&]+)/) : null;
                columnName = match ? match[1] : '';
            }
            
            columnNames.push(columnName);
            
            // Don't add filter to Actions column
            if (th.textContent.toLowerCase().includes('action')) {
                filterRow.appendChild(filterCell);
                return;
            }
            
            // Create filter input with icon
            const inputGroup = document.createElement('div');
            inputGroup.style.position = 'relative';
            
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'ajax-column-filter form-control form-control-sm';
            input.placeholder = 'Search...';
            input.style.width = '100%';
            input.style.padding = '4px 28px 4px 8px';
            input.style.fontSize = '13px';
            input.style.border = '1px solid #ced4da';
            input.style.borderRadius = '4px';
            input.dataset.columnName = columnName;
            input.dataset.columnIndex = index;
            
            // Add search icon
            const icon = document.createElement('i');
            icon.className = 'fa fa-search';
            icon.style.position = 'absolute';
            icon.style.right = '10px';
            icon.style.top = '50%';
            icon.style.transform = 'translateY(-50%)';
            icon.style.color = '#6c757d';
            icon.style.pointerEvents = 'none';
            
            // Add focus effect
            input.addEventListener('focus', function() {
                this.style.borderColor = '#007bff';
                this.style.boxShadow = '0 0 0 0.2rem rgba(0,123,255,0.25)';
                icon.style.color = '#007bff';
            });
            
            input.addEventListener('blur', function() {
                this.style.borderColor = '#ced4da';
                this.style.boxShadow = 'none';
                icon.style.color = '#6c757d';
            });
            
            // Add debounced AJAX filter
            let timeout;
            input.addEventListener('keyup', function() {
                clearTimeout(timeout);
                icon.className = 'fa fa-spinner fa-spin';
                
                timeout = setTimeout(() => {
                    performAjaxFilter(table);
                }, 500); // Wait 500ms after user stops typing
            });
            
            inputGroup.appendChild(input);
            inputGroup.appendChild(icon);
            filterCell.appendChild(inputGroup);
            filterRow.appendChild(filterCell);
        });
        
        // Insert filter row after header row
        headerRow.after(filterRow);
        
        // Store original URL for filtering
        const currentUrl = window.location.pathname;
        table.dataset.filterUrl = currentUrl;
    });
    
    // Perform AJAX filter
    function performAjaxFilter(table) {
        const filterInputs = table.querySelectorAll('.ajax-column-filter');
        const filters = {};
        let hasFilter = false;
        
        // Collect all filter values
        filterInputs.forEach(input => {
            const columnName = input.dataset.columnName;
            const value = input.value.trim();
            
            if (value && columnName) {
                filters[columnName] = value;
                hasFilter = true;
            }
        });
        
        // If no filters, don't make AJAX call
        if (!hasFilter) {
            // Reset icon
            filterInputs.forEach(input => {
                const icon = input.nextElementSibling;
                if (icon) icon.className = 'fa fa-search';
            });
            return;
        }
        
        // Get current URL and add filter params
        const baseUrl = table.dataset.filterUrl || window.location.pathname;
        const params = new URLSearchParams();
        
        // Add filter parameters
        Object.keys(filters).forEach(key => {
            params.append(`filter[${key}]`, filters[key]);
        });
        
        // Add AJAX flag
        params.append('ajax', '1');
        
        const url = `${baseUrl}?${params.toString()}`;
        
        // Show loading state
        const tbody = table.querySelector('tbody');
        const originalContent = tbody.innerHTML;
        
        // Make AJAX request
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Reset icons
            filterInputs.forEach(input => {
                const icon = input.nextElementSibling;
                if (icon) icon.className = 'fa fa-search';
            });
            
            if (data.success && data.html) {
                tbody.innerHTML = data.html;
                
                // Update pagination info if exists
                if (data.count !== undefined) {
                    updateFilterCount(table, data.count, data.total);
                }
            } else if (data.rows) {
                // Alternative: data contains row array
                renderTableRows(tbody, data.rows, table);
                updateFilterCount(table, data.rows.length, data.total || data.rows.length);
            }
        })
        .catch(error => {
            console.error('Filter error:', error);
            
            // Reset icons on error
            filterInputs.forEach(input => {
                const icon = input.nextElementSibling;
                if (icon) {
                    icon.className = 'fa fa-exclamation-triangle';
                    icon.style.color = '#dc3545';
                }
            });
            
            // Show error message
            tbody.innerHTML = `
                <tr>
                    <td colspan="100" style="text-align: center; padding: 20px; color: #dc3545;">
                        <i class="fa fa-exclamation-triangle"></i> 
                        Error loading filtered data. Please try again.
                    </td>
                </tr>
            `;
        });
    }
    
    // Update filter count display
    function updateFilterCount(table, visible, total) {
        const container = table.closest('.github-container, .view-content, .index, .inventories');
        if (!container) return;
        
        const counter = container.querySelector('.github-table-count, .paginator .counter, p');
        if (counter) {
            if (!counter.dataset.originalText) {
                counter.dataset.originalText = counter.textContent;
            }
            
            if (visible !== total) {
                counter.innerHTML = `<span style="color: #dc3545; font-weight: bold;">
                    <i class="fa fa-filter"></i> Showing ${visible} of ${total} records (filtered)
                </span>`;
            } else {
                counter.textContent = counter.dataset.originalText;
            }
        }
    }
    
    // Add Clear Filters button
    tables.forEach(table => {
        const filterInputs = table.querySelectorAll('.ajax-column-filter');
        if (filterInputs.length === 0) return;
        
        const container = table.closest('.github-container, .view-content, .index, .inventories');
        if (!container) return;
        
        // Check if button already exists
        if (container.querySelector('.clear-ajax-filters-btn')) return;
        
        const clearBtn = document.createElement('button');
        clearBtn.className = 'clear-ajax-filters-btn btn btn-sm btn-warning';
        clearBtn.innerHTML = '<i class="fa fa-times"></i> Clear Filters';
        clearBtn.style.marginLeft = '10px';
        clearBtn.style.marginBottom = '10px';
        clearBtn.style.display = 'none'; // Hide initially
        
        clearBtn.addEventListener('click', function() {
            filterInputs.forEach(input => {
                input.value = '';
                const icon = input.nextElementSibling;
                if (icon) icon.className = 'fa fa-search';
            });
            
            // Reload page without filters
            window.location.href = table.dataset.filterUrl || window.location.pathname;
        });
        
        // Show clear button when any filter has value
        filterInputs.forEach(input => {
            input.addEventListener('keyup', function() {
                const hasAnyFilter = Array.from(filterInputs).some(inp => inp.value.trim() !== '');
                clearBtn.style.display = hasAnyFilter ? 'inline-block' : 'none';
            });
        });
        
        // Try to add to appropriate location
        const actions = container.querySelector('.actions, .table-actions, h2, h3');
        if (actions) {
            actions.parentNode.insertBefore(clearBtn, actions.nextSibling);
        } else {
            table.parentNode.insertBefore(clearBtn, table);
        }
    });
});
