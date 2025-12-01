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
                <div style="display: flex; align-items: center; gap: 10px;">
                    <!-- Hamburger Dropdown Menu -->
                    <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" 
                            data-bs-toggle="dropdown" aria-expanded="false" 
                            style="padding: 6px 8px; font-size: 18px; color: #24292f; 
                                   background: transparent; border: none; text-decoration: none; margin: 0;">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List <%= $pluralHumanName %>'),
                            ['action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New <%= $singularHumanName %>'),
                            ['action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
<%
    if (!empty($associations['BelongsTo'])):
        foreach ($associations['BelongsTo'] as $alias => $details):
%>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List <%= $alias %>'),
                            ['controller' => '<%= $details['controller'] %>', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New <%= Inflector::singularize($alias) %>'),
                            ['controller' => '<%= $details['controller'] %>', 'action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
<%
        endforeach;
    endif;
%>
                    </div>
                    
                    <h1 class="github-page-title" style="margin: 0;">
                        <?= __('<%= $singularHumanName %>') ?>: <?= h($<%= $singularVar %>-><%= $displayField %>) ?>
                    </h1>
                </div>
                
                <div class="github-header-actions">
                    <?= $this->Html->link(
                        '<i class="fas fa-file-csv"></i> ' . __('CSV'),
                        ['action' => 'exportCsv'],
                        ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Export to CSV']
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-file-excel"></i> ' . __('Excel'),
                        ['action' => 'exportExcel'],
                        ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Export to Excel']
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-file-pdf"></i> ' . __('PDF'),
                        ['action' => 'exportPdf'],
                        ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Export to PDF']
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-print"></i> ' . __('Print'),
                        'javascript:window.print()',
                        ['class' => 'btn-export-light', 'escape' => false, 'title' => 'Print this page']
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-edit"></i> ' . __('Edit'),
                        ['action' => 'edit', <%= $pk %>],
                        ['class' => 'btn-export-light', 'escape' => false]
                    ) ?>
                    <?= $this->Form->postLink(
                        '<i class="fas fa-trash"></i> ' . __('Delete'),
                        ['action' => 'delete', <%= $pk %>],
                        [
                            'class' => 'btn-export-light',
                            'escape' => false,
                            'confirm' => __('Are you sure you want to delete # {0}?', <%= $pk %>)
                        ]
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-list"></i> ' . __('List'),
                        ['action' => 'index'],
                        ['class' => 'btn-export-light', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- View Content Wrapper - Modal Safe -->
    <div class="view-content-wrapper" data-view-template="true">
    
    <!-- Tab Navigation -->
    <div class="view-tabs-container">
        <ul class="view-tabs-nav" role="tablist">
            <li class="view-tab-item">
                <a href="#tab-home" class="view-tab-link active" data-tab="tab-home">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M0 1.75C0 .784.784 0 1.75 0h12.5C15.216 0 16 .784 16 1.75v12.5A1.75 1.75 0 0 1 14.25 16H1.75A1.75 1.75 0 0 1 0 14.25V1.75zm1.75-.25a.25.25 0 0 0-.25.25v12.5c0 .138.112.25.25.25h12.5a.25.25 0 0 0 .25-.25V1.75a.25.25 0 0 0-.25-.25H1.75zM3.5 6.75A.75.75 0 0 1 4.25 6h7a.75.75 0 0 1 0 1.5h-7a.75.75 0 0 1-.75-.75zm.75 2.25a.75.75 0 0 0 0 1.5h4a.75.75 0 0 0 0-1.5h-4z"></path>
                    </svg>
                    <?= __('Detail') ?>
                </a>
            </li>
<%
    // Add tabs for ALL associations (BelongsTo, HasOne, HasMany, BelongsToMany)
    $allRelations = [];
    
    // Add BelongsTo associations as tabs (show parent record details)
    foreach ($associations['BelongsTo'] as $alias => $details) {
        $allRelations[] = [
            'alias' => $alias,
            'details' => $details,
            'type' => 'BelongsTo',
            'singular' => true
        ];
    
    // Add HasOne associations as tabs
    foreach ($associations['HasOne'] as $alias => $details) {
        $allRelations[] = [
            'alias' => $alias,
            'details' => $details,
            'type' => 'HasOne',
            'singular' => true
        ];
    
    // Add HasMany associations as tabs
    foreach ($associations['HasMany'] as $alias => $details) {
        $allRelations[] = [
            'alias' => $alias,
            'details' => $details,
            'type' => 'HasMany',
            'singular' => false
        ];
    
    // Add BelongsToMany associations as tabs
    foreach ($associations['BelongsToMany'] as $alias => $details) {
        $allRelations[] = [
            'alias' => $alias,
            'details' => $details,
            'type' => 'BelongsToMany',
            'singular' => false
        ];
    
    $tabIndex = 1;
    foreach ($allRelations as $relation):
        $alias = $relation['alias'];
        $details = $relation['details'];
        $relationType = $relation['type'];
        $isSingular = $relation['singular'];
        
        $otherPluralHumanName = Inflector::humanize($details['controller']);
        $tabId = 'tab-' . strtolower(Inflector::underscore($alias));
        
        // Count records for HasMany and BelongsToMany
        $hasRecords = false;
        $recordCount = 0;
        if (!$isSingular) {
            $hasRecords = true; // Will be checked in PHP
        } else {
            $hasRecords = true; // BelongsTo/HasOne always show if associated
%>
            <li class="view-tab-item">
                <a href="#<%= $tabId %>" class="view-tab-link" data-tab="<%= $tabId %>">
                    <svg class="tab-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
<%
        if ($isSingular):
%>
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
<%
        else:
%>
                        <path d="M1.5 3.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM3 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm-.75 2.75a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0z"></path>
<%
        endif;
%>
                    </svg>
                    <?= __('<%= $otherPluralHumanName %>') ?>
<%
        if (!$isSingular):
%>
                    <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
                    <span class="tab-badge"><?= count($<%= $singularVar %>-><%= $details['property'] %>) ?></span>
                    <?php else: ?>
                    <span class="tab-badge">0</span>
                    <?php endif; ?>
<%
        endif;
%>
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
<%
    // Detect image and file fields
    $imageFields = [];
    $fileFields = [];
    foreach ($fields as $field) {
        if (preg_match('/(image|photo|picture|gambar|foto)/i', $field) && 
            !in_array($field, $primaryKey)) {
            $imageFields[] = $field;
        } elseif (preg_match('/(file|attachment|document|pdf)/i', $field) && 
                  !preg_match('/(image|photo|picture)/i', $field) &&
                  !in_array($field, $primaryKey)) {
            $fileFields[] = $field;
    
    // Check for HasMany image/file associations
    $imageAssociations = [];
    $fileAssociations = [];
    foreach ($associations['HasMany'] as $alias => $details) {
        if (preg_match('/(image|photo|picture|gallery)/i', $alias)) {
            $imageAssociations[] = ['alias' => $alias, 'details' => $details];
        } elseif (preg_match('/(file|attachment|document)/i', $alias)) {
            $fileAssociations[] = ['alias' => $alias, 'details' => $details];
    
    if (!empty($imageFields) || !empty($fileFields) || !empty($imageAssociations) || !empty($fileAssociations)):
%>
                <!-- Media Preview Section -->
                <div class="media-preview-section" style="margin-bottom: 24px;">
<%
        // Single image fields
        if (!empty($imageFields)):
            foreach ($imageFields as $imageField):
%>
                    <?php if (!empty($<%= $singularVar %>-><%= $imageField %>)): ?>
                    <div class="image-preview-container" style="text-align: center; margin-bottom: 20px; background: #f6f8fa; padding: 20px; border-radius: 8px; border: 1px solid #d0d7de;">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600; color: #24292f;">
                            <i class="fas fa-image"></i> <?= __('<%= Inflector::humanize($imageField) %>') ?>
                        </h4>
                        <div style="max-width: 800px; margin: 0 auto;">
                            <img src="<?= $this->Url->build('/' . h($<%= $singularVar %>-><%= $imageField %>)) ?>" 
                                 alt="<?= __('<%= Inflector::humanize($imageField) %>') ?>"
                                 style="max-width: 100%; height: auto; max-height: 500px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); cursor: pointer;"
                                 onclick="window.open(this.src, '_blank')"
                                 onerror="this.parentElement.parentElement.style.display='none'">
                        </div>
                        <div style="margin-top: 15px;">
                            <a href="<?= $this->Url->build('/' . h($<%= $singularVar %>-><%= $imageField %>)) ?>" 
                               download 
                               class="btn-export-light"
                               style="display: inline-block;">
                                <i class="fas fa-download"></i> <?= __('Download Image') ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
<%
            endforeach;
        endif;
        
        // Single file fields (PDF/Documents)
        if (!empty($fileFields)):
            foreach ($fileFields as $fileField):
%>
                    <?php if (!empty($<%= $singularVar %>-><%= $fileField %>)): ?>
                    <?php
                        $fileExt = pathinfo($<%= $singularVar %>-><%= $fileField %>, PATHINFO_EXTENSION);
                        $isPdf = strtolower($fileExt) === 'pdf';
                    ?>
                    <div class="file-preview-container" style="margin-bottom: 20px; background: #f6f8fa; padding: 20px; border-radius: 8px; border: 1px solid #d0d7de;">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600; color: #24292f;">
                            <i class="fas fa-file-<?= $isPdf ? 'pdf' : 'alt' ?>"></i> <?= __('<%= Inflector::humanize($fileField) %>') ?>
                        </h4>
                        <?php if ($isPdf): ?>
                        <div style="position: relative; width: 100%; height: 600px; max-height: 80vh; background: white; border-radius: 6px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            <iframe src="<?= $this->Url->build('/' . h($<%= $singularVar %>-><%= $fileField %>)) ?>" 
                                    style="width: 100%; height: 100%; border: none;"
                                    title="<?= __('<%= Inflector::humanize($fileField) %>') ?>">
                            </iframe>
                        </div>
                        <?php else: ?>
                        <div style="padding: 40px; text-align: center; background: white; border-radius: 6px; border: 2px dashed #d0d7de;">
                            <i class="fas fa-file-<?= strtolower($fileExt) === 'doc' || strtolower($fileExt) === 'docx' ? 'word' : 'alt' ?>" style="font-size: 48px; color: #0969da; margin-bottom: 15px;"></i>
                            <p style="margin: 0; color: #57606a; font-size: 14px;"><?= basename($<%= $singularVar %>-><%= $fileField %>) ?></p>
                        </div>
                        <?php endif; ?>
                        <div style="margin-top: 15px; text-align: center;">
                            <a href="<?= $this->Url->build('/' . h($<%= $singularVar %>-><%= $fileField %>)) ?>" 
                               download 
                               class="btn-export-light"
                               style="display: inline-block; margin-right: 8px;">
                                <i class="fas fa-download"></i> <?= __('Download File') ?>
                            </a>
                            <a href="<?= $this->Url->build('/' . h($<%= $singularVar %>-><%= $fileField %>)) ?>" 
                               target="_blank"
                               class="btn-export-light"
                               style="display: inline-block;">
                                <i class="fas fa-external-link-alt"></i> <?= __('Open in New Tab') ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
<%
            endforeach;
        endif;
        
        // HasMany Image Associations (Slideshow)
        if (!empty($imageAssociations)):
            foreach ($imageAssociations as $assoc):
                $alias = $assoc['alias'];
                $details = $assoc['details'];
                
                // Try to detect image field in the association
                $imageFieldName = 'image_url'; // Default
                // You might need to customize this based on your naming convention
%>
                    <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
                    <div class="image-gallery-container" style="margin-bottom: 20px; background: #f6f8fa; padding: 20px; border-radius: 8px; border: 1px solid #d0d7de;">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600; color: #24292f;">
                            <i class="fas fa-images"></i> <?= __('<%= $alias %>') ?>
                            <span class="tab-badge" style="margin-left: 8px;"><?= count($<%= $singularVar %>-><%= $details['property'] %>) ?></span>
                        </h4>
                        
                        <!-- Simple Slideshow -->
                        <div class="slideshow-container" id="slideshow-<%= strtolower($alias) %>" style="position: relative; max-width: 800px; margin: 0 auto;">
                            <?php foreach ($<%= $singularVar %>-><%= $details['property'] %> as $index => $item): ?>
                            <div class="slide" style="display: <?= $index === 0 ? 'block' : 'none' ?>; text-align: center;">
                                <img src="<?= $this->Url->build('/' . h($item-><%= $imageFieldName %>)) ?>" 
                                     alt="Image <?= $index + 1 ?>"
                                     style="max-width: 100%; height: auto; max-height: 500px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"
                                     onerror="this.parentElement.style.display='none'">
                                <div style="margin-top: 10px; color: #57606a; font-size: 13px;">
                                    <?= $index + 1 ?> / <?= count($<%= $singularVar %>-><%= $details['property'] %>) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if (count($<%= $singularVar %>-><%= $details['property'] %>) > 1): ?>
                            <!-- Navigation Arrows -->
                            <button onclick="changeSlide('slideshow-<%= strtolower($alias) %>', -1)" 
                                    style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; padding: 12px 16px; cursor: pointer; border-radius: 4px; font-size: 18px;">
                                &#10094;
                            </button>
                            <button onclick="changeSlide('slideshow-<%= strtolower($alias) %>', 1)" 
                                    style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; padding: 12px 16px; cursor: pointer; border-radius: 4px; font-size: 18px;">
                                &#10095;
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Thumbnail Navigation -->
                        <?php if (count($<%= $singularVar %>-><%= $details['property'] %>) > 1): ?>
                        <div style="display: flex; gap: 8px; margin-top: 15px; overflow-x: auto; padding: 10px 0; justify-content: center; flex-wrap: wrap;">
                            <?php foreach ($<%= $singularVar %>-><%= $details['property'] %> as $index => $item): ?>
                            <img src="<?= $this->Url->build('/' . h($item-><%= $imageFieldName %>)) ?>" 
                                 alt="Thumbnail <?= $index + 1 ?>"
                                 onclick="showSlide('slideshow-<%= strtolower($alias) %>', <?= $index ?>)"
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 2px solid transparent; transition: border-color 0.2s;"
                                 onmouseover="this.style.borderColor='#0969da'"
                                 onmouseout="this.style.borderColor='transparent'"
                                 onerror="this.style.display='none'">
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
<%
            endforeach;
        endif;
%>
                </div>

                <!-- Slideshow JavaScript -->
                <script>
                function changeSlide(containerId, direction) {
                    const container = document.getElementById(containerId);
                    const slides = container.getElementsByClassName('slide');
                    let currentIndex = -1;
                    
                    for (let i = 0; i < slides.length; i++) {
                        if (slides[i].style.display === 'block') {
                            currentIndex = i;
                            slides[i].style.display = 'none';
                            break;
                    
                    currentIndex += direction;
                    if (currentIndex >= slides.length) currentIndex = 0;
                    if (currentIndex < 0) currentIndex = slides.length - 1;
                    
                    slides[currentIndex].style.display = 'block';
                
                function showSlide(containerId, index) {
                    const container = document.getElementById(containerId);
                    const slides = container.getElementsByClassName('slide');
                    
                    for (let i = 0; i < slides.length; i++) {
                        slides[i].style.display = i === index ? 'block' : 'none';
                
                // Touch swipe support for mobile
                document.querySelectorAll('.slideshow-container').forEach(container => {
                    let touchStartX = 0;
                    let touchEndX = 0;
                    
                    container.addEventListener('touchstart', e => {
                        touchStartX = e.changedTouches[0].screenX;
                    });
                    
                    container.addEventListener('touchend', e => {
                        touchEndX = e.changedTouches[0].screenX;
                        const diff = touchStartX - touchEndX;
                        if (Math.abs(diff) > 50) {
                            changeSlide(container.id, diff > 0 ? 1 : -1);
                    });
                });
                </script>

                <!-- Mobile Responsive Styles -->
                <style>
                @media (max-width: 768px) {
                    .image-preview-container img,
                    .slideshow-container img {
                        max-height: 300px !important;
                    
                    .file-preview-container iframe {
                        height: 400px !important;
                    
                    .slideshow-container button {
                        padding: 8px 12px !important;
                        font-size: 16px !important;
                    
                    .media-preview-section {
                        margin-left: -10px;
                        margin-right: -10px;
                    
                    .image-preview-container,
                    .file-preview-container,
                    .image-gallery-container {
                        padding: 15px !important;
                        border-radius: 0 !important;
                </style>
<%
    endif;
%>
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
                // Skip image/file fields as they are already shown in preview section
                $isFileField = preg_match('/(image|file|photo|document|foto|gambar|dokumen|attachment)/i', $field);
                if (!in_array($field, ['created', 'modified', 'updated']) && !$isFileField):
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
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> ' . __('New <%= Inflector::humanize(Inflector::singularize($details['controller'])) %>'),
                                ['controller' => '<%= $details['controller'] %>', 'action' => 'add'],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-related-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover github-related-table" data-ajax-filter="true">
                                <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
                                    <tr>
                                        <th class="actions-column-header" style="width: 15px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
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
                                    </tr>
                                    <!-- Filter Row -->
                                    <tr class="filter-row" style="background-color: #f8f9fa;">
                                        <th class="actions-column-header" style="width: 15px; padding: 4px; background: transparent; border: none; position: sticky; left: 0; z-index: 10;"></th>
<%
        foreach ($otherFields as $field):
%>
                                        <th style="padding: 4px;">
                                            <div style="display: flex; gap: 3px;">
                                                <select class="form-control form-control-sm filter-operator" 
                                                        data-column="<%= $field %>" 
                                                        style="width: 70px; padding: 2px 4px; font-size: 11px;">
                                                    <option value="contains"><?= __('Contains') ?></option>
                                                    <option value="equals"><?= __('Equals') ?></option>
                                                    <option value="starts"><?= __('Starts') ?></option>
                                                    <option value="ends"><?= __('Ends') ?></option>
                                                    <option value="gt"><?= __('>') ?></option>
                                                    <option value="lt"><?= __('<') ?></option>
                                                    <option value="gte"><?= __('>=') ?></option>
                                                    <option value="lte"><?= __('<=') ?></option>
                                                    <option value="between"><?= __('Between') ?></option>
                                                </select>
                                                <input type="text" 
                                                       class="form-control form-control-sm filter-input" 
                                                       placeholder="<?= __('Filter <%= Inflector::humanize($field) %>') ?>" 
                                                       data-column="<%= $field %>"
                                                   style="font-size: 12px; height: 28px; border: 1px solid #ddd;">
                                        </th>
<%
        endforeach;
%>
                                    </tr>
                                </thead>
                                <tbody>
<%
        $otherPk = "\${$otherSingularVar}->{$details['primaryKey'][0]}";
%>
                                    <?php foreach ($<%= $singularVar %>-><%= $details['property'] %> as $<%= $otherSingularVar %>): ?>
                                    <tr class="table-row-with-actions">
                                        <td class="actions actions-column" style="padding: 5px; vertical-align: middle; white-space: nowrap; background: inherit; border: none; position: sticky; left: 0; z-index: 5; min-width: 90px;">
                                            <div class="action-buttons-hover">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-edit"></i>',
                                                    ['controller' => '<%= $details['controller'] %>', 'action' => 'edit', <%= $otherPk %>],
                                                    ['class' => 'btn-action-icon btn-edit-icon', 'escape' => false, 'title' => __('Edit')]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => '<%= $details['controller'] %>', 'action' => 'view', <%= $otherPk %>],
                                                    ['class' => 'btn-action-icon btn-view-icon', 'escape' => false, 'title' => __('View')]
                                                ) ?>
                                            </div>
                                        </td>
<%
        foreach ($otherFields as $field):
            // Check if field is a foreign key (_id suffix)
            if (substr($field, -3) === '_id'):
                $associationName = \Cake\Utility\Inflector::camelize(str_replace('_id', '', $field));
                $associationNamePlural = \Cake\Utility\Inflector::pluralize($associationName);
%>
                                        <td>
                                            <?php if ($<%= $otherSingularVar %>->has('<%= lcfirst($associationName) %>')): ?>
                                                <?php
                                                    $displayValue = $<%= $otherSingularVar %>-><%= lcfirst($associationName) %>->title;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $<%= $otherSingularVar %>-><%= lcfirst($associationName) %>->name;
                                                    if (empty($displayValue)) {
                                                        $displayValue = $<%= $otherSingularVar %>-><%= $field %>;
                                                ?>
                                                <?= $this->Html->link(h($displayValue), ['controller' => '<%= $associationNamePlural %>', 'action' => 'view', $<%= $otherSingularVar %>-><%= $field %>]) ?>
                                            <?php else: ?>
                                                <?= h($<%= $otherSingularVar %>-><%= $field %>) ?>
                                            <?php endif; ?>
                                        </td>
<%
            else:
%>
                                        <td><?= h($<%= $otherSingularVar %>-><%= $field %>) ?></td>
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
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
<%
        $tabIndex++;
    endforeach;
    
    // Add tab content for BelongsTo associations (show parent record details)
    foreach ($associations['BelongsTo'] as $alias => $details):
        $otherSingularVar = Inflector::variable($alias);
        $otherSingularHumanName = Inflector::humanize(Inflector::singularize($alias));
        $tabId = 'tab-' . strtolower(Inflector::underscore($alias));
%>
            <!-- <%= $otherSingularHumanName %> Tab (BelongsTo) -->
            <div id="<%= $tabId %>" class="view-tab-pane">
                <?php if ($<%= $singularVar %>->has('<%= $details['property'] %>')): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('<%= $otherSingularHumanName %> Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => '<%= $details['controller'] %>', 'action' => 'edit', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
<%
        // Show fields from associated record
        $otherFields = collection($details['fields'])
            ->filter(function($field) {
                return !in_array($field, ['created', 'modified', 'updated']);
            })
            ->take(10); // Limit to first 10 fields

        foreach ($otherFields as $field):
%>
                                <tr>
                                    <th class="github-detail-label"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                                    <td class="github-detail-value"><?= h($<%= $singularVar %>-><%= $details['property'] %>-><%= $field %>) ?></td>
                                </tr>
<%
        endforeach;
%>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No <%= $otherSingularHumanName %> Associated') ?></h3>
                    <p class="empty-message"><?= __('This record is not associated with any <%= $otherSingularHumanName %>.') ?></p>
                </div>
                <?php endif; ?>
            </div>
<%
    endforeach;
    
    // Add tab content for HasOne associations
    foreach ($associations['HasOne'] as $alias => $details):
        $otherSingularVar = Inflector::variable($alias);
        $otherSingularHumanName = Inflector::humanize(Inflector::singularize($alias));
        $tabId = 'tab-' . strtolower(Inflector::underscore($alias));
%>
            <!-- <%= $otherSingularHumanName %> Tab (HasOne) -->
            <div id="<%= $tabId %>" class="view-tab-pane">
                <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
                <div class="github-details-card">
                    <div class="github-details-header">
                        <h3 class="github-details-title">
                            <svg class="octicon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm.061 3.073a4 4 0 1 0-5.123 0 6.004 6.004 0 0 0-3.431 5.142.75.75 0 0 0 1.498.07 4.5 4.5 0 0 1 8.99 0 .75.75 0 1 0 1.498-.07 6.005 6.005 0 0 0-3.432-5.142z"></path>
                            </svg>
                            <?= __('<%= $otherSingularHumanName %> Details') ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-external-link-alt"></i> ' . __('View Full Record'),
                                ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right; margin-left: 10px;']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> ' . __('Edit'),
                                ['controller' => '<%= $details['controller'] %>', 'action' => 'edit', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>],
                                ['class' => 'btn-export-light', 'escape' => false, 'style' => 'float: right;']
                            ) ?>
                        </h3>
                    </div>

                    <div class="github-details-body">
                        <table class="github-details-table">
                            <tbody>
<%
        // Show fields from associated record
        $otherFields = collection($details['fields'])
            ->filter(function($field) {
                return !in_array($field, ['created', 'modified', 'updated']);
            })
            ->take(10); // Limit to first 10 fields

        foreach ($otherFields as $field):
%>
                                <tr>
                                    <th class="github-detail-label"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                                    <td class="github-detail-value"><?= h($<%= $singularVar %>-><%= $details['property'] %>-><%= $field %>) ?></td>
                                </tr>
<%
        endforeach;
%>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="github-empty-state">
                    <svg class="empty-icon" width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M10.5 5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"></path>
                    </svg>
                    <h3 class="empty-title"><?= __('No <%= $otherSingularHumanName %> Found') ?></h3>
                    <p class="empty-message"><?= __('This record does not have an associated <%= $otherSingularHumanName %>.') ?></p>
                    <?= $this->Html->link(
                        __('Add <%= $otherSingularHumanName %>'),
                        ['controller' => '<%= $details['controller'] %>', 'action' => 'add'],
                        ['class' => 'btn-export-light']
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
<%
    endforeach;
%>
        </div>
        <!-- End Tab Contents -->
    </div>
    <!-- End Tabs Container -->
</div>
<!-- End GitHub Container -->

<script>
// Tab Switching JavaScript - Modal Safe
document.addEventListener('DOMContentLoaded', function() {
    initializeTabSwitching();
});

// Also initialize when content is dynamically loaded (e.g., in modals)
if (typeof window.initializeTabSwitching === 'undefined') {
    window.initializeTabSwitching = function(container) {
        // Find container (for modal context or specific view container)
        const viewContainer = container || document.querySelector('.github-container') || document;
        
        // Use data-view-template attribute to target only the primary view template tabs
        // This prevents selecting nested tabs in child associate data
        const isDocument = (viewContainer === document);
        
        let tabLinks, tabPanes;
        
        if (isDocument) {
            // When querying whole document, find tabs within view-content-wrapper that has data-view-template
            const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
            if (!viewWrapper) {
                console.warn('No view-content-wrapper with data-view-template found');
                return;
            tabLinks = viewWrapper.querySelectorAll('.view-tabs-container .view-tab-link');
            tabPanes = viewWrapper.querySelectorAll('.view-tab-pane');
        } else {
            // When container is specified, search within it for the wrapper
            const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
            if (viewWrapper) {
                tabLinks = viewWrapper.querySelectorAll('.view-tabs-container .view-tab-link');
                tabPanes = viewWrapper.querySelectorAll('.view-tab-pane');
            } else {
                // Fallback: container might BE the wrapper already
                tabLinks = viewContainer.querySelectorAll('.view-tab-link');
                tabPanes = viewContainer.querySelectorAll('.view-tab-pane');
        
        if (tabLinks.length === 0 || tabPanes.length === 0) {
            console.error('âŒ No tabs found! TabLinks:', tabLinks.length, 'TabPanes:', tabPanes.length);
            console.error('Container:', viewContainer);
            if (isDocument) {
                console.error('View wrapper:', document.querySelector('.view-content-wrapper[data-view-template="true"]'));
            return;
        
        console.log('âœ… SUCCESS: Initializing', tabLinks.length, 'tab links and', tabPanes.length, 'tab panes');
        console.log('ðŸ“ Container:', isDocument ? 'document (full page)' : 'element (modal/container)');
        
        // Remove existing click handlers to avoid duplicates
        tabLinks.forEach(link => {
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
        });
        
        // Re-query after cloning (use same wrapper logic)
        let freshTabLinks, freshTabPanes;
        
        if (isDocument) {
            const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
            freshTabLinks = viewWrapper.querySelectorAll('.view-tabs-container .view-tab-link');
            freshTabPanes = viewWrapper.querySelectorAll('.view-tab-pane');
        } else {
            const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
            if (viewWrapper) {
                freshTabLinks = viewWrapper.querySelectorAll('.view-tabs-container .view-tab-link');
                freshTabPanes = viewWrapper.querySelectorAll('.view-tab-pane');
            } else {
                freshTabLinks = viewContainer.querySelectorAll('.view-tab-link');
                freshTabPanes = viewContainer.querySelectorAll('.view-tab-pane');
        
        freshTabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('ðŸ–±ï¸  Tab clicked:', this.getAttribute('data-tab'));
                
                // Get target tab
                const targetTab = this.getAttribute('data-tab');
                
                if (!targetTab) {
                    console.error('âŒ No data-tab attribute found on clicked element');
                    return;
                
                // Remove active class from all tabs and panes in this container
                freshTabLinks.forEach(l => {
                    l.classList.remove('active');
                });
                freshTabPanes.forEach(p => {
                    p.classList.remove('active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Find and activate corresponding pane (search within wrapper to avoid nested tabs)
                let targetPane;
                if (isDocument) {
                    const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
                    targetPane = viewWrapper ? viewWrapper.querySelector('#' + targetTab) : null;
                } else {
                    const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
                    targetPane = viewWrapper ? viewWrapper.querySelector('#' + targetTab) : viewContainer.querySelector('#' + targetTab);
                
                if (targetPane) {
                    targetPane.classList.add('active');
                    console.log('âœ… Activated pane:', targetTab);
                } else {
                    console.error('âŒ Target pane not found:', targetTab);
                    console.error('Available panes:', freshTabPanes ? Array.from(freshTabPanes).map(p => p.id) : 'none');
                
                // Store active tab in sessionStorage (optional)
                try {
                    sessionStorage.setItem('activeViewTab', targetTab);
                } catch (e) {
                    console.warn('sessionStorage not available:', e);
            });
        });
        
        // Restore active tab from sessionStorage
        try {
            const savedTab = sessionStorage.getItem('activeViewTab');
            if (savedTab) {
                // Search within wrapper to find saved tab
                let targetPane, savedLink;
                
                if (isDocument) {
                    const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
                    if (viewWrapper) {
                        targetPane = viewWrapper.querySelector('#' + savedTab);
                        savedLink = viewWrapper.querySelector('[data-tab="' + savedTab + '"]');
                } else {
                    const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
                    if (viewWrapper) {
                        targetPane = viewWrapper.querySelector('#' + savedTab);
                        savedLink = viewWrapper.querySelector('[data-tab="' + savedTab + '"]');
                    } else {
                        targetPane = viewContainer.querySelector('#' + savedTab);
                        savedLink = viewContainer.querySelector('[data-tab="' + savedTab + '"]');
                
                if (targetPane && savedLink) {
                    savedLink.click();
        } catch (e) {
            console.warn('Could not restore tab from sessionStorage:', e);
    };

// Drag to scroll for tabs navigation
document.addEventListener('DOMContentLoaded', function() {
    // Only target view template tabs, not main navigation
    const tabsNav = document.querySelector('.github-container .view-tabs-nav');
    if (!tabsNav) return;
    
    let isDown = false;
    let startX;
    let scrollLeft;
    
    tabsNav.addEventListener('mousedown', (e) => {
        // Only activate drag on empty areas, not on links
        if (e.target.closest('.view-tab-link')) return;
        
        isDown = true;
        tabsNav.classList.add('dragging');
        startX = e.pageX - tabsNav.offsetLeft;
        scrollLeft = tabsNav.scrollLeft;
        tabsNav.style.cursor = 'grabbing';
    });
    
    tabsNav.addEventListener('mouseleave', () => {
        isDown = false;
        tabsNav.classList.remove('dragging');
        tabsNav.style.cursor = 'grab';
    });
    
    tabsNav.addEventListener('mouseup', () => {
        isDown = false;
        tabsNav.classList.remove('dragging');
        tabsNav.style.cursor = 'grab';
    });
    
    tabsNav.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - tabsNav.offsetLeft;
        const walk = (x - startX) * 2; // Scroll speed multiplier
        tabsNav.scrollLeft = scrollLeft - walk;
    });
    
    // Initialize tab switching after DOM is ready
    window.initializeTabSwitching();
});

// Auto-initialize tabs in Bootstrap modals - Multiple detection methods
// Method 1: Bootstrap modal event (jQuery)
if (typeof jQuery !== 'undefined') {
    jQuery(document).on('shown.bs.modal', '.modal', function(e) {
        console.log('Modal opened (jQuery event), checking for tabs...');
        const modal = this;
        
        // Wait a bit for content to settle (especially for AJAX-loaded modals)
        setTimeout(function() {
            const modalBody = modal.querySelector('.modal-body') || modal.querySelector('.modal-content');
            if (modalBody) {
                // Look for any tabs, not just those with data-view-template
                const tabLinks = modalBody.querySelectorAll('.view-tab-link');
                if (tabLinks.length > 0) {
                    console.log('Found', tabLinks.length, 'tab links in modal, initializing...');
                    window.initializeTabSwitching(modalBody);
                } else {
                    console.log('No tab links found in modal');
        }, 100);
    });
    
    // Also listen for show event (before animation)
    jQuery(document).on('show.bs.modal', '.modal', function(e) {
        console.log('Modal showing (before animation)...');
    });

// Method 2: Vanilla JavaScript modal event (Bootstrap 5)
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up modal observers...');
    
    // Listen for all modals
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            console.log('Modal opened (vanilla event), checking for tabs...');
            const modalBody = this.querySelector('.modal-body') || this.querySelector('.modal-content');
            if (modalBody) {
                const tabLinks = modalBody.querySelectorAll('.view-tab-link');
                if (tabLinks.length > 0) {
                    console.log('Found', tabLinks.length, 'tab links in modal, initializing...');
                    window.initializeTabSwitching(modalBody);
        });
    });
    
    // Method 3: MutationObserver for dynamically added modals or content
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    // Check if the added node is a modal or contains a modal
                    let modals = [];
                    if (node.classList && node.classList.contains('modal')) {
                        modals.push(node);
                    modals.push(...node.querySelectorAll('.modal'));
                    
                    modals.forEach(function(modal) {
                        // Check if modal is visible and has tabs
                        if (modal.classList.contains('show') || modal.style.display !== 'none') {
                            const modalBody = modal.querySelector('.modal-body') || modal.querySelector('.modal-content');
                            if (modalBody) {
                                const tabLinks = modalBody.querySelectorAll('.view-tab-link');
                                if (tabLinks.length > 0) {
                                    console.log('MutationObserver: Found modal with', tabLinks.length, 'tabs, initializing...');
                                    window.initializeTabSwitching(modalBody);
                    });
                    
                    // Also check if tabs were added to an existing modal
                    if (node.closest('.modal-body')) {
                        const tabLinks = node.querySelectorAll('.view-tab-link');
                        if (tabLinks.length > 0) {
                            console.log('MutationObserver: Tabs added to modal, initializing...');
                            const modalBody = node.closest('.modal-body');
                            window.initializeTabSwitching(modalBody);
            });
        });
    });
    
    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    console.log('MutationObserver active for modal content detection');
});

// Method 4: Global function for manual initialization
window.initializeModalTabs = function(modalSelector) {
    const modal = typeof modalSelector === 'string' 
        ? document.querySelector(modalSelector) 
        : modalSelector;
    
    if (modal) {
        const modalBody = modal.querySelector('.modal-body') || modal.querySelector('.modal-content');
        if (modalBody) {
            console.log('Manual initialization requested for modal');
            window.initializeTabSwitching(modalBody);
};

// Usage examples in comments:
// After loading modal content via AJAX: window.initializeModalTabs('#myModal');
// Or pass the element directly: window.initializeModalTabs(document.getElementById('myModal'));
</script>

<style>
/* View Content Wrapper - Modal Safe Isolation */
.view-content-wrapper {
    position: relative !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;

/* Ensure tabs work in modals - override Bootstrap modal styles */
.modal .view-content-wrapper,
.modal-body .view-content-wrapper {
    overflow: visible !important;
    transform: none !important;

.modal .view-tabs-container,
.modal-body .view-tabs-container {
    overflow: visible !important;

.modal .view-tabs-nav,
.modal-body .view-tabs-nav {
    flex-wrap: nowrap !important;
    transform: none !important;

/* Fix Bootstrap modal conflicts */
.modal a.view-tab-link,
.modal-body a.view-tab-link {
    color: #586069 !important;

.modal a.view-tab-link.active,
.modal-body a.view-tab-link.active {
    color: #0969da !important;

.modal a.view-tab-link:hover,
.modal-body a.view-tab-link:hover {
    color: #24292f !important;
    text-decoration: none !important;

/* Button spacing for view actions */
.github-header-actions .btn {
    margin-right: 8px;

.github-header-actions .btn:last-child {
    margin-right: 0;

/* Tab Navigation Styles - Modal Safe with !important overrides */
.view-tabs-container {
    margin: 20px 0 !important;
    position: relative !important;
    z-index: 1 !important;
    background: #ffffff !important;
    border-radius: 6px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12) !important;
    border: 1px solid #d1d5da !important;
    overflow: hidden !important;
    display: block !important;

/* Override any modal/page styles that might affect tabs */
.modal .view-tabs-container,
.modal-body .view-tabs-container {
    margin: 20px 0 !important;
    width: 100% !important;
    max-width: 100% !important;

.view-tabs-nav {
    display: flex !important;
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    border-bottom: 2px solid #e1e4e8 !important;
    background: #fafbfc !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
    scroll-behavior: smooth !important;
    position: relative !important;
    z-index: 1 !important;
    flex-wrap: nowrap !important;

.view-tabs-nav.dragging {
    cursor: grabbing !important;
    scroll-behavior: auto !important;

/* Transparent scrollbar */
.view-tabs-nav::-webkit-scrollbar {
    height: 8px !important;

.view-tabs-nav::-webkit-scrollbar-track {
    background: transparent !important;

.view-tabs-nav::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1) !important;
    border-radius: 4px !important;

.view-tabs-nav::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.2) !important;

/* Firefox */
.view-tabs-nav {
    scrollbar-width: thin !important;
    scrollbar-color: rgba(0, 0, 0, 0.1) transparent !important;

.view-tab-item {
    margin: 0 !important;
    flex-shrink: 0 !important;
    display: block !important;

.view-tab-link {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 14px 24px !important;
    color: #586069 !important;
    text-decoration: none !important;
    border-bottom: 3px solid transparent !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    font-weight: 500 !important;
    background: transparent !important;
    border-right: 1px solid rgba(225, 228, 232, 0.5) !important;
    position: relative !important;
    width: auto !important;
    height: auto !important;
    line-height: 1.5 !important;

.view-tab-link:last-child {
    border-right: none !important;

.view-tab-link:hover {
    color: #24292f !important;
    background: rgba(175, 184, 193, 0.2) !important;
    border-bottom-color: #959da5 !important;
    text-decoration: none !important;

.view-tab-link.active {
    color: #0969da !important;
    border-bottom-color: #0969da !important;
    font-weight: 600 !important;
    background: #ffffff !important;
    z-index: 2 !important;

.view-tab-link.active::after {
    content: '' !important;
    position: absolute !important;
    bottom: -2px !important;
    left: 0 !important;
    right: 0 !important;
    height: 2px !important;
    background: #ffffff !important;

.tab-icon {
    flex-shrink: 0 !important;
    display: inline-block !important;

.tab-badge {
    display: inline-block !important;
    padding: 3px 10px !important;
    background: #e1e4e8 !important;
    border-radius: 12px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #586069 !important;
    min-width: 20px !important;
    text-align: center !important;

.view-tab-link.active .tab-badge {
    background: #0969da !important;
    color: #ffffff !important;

.view-tabs-content {
    margin-top: 20px !important;
    display: block !important;
    width: 100% !important;

.view-tab-pane {
    display: none !important;
    width: 100% !important;

.view-tab-pane.active {
    display: block !important;

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
    width: 100%;
    overflow: hidden;

.github-related-title::after {
    content: "";
    display: block;
    clear: both;

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
    background: transparent;
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

/* Action Buttons Styles (same as index page) */
.action-buttons-hover {
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
    display: flex;
    gap: 2px;

.table-row-with-actions:hover .action-buttons-hover {
    opacity: 1;

.actions-column {
    background-color: #fff !important;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);

.table-striped tbody tr:nth-of-type(odd) .actions-column {
    background-color: rgba(102, 126, 234, 0.05) !important;

.table-striped tbody tr:nth-of-type(even) .actions-column {
    background-color: #fff !important;

.table-row-with-actions:hover .actions-column {
    background-color: rgba(102, 126, 234, 0.08) !important;

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

/* Filter Row Styles */
.filter-row input.filter-input {
    width: 100%;
    padding: 4px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 12px;

.filter-row input.filter-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);

/* Drag to scroll for tables */
.table-responsive {
    cursor: grab;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;

.table-responsive:active {
    cursor: grabbing;

.table-responsive.dragging {
    cursor: grabbing;
    user-select: none;
</style>

<script>
// Drag to scroll functionality for tables
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainers = document.querySelectorAll('.table-responsive');
    
    scrollContainers.forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        container.addEventListener('mousedown', (e) => {
            // Don't activate drag on form inputs or buttons
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON' || e.target.closest('a')) {
                return;
            
            isDown = true;
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('dragging');
        });
        
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // Scroll speed multiplier
            container.scrollLeft = scrollLeft - walk;
        });
    });
});

// Table Filter JavaScript with Operator Support (client-side filtering for related tables)
document.addEventListener('DOMContentLoaded', function() {
    const tables = document.querySelectorAll('table[data-ajax-filter="true"]');
    
    tables.forEach(table => {
        const filterInputs = table.querySelectorAll('.filter-input');
        const filterOperators = table.querySelectorAll('.filter-operator');
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr.table-row-with-actions');
        
        // Function to apply filter with operator
        function applyFilter(cellText, filterValue, operator) {
            const cellVal = cellText.trim().toLowerCase();
            const filterVal = filterValue.trim().toLowerCase();
            
            if (filterVal === '') return true;
            
            // Try to parse as numbers for numeric comparisons
            const cellNum = parseFloat(cellVal);
            const filterNum = parseFloat(filterVal);
            const isNumeric = !isNaN(cellNum) && !isNaN(filterNum);
            
            switch(operator) {
                case 'equals':
                    return cellVal === filterVal;
                case 'contains':
                    return cellVal.includes(filterVal);
                case 'starts':
                    return cellVal.startsWith(filterVal);
                case 'ends':
                    return cellVal.endsWith(filterVal);
                case 'gt':
                    return isNumeric ? cellNum > filterNum : cellVal > filterVal;
                case 'lt':
                    return isNumeric ? cellNum < filterNum : cellVal < filterVal;
                case 'gte':
                    return isNumeric ? cellNum >= filterNum : cellVal >= filterVal;
                case 'lte':
                    return isNumeric ? cellNum <= filterNum : cellVal <= filterVal;
                case 'between':
                    // Format: "min,max" or "min-max"
                    const parts = filterVal.split(/[,\-]/);
                    if (parts.length === 2 && isNumeric) {
                        const min = parseFloat(parts[0]);
                        const max = parseFloat(parts[1]);
                        return cellNum >= min && cellNum <= max;
                    return cellVal.includes(filterVal);
                default:
                    return cellVal.includes(filterVal);
        
        // Function to filter all rows
        function filterRows() {
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let shouldShow = true;
                
                // Check all active filters
                filterInputs.forEach(filterInput => {
                    const filterVal = filterInput.value;
                    if (filterVal === '') return;
                    
                    const filterColumn = filterInput.getAttribute('data-column');
                    
                    // Find corresponding operator
                    let operator = 'contains'; // default
                    filterOperators.forEach(op => {
                        if (op.getAttribute('data-column') === filterColumn) {
                            operator = op.value;
                    });
                    
                    // Get column index
                    const headers = Array.from(table.querySelectorAll('thead tr:first-child th'));
                    let filterColIndex = -1;
                    headers.forEach((th, idx) => {
                        if (th.textContent.toLowerCase().includes(filterColumn.replace('_', ' '))) {
                            filterColIndex = idx;
                    });
                    
                    if (filterColIndex >= 0 && cells[filterColIndex]) {
                        const cellText = cells[filterColIndex].textContent;
                        if (!applyFilter(cellText, filterVal, operator)) {
                            shouldShow = false;
                });
                
                row.style.display = shouldShow ? '' : 'none';
            });
        
        // Attach event listeners to inputs and operators
        filterInputs.forEach(input => {
            input.addEventListener('input', filterRows);
        });
        
        filterOperators.forEach(select => {
            select.addEventListener('change', filterRows);
        });
    });
});

// Dropdown Menu Functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButton = document.getElementById('dropdownMenuButton');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownButton && dropdownMenu) {
        // Toggle dropdown on button click
        dropdownButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
        });
        
        // Close dropdown after clicking a link (with small delay for navigation)
        dropdownMenu.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                setTimeout(function() {
                    dropdownMenu.classList.remove('show');
                }, 100);
        });
        
        console.log('âœ… Dropdown menu initialized on view page');
});
</script>

<!-- Dropdown Menu CSS -->
<style>
/* Dropdown Menu Styling */
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    min-width: 200px;
    padding: 0.5rem 0;
    margin: 0.125rem 0 0;
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);

.dropdown-menu.show {
    display: block;

.dropdown-item {
    display: block;
    width: 100%;
    padding: 0.5rem 1rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
    text-decoration: none;

.dropdown-item:hover {
    color: #16181b;
    background-color: #f8f9fa;
    text-decoration: none;

.dropdown-divider {
    height: 0;
    margin: 0.5rem 0;
    overflow: hidden;
    border-top: 1px solid #e9ecef;

.dropdown-toggle::after {
    display: none; /* Remove default Bootstrap caret */

/* Position dropdown container relatively */
.github-title-row > div:first-child {
    position: relative;
</style>

</div><!-- .view-content-wrapper -->

<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
