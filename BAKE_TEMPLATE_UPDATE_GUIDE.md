# Bake Template Update Guide

## Summary of Required Updates

This guide documents all changes needed in bake templates to implement validation, preview functionality, and improved form handling.

## Files to Update

### 1. `src/Template/Bake/Template/edit.ctp`

**Changes Required:**

#### A. Add Hidden ID Field (Line ~84, after Form->create)
```php
<?= $this->Form->create($<%= $singularVar %><% if ($hasFileUpload): %>, ['type' => 'file', 'data-confirm' => 'true', 'id' => '<%= $singularVar %>Form']<% else: %>, ['data-confirm' => 'true', 'id' => '<%= $singularVar %>Form']<% endif; %>) ?>
<?php if (!empty($<%= $singularVar %>->id)): ?>
    <?= $this->Form->hidden('id') ?>
<?php endif; ?>
```

#### B. Update Date Field Detection (Line ~100)
Change from `type => 'text'` to `type => 'date'` with proper formatting:

```php
<% if (preg_match('/(date|tanggal)/i', $field)): %>
                    <div class="col-12 mb-3">
<%
$isRequired = !$fieldData['null'];
$maxLength = isset($fieldData['length']) ? $fieldData['length'] : null;
%>
                        <label class="form-label">
                            <?= __('<%= $humanize %>') %>
<% if ($isRequired): %>
                            <span class="text-danger">*</span>
<% endif; %>
                            <small class="text-muted">(Format: YYYY-MM-DD, e.g., 2025-01-15)</small>
                        </label>
                        <?= $this->Form->control('<%= $field %>', [
                            'type' => 'date',
                            'class' => 'form-control',
                            'label' => false,
<% if ($isRequired): %>
                            'required' => true,
<% endif; %>
                            'value' => $<%= $singularVar %>-><%= $field %> ? $<%= $singularVar %>-><%= $field %>->format('Y-m-d') : ''
                        ]) ?>
                    </div>
```

#### C. Update Boolean Field Detection (Line ~220)
Change from password field to checkbox for `is_*` fields:

```php
<% elseif (preg_match('/^is_/i', $field) && $fieldType === 'tinyint'): %>
                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <?= $this->Form->control('<%= $field %>', [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'label' => [
                                    'text' => __('<%= $humanize %>'),
                                    'class' => 'form-check-label'
                                ]
                            ]) ?>
                            <small class="text-muted d-block">Check if <%= strtolower($humanize) %></small>
                        </div>
                    </div>
```

#### D. Add Visual Indicators for Required Fields
Update all field types to show `*` and max length:

```php
<%
$isRequired = !$fieldData['null'];
$maxLength = isset($fieldData['length']) ? $fieldData['length'] : null;
%>
                        <label class="form-label">
                            <?= __('<%= $humanize %>') %>
<% if ($isRequired): %>
                            <span class="text-danger">*</span>
<% endif; %>
<% if ($maxLength): %>
                            <small class="text-muted">(Max <%= $maxLength %> characters)</small>
<% endif; %>
                        </label>
                        <?= $this->Form->control('<%= $field %>', [
                            'class' => 'form-control',
                            'label' => false,
<% if ($isRequired): %>
                            'required' => true,
<% endif; %>
<% if ($maxLength): %>
                            'maxlength' => <%= $maxLength %>,
<% endif; %>
                            'placeholder' => __('Enter <%= $humanize %>')
                        ]) ?>
```

#### E. Add Preview Button (Before form end, line ~380)
```php
            <div class="form-actions mt-4">
                <button type="button" class="btn btn-info" onclick="previewBeforeSave()" style="background-color: #17a2b8; border-color: #17a2b8; color: white; padding: 10px 20px; border-radius: 5px; margin-right: 10px;">
                    <i class="fas fa-eye"></i> <?= __('Preview & Validate Before Save') ?>
                </button>
                <?= $this->Form->button(__('Save <%= $singularHumanName %>'), [
                    'class' => 'btn-export-light',
                    'id' => 'submitBtn'
                ]) ?>
                <?= $this->Html->link(__('Cancel'), ['action' => 'index'], [
                    'class' => 'btn-export-light'
                ]) ?>
            </div>
```

#### F. Add Preview JavaScript Function (In script section, line ~550)
```php
// Preview Before Save Function
function previewBeforeSave() {
    var form = document.getElementById('<%= $singularVar %>Form');
    form.action = '<?= $this->Url->build(['action' => 'preview']) ?>';
    form.submit();
}
```

