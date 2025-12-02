# Git-Based Deployment Guide for TMM Application

## 🎉 Latest Deployment: December 3, 2025 ✅

**Status:** Successfully deployed Multi-Language Process Flow Help System
- **Commit:** 2ec76bf
- **Files:** 564 files updated (92 templates + 91 controllers + automation scripts)
- **Features:** Light glossy theme, 3-language support (🇮🇩 🇬🇧 🇯🇵), Japanese text fixed
- **Result:** ✅ All process flow pages working perfectly

## Quick Deploy v2.0 (Recommended - CRLF Fixed)

**Use the improved script for next deployment:**
```powershell
powershell -ExecutionPolicy Bypass -File deploy_to_production_via_git_v2.ps1
```

**What it does automatically:**
1. ✓ Checks uncommitted changes
2. ✓ Commits & pushes to GitHub (optional)
3. ✓ Pulls on production server
4. ✓ Sets file permissions (www-data:www-data)
5. ✓ Clears CakePHP cache
6. ✓ Restarts PHP-FPM & nginx
7. ✓ Verifies deployment

**Why v2.0?**
- ✅ **Fixed CRLF issues** - No more bash errors on Linux
- ✅ **Single command SSH** - Uses `cd && command` pattern
- ✅ **Better error handling** - Try/catch with status indicators
- ✅ **Improved output** - ✓ ✗ ⚠ symbols for each step

## Overview

This guide describes the Git-based deployment workflow between your local development environment (`d:\xampp\htdocs\tmm`) and the production server (`103.214.112.58:/var/www/tmm`).

## Architecture

```
Local Development (Windows)
    ↓ git commit + push
GitHub Repository (sriechoentjoro/tmm-project)
    ↓ git pull (automated)
Production Server (Ubuntu 103.214.112.58)
```

## One-Time Setup

### Step 1: Setup Git on Production Server

Run this command **ONCE** to initialize Git on the server:

```powershell
.\setup_git_on_server.ps1
```

This script will:
- ✅ Install Git on production server (if not installed)
- ✅ Initialize Git repository in `/var/www/tmm`
- ✅ Connect to GitHub repository
- ✅ Pull latest code from GitHub
- ✅ Configure Git to ignore local files (logs, cache, uploads)

**Expected Output:**
```
✅ Git installed and configured on server
✅ Git repository initialized
✅ Connected to GitHub repository
✅ Latest code pulled from GitHub
✅ Git configuration completed
```

### Step 2: Verify GitHub Access

Ensure you have:
- ✅ GitHub account with access to `sriechoentjoro/tmm-project`
- ✅ Git credentials configured locally:
  ```powershell
  git config user.name
  git config user.email
  ```
- ✅ SSH key authentication to production server (recommended)

## Daily Deployment Workflow

### Method 1: Using Deployment Script (Recommended)

**Basic deployment with auto-generated commit message:**
```powershell
.\deploy_with_git.ps1
```

**Deployment with custom commit message:**
```powershell
.\deploy_with_git.ps1 -Message "Fixed katakana templates in candidates/wizard"
```

**Skip pre-deployment tests (faster, but less safe):**
```powershell
.\deploy_with_git.ps1 -Message "Quick fix" -SkipTests
```

### Method 2: Manual Git Commands

If you prefer manual control:

```powershell
# 1. Check status
git status

# 2. Add all changes
git add -A

# 3. Commit with message
git commit -m "Your descriptive commit message"

# 4. Push to GitHub
git push origin main

# 5. Deploy to production server
ssh root@103.214.112.58 "cd /var/www/tmm && git pull origin main && rm -rf tmp/cache/* && systemctl restart php7.4-fpm && systemctl reload nginx"
```

## Deployment Script Features

The `deploy_with_git.ps1` script provides:

### ✅ Pre-Deployment Checks
- Verifies you're in the correct directory
- Shows uncommitted changes
- Validates PHP syntax in critical files:
  - `config/app.php`
  - `config/app_datasources.php`
  - `src/Controller/AppController.php`

### ✅ Git Operations
- Stages all changes (`git add -A`)
- Creates commit with your message
- Pushes to GitHub repository

### ✅ Production Deployment
- SSH connects to production server
- Stashes local changes (logs, cache)
- Pulls latest code from GitHub
- Sets correct file permissions (www-data:www-data)
- Clears CakePHP cache (models, persistent, views)
- Restarts PHP-FPM and reloads nginx

