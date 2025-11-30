# AJAX Tabs Implementation Guide
## Complete Step-by-Step Guide for Adding AJAX Lazy-Loading Tabs to View Templates

### Overview
This guide explains how to add AJAX lazy-loading tabs to view templates with existing GitHub-style tab navigation. The implementation allows displaying hasMany associations as separate tabs that load data only when clicked.

---

## Prerequisites

1. **Controller must have `getRelated*()` method** for AJAX endpoint
2. **View template must have** `view-tabs-nav` and `view-tabs-content` structure
3. **Entity variable must be correct** (e.g., `$acceptanceOrganization`, not `$entity`)

---

## Step-by-Step Implementation

### Step 1: Verify/Create AJAX Endpoint in Controller

**File:** `src/Controller/YourController.php`

```php
/**
 * AJAX method to get related records
 */
public function getRelatedAssociation()
{
    $this->autoRender = false;
    $this->response = $this->response->withType('application/json')
        ->withCharset('UTF-8');
    
    $this->log('getRelatedAssociation called', 'debug');
    $this->log('Query params: ' . json_encode($this->request->getQueryParams()), 'debug');
    
    try {
        // Get filter parameters (camelCase!)
        $filterField = $this->request->getQuery('filterField');
        $filterValue = $this->request->getQuery('filterValue');
        $page = (int)$this->request->getQuery('page', 1);
        $limit = min((int)$this->request->getQuery('limit', 50), 100);
        $filtersJson = $this->request->getQuery('filters');
        
        $this->log("Filter: $filterField = $filterValue", 'debug');
        
        // Parse column filters
        $columnFilters = $filtersJson ? json_decode($filtersJson, true) : [];
        
        // Build query
        $query = $this->YourTable->AssociationTable->find()
            ->where([$filterField => $filterValue])
            ->contain(['YourTable'])  // If needed
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->order(['AssociationTable.id' => 'DESC']);
        
        // Apply column filters
        if ($columnFilters && is_array($columnFilters)) {
            foreach ($columnFilters as $column => $filter) {
                if (empty($filter['value'])) continue;
                
                $operator = isset($filter['operator']) ? $filter['operator'] : 'contains';
                $value = $filter['value'];
                
                // Prefix column with table name
                if (strpos($column, '.') === false) {
                    $column = 'AssociationTable.' . $column;
                }
                
                switch ($operator) {
                    case 'equals':
                        $query->where([$column => $value]);
                        break;
                    case 'contains':
                        $query->where([$column . ' LIKE' => '%' . $value . '%']);
                        break;
                    case 'starts_with':
                        $query->where([$column . ' LIKE' => $value . '%']);
                        break;
                    case 'ends_with':
                        $query->where([$column . ' LIKE' => '%' . $value]);
                        break;
                    case 'greater_than':
                        $query->where([$column . ' >' => $value]);
                        break;
                    case 'less_than':
                        $query->where([$column . ' <' => $value]);
                        break;
                    case 'not_empty':
                        $query->where([$column . ' IS NOT NULL']);
                        break;
                }
            }
        }
        
        // Get total count
        $total = $query->count();
        
        // Execute query
        $results = $query->toArray();
        
        // Check file existence for image/photo/file fields
        foreach ($results as $result) {
            foreach ($result->toArray() as $field => $value) {
                if (preg_match('/(image|photo|file|document)/i', $field) && !empty($value)) {
                    $fullPath = WWW_ROOT . $value;
                    $result->{$field . '_exists'} = file_exists($fullPath);
                }
            }
        }
        
        // Build response
        $response = [
            'success' => true,
            'data' => $results,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ];
        
        return $this->response->withStringBody(json_encode($response));
        
    } catch (\Exception $e) {
        $this->log('Error in getRelatedAssociation: ' . $e->getMessage(), 'error');
        $this->log('Stack trace: ' . $e->getTraceAsString(), 'error');
        
        return $this->response->withStringBody(json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]));
    }
}
```

