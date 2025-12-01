# Test Script: LPK Registration Wizard
# This script tests the complete 3-step registration workflow

param(
    [switch]$EmailOnly,      # Test email delivery only
    [switch]$HappyPath,      # Test full happy path (Scenario LPK-001)
    [switch]$ErrorScenarios, # Test error scenarios
    [switch]$All             # Run all tests
)

$ErrorActionPreference = "Continue"
$BaseURL = "http://localhost/tmm"
$TestEmail = "test.lpk@example.com"
$TestPassword = "TestLPK2025!"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  LPK Registration Wizard - Test Suite  " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Helper function to print test status
function Write-TestResult {
    param(
        [string]$TestName,
        [bool]$Passed,
        [string]$Message = ""
    )
    
    if ($Passed) {
        Write-Host "✅ PASS: " -ForegroundColor Green -NoNewline
        Write-Host "$TestName" -ForegroundColor White
        if ($Message) {
            Write-Host "   └─ $Message" -ForegroundColor Gray
        }
    } else {
        Write-Host "❌ FAIL: " -ForegroundColor Red -NoNewline
        Write-Host "$TestName" -ForegroundColor White
        if ($Message) {
            Write-Host "   └─ $Message" -ForegroundColor Yellow
        }
    }
}

# Helper function to query database
function Invoke-MySQLQuery {
    param(
        [string]$Query,
        [string]$Database = "cms_authentication_authorization"
    )
    
    try {
        $result = mysql -u root -proot -D $Database -e "$Query" -s -N 2>&1
        if ($LASTEXITCODE -eq 0) {
            return $result
        } else {
            Write-Host "   ⚠️  MySQL Error: $result" -ForegroundColor Yellow
            return $null
        }
    } catch {
        Write-Host "   ⚠️  Exception: $_" -ForegroundColor Yellow
        return $null
    }
}

# TEST 1: Email Service Configuration
Write-Host "`n📧 TEST 1: Email Service Configuration" -ForegroundColor Cyan
Write-Host "─────────────────────────────────────────" -ForegroundColor Gray

# Check config/app.php for email settings
$configPath = "d:\xampp\htdocs\tmm\config\app.php"
if (Test-Path $configPath) {
    $configContent = Get-Content $configPath -Raw
    
    # Check SMTP host
    $smtpHostMatch = $configContent -match "'host'\s*=>\s*'smtp\.gmail\.com'"
    Write-TestResult -TestName "SMTP host configured (smtp.gmail.com)" -Passed $smtpHostMatch
    
    # Check SMTP port
    $smtpPortMatch = $configContent -match "'port'\s*=>\s*587"
    Write-TestResult -TestName "SMTP port configured (587)" -Passed $smtpPortMatch
    
    # Check TLS enabled
    $tlsMatch = $configContent -match "'tls'\s*=>\s*true"
    Write-TestResult -TestName "TLS encryption enabled" -Passed $tlsMatch
    
    # Check username configured
    $usernameMatch = $configContent -match "'username'\s*=>\s*'sriechoentjoro@gmail\.com'"
    Write-TestResult -TestName "Email username configured" -Passed $usernameMatch
    
    # Check password configured
    $passwordMatch = $configContent -match "'password'\s*=>\s*'[^']+'"
    Write-TestResult -TestName "Email password configured" -Passed $passwordMatch
    
    if ($smtpHostMatch -and $smtpPortMatch -and $tlsMatch -and $usernameMatch -and $passwordMatch) {
        Write-Host "`n✅ Email service configuration: READY" -ForegroundColor Green
    } else {
        Write-Host "`n❌ Email service configuration: INCOMPLETE" -ForegroundColor Red
        Write-Host "   Please check config/app.php" -ForegroundColor Yellow
    }
} else {
    Write-TestResult -TestName "Config file exists" -Passed $false -Message "File not found: $configPath"
}

if ($EmailOnly) {
    Write-Host "`n📬 Attempting to send test email..." -ForegroundColor Cyan
    Write-Host "   Note: This requires CakePHP console. Run manually:" -ForegroundColor Yellow
    Write-Host "   bin\cake console" -ForegroundColor Gray
    Write-Host "   # Then run email test commands" -ForegroundColor Gray
    exit 0
}

