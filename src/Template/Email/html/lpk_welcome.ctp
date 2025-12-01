<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to TMM System</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 32px;
            font-weight: 600;
        }
        .header p {
            color: #ffffff;
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #555555;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .success-banner {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
            text-align: center;
        }
        .success-banner h2 {
            margin: 0 0 10px 0;
            color: #155724;
            font-size: 24px;
        }
        .success-banner p {
            margin: 0;
            color: #155724;
            font-size: 16px;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .credentials-box h3 {
            margin: 0 0 15px 0;
            color: #667eea;
            font-size: 18px;
        }
        .credential-row {
            display: flex;
            margin-bottom: 15px;
            font-size: 15px;
        }
        .credential-label {
            font-weight: 600;
            color: #333333;
            min-width: 140px;
        }
        .credential-value {
            color: #555555;
            font-family: 'Courier New', monospace;
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .cta-button {
            text-align: center;
            margin: 40px 0;
        }
        .cta-button a {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        .features-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .features-box h3 {
            margin: 0 0 15px 0;
            color: #2196F3;
            font-size: 18px;
        }
        .features-box ul {
            margin: 0;
            padding-left: 25px;
            color: #014361;
        }
        .features-box li {
            margin-bottom: 10px;
            font-size: 14px;
        }
        .tips-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .tips-box h3 {
            margin: 0 0 15px 0;
            color: #856404;
            font-size: 18px;
        }
        .tips-box ol {
            margin: 0;
            padding-left: 25px;
            color: #856404;
        }
        .tips-box li {
            margin-bottom: 10px;
            font-size: 14px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 14px;
        }
        .footer a {
            color: #28a745;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
            .header h1 {
                font-size: 26px;
            }
            .credential-row {
                flex-direction: column;
            }
            .credential-label {
                margin-bottom: 5px;
            }
            .cta-button a {
                padding: 14px 30px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Welcome to TMM System!</h1>
            <p>Your Account is Now Active</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Dear <?= h($directorName) ?>,
            </div>

            <!-- Success Banner -->
            <div class="success-banner">
                <h2>✓ Account Successfully Activated</h2>
                <p>You can now access the TMM Training and Manpower Management System</p>
            </div>

            <div class="message">
                <p>Congratulations! Your email has been verified and your password has been set. Your account for <strong><?= h($institutionName) ?></strong> is now fully activated and ready to use.</p>
            </div>

            <!-- Login Credentials -->
            <div class="credentials-box">
                <h3>🔑 Your Login Credentials</h3>
                <div class="credential-row">
                    <span class="credential-label">Username:</span>
                    <span class="credential-value"><?= h($username) ?></span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value"><?= h($email) ?></span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Institution:</span>
                    <span class="credential-value"><?= h($institutionName) ?></span>
                </div>
                <p style="margin: 15px 0 0 0; font-size: 13px; color: #6c757d;">
                    <strong>Note:</strong> Your password is the one you created during registration. Keep it secure and don't share it with anyone.
                </p>
            </div>

            <!-- Call-to-Action Button -->
            <div class="cta-button">
                <a href="<?= $loginUrl ?>" style="color: #ffffff;">
                    🚀 LOGIN NOW
                </a>
            </div>

            <!-- What You Can Do -->
            <div class="features-box">
                <h3>📊 What You Can Do with Your Account</h3>
                <ul>
                    <li><strong>Manage Candidates:</strong> Register and manage trainee candidate profiles and documents</li>
                    <li><strong>Track Training Progress:</strong> Monitor candidate training stages and assessments</li>
                    <li><strong>Submit Apprentice Orders:</strong> Create and submit apprenticeship placement orders</li>
                    <li><strong>Export Reports:</strong> Generate and download candidate data in various formats (CSV, Excel, PDF)</li>
                    <li><strong>Dashboard Analytics:</strong> View statistics and insights about your training programs</li>
                    <li><strong>Document Management:</strong> Upload and manage certificates, licenses, and training documents</li>
                    <li><strong>Communication Hub:</strong> Direct messaging with system administrators and coordinators</li>
                </ul>
            </div>

            <!-- Getting Started Tips -->
            <div class="tips-box">
                <h3>💡 Getting Started - Quick Tips</h3>
                <ol>
                    <li><strong>Update Your Profile:</strong> Complete your institution profile with detailed information</li>
                    <li><strong>Add Candidates:</strong> Start registering your trainee candidates in the system</li>
                    <li><strong>Upload Documents:</strong> Add required certificates, licenses, and supporting documents</li>
                    <li><strong>Explore Features:</strong> Familiarize yourself with the dashboard and available tools</li>
                    <li><strong>Contact Support:</strong> Reach out if you need assistance or have questions</li>
                </ol>
            </div>

            <!-- Alternative Login Link -->
            <div class="message" style="margin-top: 30px;">
                <p style="font-size: 14px; color: #6c757d;">
                    If the button above doesn't work, copy and paste this link into your browser:
                </p>
                <p style="font-size: 12px; color: #28a745; word-break: break-all;">
                    <?= $loginUrl ?>
                </p>
            </div>

            <!-- Support Information -->
            <div class="message" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                <p style="font-size: 15px;">
                    <strong>Need Help?</strong>
                </p>
                <p style="font-size: 14px; color: #6c757d;">
                    If you have any questions or need assistance, our support team is here to help:
                </p>
                <p style="font-size: 14px; color: #6c757d;">
                    📧 Email: <a href="mailto:support@asahifamily.id" style="color: #28a745;">support@asahifamily.id</a><br>
                    📞 Phone: <a href="tel:+622189844450" style="color: #28a745;">+62 21 8984 4450</a><br>
                    🕐 Support Hours: Monday - Friday, 08:00 - 17:00 WIB
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="font-weight: 600; color: #333333; margin-bottom: 15px;">
                TMM - Training and Manpower Management System
            </p>
            <p>
                PT. ASAHI FAMILY INDONESIA
            </p>
            <p>
                Jl. Industri Raya III Blok AF No. 1, Kawasan Industri Jababeka<br>
                Cikarang, Bekasi 17530, Indonesia
            </p>
            
            <div style="margin: 20px 0;">
                <a href="mailto:support@asahifamily.id" style="margin: 0 10px;">✉ support@asahifamily.id</a>
                <span style="color: #dee2e6;">|</span>
                <a href="tel:+622189844450" style="margin: 0 10px;">📞 +62 21 8984 4450</a>
            </div>

            <p style="font-size: 12px; color: #adb5bd; margin-top: 20px;">
                © <?= date('Y') ?> PT. ASAHI FAMILY INDONESIA. All rights reserved.
            </p>
            
            <p style="font-size: 12px; color: #adb5bd;">
                This is an automated email. Please do not reply directly to this message.
            </p>
        </div>
    </div>
</body>
</html>
