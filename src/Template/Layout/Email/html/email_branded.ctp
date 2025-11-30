<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->fetch('title') ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .email-header p {
            margin: 8px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px 30px;
            line-height: 1.8;
        }
        .email-body h2 {
            color: #667eea;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .email-body p {
            margin: 15px 0;
            font-size: 15px;
            color: #555555;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box strong {
            color: #333333;
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-box span {
            color: #667eea;
            font-size: 16px;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 14px 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #856404;
        }
        .success-box {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #155724;
        }
        .email-footer {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
            font-size: 13px;
        }
        .email-footer p {
            margin: 8px 0;
            color: #bdc3c7;
        }
        .email-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #ddd, transparent);
            margin: 25px 0;
        }
        .social-links {
            margin: 20px 0 10px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #667eea;
            font-size: 20px;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 0;
            }
            .email-body {
                padding: 25px 20px;
            }
            .email-header h1 {
                font-size: 24px;
            }
            .btn {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>TMM Apprentice System</h1>
            <p>Training & Monitoring Management</p>
        </div>

        <!-- Body Content -->
        <div class="email-body">
            <?= $this->fetch('content') ?>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>TMM Apprentice Management System</strong></p>
            <p>Training & Monitoring Management for Vocational Institutions</p>
            
            <div class="divider" style="background: linear-gradient(to right, transparent, #444, transparent);"></div>
            
            <p>
                📧 <a href="mailto:sriechoentjoro@gmail.com">sriechoentjoro@gmail.com</a><br>
                🌐 <a href="http://103.214.112.58/tmm/">http://103.214.112.58/tmm/</a>
            </p>
            
            <div class="social-links">
                <a href="#" title="Facebook">📘</a>
                <a href="#" title="Twitter">🐦</a>
                <a href="#" title="LinkedIn">💼</a>
                <a href="#" title="Instagram">📷</a>
            </div>
            
            <p style="margin-top: 20px; font-size: 11px; color: #95a5a6;">
                © <?= date('Y') ?> TMM Apprentice Management System. All rights reserved.<br>
                This email was sent automatically. Please do not reply directly to this email.
            </p>
        </div>
    </div>
</body>
</html>
