document.addEventListener('DOMContentLoaded', function() {
    // Hamburger Dropdown Menu
    const dropdownButton = document.getElementById('dropdownMenuButton');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownButton && dropdownMenu) {
        dropdownButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        document.addEventListener('click', function(e) {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
        
        dropdownMenu.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                setTimeout(() => dropdownMenu.classList.remove('show'), 100);
            }
        });
    }
    // Drag to Scroll
    const scrollContainer = document.querySelector('.table-scroll-wrapper');
    let isDown = false;
    let startX;
    let scrollLeft;

    scrollContainer.addEventListener('mousedown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.tagName === 'SELECT') {
            return;
        }
        isDown = true;
        scrollContainer.classList.add('dragging');
        startX = e.pageX - scrollContainer.offsetLeft;
        scrollLeft = scrollContainer.scrollLeft;
    });

    scrollContainer.addEventListener('mouseleave', function() {
        isDown = false;
        scrollContainer.classList.remove('dragging');
    });

    scrollContainer.addEventListener('mouseup', function() {
        isDown = false;
        scrollContainer.classList.remove('dragging');
    });

    scrollContainer.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - scrollContainer.offsetLeft;
        const walk = (x - startX) * 2;
        scrollContainer.scrollLeft = scrollLeft - walk;
    });
    
    // Filter Operators
    const filterOperators = document.querySelectorAll('.filter-operator');
    const filterInputs = document.querySelectorAll('.filter-input');
    const rangeInputs = document.querySelectorAll('.filter-input-range');
    
    // Handle operator change to show/hide range input for "between"
    filterOperators.forEach(function(select) {
        select.addEventListener('change', function() {
            const column = this.getAttribute('data-column');
            const operator = this.value;
            
            const rangeInput = document.querySelector('.filter-input-range[data-column="' + column + '"]');
            
            if (operator === 'between' && rangeInput) {
                rangeInput.style.display = 'block';
            } else if (rangeInput) {
                rangeInput.style.display = 'none';
            }
            
            filterTable();
        });
    });
    
    // Add event listeners to filter inputs
    filterInputs.forEach(function(input) {
        input.addEventListener('input', filterTable);
    });
    
    // Add event listeners to range inputs for "between" operator
    rangeInputs.forEach(function(input) {
        input.addEventListener('input', filterTable);
    });
    
    function filterTable() {
        const table = document.querySelector('.table tbody');
        const rows = table.querySelectorAll('tr');
        let visibleCount = 0;
        
        rows.forEach(function(row) {
            let showRow = true;
            
            filterInputs.forEach(function(input) {
                if (!showRow) return;
                
                const filterValue = input.value.trim();
                if (!filterValue) return;
                
                const column = input.getAttribute('data-column');
                const columnIndex = getColumnIndex(column);
                
                if (columnIndex === -1) return;
                
                const cell = row.cells[columnIndex];
                if (!cell) return;
                
                const cellText = cell.textContent || cell.innerText || '';
                
                const operatorSelect = document.querySelector('.filter-operator[data-column="' + column + '"]');
                const operator = operatorSelect ? operatorSelect.value : 'like';
                
                // For "between", collect range value
                if (operator === 'between') {
                    const rangeInput = document.querySelector('.filter-input-range[data-column="' + column + '"]');
                    const filterValue2 = rangeInput ? rangeInput.value.trim() : '';
                    showRow = applyFilter(cellText, filterValue, operator, filterValue2);
                } else {
                    showRow = applyFilter(cellText, filterValue, operator);
                }
            });
            
            if (showRow) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        console.log('Filtered: ' + visibleCount + ' / ' + rows.length + ' rows visible');
    }
    
    function applyFilter(cellText, filterValue, operator, filterValue2) {
        const cellVal = cellText.trim().toLowerCase();
        const filterVal = filterValue.trim().toLowerCase();
        
        if (filterVal === '') return true;
        
        const cellNum = parseFloat(cellVal.replace(/[^0-9.-]/g, ''));
        const filterNum = parseFloat(filterVal);
        const isNumeric = !isNaN(cellNum) && !isNaN(filterNum);
        
        switch(operator) {
            case '=':
                return isNumeric ? cellNum === filterNum : cellVal === filterVal;
            case '!=':
                return isNumeric ? cellNum !== filterNum : cellVal !== filterVal;
            case '<':
                return isNumeric ? cellNum < filterNum : cellVal < filterVal;
            case '>':
                return isNumeric ? cellNum > filterNum : cellVal > filterVal;
            case '<=':
                return isNumeric ? cellNum <= filterNum : cellVal <= filterVal;
            case '>=':
                return isNumeric ? cellNum >= filterNum : cellVal >= filterVal;
            case 'between':
                if (filterValue2 && isNumeric) {
                    const filterNum2 = parseFloat(filterValue2);
                    if (!isNaN(filterNum2)) {
                        return cellNum >= filterNum && cellNum <= filterNum2;
                    }
                }
                return cellVal.includes(filterVal);
            case 'like':
                return cellVal.includes(filterVal);
            case 'not_like':
                return !cellVal.includes(filterVal);
            case 'starts_with':
                return cellVal.startsWith(filterVal);
            case 'ends_with':
                return cellVal.endsWith(filterVal);
            default:
                return cellVal.includes(filterVal);
        }
    }
    function getColumnIndex(columnName) {
        const headerCells = document.querySelectorAll('.table thead th');
        for (let i = 0; i < headerCells.length; i++) {
            const sortLink = headerCells[i].querySelector('a');
            if (sortLink) {
                const href = sortLink.getAttribute('href');
                if (href && href.includes('sort=' + columnName)) {
                    return i;
                }
            }
        }
        return -1;
    }
});
</script>
