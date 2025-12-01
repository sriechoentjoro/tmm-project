<?php
/**
 * Process Flow Visualization Template
 * 
 * Shows interactive process flow diagrams and database relationships
 * Helps users understand data flow and table associations
 */

$controllerName = $this->request->getParam('controller');
$controllerTitle = \Cake\Utility\Inflector::humanize(\Cake\Utility\Inflector::underscore($controllerName));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Flow: <?= h($controllerTitle) ?> - TMM</title>
    
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Mermaid.js for Diagrams -->
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .process-flow-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .flow-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .flow-header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
        }
        
        .flow-header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .flow-content {
            padding: 40px;
        }
        
        .flow-section {
            margin-bottom: 50px;
        }
        
        .flow-section h2 {
            color: #2d3748;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
        }
        
        .flow-section h3 {
            color: #4a5568;
            font-size: 20px;
            font-weight: 600;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        
        .mermaid {
            background: #f7fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }
        
        .table-info {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 6px;
        }
        
        .table-info h4 {
            color: #2d3748;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .association-list {
            list-style: none;
            padding: 0;
        }
        
        .association-list li {
            padding: 10px;
            margin: 5px 0;
            background: white;
            border-radius: 6px;
            border-left: 3px solid #48bb78;
        }
        
        .association-list li i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .field-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 10px;
            margin: 20px 0;
        }
        
        .field-item {
            background: white;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
        }
        
        .field-item .field-name {
            font-weight: 600;
            color: #2d3748;
        }
        
        .field-item .field-type {
            color: #718096;
            font-size: 12px;
            margin-left: 8px;
        }
        
        .field-item.required {
            border-left: 3px solid #f56565;
        }
        
        .field-item.foreign-key {
            border-left: 3px solid #4299e1;
        }
        
        .badge-custom {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }
        
        .badge-required {
            background: #fed7d7;
            color: #c53030;
        }
        
        .badge-optional {
            background: #e6fffa;
            color: #234e52;
        }
        
        .badge-fk {
            background: #bee3f8;
            color: #2c5282;
        }
        
        .workflow-steps {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin: 20px 0;
        }
        
        .workflow-step {
            background: white;
            border-radius: 8px;
            padding: 20px;
            border-left: 5px solid #667eea;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .workflow-step .step-number {
            display: inline-block;
            width: 35px;
            height: 35px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            font-weight: 700;
            margin-right: 15px;
        }
        
        .workflow-step .step-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .workflow-step .step-description {
            color: #4a5568;
            line-height: 1.6;
        }
        
        .database-indicator {
            display: inline-block;
            padding: 4px 12px;
            background: #faf089;
            color: #744210;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .alert-info-custom {
            background: #ebf8ff;
            border: 1px solid #90cdf4;
            color: #2c5282;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        
        .alert-info-custom i {
            color: #3182ce;
            margin-right: 10px;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .btn-back {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="process-flow-container">
        <div class="flow-header">
            <h1><i class="fas fa-project-diagram"></i> Process Flow: <?= h($controllerTitle) ?></h1>
            <p>Interactive visualization of data flow, database relationships, and business process</p>
        </div>
        
        <div class="flow-content">
            <?= $this->fetch('content') ?>
            
            <div style="text-align: center; padding-top: 30px;">
                <a href="javascript:history.back()" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Form
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize Mermaid.js
        mermaid.initialize({ 
            startOnLoad: true,
            theme: 'default',
            flowchart: {
                useMaxWidth: true,
                htmlLabels: true,
                curve: 'basis'
            }
        });
        
        // Print functionality
        function printFlow() {
            window.print();
        }
    </script>
</body>
</html>
