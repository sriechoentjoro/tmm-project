<?php
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
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$cakeDescription = 'TMM: Apprentice Management Modules';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('style.css') ?>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
    <script src="/js/simple-filter.js"></script>
    
    <!-- Romaji to Katakana Converter for Japanese name input -->
    <script src="/js/romaji-to-katakana.js"></script>
    
    <style>
        /* Force Top Bar Visibility */
        .top-bar {
            height: auto;
            min-height: 45px;
            overflow: visible !important;
        }
        .top-bar-section {
            display: block !important;
        }
        .top-bar-section ul.right {
            float: right !important;
            display: block !important;
            width: auto !important;
        }
        .top-bar-section ul.right li {
            float: left;
            display: block;
        }
        
        /* CRITICAL: Override Bootstrap for dropdown links */
        .header-dropdown a {
            color: #333 !important;
            text-decoration: none !important;
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        .header-dropdown a:hover {
            background-color: #f0f0f0 !important;
            color: #333 !important;
        }
        
        /* CRITICAL: Override Bootstrap for username color */
        .header-dropdown-trigger > a > span {
            color: #2c3e50 !important;
        }
        
        /* Ensure dropdown items are clickable */
        .header-dropdown li {
            pointer-events: auto !important;
        }
        
        /* Dropdown Hover Fix */
        .top-bar-section li.has-dropdown:hover > .dropdown {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            top: 100%;
        }
        
        /* Ensure icons are visible */
        .fa {
            display: inline-block;
        }
        
        /* Mobile/Small Screen Fix */
        @media only screen and (max-width: 40em) {
            .top-bar-section {
                display: block !important; /* Force show on mobile too for now */
            }
            .top-bar-section ul.right {
                float: none !important;
            }
            .top-bar-section ul.right li {
                float: none;
                display: block;
                width: 100%;
            }
        }
        
        /* Page Header Styles */
        .page-header {
            background-color: #D33C44;
            color: #ffffff;
            padding: 15px 0;
            margin-bottom: 0;
            display: block !important;
            width: 100%;
        }
        .page-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }
        .page-header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 0;
            font-weight: bold;
            line-height: 1.2;
        }
        
        /* User Menu Styles */
        .user-menu {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .user-menu li {
            margin-left: 20px;
            position: relative;
        }
        /* Top-level links in header - white text on red background */
        .user-menu > li > a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        .user-menu > li > a:hover {
            color: #f0f0f0;
        }
        /* EXCEPTION: Login link needs visible styling */
        .user-menu > li > a[href*="login"] {
            color: #ffffff !important;
            background: rgba(0,0,0,0.4) !important;
            border: 2px solid rgba(255,255,255,0.7) !important;
            padding: 8px 20px !important;
            border-radius: 4px !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
        }
        .user-menu > li > a[href*="login"]:hover {
            background: rgba(0,0,0,0.5) !important;
            border-color: rgba(255,255,255,0.9) !important;
        }
        /* Dropdown links - dark text on white background */
        .header-dropdown a {
            color: #333 !important;
            text-decoration: none;
        }
        .header-dropdown a:hover {
            background-color: #f5f5f5;
        }
    </style>

    <!-- CRITICAL: Bootstrap Override -->
    <style>
        body .header-dropdown a { color: #333 !important; text-decoration: none !important; }
        body .header-dropdown a:hover { background: #f5f5f5 !important; }
        body .header-dropdown-trigger span { color: #333 !important; }
    </style>
    
    <!-- Form Font-Family Inheritance & Enhanced Select Styling -->
    <style>
        /* Make all form elements inherit body font-family */
        input,
        select,
        textarea,
        button,
        label,
        .form-control,
        .form-select,
        option {
            font-family: inherit !important;
        }
        
        /* Enhanced Select Input Styling */
        select,
        select.form-control,
        .form-select {
            font-family: inherit !important;
            border: 2px solid #cbd5e0 !important;
            border-radius: 6px !important;
            padding: 8px 12px !important;
            background-color: #ffffff !important;
            color: #2d3748 !important;
            font-size: 14px !important;
            line-height: 1.5 !important;
            transition: all 0.2s ease !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
            background-size: 12px !important;
            padding-right: 36px !important;
            cursor: pointer !important;
        }
        
        select:hover,
        select.form-control:hover,
        .form-select:hover {
            border-color: #4299e1 !important;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1) !important;
        }
        
        select:focus,
        select.form-control:focus,
        .form-select:focus {
            outline: none !important;
            border-color: #4299e1 !important;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2) !important;
            background-color: #f7fafc !important;
        }
        
        select:disabled,
        select.form-control:disabled,
        .form-select:disabled {
            background-color: #edf2f7 !important;
            border-color: #e2e8f0 !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }
        
        /* Enhanced Input & Textarea Styling */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"],
        input[type="date"],
        input[type="password"],
        textarea,
        input.form-control,
        textarea.form-control {
            font-family: inherit !important;
            border: 2px solid #cbd5e0 !important;
            border-radius: 6px !important;
            padding: 8px 12px !important;
            background-color: #ffffff !important;
            color: #2d3748 !important;
            font-size: 14px !important;
            line-height: 1.5 !important;
            transition: all 0.2s ease !important;
        }
        
        input[type="text"]:hover,
        input[type="email"]:hover,
        input[type="tel"]:hover,
        input[type="number"]:hover,
        input[type="date"]:hover,
        input[type="password"]:hover,
        textarea:hover {
            border-color: #4299e1 !important;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="password"]:focus,
        textarea:focus {
            outline: none !important;
            border-color: #4299e1 !important;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2) !important;
            background-color: #f7fafc !important;
        }
        
        /* Placeholder text styling */
        ::placeholder {
            font-family: inherit !important;
            color: #a0aec0 !important;
            opacity: 1 !important;
        }
        
        :-ms-input-placeholder {
            font-family: inherit !important;
            color: #a0aec0 !important;
        }
        
        ::-ms-input-placeholder {
            font-family: inherit !important;
            color: #a0aec0 !important;
        }
        
        /* Label styling */
        label,
        .form-label,
        label.form-label {
            font-family: inherit !important;
            color: #2d3748 !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            margin-bottom: 6px !important;
            display: inline-block !important;
        }
        
        /* Button font inheritance */
        button,
        .btn,
        input[type="submit"],
        input[type="button"] {
            font-family: inherit !important;
        }
        
        /* Select option styling */
        select option {
            font-family: inherit !important;
            padding: 8px 12px !important;
            background-color: #ffffff !important;
            color: #2d3748 !important;
        }
        
        select option:hover,
        select option:checked {
            background-color: #edf2f7 !important;
        }
    </style>
</head>
<body>
    <!-- DEBUG: RAW SESSION DUMP -->
    <div style="background: #000; color: #0f0; padding: 10px; font-family: monospace; z-index: 9999; position: relative;">
        <strong>DEBUG INFO:</strong><br>
        Session ID: <?= $this->request->getSession()->id() ?><br>
        Auth User Check: <?= $this->request->getSession()->check('Auth.User.id') ? 'TRUE' : 'FALSE' ?><br>
        Auth User Data: <pre><?= print_r($this->request->getSession()->read('Auth.User'), true) ?></pre>
    </div>

    <div class="page-header">
        <div class="container">
            <div class="header-title">
                <h1><i class="fa fa-graduation-cap"></i> TMM - Apprentice Management</h1>
            </div>
            
            <div class="header-user-menu">
                <ul class="user-menu">
                    <?php if ($this->request->getSession()->check('Auth.User.id')): ?>
                        <!-- Language Switcher -->
                        <li class="header-dropdown-trigger">
                            <a href="#">
                                <i class="fa fa-language" style="font-size: 18px;"></i>
                                <span style="margin-left: 5px; margin-right: 5px;">
                                    <?php 
                                    $currentLang = $this->request->getSession()->read('Config.language') ?: 'ind';
                                    echo strtoupper($currentLang);
                                    ?>
                                </span>
                                <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="header-dropdown" style="display: none; position: absolute; top: 100%; right: 0; background: white; min-width: 150px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border-radius: 4px; padding: 5px 0; z-index: 1000; list-style: none;">
                                <li><?= $this->Html->link('Ã°Å¸â€¡Â®Ã°Å¸â€¡Â© Indonesia', ['controller' => 'Users', 'action' => 'changeLanguage', 'ind'], ['style' => 'display: block; padding: 8px 15px; color: #333; text-decoration: none;']) ?></li>
                                <li><?= $this->Html->link('Ã°Å¸â€¡Â¬Ã°Å¸â€¡Â§ English', ['controller' => 'Users', 'action' => 'changeLanguage', 'eng'], ['style' => 'display: block; padding: 8px 15px; color: #333; text-decoration: none;']) ?></li>
                                <li><?= $this->Html->link('Ã°Å¸â€¡Â¯Ã°Å¸â€¡Âµ Ã¦â€”Â¥Ã¦Å“Â¬Ã¨ÂªÅ¾', ['controller' => 'Users', 'action' => 'changeLanguage', 'jpn'], ['style' => 'display: block; padding: 8px 15px; color: #333; text-decoration: none;']) ?></li>
                            </ul>
                        </li>
                        
                        <!-- Simple Logout Button (Backup) -->
                        <li style="margin-right: 10px;">
                            <?= $this->Form->create(null, [
                                'url' => ['controller' => 'Users', 'action' => 'logout'],
                                'style' => 'margin: 0; padding: 0;'
                            ]) ?>
                                <?= $this->Form->button('<i class="fa fa-sign-out"></i> Logout', [
                                    'type' => 'submit',
                                    'escape' => false,
                                    'style' => 'background: #d33c44; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 14px;'
                                ]) ?>
                            <?= $this->Form->end() ?>
                        </li>
                        
                        <!-- User Profile -->
                        <li class="header-dropdown-trigger" style="position: relative;">
                            <a href="#" style="text-decoration: none; display: flex; align-items: center; padding: 8px 15px; background: rgba(255,255,255,0.9); border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <?php 
                                $user = $this->request->getSession()->read('Auth.User');
                                if (empty($user)) {
                                    $userName = 'Guest';
                                    $displayName = 'Guest';
                                } else {
                                    $userName = isset($user['fullname']) ? $user['fullname'] : (isset($user['username']) ? $user['username'] : 'User');
                                    // Humanize username - capitalize each word properly
                                    $displayName = ucwords(strtolower(str_replace('_', ' ', $userName)));
                                }
                                
                                // Generate initials from username or fullname
                                $nameParts = explode(' ', trim($displayName));
                                if (count($nameParts) >= 2) {
                                    // First and last name initials
                                    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts) - 1], 0, 1));
                                } else {
                                    // First two characters of username
                                    $initials = strtoupper(substr($displayName, 0, 2));
                                }
                                
                                // Generate background color from username (consistent color per user)
                                $colors = array('#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e', '#e67e22');
                                $colorIndex = ord($displayName[0]) % count($colors);
                                $bgColor = $colors[$colorIndex];
                                ?>
                                
                                <?php if (!empty($user['photo'])): ?>
                                    <img src="<?= h($user['photo']) ?>" alt="User" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; margin-right: 8px;">
                                <?php else: ?>
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: <?= $bgColor ?>; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; color: #fff; border: 2px solid #ddd; margin-right: 8px;">
                                        <?= h($initials) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <span style="color: #333; font-weight: 600; font-size: 14px; margin-right: 5px;"><?= h($displayName) ?></span>
                                <i class="fa fa-caret-down" style="color: #666; font-size: 12px;"></i>
                            </a>
                            <ul class="header-dropdown" style="display: none; position: absolute; top: 100%; right: 0; min-width: 180px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border-radius: 4px; padding: 5px 0; z-index: 1000; list-style: none; margin: 0;">
                                <li style="list-style: none;">
                                    <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'profile']) ?>" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f5f5f5'" onmouseout="this.style.backgroundColor='transparent'">
                                        <i class="fa fa-user" style="margin-right: 8px; width: 16px;"></i> Profile
                                    </a>
                                </li>
                                <li style="list-style: none;">
                                    <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'settings']) ?>" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f5f5f5'" onmouseout="this.style.backgroundColor='transparent'">
                                        <i class="fa fa-cog" style="margin-right: 8px; width: 16px;"></i> Settings
                                    </a>
                                </li>
                                <li style="border-top: 1px solid #eee; margin: 5px 0; list-style: none;"></li>
                                <li style="padding: 0; list-style: none;">
                                    <?= $this->Form->create(null, [
                                        'url' => ['controller' => 'Users', 'action' => 'logout'],
                                        'style' => 'margin: 0;'
                                    ]) ?>
                                        <?= $this->Form->button('<i class="fa fa-sign-out"></i> Logout', [
                                            'type' => 'submit',
                                            'escape' => false,
                                            'style' => 'width: 100%; text-align: left; display: block; padding: 10px 15px; color: #dc3545; background: none; border: none; cursor: pointer; font-size: 14px; transition: background 0.2s;',
                                            'onmouseover' => 'this.style.backgroundColor="#ffebee"',
                                            'onmouseout' => 'this.style.backgroundColor="transparent"'
                                        ]) ?>
                                    <?= $this->Form->end() ?>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Login Link (Styling handled by CSS .user-menu > li > a[href*="login"]) -->
                        <li>
                            <?= $this->Html->link(
                                '<i class="fa fa-sign-in"></i> Login',
                                ['controller' => 'Users', 'action' => 'login'],
                                ['escape' => false]
                            ) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Enhanced Tab Menu - Database-driven with Permission Filtering -->
    <?php if ($this->request->getSession()->check('Auth.User.id')): ?>
        <?= $this->element('elegant_menu') ?>
    <?php endif; ?>

    <nav class="top-bar expanded" data-topbar role="navigation">
        <ul class="title-area large-3 medium-4 columns">
            <li class="name">
                <h1><a href=""><?= $this->fetch('title') ?></a></h1>
            </li>
        </ul>
        <!-- Removed old user menu from here -->
    </nav>
    <?= $this->Flash->render() ?>
    <div class="container clearfix">
        <?= $this->fetch('content') ?>
    </div>
    <footer>
    </footer>
    <?= $this->Html->script('ajax-table-filter') ?>
