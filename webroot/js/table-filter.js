/**
 * Table Search/Filter Functionality
 * Provides client-side search across all table columns
 *
 * Two kinds of table arrive here.
 *
 * Most have no filter row, and this script builds one: a text box per column
 * that hides the rows that do not match.
 *
 * Seventy-six index templates write their own filter row instead, a far richer
 * one - a select of the real values for a foreign key, an operator beside each
 * text and number column (=, !=, <, >, between, LIKE, starts with, ends with)
 * and a second box for the far end of a range. This script used to see that row
 * and return, leaving it to whatever had put it there. Nothing had: the only
 * script that binds .filter-input is webroot/js/table-enhanced.js, and no page
 * loads it - it is named in one comment in SearchableTrait and nowhere else.
 * Neither does anything read the filter_* query parameters it would have sent:
 * FilterHandlerComponent and SearchableTrait were both written for that and
 * neither is used by any controller.
 *
 * So on those seventy-six screens every box in the filter row did nothing at
 * all. You typed, you chose, and the table sat there. Now the row is adopted
 * rather than abandoned, operators and all.
 *
 * It filters the rows on the page, which is what this script has always done.
 * Where the list is paginated that is worth saying out loud, so the count
 * message says it.
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
            
            // A filter row the template wrote itself. Bind it instead of
            // walking away from it.
            const existingRow = thead.querySelector('.filter-row');
            if (existingRow) {
                adoptFilterRow(table, existingRow);

                return;
            }

            // Two ways a template says "do not put a filter row on this one":
            // the class on the table, and the flag the baked index sets. Both
            // were written and neither was ever honoured - the layout carried
            // a block that read the flag, logged a line and did nothing else,
            // so the row appeared regardless. They only ever meant "do not
            // build one", never "do not bind the one I wrote", which is why
            // the check sits below the adoption above.
            if (window.skipAutoFilter === true || table.classList.contains('no-auto-filter')) {
                return;
            }
            
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
                    
                    // Real-time filtering. 'input' rather than 'keyup':
                    // pasting a value, or clearing the box with the mouse,
                    // presses no key, and the table used to sit there until
                    // one was.
                    input.addEventListener('input', function() {
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
    
    /**
     * Adopt a filter row the template wrote, so its boxes actually filter.
     *
     * These rows name their columns - data-column on every box - so they can
     * ask the server, which is the only way a filter can reach past the page
     * you are looking at. Each box becomes filter_<column> in the query
     * string, with filter_<column>_operator beside it and filter_<column>_to
     * for the far end of a range, and AppController::paginate() applies them.
     *
     * Not every index screen paginates. A few build their rows by hand, in
     * raw SQL, and hand the view a finished array; sending filters to one of
     * those would put words in the address bar and change nothing on the
     * page - the same silence this whole change is about. So the controller
     * says whether it paginated, and where it did not the row is narrowed
     * here in the browser instead, over the rows on the page, which is all
     * that can honestly be offered.
     *
     * The rows this script builds itself have no column names to send, so
     * they are always narrowed here.
     */
    function adoptFilterRow(table, filterRow) {
        if (window.serverSideFilter !== true) {
            adoptFilterRowLocally(table, filterRow);

            return;
        }

        const params = new URLSearchParams(window.location.search);
        let pending = null;

        const submit = function() {
            window.clearTimeout(pending);
            pending = window.setTimeout(function() {
                submitFilters(table, filterRow);
            }, 400);
        };
        const submitNow = function() {
            window.clearTimeout(pending);
            submitFilters(table, filterRow);
        };

        Array.from(filterRow.children).forEach(function(cell, index) {
            const input = cell.querySelector('.filter-input');
            if (!input) {
                return;
            }

            const column = input.dataset.column;
            input.classList.add('table-filter-input');
            input.dataset.columnIndex = index;

            const range = cell.querySelector('.filter-input-range');
            const operator = cell.querySelector('.filter-operator');

            // Put back what was asked for, so the boxes still show it after
            // the page has come back narrowed.
            if (column) {
                if (params.has('filter_' + column)) {
                    input.value = params.get('filter_' + column);
                }
                if (operator && params.has('filter_' + column + '_operator')) {
                    operator.value = params.get('filter_' + column + '_operator');
                }
                if (range && params.has('filter_' + column + '_to')) {
                    range.value = params.get('filter_' + column + '_to');
                }
            }

            if (range && operator) {
                // The far end of a range is written into the page hidden, and
                // nothing ever showed it.
                range.style.display = operator.value === 'between' ? '' : 'none';
            }

            input.addEventListener(input.tagName === 'SELECT' ? 'change' : 'input',
                input.tagName === 'SELECT' ? submitNow : submit);

            if (range) {
                range.addEventListener('input', submit);
            }

            if (operator) {
                operator.addEventListener('change', function() {
                    if (range) {
                        range.style.display = operator.value === 'between' ? '' : 'none';
                        if (operator.value !== 'between') {
                            range.value = '';
                        }
                    }
                    // An operator with an empty box changes nothing, so do not
                    // reload the page to prove it.
                    if (input.value.trim() !== '') {
                        submitNow();
                    }
                });
            }
        });

        const clear = filterRow.querySelector('.btn-clear-filter, .clear-filter');
        if (clear) {
            clear.addEventListener('click', function() {
                clearServerFilters();
            });
        }
    }

    /** Ask the server for the rows that match, keeping sort and everything else. */
    function submitFilters(table, filterRow) {
        const url = new URL(window.location.href);
        const params = new URLSearchParams();

        url.searchParams.forEach(function(value, key) {
            if (key.indexOf('filter_') !== 0 && key !== 'page') {
                params.set(key, value);
            }
        });

        filterRow.querySelectorAll('.filter-input').forEach(function(input) {
            const column = input.dataset.column;
            const value = input.value.trim();
            if (!column || value === '') {
                return;
            }

            params.set('filter_' + column, value);

            const cell = input.closest('td, th');
            const operator = cell ? cell.querySelector('.filter-operator') : null;
            if (operator && operator.value !== '') {
                params.set('filter_' + column + '_operator', operator.value);
            }

            const range = cell ? cell.querySelector('.filter-input-range') : null;
            if (range && operator && operator.value === 'between' && range.value.trim() !== '') {
                params.set('filter_' + column + '_to', range.value.trim());
            }
        });

        // A narrowed list starts at its own first page, not at the page you
        // happened to be on.
        url.search = params.toString();
        window.location.href = url.toString();
    }

    /**
     * Narrow a template's filter row here, for a screen that does not
     * paginate and so has nothing to ask the server for.
     */
    function adoptFilterRowLocally(table, filterRow) {
        Array.from(filterRow.children).forEach(function(cell, index) {
            const input = cell.querySelector('.filter-input');
            if (!input) {
                return;
            }

            input.classList.add('table-filter-input');
            input.dataset.columnIndex = index;

            const refilter = function() {
                filterTable(table);
            };
            input.addEventListener(input.tagName === 'SELECT' ? 'change' : 'input', refilter);

            const range = cell.querySelector('.filter-input-range');
            if (range) {
                range.addEventListener('input', refilter);
            }

            const operator = cell.querySelector('.filter-operator');
            if (operator) {
                operator.addEventListener('change', function() {
                    if (range) {
                        range.style.display = operator.value === 'between' ? '' : 'none';
                        if (operator.value !== 'between') {
                            range.value = '';
                        }
                    }
                    refilter();
                });
            }
        });

        const clear = filterRow.querySelector('.btn-clear-filter, .clear-filter');
        if (clear) {
            clear.addEventListener('click', function() {
                clearAllFilters(table);
            });
        }
    }

    /** Drop every filter and come back to the whole list. */
    function clearServerFilters() {
        const url = new URL(window.location.href);
        const params = new URLSearchParams();
        url.searchParams.forEach(function(value, key) {
            if (key.indexOf('filter_') !== 0 && key !== 'page') {
                params.set(key, value);
            }
        });
        url.search = params.toString();
        window.location.href = url.toString();
    }

    /** Both sides read as numbers, so compare them as numbers. */
    function asNumbers(cellText, needle) {
        const a = parseFloat(String(cellText).replace(/[^0-9.\-]/g, ''));
        const b = parseFloat(String(needle).replace(/[^0-9.\-]/g, ''));

        return (isNaN(a) || isNaN(b)) ? null : [a, b];
    }

    /**
     * Does this cell match what was asked for?
     *
     * @param {string} cellText  what the column shows
     * @param {string} needle    what was typed or chosen
     * @param {string} operator  how to compare, '' meaning contains
     * @param {string} rangeEnd  the far end, for 'between'
     * @param {boolean} exact    a select, whose options are whole values
     */
    function matchesFilter(cellText, needle, operator, rangeEnd, exact) {
        const a = String(cellText).toLowerCase().trim();
        const b = String(needle).toLowerCase().trim();
        const pair = asNumbers(cellText, needle);

        switch (operator) {
            case '!=':
                return pair ? pair[0] !== pair[1] : a !== b;
            case 'not_like':
                return a.indexOf(b) === -1;
            case '<':
                return pair ? pair[0] < pair[1] : a < b;
            case '>':
                return pair ? pair[0] > pair[1] : a > b;
            case '<=':
                return pair ? pair[0] <= pair[1] : a <= b;
            case '>=':
                return pair ? pair[0] >= pair[1] : a >= b;
            case 'between':
                const low = asNumbers(cellText, needle);
                const high = asNumbers(cellText, rangeEnd);
                if (!low) {
                    return false;
                }
                // With no far end yet, 'between' is just a lower bound rather
                // than a filter that hides everything.
                return low[0] >= low[1] && (!high || low[0] <= high[1]);
            case 'starts_with':
                return a.indexOf(b) === 0;
            case 'ends_with':
                return b === '' || a.slice(-b.length) === b;
            case '=':
                return pair ? pair[0] === pair[1] : a === b;
            case 'like':
            default:
                return exact ? a === b : a.indexOf(b) !== -1;
        }
    }

    function filterTable(table) {
        const filterInputs = table.querySelectorAll('.table-filter-input');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        const filters = Array.from(filterInputs).map(function(input) {
            const cell = input.closest('td, th');
            const operator = cell ? cell.querySelector('.filter-operator') : null;
            const range = cell ? cell.querySelector('.filter-input-range') : null;
            const isSelect = input.tagName === 'SELECT';

            return {
                index: parseInt(input.dataset.columnIndex, 10),
                // A select holds an id in its value and the name in its label,
                // while the column shows the name. Compare what is on show.
                value: isSelect
                    ? (input.value === '' ? '' : input.options[input.selectedIndex].textContent.trim())
                    : input.value.trim(),
                operator: operator ? operator.value : '',
                rangeEnd: range ? range.value.trim() : '',
                exact: isSelect
            };
        });

        rows.forEach(function(row) {
            let show = true;
            const cells = Array.from(row.querySelectorAll('td'));

            filters.forEach(function(filter) {
                if (filter.value === '' || !cells[filter.index]) {
                    return;
                }
                if (!matchesFilter(cells[filter.index].textContent, filter.value,
                        filter.operator, filter.rangeEnd, filter.exact)) {
                    show = false;
                }
            });

            row.style.display = show ? '' : 'none';
        });

        updateRowCount(table);
    }
    
    function clearAllFilters(table) {
        table.querySelectorAll('.table-filter-input, .filter-input-range').forEach(function(input) {
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            } else {
                input.value = '';
            }
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
            // This filters the rows on the page. Where the list is paginated,
            // saying "3 of 20" without saying of what would read as three in
            // the whole system.
            const paged = document.querySelector('.pagination, .paginator, ul.pagination');
            countMsg.textContent = 'Showing ' + visibleRows.length + ' of ' + allRows.length
                + (paged ? ' rows on this page' : ' records');
            countMsg.style.display = 'block';
        } else {
            countMsg.style.display = 'none';
        }
    }
})();