# TEST 2: Database Schema
Write-Host "`n🗄️  TEST 2: Database Schema Validation" -ForegroundColor Cyan
Write-Host "─────────────────────────────────────────" -ForegroundColor Gray

# Check email_verification_tokens table exists
$tableExists = Invoke-MySQLQuery -Query "SHOW TABLES LIKE 'email_verification_tokens';"
Write-TestResult -TestName "Table 'email_verification_tokens' exists" -Passed ($null -ne $tableExists)

if ($tableExists) {
    # Check table structure
    $columnCount = Invoke-MySQLQuery -Query "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME = 'email_verification_tokens' AND TABLE_SCHEMA = 'cms_authentication_authorization';"
    Write-TestResult -TestName "Table has correct column count (8 columns)" -Passed ($columnCount -eq 8)
    
    # Check indexes
    $indexCount = Invoke-MySQLQuery -Query "SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_NAME = 'email_verification_tokens' AND TABLE_SCHEMA = 'cms_authentication_authorization' AND INDEX_NAME != 'PRIMARY';"
    Write-TestResult -TestName "Table has correct indexes (5 indexes)" -Passed ($indexCount -eq 5)
}

# Check vocational_training_institutions table
$lpkTableExists = Invoke-MySQLQuery -Query "SHOW TABLES LIKE 'vocational_training_institutions';"
Write-TestResult -TestName "Table 'vocational_training_institutions' exists" -Passed ($null -ne $lpkTableExists)

# TEST 3: File Existence
Write-Host "`n📁 TEST 3: Application Files" -ForegroundColor Cyan
Write-Host "─────────────────────────────────────────" -ForegroundColor Gray

$files = @(
    @{Path="src\Model\Table\EmailVerificationTokensTable.php"; Name="EmailVerificationTokensTable.php"},
    @{Path="src\Model\Entity\EmailVerificationToken.php"; Name="EmailVerificationToken.php"},
    @{Path="src\Controller\Admin\LpkRegistrationController.php"; Name="LpkRegistrationController.php"},
    @{Path="src\Template\Admin\LpkRegistration\create.ctp"; Name="create.ctp"},
    @{Path="src\Template\Admin\LpkRegistration\index.ctp"; Name="index.ctp"},
    @{Path="src\Template\LpkRegistration\verify_email.ctp"; Name="verify_email.ctp"},
    @{Path="src\Template\LpkRegistration\set_password.ctp"; Name="set_password.ctp"},
    @{Path="src\Template\Email\html\lpk_verification.ctp"; Name="lpk_verification.ctp (HTML)"},
    @{Path="src\Template\Email\text\lpk_verification.ctp"; Name="lpk_verification.ctp (Text)"},
    @{Path="src\Template\Email\html\lpk_welcome.ctp"; Name="lpk_welcome.ctp (HTML)"},
    @{Path="src\Template\Email\text\lpk_welcome.ctp"; Name="lpk_welcome.ctp (Text)"}
)

foreach ($file in $files) {
    $fullPath = Join-Path "d:\xampp\htdocs\tmm" $file.Path
    $exists = Test-Path $fullPath
    Write-TestResult -TestName "File: $($file.Name)" -Passed $exists -Message $(if ($exists) { "Found" } else { "Missing" })
}

# TEST 4: Database Data Integrity
Write-Host "`n🔍 TEST 4: Database Data Integrity" -ForegroundColor Cyan
Write-Host "─────────────────────────────────────────" -ForegroundColor Gray

# Count total LPK registrations
$totalLPKs = Invoke-MySQLQuery -Query "SELECT COUNT(*) FROM vocational_training_institutions;"
Write-Host "   Total LPK registrations: $totalLPKs" -ForegroundColor White

if ($totalLPKs -gt 0) {
    # Count by status
    $pendingCount = Invoke-MySQLQuery -Query "SELECT COUNT(*) FROM vocational_training_institutions WHERE status = 'pending_verification';"
    $verifiedCount = Invoke-MySQLQuery -Query "SELECT COUNT(*) FROM vocational_training_institutions WHERE status = 'verified';"
    $activeCount = Invoke-MySQLQuery -Query "SELECT COUNT(*) FROM vocational_training_institutions WHERE status = 'active';"
    
    Write-Host "   ├─ Pending Verification: $pendingCount" -ForegroundColor Yellow
    Write-Host "   ├─ Verified: $verifiedCount" -ForegroundColor Blue
    Write-Host "   └─ Active: $activeCount" -ForegroundColor Green
}

