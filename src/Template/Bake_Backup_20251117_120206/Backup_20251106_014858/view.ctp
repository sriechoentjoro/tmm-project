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
 */
use Cake\Utility\Inflector;

$associations += ['BelongsTo' => [], 'HasOne' => [], 'HasMany' => [], 'BelongsToMany' => []];
$immediateAssociations = $associations['BelongsTo'] + $associations['HasOne'];
$associationFields = collection($fields)
    ->map(function($field) use ($immediateAssociations) {
        foreach ($immediateAssociations as $alias => $details) {
            if ($field === $details['foreignKey']) {
                return [$field => $details];
    })
    ->filter()
    ->reduce(function($fields, $value) {
        return $fields + $value;
    }, []);

$groupedFields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->getColumnType($field) !== 'binary';
    })
    ->groupBy(function($field) use ($schema, $associationFields) {
        $type = $schema->getColumnType($field);
        if (isset($associationFields[$field])) {
            return 'string';
        if (in_array($type, ['integer', 'float', 'decimal', 'biginteger', 'smallinteger', 'tinyinteger'])) {
            return 'number';
        if (in_array($type, ['date', 'time', 'datetime', 'timestamp'])) {
            return 'date';
        if (in_array($type, ['text', 'string'])) {
            return 'string';
        if ($type === 'boolean') {
            return 'boolean';
        return 'string';
    })
    ->toArray();
$pk = "\$${singularVar}->{$primaryKey[0]}";
%>
<!-- GitHub Style View Template -->
<div class="github-container">
    <!-- Page Header -->
    <div class="github-page-header">
        <div class="github-header-content">
            <div class="github-title-row">
                <h1 class="github-page-title">
                    <svg class="octicon" width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 9.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                        <path d="M8 0a8 8 0 100 16A8 8 0 008 0zM1.5 8a6.5 6.5 0 1113 0 6.5 6.5 0 01-13 0z"></path>
                    </svg>
                    <?= __('<%= $singularHumanName %>') ?>: <?= h($<%= $singularVar %>-><%= $displayField %>) ?>
                </h1>
                
                <div class="github-header-actions">
                    <?= $this->Html->link(
                        '<svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M11.013 1.427a1.75 1.75 0 012.474 0l1.086 1.086a1.75 1.75 0 010 2.474l-8.61 8.61c-.21.21-.47.364-.756.445l-3.251.93a.75.75 0 01-.927-.928l.929-3.25c.081-.286.235-.547.445-.758l8.61-8.61z"></path></svg> ' . __('Edit'),
                        ['action' => 'edit', <%= $pk %>],
                        ['class' => 'github-btn github-btn-primary', 'escape' => false]
                    ) ?>
                    <?= $this->Form->postLink(
                        '<svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M6.5 1.75a.25.25 0 01.25-.25h2.5a.25.25 0 01.25.25V3h-3V1.75zm4.5 0V3h2.25a.75.75 0 010 1.5H2.75a.75.75 0 010-1.5H5V1.75C5 .784 5.784 0 6.75 0h2.5C10.216 0 11 .784 11 1.75z"></path></svg> ' . __('Delete'),
                        ['action' => 'delete', <%= $pk %>],
                        ['class' => 'github-btn github-btn-danger', 'escape' => false, 'confirm' => __('Are you sure you want to delete this <%= $singularHumanName %>?')]
                    ) ?>
                    <?= $this->Html->link(
                        '<svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M1.75 2.5a.25.25 0 00-.25.25v10.5c0 .138.112.25.25.25h12.5a.25.25 0 00.25-.25V2.75a.25.25 0 00-.25-.25H1.75z"></path></svg> ' . __('List'),
                        ['action' => 'index'],
                        ['class' => 'github-btn github-btn-default', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="view-tabs-container">
        <ul class="view-tabs-nav" role="tablist">
            <li class="view-tab-item">
                <a href="#tab-home" class="view-tab-link active" data-tab="tab-home">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M6.906.664a1.749 1.749 0 0 1 2.187 0l5.25 4.2c.415.332.657.835.657 1.367v7.019A1.75 1.75 0 0 1 13.25 15h-3.5a.75.75 0 0 1-.75-.75V9H7v5.25a.75.75 0 0 1-.75.75h-3.5A1.75 1.75 0 0 1 1 13.25V6.23c0-.531.242-1.034.657-1.366l5.25-4.2z"></path>
                    </svg>
                    <?= __('Home') ?>
                </a>
            </li>
<%
    $relations = $associations['HasMany'] + $associations['BelongsToMany'];
    $tabIndex = 1;
    foreach ($relations as $alias => $details):
        $otherPluralHumanName = Inflector::humanize($details['controller']);
        $tabId = 'tab-' . strtolower(Inflector::underscore($alias));
%>
            <li class="view-tab-item">
                <a href="#<%= $tabId %>" class="view-tab-link" data-tab="<%= $tabId %>">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
                    </svg>
                    <?= __('<%= $otherPluralHumanName %>') ?>
                    <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
                    <span class="tab-badge"><?= count($<%= $singularVar %>-><%= $details['property'] %>) ?></span>
                    <?php endif; ?>
                </a>
            </li>
<%
        $tabIndex++;
    endforeach;
%>
        </ul>

        <!-- Tab Contents -->
        <div class="view-tabs-content">
            <!-- Home Tab -->
            <div id="tab-home" class="view-tab-pane active">
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M0 1.75C0 .784.784 0 1.75 0h12.5C15.216 0 16 .784 16 1.75v12.5A1.75 1.75 0 0 1 14.25 16H1.75A1.75 1.75 0 0 1 0 14.25V1.75z"></path>
                            </svg>
                            <?= __('Details') ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                <table class="github-details-table">
                    <tbody>
<%
    foreach ($groupedFields as $type => $fields):
        foreach ($fields as $field):
            $isKey = false;
            if (isset($associationFields[$field])):
                $details = $associationFields[$field];
                $isKey = true;
%>
                        <tr>
                            <th class="github-detail-label"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                            <td class="github-detail-value">
                                <?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>], ['class' => 'github-link']) : '' ?>
                            </td>
                        </tr>
<%
            endif;
            if ($isKey !== true):
                if (!in_array($field, ['created', 'modified', 'updated'])):
                    if ($type === 'number'):
%>
                        <tr>
                            <th class="github-detail-label"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                            <td class="github-detail-value"><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>
                        </tr>
<%
                    elseif ($type === 'boolean'):
%>
                        <tr>
                            <th class="github-detail-label"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                            <td class="github-detail-value">
                                <span class="github-badge <?= $<%= $singularVar %>-><%= $field %> ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $<%= $singularVar %>-><%= $field %> ? __('Yes') : __('No'); ?>
                                </span>
                            </td>
                        </tr>
<%
                    elseif ($type === 'date'):
%>
                        <tr>
                            <th class="github-detail-label"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                            <td class="github-detail-value"><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
                        </tr>
<%
                    else:
%>
                        <tr>
                            <th class="github-detail-label"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                            <td class="github-detail-value"><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
                        </tr>
<%
                    endif;
                endif;
            endif;
        endforeach;
    endforeach;
%>
                    </tbody>
                </table>
            </div>
        </div>
            </div>
            <!-- End Home Tab -->

<%
    $relations = $associations['HasMany'] + $associations['BelongsToMany'];
    $tabIndex = 1;
    foreach ($relations as $alias => $details):
        $otherSingularVar = Inflector::variable($alias);
        $otherPluralHumanName = Inflector::humanize($details['controller']);
        $tabId = 'tab-' . strtolower(Inflector::underscore($alias));
%>
            <!-- <%= $otherPluralHumanName %> Tab -->
            <div id="<%= $tabId %>" class="view-tab-pane">
                <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
                <div class="github-related-card">
                    <div class="github-related-header">
                        <h3 class="github-related-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                            </svg>
                            <?= __('Related <%= $otherPluralHumanName %>') ?>
                        </h3>
                        <?= $this->Html->link(
                            '<svg class="octicon" width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M7.75 2a.75.75 0 0 1 .75.75V7h4.25a.75.75 0 0 1 0 1.5H8.5v4.25a.75.75 0 0 1-1.5 0V8.5H2.75a.75.75 0 0 1 0-1.5H7V2.75A.75.75 0 0 1 7.75 2Z"/></svg> ' . __('New <%= Inflector::humanize(Inflector::singularize($details['controller'])) %>'),
                            ['controller' => '<%= $details['controller'] %>', 'action' => 'add'],
                            ['class' => 'github-btn github-btn-sm github-btn-primary', 'escape' => false]
                        ) ?>
                    </div>

                    <div class="github-related-body">
                        <table class="github-related-table">
                            <thead>
                                <tr>
<%
        $otherFields = collection($details['fields'])
            ->filter(function($field) {
                return !in_array($field, ['created', 'modified', 'updated']);
            })
            ->take(5);

        foreach ($otherFields as $field):
%>
                            <th><?= __('<%= Inflector::humanize($field) %>') ?></th>
<%
        endforeach;
%>
                            <th><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($<%= $singularVar %>-><%= $details['property'] %> as $<%= $otherSingularVar %>): ?>
                        <tr>
<%
        foreach ($otherFields as $field):
%>
                            <td><?= h($<%= $otherSingularVar %>-><%= $field %>) ?></td>
<%
        endforeach;

        $otherPk = "\${$otherSingularVar}->{$details['primaryKey'][0]}";
%>
                            <td class="github-related-actions">
                                <?= $this->Html->link(__('View'), ['controller' => '<%= $details['controller'] %>', 'action' => 'view', <%= $otherPk %>], ['class' => 'github-btn github-btn-sm']) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => '<%= $details['controller'] %>', 'action' => 'edit', <%= $otherPk %>], ['class' => 'github-btn github-btn-sm']) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => '<%= $details['controller'] %>', 'action' => 'delete', <%= $otherPk %>], ['class' => 'github-btn github-btn-sm github-btn-danger', 'confirm' => __('Are you sure?')]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1.5 3.25a.75.75 0 111.5 0 .75.75 0 01-1.5 0zM3 6.5a.75.75 0 100-1.5.75.75 0 000 1.5zm-.75 2.75a.75.75 0 111.5 0 .75.75 0 01-1.5 0z"></path>
                    </svg>
                    <h3><?= __('No Related <%= $otherPluralHumanName %>') ?></h3>
                    <p><?= __('There are no related <%= $otherPluralHumanName %> for this <%= $singularHumanName %>.') ?></p>
                    <?= $this->Html->link(
                        __('Add First <%= Inflector::humanize(Inflector::singularize($details['controller'])) %>'),
                        ['controller' => '<%= $details['controller'] %>', 'action' => 'add'],
                        ['class' => 'github-btn github-btn-primary']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
<%
        $tabIndex++;
    endforeach;
%>
        </div>
        <!-- End Tab Contents -->
    </div>
    <!-- End Tabs Container -->
</div>
<!-- End GitHub Container -->

<script>
// Tab Switching JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.view-tab-link');
    const tabPanes = document.querySelectorAll('.view-tab-pane');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get target tab
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and panes
            tabLinks.forEach(l => l.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding pane
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
            
            // Store active tab in sessionStorage
            sessionStorage.setItem('activeViewTab', targetTab);
        });
    });
    
    // Restore active tab from sessionStorage
    const savedTab = sessionStorage.getItem('activeViewTab');
    if (savedTab && document.getElementById(savedTab)) {
        // Click the saved tab link
        const savedLink = document.querySelector('[data-tab="' + savedTab + '"]');
        if (savedLink) {
            savedLink.click();
});
</script>