### 2. `src/Template/Bake/Template/add.ctp`

**Changes Required:** Same as edit.ctp EXCEPT:
- Remove hidden ID field check (not needed in add mode)
- Keep all other changes (visual indicators, date format, boolean fields, preview button)

### 3. `src/Template/Bake/Controller/controller.ctp`

**Add Preview Method:** Add after edit() method (around line 250):

```php
    /**
     * Preview method - Validate and show data before saving
     *
     * @return \Cake\Http\Response|null
     */
    public function preview()
    {
        if (!$this->request->is(['post', 'put', 'patch'])) {
            return $this->redirect(['action' => 'index']);
        }

        $data = $this->request->getData();
        
        // Process date fields - convert HTML5 date array to string
        foreach ($data as $field => $value) {
            if (is_array($value) && isset($value['year'], $value['month'], $value['day'])) {
                $data[$field] = sprintf(
                    '%04d-%02d-%02d',
                    $value['year'],
                    $value['month'],
                    $value['day']
                );
            }
        }
        
        // Determine mode: edit (has ID) vs add (no ID)
        $isEditMode = !empty($data['id']);
        
        // Create entity for validation (no save)
        if ($isEditMode) {
            $<%= $singularVar %> = $this-><%= $currentModelName %>->get($data['id'], ['contain' => []]);
            $<%= $singularVar %> = $this-><%= $currentModelName %>->patchEntity($<%= $singularVar %>, $data);
        } else {
            $<%= $singularVar %> = $this-><%= $currentModelName %>->newEntity($data);
        }

        // Get validation errors
        $validationErrors = $<%= $singularVar %>->getErrors();
        
        // Manual check: Edit mode requires ID in data
        if ($isEditMode && empty($data['id'])) {
            $validationErrors['id'] = ['ID is required for updating existing records'];
        }
        
        // Get database schema metadata
        $schema = $this-><%= $currentModelName %>->getSchema();
        $fieldMetadata = [];
        
        foreach ($schema->columns() as $column) {
            $columnData = $schema->getColumn($column);
            $fieldMetadata[$column] = [
                'type' => $schema->getColumnType($column),
                'nullable' => $schema->isNullable($column),
                'length' => isset($columnData['length']) ? $columnData['length'] : null,
                'default' => isset($columnData['default']) ? $columnData['default'] : null,
            ];
        }
        
        // Ensure ID in data for edit mode display
        if (!empty($<%= $singularVar %>->id) && empty($data['id'])) {
            $data['id'] = $<%= $singularVar %>->id;
        }
        
        // Load associations for display
        if (!empty($<%= $singularVar %>->id)) {
            $<%= $singularVar %> = $this-><%= $currentModelName %>->get($<%= $singularVar %>->id, [
                'contain' => [
<%
$done = [];
foreach ($associations as $type => $data):
    foreach ($data as $alias => $details):
        if (!in_array($alias, $done)):
%>
                    '<%= $alias %>',
<%
            $done[] = $alias;
        endif;
    endforeach;
endforeach;
%>
                ]
            ]);
        }

        $this->set(compact('<%= $singularVar %>', 'validationErrors', 'fieldMetadata', 'data'));
    }
```

### 4. Create New Template: `src/Template/Bake/Template/preview.ctp`

**Full Template:** (This is a NEW file to create)