# Count tokens
$totalTokens = Invoke-MySQLQuery -Query "SELECT COUNT(*) FROM email_verification_tokens;"
Write-Host "`n   Total verification tokens: $totalTokens" -ForegroundColor White

if ($totalTokens -gt 0) {
    $usedTokens = Invoke-MySQLQuery -Query "SELECT COUNT(*) FROM email_verification_tokens WHERE is_used = 1;"
    $activeTokens = Invoke-MySQLQuery -Query "SELECT COUNT(*) FROM email_verification_tokens WHERE is_used = 0 AND expires_at >= NOW();"
    $expiredTokens = Invoke-MySQLQuery -Query "SELECT COUNT(*) FROM email_verification_tokens WHERE expires_at < NOW();"
    
    Write-Host "   ├─ Used: $usedTokens" -ForegroundColor Green
    Write-Host "   ├─ Active: $activeTokens" -ForegroundColor Blue
    Write-Host "   └─ Expired: $expiredTokens" -ForegroundColor Red
}

# TEST 5: Controller Methods
Write-Host "`n🎮 TEST 5: Controller Methods" -ForegroundColor Cyan
Write-Host "─────────────────────────────────────────" -ForegroundColor Gray

$controllerPath = "d:\xampp\htdocs\tmm\src\Controller\Admin\LpkRegistrationController.php"
if (Test-Path $controllerPath) {
    $controllerContent = Get-Content $controllerPath -Raw
    
    $methods = @(
        @{Name="index()"; Pattern="public function index\(\)"},
        @{Name="create()"; Pattern="public function create\(\)"},
        @{Name="verifyEmail()"; Pattern="public function verifyEmail\(\)"},
        @{Name="setPassword()"; Pattern="public function setPassword\(\)"},
        @{Name="resendVerification()"; Pattern="public function resendVerification\(\)"},
        @{Name="_generateUsername()"; Pattern="protected function _generateUsername\("}
    )
    
    foreach ($method in $methods) {
        $exists = $controllerContent -match $method.Pattern
        Write-TestResult -TestName "Method: $($method.Name)" -Passed $exists
    }
}

# TEST 6: Model Methods
Write-Host "`n📦 TEST 6: Model Methods" -ForegroundColor Cyan
Write-Host "─────────────────────────────────────────" -ForegroundColor Gray

$modelPath = "d:\xampp\htdocs\tmm\src\Model\Table\EmailVerificationTokensTable.php"
if (Test-Path $modelPath) {
    $modelContent = Get-Content $modelPath -Raw
    
    $methods = @(
        @{Name="generateToken()"; Pattern="public function generateToken\("},
        @{Name="validateToken()"; Pattern="public function validateToken\("},
        @{Name="markAsUsed()"; Pattern="public function markAsUsed\("},
        @{Name="cleanupExpired()"; Pattern="public function cleanupExpired\("},
        @{Name="getTokenStats()"; Pattern="public function getTokenStats\("},
        @{Name="resendVerification()"; Pattern="public function resendVerification\("}
    )
    
    foreach ($method in $methods) {
        $exists = $modelContent -match $method.Pattern
        Write-TestResult -TestName "Method: $($method.Name)" -Passed $exists
    }
}

# TEST 7: Template Features
Write-Host "`n🎨 TEST 7: Template Features" -ForegroundColor Cyan
Write-Host "─────────────────────────────────────────" -ForegroundColor Gray

# Check create.ctp features
$createPath = "d:\xampp\htdocs\tmm\src\Template\Admin\LpkRegistration\create.ctp"
if (Test-Path $createPath) {
    $createContent = Get-Content $createPath -Raw
    
    Write-TestResult -TestName "Cascade dropdowns (Province → City)" -Passed ($createContent -match "master_propinsi_id")
    Write-TestResult -TestName "Required field indicators (*)" -Passed ($createContent -match "text-danger")
    Write-TestResult -TestName "Email validation" -Passed ($createContent -match "type.*email")
}

