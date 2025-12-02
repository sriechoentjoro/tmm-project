<?php
/**
 * Process Flow Help Layout
 * Multi-language documentation layout with language switcher
 * Purple gradient theme
 */

// Get current language from session
$currentLang = $this->request->getSession()->read('Config.language') ?: 'ind';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang === 'ind' ? 'id' : ($currentLang === 'eng' ? 'en' : 'ja') ?>">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php if ($currentLang === 'ind'): ?>
            Bantuan Alur Proses - <?= $this->fetch('title') ?>
        <?php elseif ($currentLang === 'eng'): ?>
            Process Flow Help - <?= $this->fetch('title') ?>
        <?php else: ?>
            プロセスフローヘルプ - <?= $this->fetch('title') ?>
        <?php endif; ?>
    </title>
    
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Mermaid.js for diagrams -->
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    
    <style>
        /* Purple Gradient Theme */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Language Switcher */
        .language-switcher {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            padding: 10px 20px;
            display: flex;
            gap: 10px;
        }
        
        .lang-btn {
            padding: 8px 16px;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        
        .lang-btn:hover {
            background: #667eea;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }
        
        .lang-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #764ba2;
        }
        
        /* Header */
        h1 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        
        h2 {
            color: #764ba2;
            font-weight: 600;
            margin-top: 40px;
            margin-bottom: 20px;
        }
        
        /* Flow Sections */
        .flow-section {
            margin-bottom: 40px;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 5px solid #667eea;
        }
        
        .alert-info-custom {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        /* Workflow Steps */
        .workflow-steps {
            margin: 30px 0;
        }
        
        .workflow-step {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 5px solid #764ba2;
        }
        
        .step-number {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            font-size: 18px;
            margin-right: 15px;
        }
        
        .step-title {
            font-size: 18px;
            font-weight: 600;
            color: #764ba2;
            margin-bottom: 10px;
        }
        
        .step-description {
            color: #666;
            line-height: 1.8;
        }
        
        /* Mermaid Diagrams */
        .mermaid {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        
        /* Back Button */
        .back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            text-decoration: none;
            color: white;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                border-radius: 0;
            }
            
            .language-switcher {
                top: 10px;
                right: 10px;
                padding: 8px 12px;
                gap: 5px;
            }
            
            .lang-btn {
                padding: 6px 12px;
                font-size: 12px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .flow-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Language Switcher -->
    <div class="language-switcher">
        <a href="?lang=ind" class="lang-btn <?= $currentLang === 'ind' ? 'active' : '' ?>">
            🇮🇩 Indonesian
        </a>
        <a href="?lang=eng" class="lang-btn <?= $currentLang === 'eng' ? 'active' : '' ?>">
            🇬🇧 English
        </a>
        <a href="?lang=jpn" class="lang-btn <?= $currentLang === 'jpn' ? 'active' : '' ?>">
            🇯🇵 日本語
        </a>
    </div>
    
    <!-- Main Content -->
    <div class="container">
        <h1>
            <i class="fas fa-book-open"></i>
            <?php if ($currentLang === 'ind'): ?>
                Bantuan Alur Proses
            <?php elseif ($currentLang === 'eng'): ?>
                Process Flow Help
            <?php else: ?>
                プロセスフローヘルプ
            <?php endif; ?>
        </h1>
        
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
        
        <!-- Back Button -->
        <div class="text-center">
            <a href="javascript:history.back()" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <?php if ($currentLang === 'ind'): ?>
                    Kembali
                <?php elseif ($currentLang === 'eng'): ?>
                    Back
                <?php else: ?>
                    戻る
                <?php endif; ?>
            </a>
        </div>
    </div>
    
    <!-- jQuery & Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Initialize Mermaid -->
    <script>
        mermaid.initialize({ 
            startOnLoad: true,
            theme: 'default',
            themeVariables: {
                primaryColor: '#667eea',
                primaryTextColor: '#fff',
                primaryBorderColor: '#764ba2',
                lineColor: '#667eea',
                secondaryColor: '#764ba2',
                tertiaryColor: '#f3e5f5'
            }
        });
    </script>
</body>
</html>
