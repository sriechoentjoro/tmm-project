<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterEmployeeStatus[]|\Cake\Collection\CollectionInterface $masterEmployeeStatuses
 */
?>
<!-- Page Header with Hamburger Menu -->
<div class="index-header" style="margin-bottom: 20px;">
    <!-- Title Row with Hamburger Dropdown -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 6px 8px; font-size: 18px; color: #24292f; background: transparent; border: none; text-decoration: none; margin: 0;">
                <i class="fas fa-bars"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List Master Employee Statuses'),
                    ['action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New Master Employee Status'),
                    ['action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
            </div>
            
            <h2 style="margin: 0; display: inline-block;"><?= __('Master Employee Statuses') ?></h2>
        </div>
        
        <div style="display: flex; align-items: center; gap: 10px;">
            <?= $this->Html->link(
                '<i class="fas fa-plus"></i> ' . __('Add New'),
                ['action' => 'add'],
                ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Add New Record']
            ) ?>
        </div>
    </div>
    
    <!-- Export Buttons Row -->
    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 5px;">
        <?= $this->Html->link(
            '<i class="fas fa-print"></i> Print',
            ['action' => 'printReport', '?' => $this->request->getQueryParams()],
            ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Print Report', 'target' => '_blank']
        ) ?>
        <?= $this->Html->link(
            '<i class="fas fa-download"></i> CSV',
            ['action' => 'exportCsv', '?' => $this->request->getQueryParams()],
            ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Export to CSV']
        ) ?>
        <?= $this->Html->link(
            '<i class="fas fa-file-excel"></i> Excel',
            ['action' => 'exportExcel', '?' => $this->request->getQueryParams()],
            ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Export to Excel']
        ) ?>
    </div>
</div>

<!-- Table Container with Horizontal Scroll -->
<div class="table-scroll-wrapper" style="overflow-x: auto; cursor: grab; -webkit-overflow-scrolling: touch; user-select: none;">
    <div class="masterEmployeeStatuses index content">
        <table class="table" style="border-collapse: collapse; width: 100%; min-width: 800px;">
            <!-- Purple Gradient Header -->
            <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" class="actions"><?= __('Actions') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('id') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('slug') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('title') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('updated') ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap;" scope="col"><?= $this->Paginator->sort('created') ?></th>
                </tr>
            
            <!-- Filter Row (INSIDE thead) -->
            <tr class="filter-row" style="background-color: #f8f9fa;">
                <td style="padding: 5px;"><!-- Actions column - no filter --></td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="id" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="<=">≤</option>
                        <option value=">=">≥</option>
                        <option value="between">Between</option>
                    </select>
                    <input type="number" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="id" style="font-size: 0.85rem; padding: 4px;">
                    <input type="number" class="filter-input-range form-control form-control-sm" placeholder="To..." data-column="id" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="slug" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="slug" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="title" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="like">LIKE</option>
                        <option value="not_like">NOT LIKE</option>
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="starts_with">Starts With</option>
                        <option value="ends_with">Ends With</option>
                    </select>
                    <input type="text" class="filter-input form-control form-control-sm" placeholder="Filter..." data-column="title" style="font-size: 0.85rem; padding: 4px;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="updated" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="<=">≤</option>
                        <option value=">=">≥</option>
                        <option value="between">Between</option>
                    </select>
                    <input type="date" class="filter-input form-control form-control-sm" data-column="updated" style="font-size: 0.85rem; padding: 4px;">
                    <input type="date" class="filter-input-range form-control form-control-sm" data-column="updated" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                </td>
                <td style="padding: 5px;">
                    <select class="filter-operator form-control form-control-sm" data-column="created" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="<=">≤</option>
                        <option value=">=">≥</option>
                        <option value="between">Between</option>
                    </select>
                    <input type="date" class="filter-input form-control form-control-sm" data-column="created" style="font-size: 0.85rem; padding: 4px;">
                    <input type="date" class="filter-input-range form-control form-control-sm" data-column="created" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                </td>
            </tr>
            </thead>
            
            <tbody>
                <?php foreach ($masterEmployeeStatuses as $masterEmployeeStatus): ?>
                <tr>
                    <!-- Action Buttons: Edit | View -->
                    <td class="actions" style="white-space: nowrap; padding: 8px;">
                        <div class="action-buttons-hover">
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $masterEmployeeStatus->id],
                                ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => 'Edit']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $masterEmployeeStatus->id],
                                ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => 'View']
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="fas fa-trash"></i>',
                                ['action' => 'delete', $masterEmployeeStatus->id],
                                ['confirm' => __('Are you sure you want to delete # {0}?', $masterEmployeeStatus->id), 'class' => 'btn-action-icon btn-delete-icon', 'escape' => false, 'title' => 'Delete']
                            ) ?>
                        </div>
                    </td>
                    <td style="padding: 8px; white-space: nowrap;"><?= $this->Number->format($masterEmployeeStatus->id) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($masterEmployeeStatus->slug) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($masterEmployeeStatus->title) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($masterEmployeeStatus->updated) ?></td>
                    <td style="padding: 8px; white-space: nowrap;"><?= h($masterEmployeeStatus->created) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="paginator" role="navigation" aria-label="Pagination" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
    <ul class="pagination" style="margin: 10px 0; display: flex; list-style: none; gap: 5px;">
        <?= $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
    <p style="margin: 10px 0;"><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
</div>

<style>
/* Dropdown Menu Styles */
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    min-width: 200px;
    padding: 0.5rem 0;
    margin: 0.125rem 0 0;
    font-size: 1rem;
    color: #212529;
    text-align: left;
    list-style: none;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    display: block;
    width: 100%;
    padding: 0.5rem 1rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    text-decoration: none;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
}

.dropdown-item:hover {
    color: #16181b;
    background-color: #f8f9fa;
}

.dropdown-divider {
    height: 0;
    margin: 0.5rem 0;
    overflow: hidden;
    border-top: 1px solid #e9ecef;
}

/* Action Buttons with Hover Effect */
.action-buttons-hover {
    display: flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
}

tr:hover .action-buttons-hover {
    opacity: 1;
}

.btn-action-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    padding: 0;
    font-size: 12px;
    border-radius: 4px;
    background-color: #2c3e50;
    color: #fff;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-action-icon:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.btn-view-icon:hover { background-color: #3498db; color: #fff; }
.btn-edit-icon:hover { background-color: #f39c12; color: #fff; }
.btn-delete-icon:hover { background-color: #e74c3c; color: #fff; }

/* Export Buttons */
.btn-export-light {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 500;
    color: #24292f;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 1px solid rgba(102, 126, 234, 0.3);
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-export-light:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
    border-color: rgba(102, 126, 234, 0.5);
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    color: #24292f;
}

/* Drag to Scroll */
.table-scroll-wrapper.dragging {
    cursor: grabbing;
    user-select: none;
}

/* Filter Input Styles */
.filter-input, .filter-operator, .filter-input-range {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.filter-input:focus, .filter-operator:focus, .filter-input-range:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}
</style>

<script>
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
