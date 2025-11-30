# View Template Tabs Redesign Requirements

## Date: 2025-11-16

## Current Problem

### Issue 1: Wrong Tab Content Type
**Current Behavior:**
- View template tabs show **DETAIL** of associated record
- Example: In Candidates/view/1, "Vocational Training Institution" tab shows LPK SEMARANG details (address, director, etc.)

**Expected Behavior:**
- View template tabs should show **LIST of RELATED RECORDS**
- Example: In Candidates/view/1, "Vocational Training Institution" tab should show TABLE of all candidates from LPK SEMARANG

### Issue 2: Filter Not Working in Index
**Current Behavior:**
- Index template has filter row (operator + input field)
- Filter does not work - no data filtering happens

**Expected Behavior:**
- Filter should work on all columns
- Special case for image/file fields: Filter should be "File Exists" / "File Not Exists"

## Redesign Specification

### 1. View Template Tabs - Related Data Lists

#### Example: Candidates/view/1 (Candidate from LPK SEMARANG)

**Tab: Vocational Training Institution**
- **Current**: Shows detail of LPK SEMARANG (name, address, director, etc.)
- **Should Be**: Shows TABLE of all candidates from LPK SEMARANG
- **Query**: `SELECT * FROM candidates WHERE vocational_training_institution_id = {current_candidate_lpk_id}`
- **Display**: Full data table with:
  - All candidate columns (ID, Name, Identity Number, etc.)
  - Filter row (operator + input field) - same as index template
  - Pagination
  - Action buttons (View, Edit - no Delete)
  - Export buttons (CSV, Excel, PDF, Print)

**Tab: Acceptance Organization**
- **Should Show**: TABLE of all candidates going to same organization
- **Query**: `SELECT * FROM candidates WHERE acceptance_organization_id = {current_candidate_org_id}`

**Tab: Apprentice Order**
- **Should Show**: TABLE of all candidates in same apprentice order
- **Query**: `SELECT * FROM candidates WHERE apprentice_order_id = {current_candidate_order_id}`

#### Pattern for All Tabs

**BelongsTo Associations:**
- Tab should show: LIST of all records sharing the same parent
- Example: Candidate belongs to LPK → Show all candidates from same LPK
- NOT: Show detail of LPK itself

**HasMany Associations:**
- Tab should show: LIST of child records
- Example: Candidate has many CandidateFamilies → Show table of all families
- This is ALREADY correct in current implementation

### 2. Filter Implementation

#### Index Template Filter
**Requirements:**
- Filter row must be functional
- Each column has:
  1. **Operator dropdown**: Contains (default), Equals, Starts With, Ends With, Greater Than, Less Than, Between, Is NULL, Is NOT NULL
  2. **Input field**: Text input for filter value
- Filter applies to visible data in real-time (client-side)
- Works with pagination

#### Special Filter for File/Image Fields
**Current Display:**
- File/image columns show file viewer icon (PDF, DOC, Image icon, etc.)
- Path stored as string in database

**Filter Options:**
- **File Exists**: Show only rows where file physically exists on server
- **File Not Exists**: Show only rows where file path is empty or file doesn't exist
- **Contains** (filename): Filter by filename/path string

**Implementation:**
- Add `data-file-field="true"` attribute to file/image columns
- Custom filter logic to check file existence
- Use AJAX to verify file existence on server

### 3. Controller Changes Required

#### Current Controller (View Method)
```php
public function view($id = null)
{
    $candidate = $this->Candidates->get($id, [
        'contain' => ['ApprenticeOrders', 'VocationalTrainingInstitutions', ...]
    ]);
    $this->set('candidate', $candidate);
}
```