**Important Notes:**
- Parameter names MUST be **camelCase**: `filterField`, `filterValue` (NOT `filter_field`, `filter_value`)
- Use `IS NOT NULL` (NOT `IS NOT => null`) for not_empty operator
- Always check file existence for image/photo fields
- Return proper JSON structure with success/error status

---

### Step 2: Add Tab Link to Navigation

**File:** `src/Template/YourController/view.ctp`

Find the `<ul class="view-tabs-nav">` section and add a new tab link **before** `</ul>`:

```php
<ul class="view-tabs-nav" role="tablist">
    <li class="view-tab-item">
        <a href="#tab-home" class="view-tab-link active" data-tab="tab-home">
            <svg class="tab-icon">...</svg>
            <?= __('Detail') ?>
        </a>
    </li>
    
    <!-- ADD THIS: New association tab -->
    <li class="view-tab-item">
        <a href="#tab-association" class="view-tab-link" data-tab="tab-association">
            <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M1.75 0A1.75 1.75 0 000 1.75v12.5C0 15.216.784 16 1.75 16h12.5A1.75 1.75 0 0016 14.25V1.75A1.75 1.75 0 0014.25 0H1.75z"></path>
            </svg>
            <?= __('Association Name') ?>
        </a>
    </li>
</ul>
<!-- End Tabs Nav -->
```

---

### Step 3: Add Tab Pane with AJAX Element

Find the `<div class="view-tabs-content">` section and add new tab pane **before** `</div><!-- End Tab Contents -->`:

```php
<div class="view-tabs-content">
    <!-- Existing Detail Tab -->
    <div id="tab-home" class="view-tab-pane active">
        ...existing content...
    </div>
    
    <!-- ADD THIS: New Association Tab with AJAX -->
    <div id="tab-association" class="view-tab-pane">
        <div id="associationtablename-pane">
        <div class="github-details-card">
            <div class="github-details-header">
                <h3 class="github-details-title">
                    <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.75 0A1.75 1.75 0 000 1.75v12.5C0 15.216.784 16 1.75 16h12.5A1.75 1.75 0 0016 14.25V1.75A1.75 1.75 0 0014.25 0H1.75z"></path>
                    </svg>
                    <?= __('Association Table Name') ?>
                </h3>
            </div>
            <div class="github-details-body">
                <?= $this->element('related_records_table', [
                    'tabId' => 'associationtablename',
                    'title' => __('Association Display Name'),
                    'filterField' => 'foreign_key_field',
                    'filterValue' => $entityVariable->id,
                    'ajaxUrl' => $this->Url->build([
                        'controller' => 'YourController', 
                        'action' => 'getRelatedAssociation'
                    ]),
                    'controller' => 'association-table-name',
                    'columns' => [
                        ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],
                        ['name' => 'field1', 'label' => 'Field 1', 'type' => 'text', 'sortable' => true],
                        ['name' => 'field2', 'label' => 'Field 2', 'type' => 'date', 'sortable' => true],
                        ['name' => 'image_path', 'label' => 'Image', 'type' => 'image', 'sortable' => false]
                    ]
                ]) ?>
            </div>
        </div>
        </div>
    </div>
    <!-- End Association Tab -->
    
</div>
<!-- End Tab Contents -->
```

**Important ID Convention:**
- Tab link `href`: `#tab-association`
- Tab pane `id`: `tab-association`
- Inner wrapper `id`: `associationtablename-pane` (for MutationObserver)
- `tabId` parameter: `associationtablename` (matches inner wrapper without `-pane`)

---

### Step 4: Configure Column Mappings

Column configuration in `related_records_table` element:

```php
'columns' => [
    // Number field
    ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],
    
    // Text field
    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'sortable' => true],
    
    // Date field
    ['name' => 'created_date', 'label' => 'Date', 'type' => 'date', 'sortable' => true],
    
    // Datetime field
    ['name' => 'created', 'label' => 'Created', 'type' => 'datetime', 'sortable' => true],
    
    // Image field (auto-detects _exists field)
    ['name' => 'image_path', 'label' => 'Image', 'type' => 'image', 'sortable' => false],
    
    // File field
    ['name' => 'document_path', 'label' => 'Document', 'type' => 'file', 'sortable' => false]
]
```

