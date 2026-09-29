<?php
/**
 * Related Records Table with Server-Side AJAX Filtering
 * Supports pagination and server-side search
 */

// Extract parameters
$tabId = isset($tabId) ? $tabId : 'related';
$title = isset($title) ? $title : 'Related Records';
$records = isset($records) ? $records : [];
$controller = isset($controller) ? $controller : '';
$columns = isset($columns) ? $columns : [];
$addUrl = isset($addUrl) ? $addUrl : null;
$currentPage = isset($currentPage) ? $currentPage : 1;
$totalRecords = isset($totalRecords) ? $totalRecords : count($records);
$limit = isset($limit) ? $limit : 50;
$totalPages = ceil($totalRecords / $limit);
// Two sets of names reach here. ApprenticeOrders/view.ctp passes
// ajaxSearchUrl, foreignKey and foreignValue; the other eight pages pass
// ajaxUrl, filterField and filterValue. Only the first set was read, so on
// those eight the whole server-side block below was compiled out - and they
// pass no 'records' either, so every one of those tabs rendered an empty
// table and nothing said why. Both sets are read now.
$ajaxSearchUrl = isset($ajaxSearchUrl) ? $ajaxSearchUrl
    : (isset($ajaxUrl) ? $ajaxUrl : null);          // URL for server-side search
$foreignKey = isset($foreignKey) ? $foreignKey
    : (isset($filterField) ? $filterField : null);  // e.g. 'apprentice_order_id'
$foreignValue = isset($foreignValue) ? $foreignValue
    : (isset($filterValue) ? $filterValue : null);  // e.g. $apprenticeOrder->id
?>

<!-- Cache Buster: Generated at <?= date('Y-m-d H:i:s') ?> -->

<style>
.static-table-wrapper {
    overflow-x: auto;
    margin: 20px 0;
}
.static-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
.static-table th,
.static-table td {
    padding: 12px 8px;
    border: 1px solid #dee2e6;
    text-align: left;
}
.static-table th {
    background: #f8f9fa;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
}
.static-table tbody tr:hover {
    background-color: #f5f5f5;
}
.static-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    flex-wrap: wrap;
    gap: 10px;
}
.static-pagination button {
    padding: 6px 12px;
    border: 1px solid #dee2e6;
    background: #fff;
    cursor: pointer;
    border-radius: 3px;
}
.static-pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.static-pagination button:hover:not(:disabled) {
    background: #0056b3;
    color: white;
}
.static-info {
    padding: 10px;
    background: #e9ecef;
    margin-bottom: 10px;
    border-radius: 4px;
}
.filter-operator {
    height: 28px !important;
    font-size: 11px !important;
    padding: 2px 4px !important;
    border: 1px solid #ced4da;
    border-radius: 3px;
}
.filter-input {
    height: 28px !important;
    font-size: 11px !important;
    padding: 2px 6px !important;
    border: 1px solid #ced4da;
    border-radius: 3px;

}</style>

