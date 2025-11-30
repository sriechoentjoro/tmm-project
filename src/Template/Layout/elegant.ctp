<?php
// Use CakePHP's request base path for static assets
$staticAssetsUrl = $this->request->getAttribute('webroot');
// Cache busting
$cacheBust = '?v=' . time();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?> - TMM Apprentice Management
    </title>
    <link rel="icon" type="image/svg+xml" href="<?= $staticAssetsUrl ?>/favicon.svg<?= $cacheBust ?>">

    <!-- Google Fonts - Nunito & Mulish -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&subset=latin-ext,vietnamese" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <!-- Font Awesome - Local to avoid tracking prevention -->
    <link rel="stylesheet" href="<?= $staticAssetsUrl ?>/css/fontawesome-all.min.css<?= $cacheBust ?>">
    
    <!-- Bootstrap 5 CSS for Dropdown -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Mobile Responsive CSS - Global -->
    <link rel="stylesheet" href="<?= $staticAssetsUrl ?>/css/mobile-responsive.css<?= $cacheBust ?>">
    
    <!-- Actions Sidebar & Table Enhancements - Global -->
    <link rel="stylesheet" href="<?= $staticAssetsUrl ?>/css/actions-table.css<?= $cacheBust ?>">
    
    <!-- Table Enhanced CSS - Hover Action Buttons -->
    <link rel="stylesheet" href="<?= $staticAssetsUrl ?>/css/table-enhanced.css<?= $cacheBust ?>">
    
    <!-- Form Styles - Actions Sidebar & Modern Form UI -->
    <link rel="stylesheet" href="<?= $staticAssetsUrl ?>/css/form-styles.css<?= $cacheBust ?>"
    
    <!-- jQuery & Bootstrap for Forms -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    
    <!-- Bootstrap Datepicker for Date Fields -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    
    <!-- CSS files commented out - using inline styles below -->
    <!-- <?= $this->Html->css('base') ?> -->

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
    
    <style>
        body {
            background: #f5f7fa;
            font-family: 'Mulish', 'Nunito', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        
        .content-wrapper {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 30px;
            margin-top: 20px;
        
        .page-header {
            background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
            color: #fff;
            padding: 20px 0;
            margin-bottom: 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        
        .page-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        
        .flash-messages {
            margin: 20px 0;
        
        .flash-message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease;
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            to {
                opacity: 1;
                transform: translateY(0);
        
        .flash-message.success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        
        .flash-message.error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        
        .flash-message.warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        
        .flash-message i {
            margin-right: 10px;
            font-size: 18px;
        
        /* Export and Print buttons */
        .btn-export,
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        
        .btn-export:hover,
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        
        .btn-export i,
        .btn-print i {
            font-size: 13px;
        
        /* Light/Transparent Export and Print buttons - mobile friendly */
        .btn-export-light {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            background: rgba(102, 126, 234, 0.1);
            color: #00BCD4;
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-left: 5px;
        
        .btn-export-light:hover {
            background: rgba(102, 126, 234, 0.2);
            border-color: rgba(102, 126, 234, 0.5);
            color: #5568d3;
            text-decoration: none;
            transform: translateY(-1px);
        
        .btn-export-light i {
            font-size: 13px;
        
        /* Mobile optimization for export buttons */
        @media (max-width: 768px) {
            .btn-export,
            .btn-print {
                padding: 5px 8px;
                font-size: 12px;
                gap: 3px;
            
            .btn-export i,
            .btn-print i {
                font-size: 12px;
            
            .btn-export-light {
                padding: 5px 8px;
                font-size: 11px;
                gap: 3px;
                margin-left: 3px;
            
            .btn-export-light i {
                font-size: 11px;
        
        /* Table row positioning for action buttons */
        .table tbody tr,
        .table-row-with-actions {
            position: relative;
        
        /* Action buttons - invisible cell */
        td.actions-cell-invisible {
            width: 0 !important;
            padding: 0 !important;
            border: none !important;
            overflow: visible !important;
            position: relative !important;
        
        /* Action button container */
        .action-buttons-hover {
            position: absolute !important;
            right: 10px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            display: inline-flex !important;
            gap: 4px !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: opacity 0.3s ease, visibility 0.3s ease !important;
            background: rgba(255, 255, 255, 0.95) !important;
            padding: 4px 8px !important;
            border-radius: 6px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
            z-index: 100 !important;
        
        /* Show on hover - desktop */
        tbody tr:hover .action-buttons-hover {
            opacity: 1 !important;
            visibility: visible !important;
        
        /* Show on touch - mobile */
        tbody tr:active .action-buttons-hover,
        tbody tr:focus-within .action-buttons-hover {
            opacity: 1 !important;
            visibility: visible !important;
        
        /* Keep visible when hovering buttons */
        .action-buttons-hover:hover {
            opacity: 1 !important;
            visibility: visible !important;
        
        /* Action button icons */
        .btn-action-icon {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 24px !important;
            height: 24px !important;
            background: #2c3e50 !important;
            color: white !important;
            border-radius: 4px !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
            border: none !important;
            cursor: pointer !important;
            margin: 0 !important;
            padding: 0 !important;
            vertical-align: middle !important;
        
        .btn-action-icon:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
            color: white !important;
            text-decoration: none !important;
        
        .btn-action-icon i {
            font-size: 11px !important;
            color: white !important;
            margin: 0 !important;
            padding: 0 !important;
        
        /* Button colors - Edit button removed, only view and delete */
        .btn-edit-icon {
            display: none !important; /* Hide edit buttons */
        
        .btn-delete-icon {
            background: #e74c3c !important;
        
        .btn-delete-icon:hover {
            background: #c0392b !important;
        
        .btn-view-icon {
            background: #3498db !important;
        
        .btn-view-icon:hover {
            background: #2980b9 !important;
        
        /* Sticky Actions column */
        th.actions,
        td.actions {
            position: sticky !important;
            right: 0 !important;
            background: transparent !important;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1) !important;
            z-index: 2 !important;
            text-align: right !important;
        
        th.actions {
            display: table-cell !important;
        
        th.actions {
            background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%) !important;
            z-index: 6 !important;
            color: white !important;
            font-weight: 600 !important;
            text-align: center !important;
        
        /* Apply zebra striping and hover to actions cell */
        tbody tr:nth-child(odd) td.actions {
            background: transparent !important;
        
        tbody tr:nth-child(even) td.actions {
            background: transparent !important;
        
        tbody tr:hover td.actions {
            background: transparent !important;
        
        /* Style table headers to match elegant-tab design */
        thead {
            background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%) !important;
        
        thead th {
            background: transparent !important;
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500 !important;
            font-size: 15px !important;
            padding: 18px 20px !important;
            border: none !important;
            border-bottom: 3px solid transparent !important;
            text-align: center !important;
            white-space: nowrap !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            min-width: 100px !important;
        
        /* Fix table column alignment */
        .table {
            table-layout: auto !important;
            width: max-content !important;
            min-width: 100% !important;
        
        .table thead th,
        .table tbody td {
            white-space: nowrap !important;
            padding-left: 20px !important;
            padding-right: 20px !important;
            vertical-align: middle !important;
            text-align: left !important;
        
        /* ID column should be narrow */
        .table thead th:first-child,
        .table tbody td:first-child {
            width: 1% !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        
        .table thead th {
            padding-top: 18px !important;
            padding-bottom: 18px !important;
        
        .table tbody td {
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        
        /* Ensure filter inputs match column width */
        .filter-row th {
            padding: 4px 8px !important;
            box-sizing: border-box !important;
        
        .filter-input {
            width: 100% !important;
            box-sizing: border-box !important;
            padding: 6px 8px !important;
            min-width: 80px !important;
        
        thead th.actions {
            min-width: 90px !important;
        
        /* Thumbnail cells should remain centered */
        .thumbnail-cell,
        th:has(+ th):first-child {
            text-align: center !important;
        
        thead th > a {
            color: rgba(255, 255, 255, 0.8) !important;
            text-decoration: none !important;
        
        thead th:hover {
            color: #fff !important;
            background: rgba(255,255,255,0.1) !important;
            border-bottom-color: rgba(255, 255, 255, 0.5) !important;
        
        thead th:hover > a {
            color: #fff !important;
        
        /* Table thumbnail styling */
        .table-thumbnail {
            max-width: 50px !important;
            max-height: 50px !important;
            width: auto !important;
            height: auto !important;
            object-fit: cover !important;
            border-radius: 4px !important;
            display: block !important;
        
        .thumbnail-cell {
            text-align: center !important;
            vertical-align: middle !important;
        
        .no-thumbnail {
            color: #ccc !important;
            font-size: 24px !important;
        
        /* Center the elegant-tabs navigation */
        .elegant-tabs {
            justify-content: center !important;
    </style>
</head>
<body>
    <!-- Page Header -->
    <div class="page-header">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="header-title">
                <h1><i class="fas fa-graduation-cap"></i> TMM - Apprentice Management</h1>
            </div>
            
            <div class="header-user-menu">
                <ul class="user-menu" style="display: flex; align-items: center; margin: 0; padding: 0; list-style: none;">
                    <?php if ($this->request->getSession()->check('Auth.User.id')): ?>
                        <!-- Language Switcher -->
                        <li class="header-dropdown-trigger" style="margin-left: 20px; position: relative;">
                            <a href="#" style="color: white; text-decoration: none; font-weight: bold; display: flex; align-items: center;">
                                <i class="fas fa-language" style="font-size: 18px;"></i>
                                <span style="margin-left: 5px; margin-right: 5px;">
                                    <?php 
                                    $currentLang = $this->request->getSession()->read('Config.language') ?: 'ind';
                                    echo strtoupper($currentLang);
                                    ?>
                                </span>
                                <i class="fas fa-caret-down"></i>
                            </a>
                            <ul class="header-dropdown" style="display: none; position: absolute; top: 100%; right: 0; background: white; min-width: 150px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border-radius: 4px; padding: 5px 0; z-index: 1000; list-style: none;">
                                <li><?= $this->Html->link('ðŸ‡®ðŸ‡© Indonesia', ['controller' => 'Users', 'action' => 'changeLanguage', 'ind'], ['style' => 'display: block; padding: 8px 15px; color: #333; text-decoration: none;']) ?></li>
                                <li><?= $this->Html->link('ðŸ‡¬ðŸ‡§ English', ['controller' => 'Users', 'action' => 'changeLanguage', 'eng'], ['style' => 'display: block; padding: 8px 15px; color: #333; text-decoration: none;']) ?></li>
                                <li><?= $this->Html->link('ðŸ‡¯ðŸ‡µ æ—¥æœ¬èªž', ['controller' => 'Users', 'action' => 'changeLanguage', 'jpn'], ['style' => 'display: block; padding: 8px 15px; color: #333; text-decoration: none;']) ?></li>
                            </ul>
                        </li>
                        
                        <!-- User Profile -->
                        <li class="header-dropdown-trigger" style="margin-left: 20px; position: relative;">
                            <a href="#" style="color: white; text-decoration: none; font-weight: bold; display: flex; align-items: center;">
                                <?php 
                                $user = $this->request->getSession()->read('Auth.User');
                                // Debug: Check if user data exists
                                if (empty($user)) {
                                    $userName = 'Guest';
                                } else {
                                    $userName = isset($user['fullname']) ? $user['fullname'] : (isset($user['username']) ? $user['username'] : 'User');
                                }
                                
                                // Generate initials from username or fullname
                                $nameParts = explode(' ', trim($userName));
                                if (count($nameParts) >= 2) {
                                    // First and last name initials
                                    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts) - 1], 0, 1));
                                } else {
                                    // First two characters of username
                                    $initials = strtoupper(substr($userName, 0, 2));
                                }
                                
                                // Generate background color from username (consistent color per user)
                                $colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e', '#e67e22'];
                                $colorIndex = ord($userName[0]) % count($colors);
                                $bgColor = $colors[$colorIndex];
                                ?>
                                
                                <?php if (!empty($user['photo'])): ?>
                                    <img src="<?= h($user['photo']) ?>" alt="User" style="width: 32px; height: 32px; border-radius: 50%; vertical-align: middle; margin-right: 8px; object-fit: cover; border: 2px solid rgba(255,255,255,0.5);">
                                <?php else: ?>
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: <?= $bgColor ?>; display: flex; align-items: center; justify-content: center; margin-right: 8px; font-weight: bold; font-size: 14px; color: white; border: 2px solid rgba(255,255,255,0.5);">
                                        <?= h($initials) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <span style="color: white; font-weight: bold; font-size: 14px; margin-right: 3px;"><?= h($userName) ?></span>
                                <i class="fas fa-caret-down" style="margin-left: 5px; color: white;"></i>
                            </a>
                            <ul class="header-dropdown" style="display: none; position: absolute; top: 100%; right: 0; background: white; min-width: 180px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border-radius: 4px; padding: 5px 0; z-index: 1000; list-style: none;">
                                <li><?= $this->Html->link('<i class="fas fa-user"></i> Profile', ['controller' => 'Users', 'action' => 'profile'], ['escape' => false, 'style' => 'display: block; padding: 8px 15px; color: #333; text-decoration: none;']) ?></li>
                                <li><?= $this->Html->link('<i class="fas fa-cog"></i> Settings', ['controller' => 'Users', 'action' => 'settings'], ['escape' => false, 'style' => 'display: block; padding: 8px 15px; color: #333; text-decoration: none;']) ?></li>
                                <li style="border-top: 1px solid #eee; margin: 5px 0;"></li>
                                <li><?= $this->Html->link('<i class="fas fa-sign-out-alt"></i> Logout', ['controller' => 'Users', 'action' => 'logout'], ['escape' => false, 'style' => 'display: block; padding: 8px 15px; color: #d33c44; text-decoration: none;']) ?></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Login Link -->
                        <li>
                            <?= $this->Html->link(
                                '<i class="fas fa-sign-in-alt"></i> Login',
                                ['controller' => 'Users', 'action' => 'login'],
                                ['escape' => false, 'style' => 'color: white; text-decoration: none; font-weight: bold; font-size: 16px; border: 1px solid white; padding: 5px 15px; border-radius: 4px;']
                            ) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Elegant Tab Menu -->
    <?= $this->element('elegant_menu') ?>

    <!-- Flash Messages -->
    <?php if ($this->Flash->render()): ?>
        <div class="container">
            <div class="flash-messages">
                <?= $this->Flash->render() ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="container">
        <div class="content-wrapper">
            <?= $this->fetch('content') ?>
        </div>
    </div>
    <script>
        // Override table-filter.js if skipAutoFilter flag is set
        if (window.skipAutoFilter === true) {
            // Prevent table-filter.js from initializing by removing the class it looks for
            document.addEventListener('DOMContentLoaded', function() {
                console.log('skipAutoFilter enabled - preventing auto-filter initialization');
                const tables = document.querySelectorAll('.table:not(.no-auto-filter)');
                // Only remove auto-filter from tables WITHOUT no-auto-filter class
                // Tables WITH no-auto-filter class should be left alone
            });
        }
    </script>
    <script src="<?= $staticAssetsUrl ?>/js/context-menu.js<?= $cacheBust ?>"></script>
    <script src="<?= $staticAssetsUrl ?>/js/submenu-position.js<?= $cacheBust ?>"></script>
    <script src="<?= $staticAssetsUrl ?>/js/table-drag-scroll.js<?= $cacheBust ?>"></script>
    <script src="<?= $staticAssetsUrl ?>/js/table-filter.js<?= $cacheBust ?>"></script>
    
    <!-- Romaji to Katakana Converter for Japanese name input -->
    <script src="<?= $staticAssetsUrl ?>/js/romaji-to-katakana.js<?= $cacheBust ?>"></script>

    <!-- Association Link Modal -->
    <div id="associationModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 95%; width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Loading...</h5>
                    <button type="button" class="close" onclick="closeAssociationModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="associationModalBody">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p>Loading data...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAssociationModal()">Close</button>
                    <a id="associationModalLink" href="#" class="btn btn-primary" target="_blank">Open Full Page</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            overflow-y: auto;
        }
        .modal.fade {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal.show {
            display: block !important;
            opacity: 1;
        }
        .modal-dialog {
            position: relative;
            margin: 50px auto;
            max-width: 95%;
            width: 95%;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .modal-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .modal-header .close {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        .modal-header .close:hover {
            opacity: 1;
        }
        .modal-body {
            padding: 20px;
            max-height: 60vh;
            overflow-y: auto;
        }
        /* Fix table alignment in modal */
        .modal-body table {
            width: 100%;
            table-layout: auto;
            border-collapse: collapse;
        }
        .modal-body table th,
        .modal-body table td {
            padding: 8px;
            vertical-align: middle;
            border: 1px solid #dee2e6;
            text-align: left;
            word-wrap: break-word;
        }
        .modal-body table thead th {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            font-weight: 600;
            white-space: nowrap;
        }
        .modal-body .table-responsive {
            overflow-x: auto;
            margin-bottom: 1rem;
            width: 100%;
        }
        .modal-body .filter-row th {
            padding: 4px !important;
        }
        .modal-body .filter-row input,
        .modal-body .filter-row select {
            width: 100%;
            min-width: 60px;
            font-size: 11px;
            padding: 2px 4px;
            height: auto;
            box-sizing: border-box;
        }
        .modal-body .filter-row > th > div {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        /* Fix GitHub container styles in modal */
        .modal-body .github-container {
            padding: 0;
            margin: 0;
        }
        .modal-body .github-page-header {
            display: none; /* Hide duplicate header in modal */
        }
        /* Ensure tab content displays properly */
        .modal-body .view-tab-pane {
            min-height: 200px;
        }
        .modal-body .github-related-table {
            width: 100%;
            margin-top: 10px;
        }
        /* Remove actions column header sticky positioning in modal */
        .modal-body .actions-column-header {
            position: static !important;
            left: auto !important;
        }
        /* Fix action buttons in modal tables */
        .modal-body .action-buttons-hover {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .modal-footer .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }
        .modal-footer .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .modal-footer .btn-secondary:hover {
            background: #5a6268;
        }
        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
            color: white;
        }
        .modal-footer .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
        }
        /* Association link styling */
        a[href*="/view/"] {
            color: #00BCD4;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        a[href*="/view/"]:hover {
            color: #0097A7;
            text-decoration: underline;
        }
    </style>

    <script>
        // Toggle Actions Sidebar Menu
        function toggleActionsMenu() {
            const sidebar = document.getElementById('actions-sidebar');
            sidebar.classList.toggle('active');
        }
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('actions-sidebar');
            if (sidebar && !sidebar.contains(e.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
        
        // Association Modal Functions
        function openAssociationModal(url, title) {
            const modal = document.getElementById('associationModal');
            const modalBody = document.getElementById('associationModalBody');
            const modalTitle = modal.querySelector('.modal-title');
            const modalLink = document.getElementById('associationModalLink');
            
            // Set title and link
            modalTitle.textContent = title || 'Details';
            modalLink.href = url;
            
            // Show loading state
            modalBody.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-3x" style="color: #00BCD4;"></i>
                    <p style="margin-top: 15px;">Loading data...</p>
                </div>
            `;
            
            // Show modal
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            
            // Load content via AJAX
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    // Parse the HTML and extract the main content
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Try to find the main content div
                    let content = doc.querySelector('.github-container') ||
                                  doc.querySelector('.content-wrapper') || 
                                  doc.querySelector('.inventories.view') ||
                                  doc.querySelector('.view') ||
                                  doc.querySelector('main') ||
                                  doc.querySelector('.container');
                    
                    if (content) {
                        // Remove action buttons and navigation elements
                        const actionsDiv = content.querySelector('.actions');
                        if (actionsDiv) actionsDiv.remove();
                        
                        const nav = content.querySelector('nav');
                        if (nav) nav.remove();
                        
                        modalBody.innerHTML = '';
                        modalBody.appendChild(content);
                    } else {
                        modalBody.innerHTML = '<div class="alert alert-warning">Could not load content. Please open full page.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading modal content:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">Error loading content.</div>';
                });
        }
        
        function closeAssociationModal() {
            const modal = document.getElementById('associationModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('associationModal');
            if (event.target === modal) {
                closeAssociationModal();
            }
        });
        
        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeAssociationModal();
            }
        });
        
        // Intercept all association links in table cells
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit for AJAX table content to load
            setTimeout(function() {
                attachModalListeners();
            }, 500);
        });
        
        // Function to attach listeners (can be called after AJAX updates)
        function attachModalListeners() {
            // Find all links in table cells that point to /view/ pages
            // EXCLUDE action buttons (btn-view-icon, btn-action-icon) - they should open full page
            const tableLinks = document.querySelectorAll('table td a[href*="/view/"]:not(.btn-view-icon):not(.btn-action-icon)');
            
            tableLinks.forEach(link => {
                // Skip if already has listener
                if (link.dataset.modalAttached) return;
                
                link.dataset.modalAttached = 'true';
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const url = this.href;
                    const title = this.textContent.trim() || 'Details';
                    openAssociationModal(url, title);
                });
            });
        }
        
        // Re-attach listeners after AJAX table updates
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        // Check if table rows were added
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1 && (node.tagName === 'TR' || node.querySelector('tr'))) {
                                attachModalListeners();
                            }
                        });
                    }
                });
            });
            
            // Observe the table body for changes
            const tables = document.querySelectorAll('table tbody');
            tables.forEach(table => {
                observer.observe(table, { childList: true, subtree: true });
            });
        }

        // ========================================================================
        // GLOBAL TAB SWITCHING FUNCTION - Available for modals and all pages
        // ========================================================================
        if (typeof window.initializeTabSwitching === 'undefined') {
            window.initializeTabSwitching = function(container) {
                // Find container (for modal context or specific view container)
                const viewContainer = container || document.querySelector('.github-container') || document;
                
                // Use data-view-template attribute to target only the primary view template tabs
                const isDocument = (viewContainer === document);
                
                let tabLinks, tabPanes;
                
                if (isDocument) {
                    // When querying whole document, find tabs within view-content-wrapper
                    const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
                    if (!viewWrapper) {
                        console.warn('No view-content-wrapper with data-view-template found');
                        return;
                    }
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
                    }
                }
                
                if (tabLinks.length === 0 || tabPanes.length === 0) {
                    console.error('No tabs found! TabLinks:', tabLinks.length, 'TabPanes:', tabPanes.length);
                    return;
                }
                
                console.log('Ã¢Å“â€¦ SUCCESS: Initializing', tabLinks.length, 'tab links and', tabPanes.length, 'tab panes');
                console.log('Ã°Å¸â€œÂ Container:', isDocument ? 'document (full page)' : 'element (modal/container)');
                
                // Remove existing click handlers to avoid duplicates
                tabLinks.forEach(link => {
                    const newLink = link.cloneNode(true);
                    link.parentNode.replaceChild(newLink, link);
                });
                
                // Re-query after cloning
                if (isDocument) {
                    const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
                    tabLinks = viewWrapper.querySelectorAll('.view-tabs-container .view-tab-link');
                    tabPanes = viewWrapper.querySelectorAll('.view-tab-pane');
                } else {
                    const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
                    if (viewWrapper) {
                        tabLinks = viewWrapper.querySelectorAll('.view-tabs-container .view-tab-link');
                        tabPanes = viewWrapper.querySelectorAll('.view-tab-pane');
                    } else {
                        tabLinks = viewContainer.querySelectorAll('.view-tab-link');
                        tabPanes = viewContainer.querySelectorAll('.view-tab-pane');
                    }
                }
                
                tabLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        console.log('Ã°Å¸â€“Â±Ã¯Â¸Â  Tab clicked:', this.getAttribute('data-tab'));
                        
                        const targetTab = this.getAttribute('data-tab');
                        if (!targetTab) {
                            console.error('No data-tab attribute found');
                            return;
                        }
                        
                        // Remove active class from all tabs and panes
                        tabLinks.forEach(l => l.classList.remove('active'));
                        tabPanes.forEach(p => p.classList.remove('active'));
                        
                        // Add active class to clicked tab
                        this.classList.add('active');
                        
                        // Find and activate corresponding pane
                        let targetPane;
                        if (isDocument) {
                            const viewWrapper = document.querySelector('.view-content-wrapper[data-view-template="true"]');
                            targetPane = viewWrapper ? viewWrapper.querySelector('#' + targetTab) : null;
                        } else {
                            const viewWrapper = viewContainer.querySelector('.view-content-wrapper[data-view-template="true"]');
                            targetPane = viewWrapper ? viewWrapper.querySelector('#' + targetTab) : viewContainer.querySelector('#' + targetTab);
                        }
                        
                        if (targetPane) {
                            targetPane.classList.add('active');
                            console.log('Activated pane:', targetTab);
                        } else {
                            console.error('Target pane not found:', targetTab);
                        }
                        // Store in sessionStorage
                        try {
                            sessionStorage.setItem('activeViewTab', targetTab);
                        } catch (e) {
                            console.warn('sessionStorage not available:', e);
                        }
                    });
                });
            };
        }
        // ========================================================================
        // END GLOBAL TAB SWITCHING FUNCTION
        // ========================================================================

        // Flash messages auto-hide after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('.flash-message');
            flashMessages.forEach(msg => {
                setTimeout(() => {
                    msg.style.transition = 'opacity 0.5s ease';
                    msg.style.opacity = '0';
                    setTimeout(() => msg.remove(), 500);
                }, 5000);
            });
        });
    </script>
    <script>
        // Header Dropdown Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownTriggers = document.querySelectorAll('.header-dropdown-trigger');
            
            dropdownTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Close other dropdowns
                    dropdownTriggers.forEach(other => {
                        if (other !== trigger) {
                            const otherDropdown = other.querySelector('.header-dropdown');
                            if (otherDropdown) otherDropdown.style.display = 'none';
                        }
                    });
                    
                    // Toggle current
                    const dropdown = this.querySelector('.header-dropdown');
                    if (dropdown) {
                        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                    }
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                document.querySelectorAll('.header-dropdown').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            });
        });
    </script>
</body>
</html>