#### New Controller (View Method)
```php
public function view($id = null)
{
    $candidate = $this->Candidates->get($id, [
        'contain' => ['ApprenticeOrders', 'VocationalTrainingInstitutions', ...]
    ]);
    
    // Load related candidates from same LPK
    $relatedByLpk = [];
    if (!empty($candidate->vocational_training_institution_id)) {
        $relatedByLpk = $this->Candidates->find('all')
            ->where(['vocational_training_institution_id' => $candidate->vocational_training_institution_id])
            ->contain(['ApprenticeOrders', 'VocationalTrainingInstitutions', ...])
            ->limit(100)
            ->toArray();
    }
    
    // Load related candidates from same organization
    $relatedByOrg = [];
    if (!empty($candidate->acceptance_organization_id)) {
        $relatedByOrg = $this->Candidates->find('all')
            ->where(['acceptance_organization_id' => $candidate->acceptance_organization_id])
            ->contain([...])
            ->limit(100)
            ->toArray();
    }
    
    // Load related candidates from same apprentice order
    $relatedByOrder = [];
    if (!empty($candidate->apprentice_order_id)) {
        $relatedByOrder = $this->Candidates->find('all')
            ->where(['apprentice_order_id' => $candidate->apprentice_order_id])
            ->contain([...])
            ->limit(100)
            ->toArray();
    }
    
    $this->set(compact('candidate', 'relatedByLpk', 'relatedByOrg', 'relatedByOrder'));
}
```

### 4. Template Changes Required

#### View Template Tab Content

**Replace This:**
```php
<!-- Detail view of associated record -->
<div class="github-details-card">
    <table class="github-details-table">
        <tr>
            <th>Name</th>
            <td><?= h($candidate->vocational_training_institution->name) ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?= h($candidate->vocational_training_institution->address) ?></td>
        </tr>
    </table>
</div>
```

