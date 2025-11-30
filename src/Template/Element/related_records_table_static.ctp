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
$ajaxSearchUrl = isset($ajaxSearchUrl) ? $ajaxSearchUrl : null; // URL for server-side search
$foreignKey = isset($foreignKey) ? $foreignKey : null; // e.g., 'apprentice_order_id'
$foreignValue = isset($foreignValue) ? $foreignValue : null; // e.g., $apprenticeOrder->id
?>

<!-- Cache Buster: Generated at <?= date('Y-m-d H:i:s') ?> -->

<style>
.static-table-wrapper {
    overflow-x: auto;
    margin: 20px 0;
.static-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
.static-table th,
.static-table td {
    padding: 12px 8px;
    border: 1px solid #dee2e6;
    text-align: left;
.static-table th {
    background: #f8f9fa;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
.static-table tbody tr:hover {
    background-color: #f5f5f5;
.static-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    flex-wrap: wrap;
    gap: 10px;
.static-pagination button {
    padding: 6px 12px;
    border: 1px solid #dee2e6;
    background: #fff;
    cursor: pointer;
    border-radius: 3px;
.static-pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
.static-pagination button:hover:not(:disabled) {
    background: #0056b3;
    color: white;
.static-info {
    padding: 10px;
    background: #e9ecef;
    margin-bottom: 10px;
    border-radius: 4px;
.filter-operator {
    height: 28px !important;
    font-size: 11px !important;
    padding: 2px 4px !important;
    border: 1px solid #ced4da;
    border-radius: 3px;
.filter-input {
    height: 28px !important;
    font-size: 11px !important;
    padding: 2px 6px !important;
    border: 1px solid #ced4da;
    border-radius: 3px;
</style>

<div id="<?= h($tabId) ?>-static" class="static-records-container">
    
    <?php if ($totalRecords > 0): ?>
        <!-- Info Bar -->
        <div class="static-info">
            <strong>Total Records:</strong> <?= number_format($totalRecords) ?> | 
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
                                            <option value="file_exists">ðŸ“ Exists</option>
                                            <option value="file_not_exists">âŒ Missing</option>
                                            <option value="contains">Contains</option>
                                        <?php elseif ($fieldType === 'number' || $fieldType === 'date' || $fieldType === 'datetime'): ?>
                                            <option value="equals">=</option>
                                            <option value="not_equals">â‰ </option>
                                            <option value="greater_than">&gt;</option>
                                            <option value="less_than">&lt;</option>
                                            <option value="greater_equal">â‰¥</option>
                                            <option value="less_equal">â‰¤</option>
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
                                            <a href="<?= $this->Url->build('/' . $value) ?>" target="_blank" title="Click to view full image">
                                                <img src="<?= $this->Url->build('/' . $value) ?>" 
                                                     style="max-width:60px;max-height:60px;object-fit:cover;border:1px solid #ddd;border-radius:3px;cursor:pointer;" 
                                                     alt="<?= h($fieldName) ?>" />
                                            </a>
                                        <?php else: ?>
                                            <svg width="60" height="60" viewBox="0 0 60 60" style="border:1px dashed #dc3545;border-radius:3px;">
                                                <rect width="60" height="60" fill="#fff5f5"/>
                                                <text x="30" y="25" font-size="24" text-anchor="middle" fill="#dc3545">ðŸ–¼</text>
                                                <text x="30" y="45" font-size="10" text-anchor="middle" fill="#dc3545">Missing</text>
                                            </svg>
                                        <?php endif; ?>
                                    <?php elseif ($type === 'image' && empty($value)): ?>
                                        <svg width="60" height="60" viewBox="0 0 60 60" style="border:1px dashed #999;border-radius:3px;">
                                            <rect width="60" height="60" fill="#f8f9fa"/>
                                            <text x="30" y="30" font-size="24" text-anchor="middle" fill="#999">ðŸ“·</text>
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
                $query[$param] = $pageNum;
                return $base . '?' . http_build_query($query);
            
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
            <strong>No records found.</strong>
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
    var ajaxUrl = '<?= $this->Url->build($ajaxSearchUrl) ?>';
    var foreignKey = '<?= h($foreignKey) ?>';
    var foreignValue = '<?= h($foreignValue) ?>';
    var filterTimeout;
    var currentAjaxPage = 1;
    var currentFilters = {};
    var originalTableContent = tbody.innerHTML; // Store original data
    
    console.log('ðŸ“¦ Original table has ' + tbody.querySelectorAll('tr').length + ' rows');
    
    function performAjaxSearch(page) {
        page = page || 1;
        currentAjaxPage = page;
        
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            // Check if ALL filter inputs are empty
            var allEmpty = true;
            filterInputs.forEach(function(input) {
                if (input.value.trim() !== '') {
                    allEmpty = false;
            });
            
            console.log('ðŸ” All inputs empty?', allEmpty);
            
            // If all inputs are empty, restore original data
            if (allEmpty) {
                console.log('âœ… Restoring original data (all filters cleared)');
                tbody.innerHTML = originalTableContent;
                var paginationDiv = container.querySelector('.static-pagination');
                if (paginationDiv) paginationDiv.style.display = 'none';
                
                // Update info bar
                var infoBar = container.querySelector('.filter-info-bar');
                if (infoBar) {
                    var totalRecords = tbody.querySelectorAll('tr').length;
                    infoBar.innerHTML = 'Showing all ' + totalRecords + ' records';
                return;
            
            // Collect filter values
            var filters = {};
            var hasFilters = false;
            
            filterInputs.forEach(function(input, index) {
                var field = input.getAttribute('data-field');
                var value = input.value.trim();
                var operator = filterOperators[index] ? filterOperators[index].value : 'contains';
                
                // Only add filter if value is not empty
                // Empty value means "show all records" regardless of operator
                if (value !== '') {
                    filters[field] = { value: value, operator: operator };
                    hasFilters = true;
            });
            
            currentFilters = filters;
            
            console.log('ðŸ” hasFilters:', hasFilters, 'filters:', filters);
            
            // Build query parameters
            var params = {
                apprentice_order_id: foreignValue,
                page: page,
                limit: 50
            };
            
            if (hasFilters) {
                params.filters = filters;
            
            // Show loading indicator
            tbody.innerHTML = '<tr><td colspan="100" style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin"></i> Searching...</td></tr>';
            
            // Hide pagination during load
            var paginationDiv = container.querySelector('.static-pagination');
            if (paginationDiv) paginationDiv.style.display = 'none';
            
            // Perform AJAX request
            var queryString = Object.keys(params).map(function(key) {
                if (key === 'filters') {
                    return 'filters=' + encodeURIComponent(JSON.stringify(params[key]));
                return key + '=' + encodeURIComponent(params[key]);
            }).join('&');
            
            fetch(ajaxUrl + '?' + queryString, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })
            .then(function(response) { 
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                return response.text(); // Get as text first
            })
            .then(function(text) {
                console.log('AJAX Response:', text.substring(0, 200)); // Log first 200 chars
                try {
                    var data = JSON.parse(text);
                    if (data.success && data.records) {
                        // Update table with new data
                        tbody.innerHTML = '';
                        
                        if (data.records.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="100" style="text-align:center;padding:20px;">No records found</td></tr>';
                        } else {
                            data.records.forEach(function(record) {
                                var row = '<tr>';
                                row += '<td>' + (record.id || '-') + '</td>';
                                row += '<td>' + (record.tmm_code || '-') + '</td>';
                                row += '<td>' + (record.name || '-') + '</td>';
                                row += '<td>' + (record.identity_number || '-') + '</td>';
                                row += '<td>' + (record.birth_date || '-') + '</td>';
                                
                                // Image column
                                if (record.image_photo) {
                                    row += '<td><img src="/' + record.image_photo + '" style="max-width:60px;max-height:60px;object-fit:cover;border-radius:3px;" /></td>';
                                } else {
                                    row += '<td><svg width="60" height="60" viewBox="0 0 60 60" style="border:1px dashed #999;border-radius:3px;"><rect width="60" height="60" fill="#f8f9fa"/><text x="30" y="30" font-size="24" text-anchor="middle" fill="#999">ðŸ“·</text></svg></td>';
                                
                                row += '<td><a href="/apprentices/view/' + record.id + '" class="btn btn-xs btn-info">View</a></td>';
                                row += '</tr>';
                                tbody.innerHTML += row;
                            });
                        
                        // Update info bar
                        var infoDiv = container.querySelector('.static-info');
                        if (infoDiv) {
                            var filterText = hasFilters ? '<strong style="color:#007bff;">Filtered Results:</strong> ' : '<strong>Total Records:</strong> ';
                            infoDiv.innerHTML = filterText + data.total + ' records | ' +
                                              '<strong>Page:</strong> ' + data.page + ' of ' + data.pages + ' | ' +
                                              '<strong>Showing:</strong> ' + ((data.page - 1) * 50 + 1) + '-' + 
                                              Math.min(data.page * 50, data.total);
                        
                        // Update/create pagination
                        updateAjaxPagination(data.page, data.pages);
                        
                    } else {
                        var errorMsg = data.error || 'Unknown error';
                        tbody.innerHTML = '<tr><td colspan="100" style="text-align:center;padding:20px;color:red;">Error: ' + errorMsg + '</td></tr>';
                } catch(parseError) {
                    console.error('JSON Parse Error:', parseError);
                    console.error('Response was:', text);
                    tbody.innerHTML = '<tr><td colspan="100" style="text-align:center;padding:20px;color:red;">Invalid JSON response. Check console for details.</td></tr>';
            })
            .catch(function(error) {
                console.error('Ajax error:', error);
                tbody.innerHTML = '<tr><td colspan="100" style="text-align:center;padding:20px;color:red;">Error: ' + error.message + '</td></tr>';
            });
            
        }, 500); // 500ms debounce for server requests
    
    function updateAjaxPagination(currentPage, totalPages) {
        var existingPagination = container.querySelector('.static-pagination');
        
        if (totalPages <= 1) {
            if (existingPagination) existingPagination.style.display = 'none';
            return;
        
        var paginationHTML = '<div class="static-pagination">' +
            '<div>' +
            '<button onclick="performAjaxSearch_' + '<?= h($tabId) ?>' + '(1)" ' + (currentPage === 1 ? 'disabled' : '') + '>First</button>' +
            '<button onclick="performAjaxSearch_' + '<?= h($tabId) ?>' + '(' + (currentPage - 1) + ')" ' + (currentPage === 1 ? 'disabled' : '') + '>Previous</button>' +
            '</div>' +
            '<div>Page ' + currentPage + ' of ' + totalPages + '</div>' +
            '<div>' +
            '<button onclick="performAjaxSearch_' + '<?= h($tabId) ?>' + '(' + (currentPage + 1) + ')" ' + (currentPage >= totalPages ? 'disabled' : '') + '>Next</button>' +
            '<button onclick="performAjaxSearch_' + '<?= h($tabId) ?>' + '(' + totalPages + ')" ' + (currentPage >= totalPages ? 'disabled' : '') + '>Last</button>' +
            '</div>' +
            '</div>';
        
        if (existingPagination) {
            existingPagination.outerHTML = paginationHTML;
        } else {
            var tableWrapper = container.querySelector('.static-table-wrapper');
            if (tableWrapper) {
                tableWrapper.insertAdjacentHTML('afterend', paginationHTML);
    
    // Make function globally accessible for pagination buttons
    window['performAjaxSearch_' + '<?= h($tabId) ?>'] = performAjaxSearch;
    
    // Attach AJAX event listeners
    filterInputs.forEach(function(input) {
        input.addEventListener('keyup', function() { performAjaxSearch(1); });
        input.addEventListener('paste', function() { performAjaxSearch(1); });
    });
    
    filterOperators.forEach(function(select) {
        select.addEventListener('change', function() { performAjaxSearch(1); });
    });
    
    console.log('âœ… Server-side AJAX search with pagination initialized for <?= h($tabId) ?>');
    
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
                rowData.row.style.display = visible ? '' : 'none';
                if (visible) visibleCount++;
            });
            
            var infoDiv = container.querySelector('.static-info');
            if (infoDiv && hasFilters) {
                infoDiv.innerHTML = '<strong style="color:#007bff;">Filtered:</strong> ' + visibleCount + ' of <?= $totalRecords ?> records';
        }, 300);
    
    filterInputs.forEach(function(input) {
        input.addEventListener('keyup', applyFilters);
        input.addEventListener('paste', applyFilters);
    });
    
    filterOperators.forEach(function(select) {
        select.addEventListener('change', applyFilters);
    });
    
    console.log('âœ… Client-side filtering initialized for <?= h($tabId) ?>');
    <?php endif; ?>
    
})();
</script>