**Field Types:**
- `number` - Numeric values
- `text` - String values  
- `date` - Date only (formatted as date)
- `datetime` - Date + time (formatted as datetime)
- `image` - Shows thumbnail with modal preview
- `file` - Shows download link

---

## Real Example: AcceptanceOrganizations

### Controller Method

```php
public function getRelatedStories()
{
    // ... (see Step 1 for full code)
    
    $query = $this->AcceptanceOrganizations->AcceptanceOrganizationStories->find()
        ->where(['acceptance_organization_id' => $filterValue])
        ->contain(['AcceptanceOrganizations'])
        ->limit($limit)
        ->offset(($page - 1) * $limit)
        ->order(['AcceptanceOrganizationStories.id' => 'DESC']);
    
    // ... (rest of code)
}
```

### View Template

**Tab Link:**
```php
<li class="view-tab-item">
    <a href="#tab-stories" class="view-tab-link" data-tab="tab-stories">
        <svg class="tab-icon">...</svg>
        <?= __('Stories') ?>
    </a>
</li>
```

**Tab Pane:**
```php
<div id="tab-stories" class="view-tab-pane">
    <div id="acceptanceorganizationstories-pane">
    <div class="github-details-card">
        <div class="github-details-header">
            <h3 class="github-details-title">
                <?= __('Acceptance Organization Stories') ?>
            </h3>
        </div>
        <div class="github-details-body">
            <?= $this->element('related_records_table', [
                'tabId' => 'acceptanceorganizationstories',
                'title' => __('Stories'),
                'filterField' => 'acceptance_organization_id',
                'filterValue' => $acceptanceOrganization->id,
                'ajaxUrl' => $this->Url->build([
                    'controller' => 'AcceptanceOrganizations', 
                    'action' => 'getRelatedStories'
                ]),
                'controller' => 'acceptance-organization-stories',
                'columns' => [
                    ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'sortable' => true],
                    ['name' => 'date_occurrence', 'label' => 'Date', 'type' => 'date', 'sortable' => true],
                    ['name' => 'problem_classification', 'label' => 'Classification', 'type' => 'text', 'sortable' => true],
                    ['name' => 'problem_contents', 'label' => 'Problem', 'type' => 'text', 'sortable' => true],
                    ['name' => 'problem_solution', 'label' => 'Solution', 'type' => 'text', 'sortable' => true],
                    ['name' => 'image_path', 'label' => 'Image', 'type' => 'image', 'sortable' => false]
                ]
            ]) ?>
        </div>
    </div>
    </div>
</div>
```

---

## Common Issues & Solutions

### 1. "Tab pane NOT found" Error

**Cause:** ID mismatch between `tabId` and actual HTML element ID

**Solution:** 
- Ensure `tabId` parameter matches inner wrapper ID without `-pane`
- Example: `tabId => 'stories'` → HTML: `<div id="stories-pane">`

### 2. "No data available" when data exists

