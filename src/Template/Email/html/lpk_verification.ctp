<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - TMM System</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
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
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin: 0 0 15px 0;
            color: #667eea;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .info-label {
            font-weight: 600;
            color: #333333;
            min-width: 180px;
        }
        .info-value {
            color: #555555;
        }
        .cta-button {
            text-align: center;
            margin: 40px 0;
        }
        .cta-button a {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .help-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .help-box p {
            margin: 5px 0;
            color: #014361;
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
            color: #667eea;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
            .header h1 {
                font-size: 24px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
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
            <h1>🎓 TMM System Registration</h1>
            <p>Verify Your Email Address</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Dear <?= h($directorName) ?>,
            </div>

            <div class="message">
                <p>Congratulations! Your Vocational Training Institution <strong><?= h($institutionName) ?></strong> has been successfully registered in the TMM (Training and Manpower Management) System.</p>
                
                <p>To complete your registration and activate your account, please verify your email address by clicking the button below:</p>
            </div>

            <!-- Call-to-Action Button -->
            <div class="cta-button">
                <a href="<?= $verificationUrl ?>" style="color: #ffffff;">
                    ✓ VERIFY EMAIL ADDRESS
                </a>
            </div>

            <!-- Registration Details -->
            <div class="info-box">
                <h3>📋 Registration Details</h3>
                <div class="info-row">
                    <span class="info-label">Institution Name:</span>
                    <span class="info-value"><?= h($institutionName) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Registration Number:</span>
                    <span class="info-value"><?= h($registrationNumber) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email Address:</span>
                    <span class="info-value"><?= h($email) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Director Name:</span>
                    <span class="info-value"><?= h($directorName) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Registered By:</span>
                    <span class="info-value"><?= h($registeredByAdmin) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Registration Date:</span>
                    <span class="info-value"><?= h($registrationDate) ?></span>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="warning-box">
                <p><strong>⏰ Important:</strong> This verification link will expire in <strong>24 hours</strong>. Please verify your email as soon as possible to avoid delays in account activation.</p>
            </div>

            <!-- What Happens Next -->
            <div class="help-box">
                <p><strong>📌 What Happens Next?</strong></p>
                <p>After verifying your email, you'll be directed to create a secure password for your account. Once completed, you'll receive a welcome email with your login credentials and instructions on how to access the TMM system.</p>
            </div>

            <!-- Alternative Link -->
            <div class="message" style="margin-top: 30px;">
                <p style="font-size: 14px; color: #6c757d;">
                    If the button above doesn't work, copy and paste this link into your browser:
                </p>
                <p style="font-size: 12px; color: #667eea; word-break: break-all;">
                    <?= $verificationUrl ?>
                </p>
            </div>

            <!-- Security Notice -->
            <div class="message" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                <p style="font-size: 14px; color: #6c757d;">
                    <strong>🔒 Security Notice:</strong> If you did not request this registration or believe this email was sent to you by mistake, please contact our support team immediately at <a href="mailto:support@asahifamily.id" style="color: #667eea;">support@asahifamily.id</a>
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