<style>
/* Tab Navigation Styles */
.view-tabs-container {
    margin: 20px 0;

.view-tabs-nav {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    border-bottom: 1px solid var(--github-border-default, #d0d7de);
    background: var(--github-canvas-default, #ffffff);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;

.view-tab-item {
    margin: 0;

.view-tab-link {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 12px 20px;
    color: var(--github-fg-muted, #656d76);
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
    cursor: pointer;
    white-space: nowrap;

.view-tab-link:hover {
    color: var(--github-fg-default, #24292f);
    border-bottom-color: var(--github-border-muted, #d8dee4);

.view-tab-link.active {
    color: var(--github-fg-default, #24292f);
    border-bottom-color: #667eea;
    font-weight: 600;

.tab-icon {
    flex-shrink: 0;

.tab-badge {
    display: inline-block;
    padding: 2px 8px;
    background: var(--github-canvas-subtle, #f6f8fa);
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    color: var(--github-fg-default, #24292f);

.view-tab-link.active .tab-badge {
    background: #667eea;
    color: #ffffff;

.view-tabs-content {
    margin-top: 20px;

.view-tab-pane {
    display: none;

.view-tab-pane.active {
    display: block;

/* Empty State */
.github-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--github-canvas-default, #ffffff);
    border: 1px solid var(--github-border-default, #d0d7de);
    border-radius: 12px;

.github-empty-state .empty-icon {
    opacity: 0.3;
    margin-bottom: 16px;

.github-empty-state h3 {
    margin: 0 0 8px;
    font-size: 18px;
    color: var(--github-fg-default, #24292f);

.github-empty-state p {
    margin: 0 0 20px;
    color: var(--github-fg-muted, #656d76);

/* GitHub Style View CSS */
.github-view-container {
    display: flex;
    flex-direction: column;
    gap: 20px;

.github-details-card,
.github-related-card {
    background: var(--github-canvas-default, #ffffff);
    border: 1px solid var(--github-border-default, #d0d7de);
    border-radius: 12px;
    overflow: hidden;

.github-details-header,
.github-related-header {
    padding: 16px 20px;
    background: var(--github-canvas-subtle, #f6f8fa);
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);
    display: flex;
    align-items: center;
    justify-content: space-between;

.github-details-title,
.github-related-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--github-fg-default, #24292f);

.github-details-body,
.github-related-body {
    padding: 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;

.github-details-table {
    width: 100%;
    border-collapse: collapse;

.github-details-table tr {
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);

.github-details-table tr:last-child {
    border-bottom: none;

.github-detail-label {
    width: 200px;
    padding: 16px 20px;
    font-weight: 600;
    color: var(--github-fg-default, #24292f);
    background: var(--github-canvas-subtle, #f6f8fa);
    text-align: left;
    vertical-align: top;

.github-detail-value {
    padding: 16px 20px;
    color: var(--github-fg-default, #24292f);

.github-link {
    color: var(--github-accent-fg, #0969da);
    text-decoration: none;

.github-link:hover {
    text-decoration: underline;

.github-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;

.badge-success {
    background: rgba(26, 127, 55, 0.1);
    color: #1a7f37;

.badge-secondary {
    background: var(--github-canvas-subtle, #f6f8fa);
    color: var(--github-fg-muted, #656d76);

.github-related-table {
    width: 100%;
    min-width: 600px;
    border-collapse: collapse;

.github-related-table thead th {
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);
    white-space: nowrap;
    background: var(--github-canvas-subtle, #f6f8fa);
    position: sticky;
    top: 0;
    z-index: 10;

.github-related-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--github-border-muted, #d8dee4);

.github-related-table tbody tr:last-child td {
    border-bottom: none;

.github-related-actions {
    display: flex;
    gap: 4px;

/* Mobile Full Width Optimization */
@media (max-width: 768px) {
    .content-wrapper,
    .github-details-card,
    .github-related-card,
    .github-form-card,
    .inventories.index.content {
        margin: 0 8px;
        width: calc(100% - 16px);
        max-width: 100%;

    body {
        padding: 0;
        margin: 0;

    .container {
        padding: 0 8px;
    
    /* Mobile Tab Styles */
    .view-tab-link {
        padding: 10px 16px;
        font-size: 14px;
    
    .tab-icon {
        width: 14px;
        height: 14px;
    
    .tab-badge {
        font-size: 11px;
        padding: 1px 6px;
    
    .github-detail-label {
        width: 120px;
        padding: 12px 16px;
        font-size: 14px;
    
    .github-detail-value {
        padding: 12px 16px;
        font-size: 14px;
    
    .github-related-table {
        font-size: 14px;
    
    .github-related-actions {
        flex-direction: column;
        gap: 4px;
    
    .github-related-actions .github-btn {
        width: 100%;
        text-align: center;

@media (max-width: 480px) {
    .view-tab-link {
        padding: 8px 12px;
        font-size: 13px;
    
    .github-detail-label {
        width: 100px;
        padding: 10px 12px;
        font-size: 13px;
    
    .github-detail-value {
        padding: 10px 12px;
        font-size: 13px;
</style>
