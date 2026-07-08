<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <title><?= $this->fetch('title') ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            padding: 20px;
            background: white;
        
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        
        .print-header h1 {
            font-size: 20pt;
            margin-bottom: 5px;
            color: #667eea;
            font-weight: bold;
        
        .print-info {
            font-size: 10pt;
            color: #666;
            margin-top: 5px;
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        
        table th {
            background: #667eea;
            color: white;
            border: 1px solid #333;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
        
        table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            font-size: 10pt;
            word-wrap: break-word;
        
        table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        
        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        
        .print-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #667eea;
            font-size: 9pt;
            color: #666;
            text-align: center;
        
        .company-info {
            font-size: 8pt;
            color: #999;
            margin-top: 5px;
        
        kbd {
            background-color: #eee;
            border: 1px solid #b4b4b4;
            border-radius: 3px;
            padding: 2px 4px;
            font-size: 0.85em;
        
        @page {
            size: A4 landscape;
            margin: 1cm;
        
        @media print {
            .no-print {
                display: none !important;
            
            table th {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
    </style>
</head>
<body>
    <div class="print-controls no-print" style="margin-bottom: 20px;">
        <div style="background: #e8f0fe; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #667eea;">
            <div style="font-size: 16px; font-weight: bold; color: #667eea; margin-bottom: 12px;">
                ðŸ“„ How to Save as PDF
            </div>
            <ul style="margin: 10px 0 0 20px; color: #333; font-size: 12pt; line-height: 2;">
                <li><strong>Chrome/Edge:</strong> Click "Print" below â†’ Destination: <em>"Save as PDF"</em> â†’ Save</li>
                <li><strong>Firefox:</strong> Click "Print" below â†’ Printer: <em>"Microsoft Print to PDF"</em> â†’ Save</li>
                <li><strong>Quick Shortcut:</strong> Press <kbd>Ctrl+P</kbd> then select PDF as printer destination</li>
            </ul>
        </div>
        <div style="text-align: right;">
            <button onclick="window.print()" style="padding: 12px 28px; font-size: 13pt; cursor: pointer; background: #667eea; color: white; border: none; border-radius: 6px;">
                ðŸ–¨ï¸ Print / Save as PDF
            </button>
            <button onclick="window.close()" style="padding: 12px 28px; font-size: 13pt; cursor: pointer; margin-left: 10px; background: #6c757d; color: white; border: none; border-radius: 6px;">
                âœ– Close
            </button>
        </div>
    </div>
    
    <?= $this->fetch('content') ?>
</body>
</html>

