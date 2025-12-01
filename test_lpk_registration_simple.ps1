# Simple Test Script: LPK Registration Wizard
# Tests the Phase 3-4 implementation

$ErrorActionPreference = "Continue"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  LPK Registration - Test Suite" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# TEST 1: Email Configuration
Write-Host "TEST 1: Email Service Configuration" -ForegroundColor Cyan
Write-Host "-----------------------------------" -ForegroundColor Gray

$configPath = "d:\xampp\htdocs\tmm\config\app.php"
if (Test-Path $configPath) {
    $config = Get-Content $configPath -Raw
    
    if ($config -match "smtp\.gmail\.com") {
        Write-Host "[PASS] SMTP host configured" -ForegroundColor Green
    } else {
        Write-Host "[FAIL] SMTP host not found" -ForegroundColor Red
    }
    
    if ($config -match "'port'\s*=>\s*587") {
        Write-Host "[PASS] SMTP port 587 configured" -ForegroundColor Green
    } else {
        Write-Host "[FAIL] SMTP port not configured" -ForegroundColor Red
    }
    
    if ($config -match "'tls'\s*=>\s*true") {
        Write-Host "[PASS] TLS encryption enabled" -ForegroundColor Green
    } else {
        Write-Host "[FAIL] TLS not enabled" -ForegroundColor Red
    }
    
    if ($config -match "sriechoentjoro@gmail\.com") {
        Write-Host "[PASS] Email username configured" -ForegroundColor Green
    } else {
        Write-Host "[FAIL] Email username not found" -ForegroundColor Red
    }
} else {
    Write-Host "[FAIL] Config file not found" -ForegroundColor Red
}

# TEST 2: File Existence
Write-Host "`nTEST 2: Application Files" -ForegroundColor Cyan
Write-Host "-----------------------------------" -ForegroundColor Gray

$files = @(
    "src\Model\Table\EmailVerificationTokensTable.php",
    "src\Model\Entity\EmailVerificationToken.php",
    "src\Controller\Admin\LpkRegistrationController.php",
    "src\Template\Admin\LpkRegistration\create.ctp",
    "src\Template\Admin\LpkRegistration\index.ctp",
    "src\Template\LpkRegistration\verify_email.ctp",
    "src\Template\LpkRegistration\set_password.ctp"
)

foreach ($file in $files) {
    $path = "d:\xampp\htdocs\tmm\$file"
    if (Test-Path $path) {
        Write-Host "[PASS] $file" -ForegroundColor Green
    } else {
        Write-Host "[FAIL] $file NOT FOUND" -ForegroundColor Red
    }
}

# TEST 3: Database Tables
Write-Host "`nTEST 3: Database Schema" -ForegroundColor Cyan
Write-Host "-----------------------------------" -ForegroundColor Gray

try {
    $result = mysql -u root -proot -D cms_authentication_authorization -e "SHOW TABLES LIKE 'email_verification_tokens';" 2>&1
    if ($result -match "email_verification_tokens") {
        Write-Host "[PASS] Table email_verification_tokens exists" -ForegroundColor Green
        
        # Count columns
        $cols = mysql -u root -proot -D cms_authentication_authorization -e "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME='email_verification_tokens' AND TABLE_SCHEMA='cms_authentication_authorization';" -s -N 2>&1
        if ($cols -eq 8) {
            Write-Host "[PASS] Table has 8 columns" -ForegroundColor Green
        } else {
            Write-Host "[WARN] Table has $cols columns (expected 8)" -ForegroundColor Yellow
        }
    } else {
        Write-Host "[FAIL] Table email_verification_tokens NOT FOUND" -ForegroundColor Red
    }
} catch {
    Write-Host "[FAIL] Database connection error" -ForegroundColor Red
}

# TEST 4: Controller Methods
Write-Host "`nTEST 4: Controller Methods" -ForegroundColor Cyan
Write-Host "-----------------------------------" -ForegroundColor Gray

$controllerPath = "d:\xampp\htdocs\tmm\src\Controller\Admin\LpkRegistrationController.php"
if (Test-Path $controllerPath) {
    $controller = Get-Content $controllerPath -Raw
    
    $methods = @("index", "create", "verifyEmail", "setPassword", "resendVerification", "_generateUsername")
    foreach ($method in $methods) {
        if ($controller -match "function $method") {
            Write-Host "[PASS] Method $method() exists" -ForegroundColor Green
        } else {
            Write-Host "[FAIL] Method $method() NOT FOUND" -ForegroundColor Red
        }
    }
}

# TEST 5: Model Methods
Write-Host "`nTEST 5: Model Methods" -ForegroundColor Cyan
Write-Host "-----------------------------------" -ForegroundColor Gray

$modelPath = "d:\xampp\htdocs\tmm\src\Model\Table\EmailVerificationTokensTable.php"
if (Test-Path $modelPath) {
    $model = Get-Content $modelPath -Raw
    
    $methods = @("generateToken", "validateToken", "markAsUsed", "cleanupExpired", "getTokenStats", "resendVerification")
    foreach ($method in $methods) {
        if ($model -match "function $method") {
            Write-Host "[PASS] Method $method() exists" -ForegroundColor Green
        } else {
            Write-Host "[FAIL] Method $method() NOT FOUND" -ForegroundColor Red
        }
    }
}

# TEST 6: Email Templates
Write-Host "`nTEST 6: Email Templates" -ForegroundColor Cyan
Write-Host "-----------------------------------" -ForegroundColor Gray

$emailTemplates = @(
    "src\Template\Email\html\lpk_verification.ctp",
    "src\Template\Email\text\lpk_verification.ctp",
    "src\Template\Email\html\lpk_welcome.ctp",
    "src\Template\Email\text\lpk_welcome.ctp"
)

foreach ($template in $emailTemplates) {
    $path = "d:\xampp\htdocs\tmm\$template"
    if (Test-Path $path) {
        Write-Host "[PASS] $template" -ForegroundColor Green
    } else {
        Write-Host "[FAIL] $template NOT FOUND" -ForegroundColor Red
    }
}

# Summary
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Test Suite Complete" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor White
Write-Host "1. Start XAMPP (Apache + MySQL)" -ForegroundColor Gray
Write-Host "2. Navigate to: http://localhost/tmm/admin/lpk-registration/create" -ForegroundColor Gray
Write-Host "3. Fill registration form and submit" -ForegroundColor Gray
Write-Host "4. Check email for verification link" -ForegroundColor Gray
Write-Host "5. Complete password setup" -ForegroundColor Gray
Write-Host "6. Test login" -ForegroundColor Gray
Write-Host ""
Write-Host "Documentation:" -ForegroundColor White
Write-Host "- ADMIN_GUIDE_LPK_REGISTRATION.md" -ForegroundColor Gray
Write-Host "- PHASE_3_4_TESTING_GUIDE.md" -ForegroundColor Gray
Write-Host ""