<script>
/**
 * AJAX Table Filter - Server-side filtering via Controller
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find all tables that need AJAX filters
    const tables = document.querySelectorAll('table.table, table.github-table, table[class*="inventories"], table[class*="data"]');
    
    tables.forEach(table => {
        const thead = table.querySelector('thead');
        if (!thead) return;
        
        const headerRow = thead.querySelector('tr');
        if (!headerRow) return;
        
        // Check if filter row already exists
        if (thead.querySelector('.ajax-filter-row')) return;
        
        // Create filter row
        const filterRow = document.createElement('tr');
        filterRow.className = 'ajax-filter-row';
        filterRow.style.backgroundColor = '#f8f9fa';
        filterRow.style.borderBottom = '2px solid #dee2e6';
        
        // Get column names from headers
        const headers = headerRow.querySelectorAll('th');
        const columnNames = [];
        
        headers.forEach((th, index) => {
            const filterCell = document.createElement('th');
            filterCell.style.padding = '8px 12px';
            
            // Get column name from header link or text
            const sortLink = th.querySelector('a');
            let columnName = '';
            
            if (sortLink) {
                // Extract column name from sort URL
                const href = sortLink.getAttribute('href');
                const match = href ? href.match(/sort=([^&]+)/) : null;
                columnName = match ? match[1] : '';
            }
            
            columnNames.push(columnName);
            
            // Don't add filter to Actions column
            if (th.textContent.toLowerCase().includes('action')) {
                filterRow.appendChild(filterCell);
                return;
            }
            
            // Create filter input with icon
            const inputGroup = document.createElement('div');
            inputGroup.style.position = 'relative';
            
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'ajax-column-filter form-control form-control-sm';
            input.placeholder = 'Search...';
            input.style.width = '100%';
            input.style.padding = '4px 28px 4px 8px';
            input.style.fontSize = '13px';
            input.style.border = '1px solid #ced4da';
            input.style.borderRadius = '4px';
            input.dataset.columnName = columnName;
            input.dataset.columnIndex = index;
            
            // Add search icon
            const icon = document.createElement('i');
            icon.className = 'fa fa-search';
            icon.style.position = 'absolute';
            icon.style.right = '10px';
            icon.style.top = '50%';
            icon.style.transform = 'translateY(-50%)';
            icon.style.color = '#6c757d';
            icon.style.pointerEvents = 'none';
            
            // Add focus effect
            input.addEventListener('focus', function() {
                this.style.borderColor = '#007bff';
                this.style.boxShadow = '0 0 0 0.2rem rgba(0,123,255,0.25)';
                icon.style.color = '#007bff';
            });
            
            input.addEventListener('blur', function() {
                this.style.borderColor = '#ced4da';
                this.style.boxShadow = 'none';
                icon.style.color = '#6c757d';
            });
            
            // Add debounced AJAX filter
            let timeout;
            input.addEventListener('keyup', function() {
                clearTimeout(timeout);
                icon.className = 'fa fa-spinner fa-spin';
                
                timeout = setTimeout(() => {
                    performAjaxFilter(table);
                }, 500); // Wait 500ms after user stops typing
            });
            
            inputGroup.appendChild(input);
            inputGroup.appendChild(icon);
            filterCell.appendChild(inputGroup);
            filterRow.appendChild(filterCell);
        });
        
        // Insert filter row after header row
        headerRow.after(filterRow);
        
        // Store original URL for filtering
        const currentUrl = window.location.pathname;
        table.dataset.filterUrl = currentUrl;
    });
    
    // Perform AJAX filter
    function performAjaxFilter(table) {
        const filterInputs = table.querySelectorAll('.ajax-column-filter');
        const filters = {};
        let hasFilter = false;
        
        // Collect all filter values
        filterInputs.forEach(input => {
            const columnName = input.dataset.columnName;
            const value = input.value.trim();
            
            if (value && columnName) {
                filters[columnName] = value;
                hasFilter = true;
            }
        });
        
        // If no filters, don't make AJAX call
        if (!hasFilter) {
            // Reset icon
            filterInputs.forEach(input => {
                const icon = input.nextElementSibling;
                if (icon) icon.className = 'fa fa-search';
            });
            return;
        }
        
        // Get current URL and add filter params
        const baseUrl = table.dataset.filterUrl || window.location.pathname;
        const params = new URLSearchParams();
        
        // Add filter parameters
        Object.keys(filters).forEach(key => {
            params.append(`filter[${key}]`, filters[key]);
        });
        
        // Add AJAX flag
        params.append('ajax', '1');
        
        const url = `${baseUrl}?${params.toString()}`;
        
        // Show loading state
        const tbody = table.querySelector('tbody');
        const originalContent = tbody.innerHTML;
        
        // Make AJAX request
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Reset icons
            filterInputs.forEach(input => {
                const icon = input.nextElementSibling;
                if (icon) icon.className = 'fa fa-search';
            });
            
            if (data.success && data.html) {
                tbody.innerHTML = data.html;
                
                // Update pagination info if exists
                if (data.count !== undefined) {
                    updateFilterCount(table, data.count, data.total);
                } else if (data.rows) {
                    // Alternative: data contains row array
                    renderTableRows(tbody, data.rows, table);
                    updateFilterCount(table, data.rows.length, data.total || data.rows.length);
                }
            }
        })
        .catch(error => {
            console.error('Filter error:', error);
            
            // Reset icons on error
            filterInputs.forEach(input => {
                const icon = input.nextElementSibling;
                if (icon) {
                    icon.className = 'fa fa-exclamation-triangle';
                    icon.style.color = '#dc3545';
                }
            });
            
            // Show error message
            tbody.innerHTML = `
                <tr>
                    <td colspan="100" style="text-align: center; padding: 20px; color: #dc3545;">
                        <i class="fa fa-exclamation-triangle"></i> 
                        Error loading filtered data. Please try again.
                    </td>
                </tr>
            `;
        });
    }
    
    // Update filter count display
    function updateFilterCount(table, visible, total) {
        const container = table.closest('.github-container, .view-content, .index, .inventories');
        if (!container) return;
        
        const counter = container.querySelector('.github-table-count, .paginator .counter, p');
        if (counter) {
            if (!counter.dataset.originalText) {
                counter.dataset.originalText = counter.textContent;
            }
            
            if (visible !== total) {
                counter.innerHTML = `<span style="color: #dc3545; font-weight: bold;">
                    <i class="fa fa-filter"></i> Showing ${visible} of ${total} records (filtered)
                </span>`;
            } else {
                counter.textContent = counter.dataset.originalText;
            }
        }
    }
    
    // Add Clear Filters button
    tables.forEach(table => {
        const filterInputs = table.querySelectorAll('.ajax-column-filter');
        if (filterInputs.length === 0) return;
        
        const container = table.closest('.github-container, .view-content, .index, .inventories');
        if (!container) return;
        
        // Check if button already exists
        if (container.querySelector('.clear-ajax-filters-btn')) return;
        
        const clearBtn = document.createElement('button');
        clearBtn.className = 'clear-ajax-filters-btn btn btn-sm btn-warning';
        clearBtn.innerHTML = '<i class="fa fa-times"></i> Clear Filters';
        clearBtn.style.marginLeft = '10px';
        clearBtn.style.marginBottom = '10px';
        clearBtn.style.display = 'none'; // Hide initially
        
        clearBtn.addEventListener('click', function() {
            filterInputs.forEach(input => {
                input.value = '';
                const icon = input.nextElementSibling;
                if (icon) icon.className = 'fa fa-search';
            });
            
            // Reload page without filters
            window.location.href = table.dataset.filterUrl || window.location.pathname;
        });
        
        // Show clear button when any filter has value
        filterInputs.forEach(input => {
            input.addEventListener('keyup', function() {
                const hasAnyFilter = Array.from(filterInputs).some(inp => inp.value.trim() !== '');
                clearBtn.style.display = hasAnyFilter ? 'inline-block' : 'none';
            });
        });
        
        // Try to add to appropriate location
        const actions = container.querySelector('.actions, .table-actions, h2, h3');
        if (actions) {
            actions.parentNode.insertBefore(clearBtn, actions.nextSibling);
        } else {
            table.parentNode.insertBefore(clearBtn, table);
        }
    });

});
</script>

<!-- Header User Dropdown Handler - STANDALONE -->
<script>
(function() {
    'use strict';
    
    console.log('Ã°Å¸â€Â§ Initializing header dropdown handler...');
    
    function initDropdown() {
        // Find the trigger link
        var triggerLinks = document.querySelectorAll('.header-dropdown-trigger > a');
        console.log('Found trigger links:', triggerLinks.length);
        
        triggerLinks.forEach(function(triggerLink) {
            // Remove old listeners by cloning
            var newTrigger = triggerLink.cloneNode(true);
            triggerLink.parentNode.replaceChild(newTrigger, triggerLink);
            
            newTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Ã°Å¸â€“Â±Ã¯Â¸Â Dropdown trigger clicked');
                
                var dropdown = this.parentElement.querySelector('.header-dropdown');
                if (!dropdown) {
                    console.error('Ã¢ÂÅ’ Dropdown menu not found!');
                    return;
                }
                
                // Close all other dropdowns
                document.querySelectorAll('.header-dropdown').forEach(function(d) {
                    if (d !== dropdown) d.style.display = 'none';
                });
                
                // Toggle this dropdown
                var isVisible = dropdown.style.display === 'block';
                dropdown.style.display = isVisible ? 'none' : 'block';
                console.log('Ã¢Å“â€¦ Dropdown toggled:', !isVisible ? 'OPEN' : 'CLOSED');
            });
        });
        
        // Make sure ALL links in dropdown are clickable
        var dropdownLinks = document.querySelectorAll('.header-dropdown a');
        console.log('Found dropdown links:', dropdownLinks.length);
        
        dropdownLinks.forEach(function(link) {
            link.style.pointerEvents = 'auto';
            link.style.cursor = 'pointer';
            
            link.addEventListener('click', function(e) {
                console.log('Ã°Å¸â€â€” Link clicked:', this.href);
                // Let the link work normally
            });
        });
        
        // Make logout button work
        var logoutButtons = document.querySelectorAll('.header-dropdown form button[type="submit"]');
        console.log('Found logout buttons:', logoutButtons.length);
        
        logoutButtons.forEach(function(btn) {
            // Clone to remove any blocking handlers
            var newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            newBtn.style.pointerEvents = 'auto';
            newBtn.style.cursor = 'pointer';
            
            newBtn.addEventListener('click', function(e) {
                console.log('Ã°Å¸â€â€œ Logout button clicked - submitting form');
                // Let form submit normally
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.header-dropdown-trigger')) {
                document.querySelectorAll('.header-dropdown').forEach(function(d) {
                    d.style.display = 'none';
                });
            }
        });
        
        console.log('Ã¢Å“â€¦ Header dropdown initialized successfully');
    }
    
    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDropdown);
    } else {
        initDropdown();
    }
})();
</script>
</body>
</html>
