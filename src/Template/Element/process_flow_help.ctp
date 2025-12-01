<?php
/**
 * Process Flow Help Button Element
 * 
 * Displays a floating help button that opens process flow documentation
 * 
 * Usage:
 * <?= $this->element('process_flow_help', ['controller' => $this->request->getParam('controller')]) ?>
 * 
 * @var string $controller Current controller name
 */

$controller = isset($controller) ? $controller : $this->request->getParam('controller');
$action = $this->request->getParam('action');
?>

<!-- Floating Help Button -->
<div class="process-flow-help-button">
    <a href="<?= $this->Url->build(['controller' => $controller, 'action' => 'processFlow', 'prefix' => $this->request->getParam('prefix')]) ?>" 
       class="btn-help-float" 
       target="_blank"
       title="View Process Flow & Database Relationship">
        <i class="fas fa-question-circle"></i>
        <span class="help-text">Help</span>
    </a>
</div>

<style>
    /* Floating Help Button Styling */
    .process-flow-help-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
    }
    
    .btn-help-float {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff !important;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        text-decoration: none !important;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 3px solid rgba(255, 255, 255, 0.2);
    }
    
    .btn-help-float:hover {
        transform: scale(1.1) translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
    
    .btn-help-float:active {
        transform: scale(0.95);
    }
    
    .btn-help-float i {
        font-size: 28px;
        animation: pulse 2s infinite;
    }
    
    .btn-help-float .help-text {
        display: none;
        position: absolute;
        right: 70px;
        top: 50%;
        transform: translateY(-50%);
        background: #2d3748;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        white-space: nowrap;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .btn-help-float .help-text::after {
        content: '';
        position: absolute;
        right: -8px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid #2d3748;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
    }
    
    .btn-help-float:hover .help-text {
        display: block;
        animation: slideIn 0.3s ease;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-50%) translateX(10px);
        }
        to {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .process-flow-help-button {
            bottom: 20px;
            right: 20px;
        }
        
        .btn-help-float {
            width: 50px;
            height: 50px;
        }
        
        .btn-help-float i {
            font-size: 24px;
        }
        
        .btn-help-float .help-text {
            display: none !important;
        }
    }
    
    /* Print: Hide Help Button */
    @media print {
        .process-flow-help-button {
            display: none !important;
        }
    }
</style>