```php
<%
/**
 * Bake Template for Preview Page
 */
use Cake\Utility\Inflector;

$associations = array_merge(
    $belongsTo,
    $hasOne,
    $hasMany,
    $belongsToMany
);
%>
<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\<%= $entityClass %> $<%= $singularVar %>
 * @var array $validationErrors
 * @var array $fieldMetadata
 * @var array $data
 */
use Cake\Utility\Inflector;
?>

<style>
/* Preview Page Styling */
.validation-summary {
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 8px;
    border: 2px solid;
}

.validation-summary.passed {
    background-color: #d4edda;
    border-color: #28a745;
}

.validation-summary.failed {
    background-color: #f8d7da;
    border-color: #dc3545;
}

.field-preview {
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    background-color: #ffffff;
}

.field-preview.has-error {
    border-color: #dc3545;
    background-color: #fff5f5;
}

.field-label {
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.field-value {
    padding: 8px 12px;
    background-color: #f8f9fa;
    border-radius: 4px;
    margin-bottom: 8px;
    font-family: monospace;
}

.field-meta {
    display: flex;
    gap: 15px;
    font-size: 0.85rem;
    color: #6c757d;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-required {
    background-color: #dc3545;
    color: white;
}

.badge-optional {
    background-color: #6c757d;
    color: white;
}

.badge-type {
    background-color: #17a2b8;
    color: white;
}

.validation-error {
    margin-top: 10px;
    padding: 10px;
    background-color: #f8d7da;
    border-left: 4px solid #dc3545;
    border-radius: 4px;
}

.error-message {
    color: #721c24;
    font-weight: 500;
}

.empty-value {
    color: #999;
    font-style: italic;
}

.form-actions {
    margin-top: 30px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    display: flex;
    gap: 10px;
}
</style>

<div class="<%= $pluralVar %> preview content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-eye"></i> <?= __('Preview Data Before Saving') ?>
            </h3>
            <p><?= __('Please review your data and validation status before saving to database.') ?></p>
        </div>

        <div class="card-body">
            <!-- Validation Summary -->
            <div class="validation-summary <?= empty($validationErrors) ? 'passed' : 'failed' ?>">
                <?php if (empty($validationErrors)): ?>
                    <h4 style="color: #28a745;">✓ All Validation Passed!</h4>
                    <p><?= __('Your data meets all requirements and is ready to be saved.') ?></p>
                <?php else: ?>
                    <h4 style="color: #dc3545;">⚠ Validation Errors Found</h4>
                    <p><strong><?= count($validationErrors) ?> field(s)</strong> need attention before saving.</p>
                <?php endif; ?>
            </div>

            <h3><?= __('Data Preview with Validation Rules') ?></h3>
            
            <?php foreach ($fieldMetadata as $field => $meta): ?>
                <?php 
                $hasError = isset($validationErrors[$field]);
                $isRequired = !$meta['nullable'];
                $value = isset($data[$field]) ? $data[$field] : null;
                $displayValue = $value;
                
                // Handle foreign keys - show association name instead of ID
                $isForeignKey = substr($field, -3) === '_id';
                if ($isForeignKey && !empty($<%= $singularVar %>->id)) {
                    $associationName = Inflector::camelize(str_replace('_id', '', $field));
                    $associationProperty = Inflector::variable($associationName);
                    $associationPropertyPlural = Inflector::variable(Inflector::pluralize($associationName));
                    
                    $assocFound = false;
                    
                    // Try singular first
                    if ($<%= $singularVar %>->has($associationProperty)) {
                        $assoc = $<%= $singularVar %>->get($associationProperty);
                        if (is_object($assoc)) {
                            if (isset($assoc->title)) {
                                $displayValue = h($assoc->title);
                                $assocFound = true;
                            } elseif (isset($assoc->name)) {
                                $displayValue = h($assoc->name);
                                $assocFound = true;
                            } elseif (isset($assoc->fullname)) {
                                $displayValue = h($assoc->fullname);
                                $assocFound = true;
                            }
                        }
                    }
                    
                    // Try plural if singular didn't work
                    if (!$assocFound && $<%= $singularVar %>->has($associationPropertyPlural)) {
                        $assoc = $<%= $singularVar %>->get($associationPropertyPlural);
                        if (is_object($assoc)) {
                            if (isset($assoc->title)) {
                                $displayValue = h($assoc->title);
                                $assocFound = true;
                            } elseif (isset($assoc->name)) {
                                $displayValue = h($assoc->name);
                                $assocFound = true;
                            } elseif (isset($assoc->fullname)) {
                                $displayValue = h($assoc->fullname);
                                $assocFound = true;
                            }
                        }
                    }
                    
                    if (!$assocFound) {
                        $displayValue = h($value);
                    }
                }
                // Handle boolean/checkbox fields
                elseif ($meta['type'] === 'boolean' || $meta['type'] === 'tinyint') {
                    $displayValue = '<input type="checkbox" ' . ($value ? 'checked' : '') . ' disabled> ' 
                                  . ($value ? 'Yes' : 'No');
                }
                // Handle date arrays
                elseif (is_array($value) && $meta['type'] === 'date') {
                    $displayValue = sprintf('%04d-%02d-%02d', $value['year'], $value['month'], $value['day']);
                }
                // Handle date objects
                elseif (is_object($value) && method_exists($value, 'format')) {
                    $displayValue = $value->format('Y-m-d H:i:s');
                }
                // Empty values
                elseif ($value === null || $value === '') {
                    $displayValue = '<span class="empty-value">(empty)</span>';
                }
                else {
                    $displayValue = h($value);
                }
                ?>
                
                <div class="field-preview <?= $hasError ? 'has-error' : '' ?>">
                    <div class="field-label">
                        <?= Inflector::humanize($field) ?>
                        <?php if ($isRequired): ?>
                            <span class="badge badge-required">REQUIRED</span>
                        <?php else: ?>
                            <span class="badge badge-optional">Optional</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="field-value"><?= $displayValue ?></div>
                    
                    <div class="field-meta">
                        <span class="badge badge-type"><?= strtoupper($meta['type']) ?></span>
                        <?php if ($meta['length']): ?>
                            <span>Max: <?= $meta['length'] ?> chars</span>
                        <?php endif; ?>
                        <span style="color: <?= $meta['nullable'] ? '#6c757d' : '#dc3545' ?>">
                            Nullable: <?= $meta['nullable'] ? 'Yes' : 'No' ?>
                        </span>
                    </div>
                    
                    <?php if ($hasError): ?>
                        <div class="validation-error">
                            <?php foreach ($validationErrors[$field] as $error): ?>
                                <div class="error-message">❌ <?= h($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <?php if (empty($validationErrors)): ?>
                    <?= $this->Form->create($<%= $singularVar %>, ['url' => ['action' => 'edit', $<%= $singularVar %>->id]]) ?>
                    <?= $this->Form->hidden('id') ?>
                    <?= $this->Form->button(__('Confirm & Save'), ['class' => 'btn btn-success']) ?>
                    <?= $this->Form->end() ?>
                <?php else: ?>
                    <button class="btn btn-success" disabled><?= __('Cannot Save - Fix Errors First') ?></button>
                <?php endif; ?>
                
                <?= $this->Html->link(__('Back to Edit'), 
                    ['action' => 'edit', isset($<%= $singularVar %>->id) ? $<%= $singularVar %>->id : null], 
                    ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
</div>
```