**Causes:**
- Wrong parameter names (filter_field vs filterField)
- Wrong filterField value (column doesn't exist)
- Wrong table name in query

**Solution:**
- Check controller uses `filterField` and `filterValue` (camelCase)
- Verify column name in database: `SHOW COLUMNS FROM table_name`
- Check association in Table file

### 3. SQL Syntax Error

**Common errors:**
- `IS NOT = NULL` → Use `IS NOT NULL` 
- Wrong ORDER BY column → Use existing column like `id`

**Solution:**
- Fix operator in controller: `$query->where([$column . ' IS NOT NULL'])`
- Check column exists: `DESCRIBE table_name`

### 4. Images not showing

**Causes:**
- File doesn't exist on server
- Wrong path in database
- File existence check not working

**Solution:**
- Controller must set `{field}_exists` flag for image fields
- Check file path is relative to webroot: `img/uploads/file.jpg`
- Verify file exists: `file_exists(WWW_ROOT . $path)`

---

## Checklist for New Implementation

- [ ] Controller has `getRelated*()` method
- [ ] Method uses **camelCase** parameters: `filterField`, `filterValue`
- [ ] Method uses `IS NOT NULL` (not `IS NOT => null`)
- [ ] Method checks file existence for image fields
- [ ] Method returns proper JSON structure
- [ ] View template has `view-tabs-nav` structure
- [ ] Tab link added with correct `href="#tab-name"`
- [ ] Tab pane added with correct `id="tab-name"`
- [ ] Inner wrapper has `id="{tabId}-pane"`
- [ ] `tabId` parameter matches inner wrapper (without `-pane`)
- [ ] `filterField` matches foreign key column
- [ ] `filterValue` uses correct entity variable (`$entityVar->id`)
- [ ] `ajaxUrl` points to correct controller/action
- [ ] `columns` array has all visible fields with correct types
- [ ] Cache cleared after changes
- [ ] Tested in browser with console open

---

## Testing Procedure

1. **Clear cache:**
   ```bash
   bin\cake cache clear_all
   ```

2. **Open view page in browser:**
   ```
   http://localhost/project_tmm/your-controller/view/1
   ```

3. **Open browser console (F12)**

4. **Click AJAX tab and check for:**
   - ✅ "🔍 Tab pane found for..."
   - ✅ "✅ Loading data for..."
   - ✅ "📦 Data: {success: true, ...}"
   - ❌ No "❌ Tab pane NOT found..."
   - ❌ No SQL errors
   - ❌ No 404/500 errors

5. **Verify UI:**
   - Tab displays properly
   - Data loads without refresh
   - Filtering works
   - Pagination works
   - Images show thumbnails
   - View links work

---

## Controllers to Implement

Based on existing `getRelated*()` methods:

1. ✅ **AcceptanceOrganizations** - COMPLETED
   - Method: `getRelatedStories()`
   - Association: AcceptanceOrganizationStories

2. ⏳ **ApprenticeOrders** - TODO
   - Method: `getRelated()`
   - Need to identify associations

3. ⏳ **Apprentices** - TODO
   - Method: `getRelated()`
   - Need to identify associations

4. ⏳ **Trainees** - TODO
   - Method: `getRelated()`
   - Need to identify associations

5. ⏳ **CooperativeAssociations** - TODO
   - Method: `getRelated()`
   - Need to identify associations

6. ⏳ **Users** - TODO
   - Method: `getRelated()`
   - Need to identify associations

7. ⏳ **VocationalTrainingInstitutions** - TODO
   - Method: `getRelated()`
   - Need to identify associations

8. ⏳ **MasterAuthorizationRoles** - TODO
   - Method: `getRelated()`
   - Need to identify associations

9. ⏳ **MasterCandidateInterviewResults** - TODO
   - Method: `getRelated()`
   - Need to identify associations

10. ⏳ **MasterCandidateInterviewTypes** - TODO
    - Method: `getRelated()`
    - Need to identify associations

11. ⏳ **MasterJapanPrefectures** - TODO
    - Method: `getRelated()`
    - Need to identify associations

---

## Notes

- Each controller can have multiple AJAX tabs for different associations
- File without BOM is critical - always use UTF8Encoding($false) in PowerShell
- Console logging is helpful during development but should be removed in production
- AJAX tabs lazy-load only when clicked (improves initial page load performance)
- MutationObserver detects tab visibility changes for auto-loading

---

## Reference Files

- Element: `src/Template/Element/related_records_table.ctp`
- Example Controller: `src/Controller/AcceptanceOrganizationsController.php`
- Example View: `src/Template/AcceptanceOrganizations/view.ctp`
- This Guide: `AJAX_TABS_IMPLEMENTATION_GUIDE.md`

---

**Last Updated:** November 16, 2025
**Status:** AcceptanceOrganizations completed and working ✅