<div id="<?= h($tabId) ?>-static" class="static-records-container">
    
    <?php if ($totalRecords > 0): ?>
        <!-- Info Bar -->
        <div class="static-info">
            <strong><?= __('Total Records:') ?></strong> <?= number_format($totalRecords) ?> | 
            <strong>Page:</strong> <?= $currentPage ?> of <?= $totalPages ?> | 
            <strong>Showing:</strong> <?= min(($currentPage - 1) * $limit + 1, $totalRecords) ?>-<?= min($currentPage * $limit, $totalRecords) ?>
        </div>

        <!-- Table -->
        <div class="static-table-wrapper">
            <table class="static-table table-sm table-bordered">
                <thead>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th><?= h(isset($col['label']) ? $col['label'] : $col['name']) ?></th>
                        <?php endforeach; ?>
                        <th>Actions</th>
                    </tr>
                    <!-- Filter Row with Operator Support -->
                    <tr class="filter-row" style="background: #fff;">
                        <?php foreach ($columns as $col): ?>
                            <?php 
                            $fieldType = isset($col['type']) ? $col['type'] : 'text';
                            $fieldName = isset($col['name']) ? $col['name'] : '';
                            ?>
                            <th style="padding: 3px 2px; vertical-align: middle;">
                                <div style="display: flex; gap: 3px; align-items: stretch;">
                                    <select class="filter-operator" 
                                            data-field="<?= h($fieldName) ?>"
                                            title="Filter operator">
                                        <?php if ($fieldType === 'file' || $fieldType === 'image'): ?>
                                            <option value="file_exists">📁 Exists</option>
                                            <option value="file_not_exists">❌ Missing</option>
                                            <option value="contains">Contains</option>
                                        <?php elseif ($fieldType === 'number' || $fieldType === 'date' || $fieldType === 'datetime'): ?>
                                            <option value="equals">=</option>
                                            <option value="not_equals">≠</option>
                                            <option value="greater_than">&gt;</option>
                                            <option value="less_than">&lt;</option>
                                            <option value="greater_equal">≥</option>
                                            <option value="less_equal">≤</option>
                                            <option value="contains">Contains</option>
                                        <?php else: ?>
                                            <option value="contains">Contains</option>
                                            <option value="equals">Equals</option>
                                            <option value="not_equals">Not Equals</option>
                                            <option value="starts_with">Starts With</option>
                                            <option value="ends_with">Ends With</option>
                                        <?php endif; ?>
                                    </select>
                                    <input type="text" 
                                           class="filter-input" 
                                           data-field="<?= h($fieldName) ?>"
                                           placeholder="<?= $fieldType === 'file' || $fieldType === 'image' ? 'Optional...' : 'Value...' ?>"
                                           title="Filter value">
                                </div>
                            </th>
                        <?php endforeach; ?>
                        <th style="padding: 3px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <?php foreach ($columns as $col): ?>
                                <?php 
                                $fieldName = isset($col['name']) ? $col['name'] : (isset($col['field']) ? $col['field'] : '');
                                $value = isset($record->{$fieldName}) ? $record->{$fieldName} : (isset($record[$fieldName]) ? $record[$fieldName] : '');
                                $type = isset($col['type']) ? $col['type'] : 'text';
                                ?>
                                <td>
                                    <?php if ($type === 'image' && !empty($value)): ?>
                                        <?php $imagePath = WWW_ROOT . ltrim($value, '/'); ?>
                                        <?php if (file_exists($imagePath)): ?>
                                            <a href="<?= $this->Url->build('/' . $value) ?>" target="_blank" title="<?= __('Click to view full image') ?>">
                                                <img src="<?= $this->Url->build('/' . $value) ?>" 
                                                     style="max-width:60px;max-height:60px;object-fit:cover;border:1px solid #ddd;border-radius:3px;cursor:pointer;" 
                                                     alt="<?= h($fieldName) ?>" />
                                            </a>
                                        <?php else: ?>
                                            <svg width="60" height="60" viewBox="0 0 60 60" style="border:1px dashed #dc3545;border-radius:3px;">
                                                <rect width="60" height="60" fill="#fff5f5"/>
                                                <text x="30" y="25" font-size="24" text-anchor="middle" fill="#dc3545">🖼</text>
                                                <text x="30" y="45" font-size="10" text-anchor="middle" fill="#dc3545">Missing</text>
                                            </svg>
                                        <?php endif; ?>
                                    <?php elseif ($type === 'image' && empty($value)): ?>
                                        <svg width="60" height="60" viewBox="0 0 60 60" style="border:1px dashed #999;border-radius:3px;">
                                            <rect width="60" height="60" fill="#f8f9fa"/>
                                            <text x="30" y="30" font-size="24" text-anchor="middle" fill="#999">📷</text>
                                            <text x="30" y="50" font-size="9" text-anchor="middle" fill="#999">No Image</text>
                                        </svg>
                                    <?php elseif ($type === 'file' && !empty($value)): ?>
                                        <?= $this->element('file_viewer', ['filePath' => $value]) ?>
                                    <?php elseif ($type === 'date' && !empty($value)): ?>
                                        <?= h(is_object($value) ? $value->format('Y-m-d') : $value) ?>
                                    <?php elseif ($type === 'datetime' && !empty($value)): ?>
                                        <?= h(is_object($value) ? $value->format('Y-m-d H:i:s') : $value) ?>
                                    <?php elseif ($type === 'number'): ?>
                                        <?= $this->Number->format($value) ?>
                                    <?php else: ?>
                                        <?= h($value ?: '-') ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <?= $this->Html->link('View', 
                                    ['controller' => $controller, 'action' => 'view', isset($record->id) ? $record->id : $record['id']], 
                                    ['class' => 'btn btn-xs btn-info']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <?php 
            // Use tab-specific query parameter to avoid conflicts
            $pageParam = strtolower($controller) . '_page';
            $currentUrl = $this->request->getRequestTarget();
            $baseUrl = strtok($currentUrl, '?');
            
            // Build URL with preserved query params
            function buildPageUrl($base, $param, $pageNum, $existingQuery) {
                $query = [];
                if (!empty($existingQuery)) {
                    parse_str($existingQuery, $query);
                }
                $query[$param] = $pageNum;
                return $base . '?' . http_build_query($query);
            }

            $existingQuery = parse_url($currentUrl, PHP_URL_QUERY);
            ?>
            <div class="static-pagination">
                <div>
                    <button onclick="window.location.href='<?= buildPageUrl($baseUrl, $pageParam, 1, $existingQuery) ?>'" 
                            <?= $currentPage === 1 ? 'disabled' : '' ?>>First</button>
                    <button onclick="window.location.href='<?= buildPageUrl($baseUrl, $pageParam, $currentPage - 1, $existingQuery) ?>'" 
                            <?= $currentPage === 1 ? 'disabled' : '' ?>>Previous</button>
                </div>
                <div>Page <?= $currentPage ?> of <?= $totalPages ?></div>
                <div>
                    <button onclick="window.location.href='<?= buildPageUrl($baseUrl, $pageParam, $currentPage + 1, $existingQuery) ?>'" 
                            <?= $currentPage >= $totalPages ? 'disabled' : '' ?>>Next</button>
                    <button onclick="window.location.href='<?= buildPageUrl($baseUrl, $pageParam, $totalPages, $existingQuery) ?>'" 
                            <?= $currentPage >= $totalPages ? 'disabled' : '' ?>>Last</button>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info" style="margin: 20px 0;">
            <strong><?= __('No records found.') ?></strong>
            <?php if ($addUrl): ?>
                <?= $this->Html->link('Add New', $addUrl, ['class' => 'btn btn-sm btn-primary', 'style' => 'margin-left: 10px;']) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($addUrl && $totalRecords > 0): ?>
        <div style="margin-top: 15px;">
            <?= $this->Html->link('+ Add New', $addUrl, ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    <?php endif; ?>

</div>

<script>
(function() {
    var container = document.getElementById('<?= h($tabId) ?>-static');
    if (!container) return;
    
    var table = container.querySelector('.static-table');
    var filterInputs = container.querySelectorAll('.filter-input');
    var filterOperators = container.querySelectorAll('.filter-operator');
    var tbody = table.querySelector('tbody');
    
    <?php if ($ajaxSearchUrl && $foreignKey && $foreignValue): ?>
    // SERVER-SIDE AJAX FILTERING
    //
    // What this used to send and read did not match the endpoint at either
    // end: it sent a hardcoded apprentice_order_id whatever table the tab was
    // for, never sent the column to filter on, read data.records where the
    // endpoint wrote data.data, and read data.total where it wrote
    // data.pagination.total. It then drew every row out of tmm_code,
    // identity_number, birth_date and image_photo regardless of the columns
    // the tab was given. It is driven by those columns now.
    var ajaxUrl = '<?= $this->Url->build($ajaxSearchUrl) ?>';
    var filterField = '<?= h($foreignKey) ?>';
    var filterValue = '<?= h($foreignValue) ?>';
    var viewController = '<?= h($controller) ?>';
    var columns = <?= json_encode(array_map(function ($col) {
        return [
            'name' => isset($col['name']) ? $col['name'] : (isset($col['field']) ? $col['field'] : ''),
            'type' => isset($col['type']) ? $col['type'] : 'text',
        ];
    }, $columns)) ?>;
    var filterTimeout;
    var currentAjaxPage = 1;

    function esc(value) {
        if (value === null || typeof value === 'undefined' || value === '') {
            return '';
        }
        return String(value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function cellFor(record, column) {
        var value = record[column.name];
        if (column.type === 'image') {
            if (!value) {
                return '<td><span style="color:#999;font-size:11px;"><?= h(__('No Image')) ?></span></td>';
            }
            // The endpoint says whether the path is still a file on disk, so a
            // replaced or deleted photo says "missing" rather than showing a
            // broken image icon.
            if (record[column.name + '_exists'] === false) {
                return '<td><span style="color:#dc3545;font-size:11px;"><?= h(__('Missing')) ?></span></td>';
            }
            return '<td><a href="/' + esc(value) + '" target="_blank">'
                + '<img src="/' + esc(value) + '" style="max-width:60px;max-height:60px;'
                + 'object-fit:cover;border:1px solid #ddd;border-radius:3px;" /></a></td>';
        }
        if (column.type === 'file') {
            if (!value) {
                return '<td>-</td>';
            }
            if (record[column.name + '_exists'] === false) {
                return '<td><span style="color:#dc3545;font-size:11px;"><?= h(__('Missing')) ?></span></td>';
            }
            return '<td><a href="/' + esc(value) + '" target="_blank"><?= h(__('Open')) ?></a></td>';
        }
        if (value === null || typeof value === 'undefined' || value === '') {
            return '<td>-</td>';
        }
        // A date arrives as an ISO string; only the part anybody reads is kept.
        if (column.type === 'date' && String(value).length >= 10) {
            return '<td>' + esc(String(value).substring(0, 10)) + '</td>';
        }
        if (column.type === 'datetime' && String(value).length >= 10) {
            return '<td>' + esc(String(value).substring(0, 19).replace('T', ' ')) + '</td>';
        }
        return '<td>' + esc(value) + '</td>';
    }

    function say(message, colour) {
        tbody.innerHTML = '<tr><td colspan="100" style="text-align:center;padding:20px;'
            + (colour ? 'color:' + colour + ';' : '') + '">' + esc(message) + '</td></tr>';
    }

    function performAjaxSearch(page) {
        page = page || 1;
        currentAjaxPage = page;

        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function () {
            var filters = {};
            var hasFilters = false;
            filterInputs.forEach(function (input) {
                var value = input.value.trim();
                if (value === '') {
                    return;
                }
                var field = input.getAttribute('data-field');
                var operatorBox = container.querySelector('.filter-operator[data-field="' + field + '"]');
                filters[field] = {
                    value: value,
                    operator: operatorBox ? operatorBox.value : 'contains'
                };
                hasFilters = true;
            });

            var params = {
                filter_field: filterField,
                filter_value: filterValue,
                page: page,
                limit: 50
            };
            if (hasFilters) {
                params.filters = JSON.stringify(filters);
            }

            var queryString = Object.keys(params).map(function (key) {
                return key + '=' + encodeURIComponent(params[key]);
            }).join('&');

            say('<?= h(__('Searching...')) ?>');
            var paginationDiv = container.querySelector('.static-pagination');
            if (paginationDiv) { paginationDiv.style.display = 'none'; }

            fetch(ajaxUrl + '?' + queryString, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })
            .then(function (response) { return response.text(); })
            .then(function (text) {
                var data;
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    // A refusal does not arrive as an error status. The
                    // permission check redirects, so the answer is a whole HTML
                    // page with a 200 on it and only the parse fails. Saying
                    // "invalid response" to that sends the reader looking for a
                    // fault that is not there.
                    var refused = text.slice(0, 200).indexOf('<') === 0;
                    say(refused
                        ? '<?= h(__('You are not allowed to read these records.')) ?>'
                        : '<?= h(__('The list could not be read.')) ?>', '#dc3545');
                    return;
                }

                if (!data.success) {
                    say(data.error || '<?= h(__('The list could not be read.')) ?>', '#dc3545');
                    return;
                }

                var records = data.records || [];
                var pagination = data.pagination || { page: page, pages: 1, total: records.length, limit: 50 };

                if (records.length === 0) {
                    say('<?= h(__('No records found')) ?>');
                } else {
                    var html = '';
                    records.forEach(function (record) {
                        html += '<tr>';
                        columns.forEach(function (column) {
                            html += cellFor(record, column);
                        });
                        html += '<td><a href="/' + esc(viewController.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase())
                            + '/view/' + esc(record.id) + '" class="btn btn-xs btn-info"><?= h(__('View')) ?></a></td>';
                        html += '</tr>';
                    });
                    tbody.innerHTML = html;
                }

                if (data.refused && data.refused.length) {
                    console.warn('Ignored filter column(s) this table does not have:', data.refused);
                }

                var infoDiv = container.querySelector('.static-info');
                if (infoDiv) {
                    var shown = pagination.total === 0 ? 0 : ((pagination.page - 1) * pagination.limit + 1);
                    infoDiv.innerHTML = (hasFilters
                            ? '<strong style="color:#007bff;"><?= h(__('Filtered Results:')) ?></strong> '
                            : '<strong><?= h(__('Total Records:')) ?></strong> ')
                        + pagination.total + ' | <strong><?= h(__('Page:')) ?></strong> '
                        + pagination.page + ' / ' + Math.max(1, pagination.pages)
                        + ' | <strong><?= h(__('Showing:')) ?></strong> ' + shown + '-'
                        + Math.min(pagination.page * pagination.limit, pagination.total);
                }

                updateAjaxPagination(pagination.page, Math.max(1, pagination.pages));
            })
            .catch(function (error) {
                console.error('Related records request failed:', error);
                say('<?= h(__('The list could not be read.')) ?>', '#dc3545');
            });
        }, 300);
    }

    function updateAjaxPagination(currentPage, totalPages) {
        var existingPagination = container.querySelector('.static-pagination');
        if (totalPages <= 1) {
            if (existingPagination) { existingPagination.style.display = 'none'; }
            return;
        }
        var call = "performAjaxSearch_<?= h($tabId) ?>";
        var paginationHTML = '<div class="static-pagination"><div>'
            + '<button onclick="' + call + '(1)" ' + (currentPage === 1 ? 'disabled' : '') + '><?= h(__('First')) ?></button>'
            + '<button onclick="' + call + '(' + (currentPage - 1) + ')" ' + (currentPage === 1 ? 'disabled' : '') + '><?= h(__('Previous')) ?></button>'
            + '</div><div><?= h(__('Page')) ?> ' + currentPage + ' / ' + totalPages + '</div><div>'
            + '<button onclick="' + call + '(' + (currentPage + 1) + ')" ' + (currentPage >= totalPages ? 'disabled' : '') + '><?= h(__('Next')) ?></button>'
            + '<button onclick="' + call + '(' + totalPages + ')" ' + (currentPage >= totalPages ? 'disabled' : '') + '><?= h(__('Last')) ?></button>'
            + '</div></div>';

        if (existingPagination) {
            existingPagination.outerHTML = paginationHTML;
            existingPagination = container.querySelector('.static-pagination');
            if (existingPagination) { existingPagination.style.display = ''; }
            return;
        }
        var tableWrapper = container.querySelector('.static-table-wrapper');
        if (tableWrapper) {
            tableWrapper.insertAdjacentHTML('afterend', paginationHTML);
        }
    }

    window['performAjaxSearch_<?= h($tabId) ?>'] = performAjaxSearch;

    filterInputs.forEach(function (input) {
        input.addEventListener('keyup', function () { performAjaxSearch(1); });
        input.addEventListener('paste', function () { performAjaxSearch(1); });
    });
    filterOperators.forEach(function (select) {
        select.addEventListener('change', function () { performAjaxSearch(1); });
    });

    // Eight of the nine pages pass no records at all, so the table they render
    // is empty before anybody types anything. The first page is fetched on
    // load, which is what makes the tab show its rows.
    if (tbody.querySelectorAll('tr').length === 0) {
        performAjaxSearch(1);
    }
    <?php else: ?>
    // CLIENT-SIDE FILTERING (fallback if no AJAX URL provided)
    var allRows = Array.from(tbody.querySelectorAll('tr'));
    var rowsData = allRows.map(function(row) {
        var cells = Array.from(row.querySelectorAll('td'));
        return {
            row: row,
            cells: cells,
            text: cells.map(function(cell) { return cell.textContent.trim().toLowerCase(); })
        };
    });
    
    function matchesFilter(cellText, filterValue, operator, cellElement) {
        if (!filterValue) return true;
        filterValue = filterValue.toLowerCase();
        
        switch(operator) {
            case 'contains': return cellText.indexOf(filterValue) !== -1;
            case 'equals': return cellText === filterValue;
            case 'not_equals': return cellText !== filterValue;
            case 'starts_with': return cellText.indexOf(filterValue) === 0;
            case 'ends_with': return cellText.lastIndexOf(filterValue) === cellText.length - filterValue.length;
            case 'greater_than':
                var num1 = parseFloat(cellText);
                var num2 = parseFloat(filterValue);
                return !isNaN(num1) && !isNaN(num2) && num1 > num2;
            case 'less_than':
                var num1 = parseFloat(cellText);
                var num2 = parseFloat(filterValue);
                return !isNaN(num1) && !isNaN(num2) && num1 < num2;
            case 'file_exists':
                var hasImage = cellElement.querySelector('img') !== null;
                var hasMissingSvg = cellElement.querySelector('svg text') !== null && 
                                   cellElement.textContent.toLowerCase().indexOf('missing') !== -1;
                return hasImage && !hasMissingSvg;
            case 'file_not_exists':
                var hasMissingSvg = cellElement.querySelector('svg text') !== null && 
                                   cellElement.textContent.toLowerCase().indexOf('missing') !== -1;
                return hasMissingSvg || cellElement.textContent.trim() === '-';
            default: return cellText.indexOf(filterValue) !== -1;
    
        }
    }
    var filterTimeout;
    function applyFilters() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            var filters = [];
            var hasFilters = false;
            
            filterInputs.forEach(function(input, index) {
                var value = input.value.trim();
                var operator = filterOperators[index] ? filterOperators[index].value : 'contains';
                if (value || operator === 'file_exists' || operator === 'file_not_exists') {
                    filters[index] = { value: value, operator: operator };
                    hasFilters = true;
                }
            });
            
            var visibleCount = 0;
            rowsData.forEach(function(rowData) {
                var visible = true;
                if (hasFilters) {
                    for (var colIndex = 0; colIndex < filters.length; colIndex++) {
                        if (filters[colIndex]) {
                            var filterValue = filters[colIndex].value.toLowerCase();
                            var operator = filters[colIndex].operator;
                            var cellText = rowData.text[colIndex] || '';
                            var cellElement = rowData.cells[colIndex];
                            
                            if (!matchesFilter(cellText, filterValue, operator, cellElement)) {
                                visible = false;
                                break;
                            }
                        }
                    }
                }
                rowData.row.style.display = visible ? '' : 'none';
                if (visible) visibleCount++;
            });
            
            var infoDiv = container.querySelector('.static-info');
            if (infoDiv && hasFilters) {
                infoDiv.innerHTML = '<strong style="color:#007bff;">Filtered:</strong> ' + visibleCount + ' of <?= $totalRecords ?> records';
            }
        }, 300);
    
    }
    filterInputs.forEach(function(input) {
        input.addEventListener('keyup', applyFilters);
        input.addEventListener('paste', applyFilters);
    });
    
    filterOperators.forEach(function(select) {
        select.addEventListener('change', applyFilters);
    });
    
    console.log('✅ Client-side filtering initialized for <?= h($tabId) ?>');
    <?php endif; ?>
    
})();
</script>