### ✅ Post-Deployment Verification
- Tests HTTP endpoint (http://103.214.112.58/tmm/)
- Shows recent error logs
- Displays deployment summary

## What Gets Deployed

### Files Included in Git (Deployed)
- ✅ PHP source code (`src/`)
- ✅ Templates (`src/Template/`)
- ✅ Configuration files (`config/app.php`, `config/app_datasources.php`)
- ✅ Static assets (`webroot/css/`, `webroot/js/`, `webroot/img/icons/`)
- ✅ Composer files (`composer.json`)
- ✅ Documentation (`.md` files)

### Files Excluded from Git (Not Deployed)
- ❌ Local config overrides (`config/app_local.php`)
- ❌ Logs (`logs/*.log`)
- ❌ Cache (`tmp/cache/*`)
- ❌ Sessions (`tmp/sessions/*`)
- ❌ Uploads (`webroot/img/uploads/*`, `webroot/files/uploads/*`)
- ❌ Vendor packages (`vendor/*` - managed by Composer)
- ❌ Database backups (`backups/*.sql`)
- ❌ Temporary fix scripts (`fix_*.py`, `temp_*.txt`)

## File Management

### Configuration Files

**Development Config (Local):**
- `config/app.php` - Main config with `'debug' => true`
- `config/app_datasources.php` - Database connections

**Production Config (Server):**
- `/var/www/tmm/config/app.php` - Same file, but with `'debug' => false`
- `/var/www/tmm/config/app_local.php` - Server-specific overrides (not in Git)

### Uploads & Large Files

**Not managed by Git** (use rsync or scp for large transfers):

```powershell
# Upload images to production
scp -r webroot/img/uploads/* root@103.214.112.58:/var/www/tmm/webroot/img/uploads/

# Upload files to production
scp -r webroot/files/uploads/* root@103.214.112.58:/var/www/tmm/webroot/files/uploads/
```

### Database Changes

**Database is NOT managed by Git**. For database updates:

```powershell
# Export local databases
.\export_all_databases.ps1

# Upload to production
scp backups/cms_*.sql root@103.214.112.58:/tmp/

# Import on production
ssh root@103.214.112.58
mysql -u root -p < /tmp/cms_masters.sql
# ... import other databases
```

## Deployment Scenarios

### Scenario 1: Code-Only Changes (Templates, Controllers)

**Use Git deployment:**
```powershell
.\deploy_with_git.ps1 -Message "Fixed katakana templates"
```

**Result:**
- ✅ Code updated on production
- ✅ Cache cleared
- ✅ Services restarted
- ✅ Application immediately reflects changes

### Scenario 2: Configuration Changes (app.php, app_datasources.php)

**Use Git deployment + manual verification:**
```powershell
# Deploy via Git
.\deploy_with_git.ps1 -Message "Updated database config"

# Verify config loaded correctly
ssh root@103.214.112.58 "cd /var/www/tmm && php -r 'var_dump(include \"config/app.php\");' | head -20"
```

### Scenario 3: Database Schema Changes

**Deploy code first, then update database:**
```powershell
# 1. Deploy code changes
.\deploy_with_git.ps1 -Message "Added admin_audit_logs table"

# 2. Run migration on production
ssh root@103.214.112.58
cd /var/www/tmm
mysql -u root -p cms_authentication_authorization < /path/to/migration.sql
```

### Scenario 4: New File Uploads (Images, Documents)

**Use scp/rsync, NOT Git:**
```powershell
# Upload new images
scp -r webroot/img/uploads/new_images/* root@103.214.112.58:/var/www/tmm/webroot/img/uploads/new_images/

# Set permissions
ssh root@103.214.112.58 "chown -R www-data:www-data /var/www/tmm/webroot/img/uploads/ && chmod -R 775 /var/www/tmm/webroot/img/uploads/"
```

### Scenario 5: Rollback to Previous Version

**Use Git to rollback:**
```powershell
# View commit history
git log --oneline -10

# Rollback to specific commit locally
git reset --hard <commit-hash>

# Force push to GitHub
git push origin main --force

# Pull on production
ssh root@103.214.112.58 "cd /var/www/tmm && git fetch origin && git reset --hard origin/main && rm -rf tmp/cache/* && systemctl restart php7.4-fpm"
```

## Troubleshooting

### Issue 1: Git Push Fails (Authentication)

**Error:** `fatal: Authentication failed`

**Solution:**
```powershell
# Configure Git credentials
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# Use personal access token (not password)
# GitHub → Settings → Developer settings → Personal access tokens
```

### Issue 2: Git Pull Fails on Server (Merge Conflict)

**Error:** `error: Your local changes to the following files would be overwritten by merge`

**Solution:**
```bash
ssh root@103.214.112.58
cd /var/www/tmm

# Stash local changes
git stash

# Pull latest
git pull origin main

# Re-apply stashed changes (if needed)
git stash pop
```

### Issue 3: Production Server Out of Sync

**Symptom:** Production shows old code after deployment

**Solution:**
```bash
ssh root@103.214.112.58
cd /var/www/tmm

# Force reset to GitHub version
git fetch origin
git reset --hard origin/main

# Clear cache completely
rm -rf tmp/cache/*
rm -rf tmp/sessions/*

# Restart services
systemctl restart php7.4-fpm
systemctl reload nginx
```

### Issue 4: .gitignore Not Working

**Symptom:** Files are tracked that should be ignored

**Solution:**
```powershell
# Remove files from Git index but keep locally
git rm --cached logs/*.log
git rm --cached tmp/cache/* -r
git rm --cached webroot/img/uploads/* -r

# Commit the removal
git commit -m "Updated .gitignore - removed tracked files"
git push origin main
```

## Best Practices

### ✅ DO

1. **Always use descriptive commit messages:**
   ```powershell
   .\deploy_with_git.ps1 -Message "Fixed JavaScript syntax in Candidates/wizard.ctp - added 9 missing braces"
   ```

2. **Test locally before deploying:**
   - Clear local cache: `rm -rf tmp/cache/*`
   - Test in browser: `http://localhost/tmm`
   - Check PHP syntax: `php -l src/Controller/YourController.php`

3. **Deploy frequently with small changes:**
   - Easier to identify issues
   - Faster rollback if needed
   - Clear audit trail

4. **Check error logs after deployment:**
   ```bash
   ssh root@103.214.112.58 "tail -50 /var/www/tmm/logs/error.log"
   ```

5. **Keep commits atomic:**
   - One feature per commit
   - One bug fix per commit
   - Easier to revert specific changes

### ❌ DON'T

1. **Don't commit sensitive data:**
   - ❌ Database passwords
   - ❌ API keys
   - ❌ Email credentials
   - ✅ Use `config/app_local.php` (excluded from Git)

2. **Don't commit large files:**
   - ❌ Database dumps (use `export_all_databases.ps1`)
   - ❌ Uploaded images (use rsync/scp)
   - ❌ Vendor packages (use Composer)

3. **Don't force push without backup:**
   - `git push --force` can lose data
   - Always create backup branch first

4. **Don't edit directly on production:**
   - Always edit locally, then deploy via Git
   - Keeps development history consistent

5. **Don't skip commit messages:**
   - Generic messages like "update" are useless
   - Be specific about what changed and why

## Quick Reference Commands

### Check Deployment Status
```powershell
# Local
git status
git log -5 --oneline

# Production
ssh root@103.214.112.58 "cd /var/www/tmm && git log -5 --oneline && git status"
```

### Compare Local vs Production
```powershell
# Show difference between local and production
ssh root@103.214.112.58 "cd /var/www/tmm && git rev-parse HEAD" | Set-Variable -Name ProductionCommit
git rev-parse HEAD | Set-Variable -Name LocalCommit
if ($LocalCommit -eq $ProductionCommit) {
    Write-Host "✅ Local and production are in sync"
} else {
    Write-Host "⚠️  Local and production differ"
    git log --oneline $ProductionCommit..$LocalCommit
}
```

### Quick Deploy (One Command)
```powershell
# Add all, commit, push, deploy
git add -A; git commit -m "Quick update"; git push origin main; ssh root@103.214.112.58 "cd /var/www/tmm && git pull origin main && rm -rf tmp/cache/* && systemctl restart php7.4-fpm"
```

### View Deployment History
```powershell
# Show last 10 deployments
git log --oneline --graph --decorate -10
```

## Support

### Documentation Files
- `deploy_with_git.ps1` - Main deployment script
- `setup_git_on_server.ps1` - One-time server setup
- `.gitignore` - Files excluded from Git

### Useful Links
- GitHub Repository: https://github.com/sriechoentjoro/tmm-project
- Production Server: http://103.214.112.58/tmm/
- CakePHP Documentation: https://book.cakephp.org/3/

### Getting Help

**Check deployment logs:**
```bash
ssh root@103.214.112.58 "tail -100 /var/www/tmm/logs/error.log"
```

**Check service status:**
```bash
ssh root@103.214.112.58 "systemctl status php7.4-fpm && systemctl status nginx"
```

**Test application:**
```powershell
Invoke-WebRequest -Uri "http://103.214.112.58/tmm/" -UseBasicParsing
```

---

**Last Updated:** December 1, 2025
**Version:** 1.0
**Author:** TMM Development Team
