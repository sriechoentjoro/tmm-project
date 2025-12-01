<%
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         2.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 *
 * Enhanced Index Template with Filters, Scroll, Export
 * CUSTOM TEMPLATE VERSION: 2025-11-14-FALLBACK-FK-DETECTION
 */
use Cake\Utility\Inflector;

// Detect image fields for thumbnail display
$imageFields = [];
foreach ($fields as $field) {
    if (preg_match('/(image|photo|foto|gambar|picture|img)/i', $field)) {
        $imageFields[] = $field;
$firstImageField = !empty($imageFields) ? $imageFields[0] : null;

$fields = collection($fields)
    ->filter(function($field) use ($schema, $imageFields) {
        // Exclude binary and text fields
        if (in_array($schema->getColumnType($field), ['binary', 'text'])) {
            return false;
        // If we have image thumbnail, exclude image URL fields from columns
        if (!empty($imageFields) && in_array($field, $imageFields)) {
            return false;
        return true;
    })
    ->take(10);
%><!-- Page Header -->
<div class="index-header" style="margin-bottom: 20px;">
    <!-- Title Row -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 6px 8px; font-size: 18px; color: #24292f; background: transparent; border: none; text-decoration: none; margin: 0;">
                <i class="fas fa-bars"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#" onclick="return false;">
                    <?= $this->Html->link(
                        '<i class="fas fa-list"></i> ' . __('List <%= $pluralHumanName %>'),
                        ['action' => 'index'],
                        ['class' => 'dropdown-item', 'escape' => false]
                    ) ?>
                </a>
                <a class="dropdown-item" href="#" onclick="return false;">
                    <?= $this->Html->link(
                        '<i class="fas fa-plus"></i> ' . __('New <%= $singularHumanName %>'),
                        ['action' => 'add'],
                        ['class' => 'dropdown-item', 'escape' => false]
                    ) ?>
                </a>
<%
    if (!empty($associations['BelongsTo'])):
        foreach ($associations['BelongsTo'] as $alias => $details):
%>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" onclick="return false;">
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List <%= $alias %>'),
                    ['controller' => '<%= $details['controller'] %>', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
            </a>
            <a class="dropdown-item" href="#" onclick="return false;">
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New <%= Cake\Utility\Inflector::humanize(Cake\Utility\Inflector::underscore(Cake\Utility\Inflector::singularize($alias))) %>'),
                    ['controller' => '<%= $details['controller'] %>', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
            </a>
<%
        endforeach;
    endif;
    if (!empty($associations['BelongsToMany'])):
        foreach ($associations['BelongsToMany'] as $alias => $details):
%>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" onclick="return false;">
                <?= $this->Html->link(
                    '<i class="fas fa-list"></i> ' . __('List <%= $alias %>'),
                    ['controller' => '<%= $details['controller'] %>', 'action' => 'index'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
            </a>
            <a class="dropdown-item" href="#" onclick="return false;">
                <?= $this->Html->link(
                    '<i class="fas fa-plus"></i> ' . __('New <%= Cake\Utility\Inflector::humanize(Cake\Utility\Inflector::underscore(Cake\Utility\Inflector::singularize($alias))) %>'),
                    ['controller' => '<%= $details['controller'] %>', 'action' => 'add'],
                    ['class' => 'dropdown-item', 'escape' => false]
                ) ?>
            </a>
<%
        endforeach;
    endif;
%>
        </div>
        
        <h2 style="margin: 0; display: inline-block;"><?= __('<%= $pluralHumanName %>') ?></h2>
    </div>
        
        <div style="display: flex; align-items: center; gap: 10px;">
            <?= $this->Html->link(
                '<i class="fas fa-plus"></i> ' . __('Add New'),
                ['action' => 'add'],
                ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Add New Record']
            ) ?>
        </div>
    </div>
    
    <!-- Buttons Row -->
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

<?php /* Table Info Bar - Data Counter
<div class="table-info-bar">
    <span class="table-count">
        <i class="fas fa-database"></i> 
         <?= $this->Paginator->counter(['format' => '{{count}} records']) ?>
    </span>
</div>
*/ ?>

<!-- Table Container with Horizontal Scroll -->
<div class="table-container">
    <div class="scroll-hint" style="margin:0 auto; color: #6c757d;"><i class="fas fa-arrows-alt-h"></i> Scroll horizontally to see more columns</div>
    <div class="table-scroll-wrapper">
        <table class="table table-striped table-hover no-auto-filter" data-ajax-filter="true">
            <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                <tr>
                    <th class="actions-column-header" style="width: 80px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); border: none; position: sticky; left: 0; z-index: 10;">Actions</th>
<%
    foreach ($fields as $field):
        if ($firstImageField && $field === $primaryKey[0]):
%>
                    <th style="text-align: center;"><?= __('Thumbnail') ?></th>
<%
        endif;
        if (isset($schema)) {
            $columnType = $schema->getColumnType($field);
        
        // Check if it's a foreign key and use association alias as label
        $headerLabel = $field;
        if (!empty($associations['BelongsTo'])) {
            foreach ($associations['BelongsTo'] as $alias => $details) {
                if ($field === $details['foreignKey']) {
                    $headerLabel = $alias;
                    break;
%>
                    <th><?= $this->Paginator->sort('<%= $field %>', '<%= Inflector::humanize($headerLabel) %>') ?></th>
<%
    endforeach;
%>
                </tr>
                <!-- Combined Operator + Filter Row -->
                <tr class="filter-row" style="background-color: #f8f9fa;">
                    <th class="actions-column-header" style="width: 80px; padding: 4px; background: #f8f9fa; border: none; position: sticky; left: 0; z-index: 10;"></th>
<%
    $columnIndex = 0;
    foreach ($fields as $field):
        if ($firstImageField && $field === $primaryKey[0]):
%>
                    <th style="padding: 4px;"></th>
<%
        endif;
        $columnType = isset($schema) ? $schema->getColumnType($field) : 'string';
        $filterType = 'text';
        
        // Humanize field name for placeholder
        $fieldLabel = Inflector::humanize($field);
        
        // Check if it's a foreign key - use association name for filter, not ID
        $isForeignKey = false;
        $associationName = '';
        $associationVarName = '';
        
        // Method 1: Check associations array (may not work in CakePHP 3.9 bake)
        if (!empty($associations['BelongsTo'])) {
            foreach ($associations['BelongsTo'] as $alias => $details) {
                if ($field === $details['foreignKey']) {
                    $isForeignKey = true;
                    // Use singular form of association name
                    $associationName = strtolower(Inflector::singularize($alias));
                    $associationVarName = strtolower($alias); // e.g., 'personnels'
                    $fieldLabel = Inflector::humanize($alias);
                    break;
        
        // Method 2: Fallback - detect foreign keys by naming convention (*_id pattern)
        if (!$isForeignKey && preg_match('/^(.+)_id$/', $field, $matches)) {
            // Field ends with _id, likely a foreign key
            $isForeignKey = true;
            // Extract base name (e.g., personnel_id -> personnel -> personnels)
            $baseName = $matches[1];
            $pluralName = Inflector::pluralize($baseName); // personnels, roles, etc
            $associationName = $pluralName;
            $associationVarName = strtolower($pluralName); // personnels, roles (for variable name)
            $fieldLabel = Inflector::humanize($pluralName); // Personnels, Roles
        
        // Different placeholder for foreign keys vs regular fields
        if ($isForeignKey) {
            $placeholder = $fieldLabel . ' name...';
        } else {
            $placeholder = $fieldLabel . '...';
        
        // Detect image/file fields - no filter
        $isFileField = (strpos($field, 'image') !== false || 
                       strpos($field, 'photo') !== false || 
                       strpos($field, 'file') !== false || 
                       strpos($field, 'attachment') !== false ||
                       strpos($field, 'document') !== false);
        
        if ($isFileField):
%>
                    <th class="filter-file" style="padding: 4px;"><i class="fas fa-ban text-muted" title="No filter for files"></i></th>
<%
        else:
            // For foreign keys, use dropdown filter with association data (NO OPERATOR NEEDED)
            if ($isForeignKey) {
                $filterType = 'select';
                $filterColumn = $field; // Use actual field name (e.g., personnel_id)
                // $associationVarName already set above (e.g., 'personnels', 'roles')
%>
                    <th style="padding: 4px;">
                        <select class="filter-input form-control form-control-sm" data-column="<%= $filterColumn %>" data-type="<%= $filterType %>" style="font-size: 0.85rem; padding: 4px;">
                            <option value="">All <%= $fieldLabel %></option>
                            <?php foreach ($<%= $associationVarName %> as $id => $title): ?>
                                <option value="<?= $id ?>"><?= h($title) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </th>
<%
            } else {
                $filterColumn = $field;
                if ($columnType === 'integer' || $columnType === 'biginteger') {
                    $filterType = 'number';
                    $placeholder = $fieldLabel . '...';
%>
                    <th style="padding: 4px;">
                        <select class="filter-operator form-control form-control-sm" data-column="<%= $filterColumn %>" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                            <option value="=">=</option>
                            <option value="!=">!=</option>
                            <option value="<">&lt;</option>
                            <option value=">">&gt;</option>
                            <option value="<=">â‰¤</option>
                            <option value=">=">â‰¥</option>
                            <option value="between">Between</option>
                        </select>
                        <input type="<%= $filterType %>" class="filter-input form-control form-control-sm" placeholder="<%= $placeholder %>" data-column="<%= $filterColumn %>" data-type="<%= $filterType %>" style="font-size: 0.85rem;">
                        <input type="<%= $filterType %>" class="filter-input-range form-control form-control-sm" placeholder="To..." data-column="<%= $filterColumn %>" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                    </th>
<%
                } elseif ($columnType === 'date' || $columnType === 'datetime' || $columnType === 'timestamp') {
                    $filterType = 'date';
                    $placeholder = '';
%>
                    <th style="padding: 4px;">
                        <select class="filter-operator form-control form-control-sm" data-column="<%= $filterColumn %>" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                            <option value="=">=</option>
                            <option value="!=">!=</option>
                            <option value="<">&lt;</option>
                            <option value=">">&gt;</option>
                            <option value="<=">â‰¤</option>
                            <option value=">=">â‰¥</option>
                            <option value="between">Between</option>
                        </select>
                        <input type="<%= $filterType %>" class="filter-input form-control form-control-sm" data-column="<%= $filterColumn %>" data-type="<%= $filterType %>" style="font-size: 0.85rem;">
                        <input type="<%= $filterType %>" class="filter-input-range form-control form-control-sm" data-column="<%= $filterColumn %>" style="display:none; margin-top: 2px; font-size: 0.85rem;">
                    </th>
<%
                } else {
                    // Text fields with LIKE operators
%>
                    <th style="padding: 4px;">
                        <select class="filter-operator form-control form-control-sm" data-column="<%= $filterColumn %>" style="font-size: 0.75rem; padding: 2px 4px; margin-bottom: 2px;">
                            <option value="like">LIKE</option>
                            <option value="not_like">NOT LIKE</option>
                            <option value="=">=</option>
                            <option value="!=">!=</option>
                            <option value="starts_with">Starts With</option>
                            <option value="ends_with">Ends With</option>
                            <option value="contains">Contains</option>
                        </select>
                        <input type="<%= $filterType %>" class="filter-input form-control form-control-sm" placeholder="<%= $placeholder %>" data-column="<%= $filterColumn %>" data-type="<%= $filterType %>" style="font-size: 0.85rem;">
                    </th>
<%
        endif;
    endforeach;
%>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php foreach ($<%= $pluralVar %> as $<%= $singularVar %>): ?>
                <tr class="table-row-with-actions">
                    <td class="actions actions-column" style="padding: 8px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; overflow: visible;">
                        <div class="action-buttons-hover" style="display: flex !important; gap: 5px; width: 70px; min-width: 70px; visibility: visible !important;">
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $<%= $singularVar %>-><%= $primaryKey[0] %>],
                                ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit'), 'style' => 'display: inline-flex !important;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $<%= $singularVar %>-><%= $primaryKey[0] %>],
                                ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View'), 'style' => 'display: inline-flex !important;']
                            ) ?>
                        </div>
                    </td>
<%
    foreach ($fields as $field):
        if ($firstImageField && $field === $primaryKey[0]):
%>
                    <td class="thumbnail-cell" style="text-align: center; vertical-align: middle;">
                        <?php if (!empty($<%= $singularVar %>-><%= $firstImageField %>)): ?>
                            <img src="<?= $this->Url->build('/' . h($<%= $singularVar %>-><%= $firstImageField %>)) ?>" 
                                 alt="<?= h(isset($<%= $singularVar %>->title) ? $<%= $singularVar %>->title : 'Thumbnail') ?>" 
                                 class="table-thumbnail"
                                 onerror="this.style.display='none'">
                        <?php else: ?>
                            <span class="no-thumbnail"><i class="fas fa-image"></i></span>
                        <?php endif; ?>
                    </td>
<%
        endif;
        $isKey = false;
        if (!empty($associations['BelongsTo'])) {
            foreach ($associations['BelongsTo'] as $alias => $details) {
                if ($field === $details['foreignKey']) {
                    $isKey = true;
%>
                    <td><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></td>
<%
                    break;
        if ($isKey !== true) {
            // Check if it's a file/image field
            $isFileField = (strpos($field, 'image') !== false || 
                           strpos($field, 'photo') !== false || 
                           strpos($field, 'file') !== false || 
                           strpos($field, 'attachment') !== false ||
                           strpos($field, 'document') !== false);
            
            if ($isFileField):
%>
                    <td class="file-cell">
                        <?php if (!empty($<%= $singularVar %>-><%= $field %>)): ?>
                            <?php
                            $fileUrl = $<%= $singularVar %>-><%= $field %>;
                            $extension = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                            
                            // Icon based on extension
                            $iconClass = 'fa-file';
                            $iconColor = '#6c757d';
                            if ($isImage) {
                                $iconClass = 'fa-file-image';
                                $iconColor = '#28a745';
                            } elseif (in_array($extension, ['pdf'])) {
                                $iconClass = 'fa-file-pdf';
                                $iconColor = '#dc3545';
                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                $iconClass = 'fa-file-word';
                                $iconColor = '#2b579a';
                            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                $iconClass = 'fa-file-excel';
                                $iconColor = '#217346';
                            } elseif (in_array($extension, ['zip', 'rar', '7z'])) {
                                $iconClass = 'fa-file-archive';
                                $iconColor = '#fd7e14';
                            ?>
                            <a href="javascript:void(0)" 
                               onclick="previewFile('<?= $this->Url->build('/' . $fileUrl) ?>', '<?= $extension ?>', <?= $isImage ? 'true' : 'false' ?>)"
                               title="Click to preview"
                               style="color: <?= $iconColor ?>; font-size: 1.5rem;">
                                <i class="fas <?= $iconClass ?>"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-muted"><i class="fas fa-minus"></i></span>
                        <?php endif; ?>
                    </td>
<%
            endif; // Close if ($isFileField)
            
            if (!$isFileField && isset($schema) && !in_array($schema->getColumnType($field), ['integer', 'biginteger', 'decimal', 'float'])):
%>
                    <td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
<%
            elseif (!$isFileField):
%>
                    <td><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>
<%
            endif;
    endforeach;
%>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="paginator" role="navigation" aria-label="Pagination" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
    <ul class="pagination" role="group" style="margin-bottom: 0;">
        <?= $this->Paginator->first('<i class="fas fa-angle-double-left"></i>', ['escape' => false]) ?>
        <?= $this->Paginator->prev('<i class="fas fa-angle-left"></i> ' . __('Previous'), ['escape' => false]) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('Next') . ' <i class="fas fa-angle-right"></i>', ['escape' => false]) ?>
        <?= $this->Paginator->last('<i class="fas fa-angle-double-right"></i>', ['escape' => false]) ?>
    </ul>
    <p class="pagination-info" style="margin-bottom: 0; color: #6c757d; font-size: 0.9rem;">
        <?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?>
    </p>
</div>

<!-- Bootstrap Bundle with Popper for Dropdown -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Filter Search Script -->
<script>
let filterTimeout;

// Handle operator change to show/hide range input for "between"
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.filter-operator').forEach(function(select) {
        select.addEventListener('change', function() {
            const column = this.getAttribute('data-column');
            const operator = this.value;
            const filterInput = document.querySelector('.filter-input[data-column="' + column + '"]');
            const rangeInput = document.querySelector('.filter-input-range[data-column="' + column + '"]');
            
            if (operator === 'between' && rangeInput) {
                rangeInput.style.display = 'block';
                rangeInput.style.marginTop = '2px';
                
                // Update placeholder based on data type
                const dataType = filterInput.getAttribute('data-type');
                if (dataType === 'number') {
                    filterInput.placeholder = 'From...';
                    rangeInput.placeholder = 'To...';
                } else if (dataType === 'date') {
                    filterInput.placeholder = 'Start Date';
                    rangeInput.placeholder = 'End Date';
                } else {
                    filterInput.placeholder = 'From...';
                    rangeInput.placeholder = 'To...';
            } else if (rangeInput) {
                rangeInput.style.display = 'none';
                rangeInput.value = '';
                
                // Reset placeholder to original
                const dataType = filterInput.getAttribute('data-type');
                if (dataType === 'number') {
                    const fieldLabel = column.replace(/_/g, ' ');
                    filterInput.placeholder = fieldLabel.charAt(0).toUpperCase() + fieldLabel.slice(1) + '...';
                } else if (dataType === 'text') {
                    const columnLabel = column.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                    filterInput.placeholder = columnLabel + '...';
                } else if (dataType === 'date') {
                    filterInput.placeholder = '';
        });
    });
    
    // Add change event listeners to all filter inputs (including dropdowns)
    document.querySelectorAll('.filter-input').forEach(function(input) {
        if (input.tagName === 'SELECT') {
            // For dropdowns, trigger filter immediately on change
            input.addEventListener('change', function() {
                filterSearch();
            });
        } else {
            // For text/number inputs, use keyup with debounce
            input.addEventListener('keyup', function() {
                filterSearch();
            });
    });
    
    // Add event listeners to range inputs for "between" operator
    document.querySelectorAll('.filter-input-range').forEach(function(input) {
        input.addEventListener('keyup', function() {
            filterSearch();
        });
        input.addEventListener('change', function() {
            filterSearch();
        });
    });
});

function filterSearch() {
    clearTimeout(filterTimeout);
    
    filterTimeout = setTimeout(function() {
        // Collect filter values with operators
        const filters = {};
        document.querySelectorAll('.filter-input').forEach(function(input) {
            const column = input.getAttribute('data-column');
            const dataType = input.getAttribute('data-type');
            const value = (input.tagName === 'SELECT') ? input.value : input.value.trim();
            
            if (value) {
                // For select dropdowns (foreign keys), use exact match
                if (dataType === 'select') {
                    filters[column] = {
                        operator: '=',
                        value: value
                    };
                } else {
                    const operatorSelect = document.querySelector('.filter-operator[data-column="' + column + '"]');
                    const operator = operatorSelect ? operatorSelect.value : 'like';
                    
                    // For "between", collect range value
                    if (operator === 'between') {
                        const rangeInput = document.querySelector('.filter-input-range[data-column="' + column + '"]');
                        const rangeValue = rangeInput ? rangeInput.value.trim() : '';
                        if (rangeValue) {
                            filters[column] = {
                                operator: operator,
                                value: value,
                                range: rangeValue
                            };
                        } else {
                            filters[column] = {
                                operator: '>=',
                                value: value
                            };
                    } else {
                        filters[column] = {
                            operator: operator,
                            value: value
                        };
        });
        
        // Build query string
        const params = new URLSearchParams();
        Object.keys(filters).forEach(function(key) {
            params.append('filter[' + key + '][operator]', filters[key].operator);
            params.append('filter[' + key + '][value]', filters[key].value);
            if (filters[key].range) {
                params.append('filter[' + key + '][range]', filters[key].range);
        });
        
        const url = '<?= $this->Url->build(['action' => 'filter']) ?>?' + params.toString();
        
        // Show loading
        const tbody = document.getElementById('table-body');
        tbody.innerHTML = '<tr><td colspan="100" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
        
        // AJAX request
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            return response.text();
        })
        .then(function(text) {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    tbody.innerHTML = data.html;
                    console.log('Filtered results:', data.count);
                } else {
                    tbody.innerHTML = '<tr><td colspan="100" class="text-center text-danger">Error loading data</td></tr>';
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response text:', text.substring(0, 500));
                tbody.innerHTML = '<tr><td colspan="100" class="text-center text-danger">Server returned invalid response. Check console for details.</td></tr>';
        })
        .catch(function(error) {
            console.error('Filter error:', error);
            tbody.innerHTML = '<tr><td colspan="100" class="text-center text-danger">Error: ' + error.message + '</td></tr>';
        });
    }, 500); // Debounce 500ms

function clearFilters() {
    document.querySelectorAll('.filter-input, .filter-input-range').forEach(function(input) {
        input.value = '';
    });
    document.querySelectorAll('.filter-operator').forEach(function(select) {
        select.selectedIndex = 0;
    });
    // Don't call filterSearch - just reload page
    window.location.reload();

// Enable drag-to-scroll on page load
window.addEventListener('DOMContentLoaded', function() {
    // Don't auto-load AJAX filter - let PHP render the initial data
    // filterSearch(); // Commented out - causes blank table on load
    
    // Enable drag-to-scroll on table
    const scrollWrapper = document.querySelector('.table-scroll-wrapper');
    let isDown = false;
    let startX;
    let scrollLeft;
    
    scrollWrapper.addEventListener('mousedown', (e) => {
        // Don't enable drag if clicking on input, button, or link
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('a')) {
            return;
        isDown = true;
        scrollWrapper.style.cursor = 'grabbing';
        scrollWrapper.style.userSelect = 'none';
        startX = e.pageX - scrollWrapper.offsetLeft;
        scrollLeft = scrollWrapper.scrollLeft;
    });
    
    scrollWrapper.addEventListener('mouseleave', () => {
        isDown = false;
        scrollWrapper.style.cursor = 'grab';
    });
    
    scrollWrapper.addEventListener('mouseup', () => {
        isDown = false;
        scrollWrapper.style.cursor = 'grab';
    });
    
    scrollWrapper.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - scrollWrapper.offsetLeft;
        const walk = (x - startX) * 2; // Scroll speed multiplier
        scrollWrapper.scrollLeft = scrollLeft - walk;
    });
    
    // Set initial cursor
    scrollWrapper.style.cursor = 'grab';
});

// File Preview Function
function previewFile(url, extension, isImage) {
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'filePreviewModal';
    modal.setAttribute('tabindex', '-1');
    
    let content = '';
    if (isImage) {
        content = `<img src="${url}" class="img-fluid" alt="Preview" style="max-width: 100%; max-height: 80vh;">`;
    } else if (extension === 'pdf') {
        content = `<iframe src="${url}" style="width: 100%; height: 80vh; border: none;"></iframe>`;
    } else {
        content = `
            <div class="text-center p-5">
                <i class="fas fa-file fa-5x mb-3 text-primary"></i>
                <h5>File: ${url.split('/').pop()}</h5>
                <p class="text-muted">Extension: .${extension}</p>
                <a href="${url}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Open in New Tab
                </a>
                <a href="${url}" download class="btn btn-success">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        `;
    
    modal.innerHTML = `
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye"></i> File Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    ${content}
                </div>
                <div class="modal-footer">
                    <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Open
                    </a>
                    <a href="${url}" download class="btn btn-sm btn-outline-success">
                        <i class="fas fa-download"></i> Download
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Remove modal after hide
    modal.addEventListener('hidden.bs.modal', function() {
        modal.remove();
    });

// Export table to CSV
function exportTableToCSV(table) {
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length - 1; j++) { // Exclude last column (Actions)
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/\s+/g, ' ').trim();
            data = data.replace(/"/g, '""'); // Escape quotes
            row.push('"' + data + '"');
        
        csv.push(row.join(','));
    
    // Download CSV
    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = 'export_' + new Date().getTime() + '.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);

// Print only the table
function printTable() {
    const table = document.querySelector('.table-striped');
    if (!table) return;
    
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Table</title>');
    printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: "Mulish", Arial, sans-serif; margin: 20px; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; }');
    printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
    printWindow.document.write('th { background-color: #667eea; color: white; font-weight: bold; }');
    printWindow.document.write('tr:nth-child(even) { background-color: #f9f9f9; }');
    printWindow.document.write('.actions { display: none; }'); // Hide actions column
    printWindow.document.write('.filter-row { display: none; }'); // Hide filter row
    printWindow.document.write('img { max-width: 50px; max-height: 50px; }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h2><?= __("<%= $pluralHumanName %>") ?></h2>');
    printWindow.document.write(table.outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
</script>

<style>
/* Smaller placeholder text */
.filter-input::placeholder {
    font-size: 0.8em;
    opacity: 0.7;

.filter-input::-webkit-input-placeholder {
    font-size: 0.8em;
    opacity: 0.7;

.filter-input::-moz-placeholder {
    font-size: 0.8em;
    opacity: 0.7;

.filter-input:-ms-input-placeholder {
    font-size: 0.8em;
    opacity: 0.7;

/* Hover-only action buttons - always visible on hover */
.action-buttons-hover {
    opacity: 1 !important;
    transition: opacity 0.2s ease-in-out;
    display: flex;
    gap: 2px;

.table-row-with-actions:hover .action-buttons-hover {
    opacity: 1 !important;
    transform: scale(1.05);

/* Sticky action column with proper background */
.actions-column {
    background-color: #fff !important;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    position: sticky !important;
    left: 0 !important;
    z-index: 5 !important;

.actions-column-header {
    position: sticky !important;
    left: 0 !important;
    z-index: 10 !important;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);

.table-striped tbody tr:nth-of-type(odd) .actions-column {
    background-color: rgba(102, 126, 234, 0.05) !important;

.table-striped tbody tr:nth-of-type(even) .actions-column {
    background-color: #fff !important;

.table-row-with-actions:hover .actions-column {
    background-color: rgba(102, 126, 234, 0.08) !important;

/* Action buttons styled like export buttons */
.btn-action-icon {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 8px;
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    border: 1px solid rgba(102, 126, 234, 0.3);
    border-radius: 6px;
    font-size: 11px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    margin: 0 2px;

.btn-action-icon:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-color: #667eea;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    text-decoration: none;
</style>

<script>
// Prevent auto-filter initialization - this table has manual filters
window.skipAutoFilter = true;

// Client-side table filtering with operator support
document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('table');
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr.table-row-with-actions'));
    const filterInputs = document.querySelectorAll('.filter-input');
    const filterOperators = document.querySelectorAll('.filter-operator');
    const rangeInputs = document.querySelectorAll('.filter-input-range');
    
    // Function to apply filter with operator
    function applyFilter(cellText, filterValue, operator, filterValue2) {
        const cellVal = cellText.trim().toLowerCase();
        const filterVal = filterValue.trim().toLowerCase();
        
        if (filterVal === '') return true;
        
        // Try to parse as numbers for numeric comparisons
        const cellNum = parseFloat(cellVal.replace(/[^0-9.-]/g, ''));
        const filterNum = parseFloat(filterVal);
        const isNumeric = !isNaN(cellNum) && !isNaN(filterNum);
        
        switch(operator) {
            case '=':
            case 'equals':
                return isNumeric ? cellNum === filterNum : cellVal === filterVal;
            case '!=':
                return isNumeric ? cellNum !== filterNum : cellVal !== filterVal;
            case '<':
            case 'lt':
                return isNumeric ? cellNum < filterNum : cellVal < filterVal;
            case '>':
            case 'gt':
                return isNumeric ? cellNum > filterNum : cellVal > filterVal;
            case '<=':
            case 'lte':
                return isNumeric ? cellNum <= filterNum : cellVal <= filterVal;
            case '>=':
            case 'gte':
                return isNumeric ? cellNum >= filterNum : cellVal >= filterVal;
            case 'between':
                if (filterValue2 && isNumeric) {
                    const filterNum2 = parseFloat(filterValue2);
                    return cellNum >= filterNum && cellNum <= filterNum2;
                return cellVal.includes(filterVal);
            case 'like':
            case 'contains':
                return cellVal.includes(filterVal);
            case 'not_like':
                return !cellVal.includes(filterVal);
            case 'starts_with':
            case 'starts':
                return cellVal.startsWith(filterVal);
            case 'ends_with':
            case 'ends':
                return cellVal.endsWith(filterVal);
            default:
                return cellVal.includes(filterVal);
    
    // Function to get column index from data-column attribute
    function getColumnIndex(columnName) {
        const headers = Array.from(table.querySelectorAll('thead tr:first-child th'));
        for (let i = 0; i < headers.length; i++) {
            const headerText = headers[i].textContent.trim().toLowerCase();
            const columnNameLower = columnName.replace(/_/g, ' ').toLowerCase();
            if (headerText.includes(columnNameLower) || headerText === columnNameLower) {
                return i;
        return -1;
    
    // Main filter function
    function filterTable() {
        rows.forEach(row => {
            let shouldShow = true;
            const cells = row.querySelectorAll('td');
            
            // Check each filter input
            filterInputs.forEach((input, idx) => {
                const filterVal = input.value.trim();
                if (filterVal === '') return;
                
                const columnName = input.getAttribute('data-column');
                const columnIndex = getColumnIndex(columnName);
                
                if (columnIndex === -1 || !cells[columnIndex]) return;
                
                // Find corresponding operator
                let operator = 'like'; // default
                filterOperators.forEach(op => {
                    if (op.getAttribute('data-column') === columnName) {
                        operator = op.value;
                });
                
                // Find corresponding range input for "between" operator
                let filterVal2 = '';
                if (operator === 'between') {
                    rangeInputs.forEach(rangeInput => {
                        if (rangeInput.getAttribute('data-column') === columnName) {
                            filterVal2 = rangeInput.value.trim();
                    });
                
                const cellText = cells[columnIndex].textContent;
                if (!applyFilter(cellText, filterVal, operator, filterVal2)) {
                    shouldShow = false;
            });
            
            row.style.display = shouldShow ? '' : 'none';
        });
        
        // Update row count display if exists
        const visibleCount = rows.filter(row => row.style.display !== 'none').length;
        console.log(`Showing ${visibleCount} of ${rows.length} rows`);
    
    // Attach event listeners
    filterInputs.forEach(input => {
        input.addEventListener('input', filterTable);
        input.addEventListener('change', filterTable);
    });
    
    filterOperators.forEach(select => {
        select.addEventListener('change', function() {
            const columnName = this.getAttribute('data-column');
            // Show/hide range input for "between" operator
            rangeInputs.forEach(rangeInput => {
                if (rangeInput.getAttribute('data-column') === columnName) {
                    rangeInput.style.display = this.value === 'between' ? 'block' : 'none';
            });
            filterTable();
        });
    });
    
    rangeInputs.forEach(input => {
        input.addEventListener('input', filterTable);
        input.addEventListener('change', filterTable);
    });
});
</script>

<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