**With This:**
```php
<!-- Data table of related records -->
<div class="github-table-container">
    <div class="table-actions">
        <!-- Export buttons -->
        <button class="btn-export">CSV</button>
        <button class="btn-export">Excel</button>
        <button class="btn-export">PDF</button>
    </div>
    
    <table class="github-data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Identity Number</th>
                <th>Actions</th>
            </tr>
            <!-- Filter row -->
            <tr class="filter-row">
                <th>
                    <select class="filter-operator">
                        <option>Contains</option>
                        <option>Equals</option>
                    </select>
                    <input type="text" class="filter-input" placeholder="Filter...">
                </th>
                <!-- Repeat for each column -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($relatedByLpk as $related): ?>
            <tr>
                <td><?= h($related->id) ?></td>
                <td><?= h($related->name) ?></td>
                <td><?= h($related->identity_number) ?></td>
                <td>
                    <?= $this->Html->link('View', ['action' => 'view', $related->id]) ?>
                    <?= $this->Html->link('Edit', ['action' => 'edit', $related->id]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

### 5. Implementation Plan

#### Phase 1: Fix Candidates View Tabs
1. Update `CandidatesController::view()` to load related data
2. Replace tab content in `Candidates/view.ctp`:
   - Vocational Training Institution tab → Table of candidates from same LPK
   - Acceptance Organization tab → Table of candidates going to same org
   - Apprentice Order tab → Table of candidates in same order
3. Add filter row to each tab table
4. Add export buttons to each tab

#### Phase 2: Fix Trainees View Tabs
1. Update `TraineesController::view()` to load related data
2. Update `Trainees/view.ctp` tabs
3. Same pattern as Candidates

#### Phase 3: Fix Apprentices View Tabs
1. Update `ApprenticesController::view()` to load related data
2. Update `Apprentices/view.ctp` tabs
3. Same pattern as Candidates/Trainees

#### Phase 4: Implement Working Filter in Index
1. Add JavaScript filter logic to index templates
2. Handle operator (Contains, Equals, etc.)
3. Handle special case for file/image fields (File Exists/Not Exists)
4. Real-time filtering on client side

#### Phase 5: Apply to All Tables
1. Create automation script to apply pattern to all view templates
2. Test thoroughly
3. Document pattern for future bakes

### 6. Filter Logic Specification

#### Text/Number Fields
```javascript
function applyFilter(column, operator, value) {
    switch(operator) {
        case 'contains':
            return cellValue.toLowerCase().includes(value.toLowerCase());
        case 'equals':
            return cellValue == value;
        case 'starts_with':
            return cellValue.toLowerCase().startsWith(value.toLowerCase());
        case 'ends_with':
            return cellValue.toLowerCase().endsWith(value.toLowerCase());
        case 'greater_than':
            return parseFloat(cellValue) > parseFloat(value);
        case 'less_than':
            return parseFloat(cellValue) < parseFloat(value);
        case 'is_null':
            return cellValue === '' || cellValue === null;
        case 'is_not_null':
            return cellValue !== '' && cellValue !== null;
    }
}
```

#### File/Image Fields
```javascript
function applyFileFilter(filePath, operator) {
    if (operator === 'file_exists') {
        // Check if file icon is not showing "File Not Found" warning
        return !$(cell).find('.file-not-found').length;
    }
    if (operator === 'file_not_exists') {
        return $(cell).find('.file-not-found').length > 0;
    }
    // Regular string filter on path
    return filePath.includes(value);
}
```

### 7. Benefits

#### User Experience
- ✅ Relevant data in tabs (related records, not parent details)
- ✅ Can analyze related data without leaving current record
- ✅ Filter and search within related data
- ✅ Export related data independently

#### Developer Experience
- ✅ Consistent pattern across all view templates
- ✅ Reusable tab table component
- ✅ Clear separation: View = related lists, Index = main list

#### Business Value
- ✅ Better data analysis (e.g., see all candidates from same LPK)
- ✅ Spot patterns (e.g., which LPK has most successful candidates)
- ✅ Quality control (e.g., find records with missing files)

### 8. Technical Considerations

#### Performance
- **Concern**: Loading related data in view method may slow down page
- **Solution**: 
  - Limit related data to 100 records per tab
  - Use AJAX to load tab content on demand (lazy loading)
  - Add "View All" link to full index with filter pre-applied

#### Memory
- **Concern**: Multiple related datasets in one view may use too much memory
- **Solution**:
  - Load tab content via AJAX when tab is clicked
  - Don't load all tabs at once
  - Use pagination within tabs

#### Filter Performance
- **Concern**: Client-side filter may be slow for large datasets
- **Solution**:
  - Implement server-side filtering via AJAX
  - Add "Apply Filter" button instead of real-time
  - Show loading indicator during filter operation

### 9. File Existence Check Implementation

#### Client-Side Approach
```javascript
// Check if file-viewer element shows warning
$('.file-viewer').each(function() {
    if ($(this).find('.file-warning').length > 0) {
        $(this).attr('data-file-exists', 'false');
    } else {
        $(this).attr('data-file-exists', 'true');
    }
});
```

#### Server-Side Approach (Recommended)
```php
// In controller
foreach ($candidates as $candidate) {
    $candidate->image_photo_exists = !empty($candidate->image_photo) && 
                                     file_exists(WWW_ROOT . $candidate->image_photo);
}
```

### 10. Next Steps

1. **Approval**: Review and approve this redesign specification
2. **Prototype**: Implement for Candidates view first
3. **Review**: Test and refine the prototype
4. **Scale**: Apply to Trainees and Apprentices
5. **Automate**: Create script for all other tables
6. **Document**: Update GitHub Copilot instructions with new pattern
7. **Bake Templates**: Update bake templates to generate correct tab structure

## Questions for Clarification

1. **Tab Content**: Confirm that tabs should show related records list (not parent detail)
2. **Filter Type**: Client-side (fast, works on visible data) or Server-side (slower, works on all data)?
3. **Pagination**: Should related data in tabs be paginated?
4. **Export**: Should each tab have independent export buttons?
5. **Performance**: Is 100 records per tab acceptable, or need pagination/AJAX?

## References
- Current Implementation: `src/Template/Candidates/view.ctp` line 2188+
- Filter Example: `src/Template/Candidates/index.ctp` line 288+
- File Viewer: `src/Template/Element/file_viewer.ctp`
- GitHub Instructions: `.github/copilot-instructions.md`