### 5. `src/Template/Bake/Model/table.ctp`

**Add ID Validation:** Update validationDefault method (around line 80):

```php
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create')
            ->notEmptyString('id', 'ID is required for update', 'update');
            
<% foreach ($fields as $field => $data): %>
<% // existing field validation %>
<% endforeach; %>

        return $validator;
    }
```

## Implementation Steps

1. **Backup Current Templates:**
   ```powershell
   Copy-Item -Recurse src/Template/Bake src/Template/Bake_Backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')
   ```

2. **Update Templates One by One:**
   - Start with `preview.ctp` (new file)
   - Update `controller.ctp` (add preview method)
   - Update `edit.ctp` (all changes)
   - Update `add.ctp` (similar to edit)
   - Update `table.ctp` (ID validation)

3. **Test with One Table:**
   ```powershell
   bin\cake bake all TestTable --connection default --force
   bin\cake cache clear_all
   ```

4. **Verify:**
   - Edit page shows ID hidden field
   - Date fields use HTML5 date input with YYYY-MM-DD
   - is_* fields show as checkboxes
   - Required fields have red *
   - Preview button exists
   - Preview page works
   - Validation detects errors

5. **Apply to All Tables:**
   ```powershell
   # Re-bake all tables with new templates
   .\bake_all_cms_databases.ps1
   ```

## Quick Reference

### Field Type Detection Patterns:
- **Date**: `/date|tanggal/i` → HTML5 date input
- **Boolean**: `^is_` + tinyint → Checkbox
- **Email**: `/email|e_mail/i` → Email input with validation
- **Password**: `/password|pass|pwd/i` → Password input (NOT for is_* fields!)
- **File**: `/file|attachment/i` → File input
- **Image**: `/image|photo|gambar|foto/i` → File input with image preview
- **Foreign Key**: `_id$` → Dropdown with association names

### Required Field Indicators:
```php
<% if (!$fieldData['null']): %>
    <span class="text-danger">*</span>
<% endif; %>
```

### Max Length Indicators:
```php
<% if (isset($fieldData['length'])): %>
    <small class="text-muted">(Max <%= $fieldData['length'] %> characters)</small>
<% endif; %>
```

## Notes

- All changes maintain PHP 5.6 compatibility
- Preview page uses CSS inline for portability
- Association display tries both singular and plural forms
- Date conversion handles both array and object formats
- Boolean fields detect `is_*` prefix AND tinyint type
- ID field only added in edit mode, not add mode
