<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\App\Model\Entity\Candidate $candidate * @var array $validationErrors
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

.validation-summary.passed {
    background-color: #d4edda;
    border-color: #28a745;

.validation-summary.failed {
    background-color: #f8d7da;
    border-color: #dc3545;

.field-preview {
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    background-color: #ffffff;

.field-preview.has-error {
    border-color: #dc3545;
    background-color: #fff5f5;

.field-label {
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;

.field-value {
    padding: 8px 12px;
    background-color: #f8f9fa;
    border-radius: 4px;
    margin-bottom: 8px;
    font-family: monospace;

.field-meta {
    display: flex;
    gap: 15px;
    font-size: 0.85rem;
    color: #6c757d;

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;

.badge-required {
    background-color: #dc3545;
    color: white;

.badge-optional {
    background-color: #6c757d;
    color: white;

.badge-type {
    background-color: #17a2b8;
    color: white;

.validation-error {
    margin-top: 10px;
    padding: 10px;
    background-color: #f8d7da;
    border-left: 4px solid #dc3545;
    border-radius: 4px;

.error-message {
    color: #721c24;
    font-weight: 500;

.empty-value {
    color: #999;
    font-style: italic;

.form-actions {
    margin-top: 30px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    display: flex;
    gap: 10px;
</style>

<div class="candidates preview content">
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
                    <h4 style="color: #28a745;">âœ“ All Validation Passed!</h4>
                    <p><?= __('Your data meets all requirements and is ready to be saved.') ?></p>
                <?php else: ?>
                    <h4 style="color: #dc3545;">âš  Validation Errors Found</h4>
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
                if ($isForeignKey && !empty($candidate->id)) {
                    $associationName = Inflector::camelize(str_replace('_id', '', $field));
                    $associationProperty = Inflector::variable($associationName);
                    $associationPropertyPlural = Inflector::variable(Inflector::pluralize($associationName));
                    
                    $assocFound = false;
                    
                    // Try singular first
                    if ($candidate->has($associationProperty)) {
                        $assoc = $candidate->get($associationProperty);
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
                    
                    // Try plural if singular didn't work
                    if (!$assocFound && $candidate->has($associationPropertyPlural)) {
                        $assoc = $candidate->get($associationPropertyPlural);
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
                    
                    if (!$assocFound) {
                        $displayValue = h($value);
                // Handle boolean/checkbox fields
                elseif ($meta['type'] === 'boolean' || $meta['type'] === 'tinyint') {
                    $displayValue = '<input type="checkbox" ' . ($value ? 'checked' : '') . ' disabled> ' 
                                  . ($value ? 'Yes' : 'No');
                // Handle date arrays
                elseif (is_array($value) && $meta['type'] === 'date') {
                    $displayValue = sprintf('%04d-%02d-%02d', $value['year'], $value['month'], $value['day']);
                // Handle date objects
                elseif (is_object($value) && method_exists($value, 'format')) {
                    $displayValue = $value->format('Y-m-d H:i:s');
                // Empty values
                elseif ($value === null || $value === '') {
                    $displayValue = '<span class="empty-value">(empty)</span>';
                else {
                    $displayValue = h($value);
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
                                <div class="error-message">âŒ <?= h($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <?php if (empty($validationErrors)): ?>
                    <?= $this->Form->create($candidate, ['url' => ['action' => 'edit', $candidate->id]]) ?>
                    <?= $this->Form->hidden('id') ?>
                    <?= $this->Form->button(__('Confirm & Save'), ['class' => 'btn btn-success']) ?>
                    <?= $this->Form->end() ?>
                <?php else: ?>
                    <button class="btn btn-success" disabled><?= __('Cannot Save - Fix Errors First') ?></button>
                <?php endif; ?>
                
                <?= $this->Html->link(__('Back to Edit'), 
                    ['action' => 'edit', isset($candidate->id) ? $candidate->id : null], 
                    ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
</div>