# Check set_password.ctp features
$passwordPath = "d:\xampp\htdocs\tmm\src\Template\LpkRegistration\set_password.ctp"
if (Test-Path $passwordPath) {
    $passwordContent = Get-Content $passwordPath -Raw
    
    Write-TestResult -TestName "Password strength meter" -Passed ($passwordContent -match "password-strength")
    Write-TestResult -TestName "Password requirements checklist" -Passed ($passwordContent -match "requirement")
    Write-TestResult -TestName "Password confirmation" -Passed ($passwordContent -match "password_confirm")
}

# Check verify_email.ctp features
$verifyPath = "d:\xampp\htdocs\tmm\src\Template\LpkRegistration\verify_email.ctp"
if (Test-Path $verifyPath) {
    $verifyContent = Get-Content $verifyPath -Raw
    
    Write-TestResult -TestName "Success state display" -Passed ($verifyContent -match "status.*success")
    Write-TestResult -TestName "Error state display" -Passed ($verifyContent -match "status.*error")
    Write-TestResult -TestName "Countdown timer" -Passed ($verifyContent -match "countdown")
}

# HAPPY PATH TEST
if ($HappyPath -or $All) {
    Write-Host "`n🎯 TEST 8: Happy Path Scenario (LPK-001)" -ForegroundColor Cyan
    Write-Host "─────────────────────────────────────────" -ForegroundColor Gray
    Write-Host ""
    Write-Host "⚠️  This test requires manual execution" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Follow these steps:" -ForegroundColor White
    Write-Host ""
    Write-Host "STEP 1: Admin Registration" -ForegroundColor Cyan
    Write-Host "  1. Navigate to: $BaseURL/admin/lpk-registration/create" -ForegroundColor Gray
    Write-Host "  2. Fill form with test data:" -ForegroundColor Gray
    Write-Host "     - Institution Name: TEST LPK AUTOMATION" -ForegroundColor Gray
    Write-Host "     - Registration Number: AUTO-TEST-$(Get-Date -Format 'yyyyMMdd-HHmmss')" -ForegroundColor Gray
    Write-Host "     - Director Name: Test Director" -ForegroundColor Gray
    Write-Host "     - Email: $TestEmail" -ForegroundColor Gray
    Write-Host "     - Phone: 081234567890" -ForegroundColor Gray
    Write-Host "     - Address: Jl. Test No. 123" -ForegroundColor Gray
    Write-Host "  3. Submit form" -ForegroundColor Gray
    Write-Host "  4. Verify success message displayed" -ForegroundColor Gray
    Write-Host ""
    Write-Host "STEP 2: Email Verification" -ForegroundColor Cyan
    Write-Host "  1. Check inbox: $TestEmail" -ForegroundColor Gray
    Write-Host "  2. Click 'VERIFY EMAIL ADDRESS' button" -ForegroundColor Gray
    Write-Host "  3. Verify success page with checkmark" -ForegroundColor Gray
    Write-Host "  4. Wait for auto-redirect (5 seconds)" -ForegroundColor Gray
    Write-Host ""
    Write-Host "STEP 3: Password Setup" -ForegroundColor Cyan
    Write-Host "  1. Enter password: $TestPassword" -ForegroundColor Gray
    Write-Host "  2. Confirm password: $TestPassword" -ForegroundColor Gray
    Write-Host "  3. Check 'I agree to Terms' checkbox" -ForegroundColor Gray
    Write-Host "  4. Click 'Set Password & Activate Account'" -ForegroundColor Gray
    Write-Host "  5. Verify welcome email received" -ForegroundColor Gray
    Write-Host ""
    Write-Host "STEP 4: Login Test" -ForegroundColor Cyan
    Write-Host "  1. Navigate to: $BaseURL/users/login" -ForegroundColor Gray
    Write-Host "  2. Username: test_lpk_automation" -ForegroundColor Gray
    Write-Host "  3. Password: $TestPassword" -ForegroundColor Gray
    Write-Host "  4. Verify login successful" -ForegroundColor Gray
    Write-Host ""
    
    # Database verification queries
    Write-Host "Database Verification Queries:" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "-- Check LPK status" -ForegroundColor Gray
    Write-Host "SELECT id, name, email, status, email_verified_at FROM vocational_training_institutions WHERE email = '$TestEmail';" -ForegroundColor White
    Write-Host ""
    Write-Host "-- Check token" -ForegroundColor Gray
    Write-Host "SELECT token, LENGTH(token) as len, is_used, expires_at FROM email_verification_tokens WHERE user_email = '$TestEmail' ORDER BY created DESC LIMIT 1;" -ForegroundColor White
    Write-Host ""
    Write-Host "-- Check user account" -ForegroundColor Gray
    Write-Host "SELECT username, email, status, is_active, institution_type FROM users WHERE email = '$TestEmail';" -ForegroundColor White
    Write-Host ""
    Write-Host "-- Check activity logs" -ForegroundColor Gray
    Write-Host "SELECT activity_type, description, created FROM stakeholder_activities WHERE stakeholder_type = 'vocational_training' ORDER BY created DESC LIMIT 5;" -ForegroundColor White
    Write-Host ""
}

# ERROR SCENARIOS TEST
if ($ErrorScenarios -or $All) {
    Write-Host "`n⚠️  TEST 9: Error Scenarios" -ForegroundColor Cyan
    Write-Host "─────────────────────────────────────────" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Manual tests to perform:" -ForegroundColor White
    Write-Host ""
    Write-Host "1. Expired Token Test" -ForegroundColor Yellow
    Write-Host "   - Manually expire a token in database:" -ForegroundColor Gray
    Write-Host "     UPDATE email_verification_tokens SET expires_at = DATE_SUB(NOW(), INTERVAL 25 HOUR) WHERE user_email = 'test@example.com';" -ForegroundColor White
    Write-Host "   - Try to verify with expired link" -ForegroundColor Gray
    Write-Host "   - Expected: 'Link has expired' error" -ForegroundColor Gray
    Write-Host ""
    Write-Host "2. Used Token Test" -ForegroundColor Yellow
    Write-Host "   - Complete verification once" -ForegroundColor Gray
    Write-Host "   - Click same verification link again" -ForegroundColor Gray
    Write-Host "   - Expected: 'Link already used' error" -ForegroundColor Gray
    Write-Host ""
    Write-Host "3. Invalid Token Test" -ForegroundColor Yellow
    Write-Host "   - Navigate to: $BaseURL/lpk-registration/verify-email/invalidtoken123" -ForegroundColor Gray
    Write-Host "   - Expected: 'Invalid token' error" -ForegroundColor Gray
    Write-Host ""
    Write-Host "4. Weak Password Test" -ForegroundColor Yellow
    Write-Host "   - Try passwords: 'test', '12345678', 'Test1234'" -ForegroundColor Gray
    Write-Host "   - Expected: Submit button disabled, requirements show red" -ForegroundColor Gray
    Write-Host ""
    Write-Host "5. Password Mismatch Test" -ForegroundColor Yellow
    Write-Host "   - Enter password: TestLPK2025!" -ForegroundColor Gray
    Write-Host "   - Confirm: TestLPK2025!!" -ForegroundColor Gray
    Write-Host "   - Expected: 'Passwords do not match' error" -ForegroundColor Gray
    Write-Host ""
}

# SUMMARY
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Test Suite Complete" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor White
Write-Host "  1. Run manual happy path test: .\test_lpk_registration.ps1 -HappyPath" -ForegroundColor Gray
Write-Host "  2. Run error scenario tests: .\test_lpk_registration.ps1 -ErrorScenarios" -ForegroundColor Gray
Write-Host "  3. Deploy to production after all tests pass" -ForegroundColor Gray
Write-Host ""
Write-Host "Documentation:" -ForegroundColor White
Write-Host "  - Admin Guide: ADMIN_GUIDE_LPK_REGISTRATION.md" -ForegroundColor Gray
Write-Host "  - Testing Guide: PHASE_3_4_TESTING_GUIDE.md" -ForegroundColor Gray
Write-Host "  - Implementation: PHASE_3_4_IMPLEMENTATION_COMPLETE.md" -ForegroundColor Gray
Write-Host ""
