<?php
/**
 * LPK Registration Process Flow
 * 
 * Visual documentation of the 3-step LPK registration workflow
 * and database relationships
 */

?>

<!-- Shared process-flow styling and the mermaid loader -->
<?= $this->element('process_flow_assets') ?>

<!-- Process Overview -->
<div class="flow-section">
    <h2><i class="fas fa-clipboard-list"></i> <?= __('Registration Process Overview') ?></h2>
    
    <div class="alert-info-custom">
        <i class="fas fa-info-circle"></i>
        <?= __('<strong>LPK Registration</strong> is a 3-step wizard process to register new Vocational Training Institutions (Lembaga Pelatihan Kerja) into the system with email verification and secure account creation.') ?>
    </div>
    
    <div class="workflow-steps">
        <div class="workflow-step">
            <span class="step-number">1</span>
            <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                <div class="step-title">
                    Admin Creates LPK Record
                    <span class="database-indicator">vocational_training_institutions</span>
                </div>
                <div class="step-description">
                    <strong>Who:</strong> <?= __('System Administrator') ?><br>
                    <strong>Action:</strong> <?= __('Fill out LPK registration form with institution details') ?><br>
                    <strong>Database:</strong> <?= __('Creates record in <code>vocational_training_institutions</code> table with status = <code>pending_verification</code>') ?><br>
                    <strong>Trigger:</strong> <?= __('Generates verification token in <code>email_verification_tokens</code> table') ?><br>
                    <strong>Email:</strong> <?= __('Sends verification email to LPK email address') ?>
                </div>
            </div>
        </div>
        
        <div class="workflow-step">
            <span class="step-number">2</span>
            <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                <div class="step-title">
                    LPK Verifies Email
                    <span class="database-indicator">email_verification_tokens</span>
                </div>
                <div class="step-description">
                    <strong>Who:</strong> <?= __('LPK Staff (via email link)') ?><br>
                    <strong>Action:</strong> <?= __('Click verification link in email') ?><br>
                    <strong>Database:</strong> <?= __('Marks token as <code>is_used = 1</code> in <code>email_verification_tokens</code>') ?><br>
                    <strong>Update:</strong> <?= __('Changes status to <code>email_verified</code> in <code>vocational_training_institutions</code>') ?><br>
                    <strong>Redirect:</strong> <?= __('To password setup page') ?>
                </div>
            </div>
        </div>
        
        <div class="workflow-step">
            <span class="step-number">3</span>
            <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                <div class="step-title">
                    LPK Sets Password & Account Activated
                    <span class="database-indicator">users</span>
                </div>
                <div class="step-description">
                    <strong>Who:</strong> <?= __('LPK Staff') ?><br>
                    <strong>Action:</strong> <?= __('Create password for login') ?><br>
                    <strong>Database:</strong> <?= __('Creates user account in <code>users</code> table with role = <code>lpk</code>') ?><br>
                    <strong>Update:</strong> <?= __('Changes status to <code>active</code> in <code>vocational_training_institutions</code>') ?><br>
                    <strong>Email:</strong> <?= __('Sends welcome email with login instructions') ?><br>
                    <strong>Result:</strong> <?= __('LPK can now login to the system') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Flow Diagram -->
<div class="flow-section">
    <h2><i class="fas fa-sitemap"></i> <?= __('Visual Process Flow') ?></h2>
    
    <div class="mermaid">
graph TD
    A["👤 <?= __('Admin Opens<br/>LPK Registration Form') ?>"] --> B{"<?= __('Fill Form<br/>Valid?') ?>"}
    B -->|"<?= __('No') ?>"| A
    B -->|"<?= __('Yes') ?>"| C["💾 <?= __('Save to') ?><br/>vocational_training_institutions<br/>status=pending_verification"]
    C --> D["🔑 <?= __('Generate Token') ?><br/>email_verification_tokens<br/><?= __('64 random chars') ?>"]
    D --> E["📧 <?= __('Send Verification Email<br/>to LPK Email') ?>"]
    E --> F["📬 <?= __('LPK Receives Email') ?>"]
    F --> G{"<?= __('LPK Clicks<br/>Verification Link?') ?>"}
    G -->|"<?= __('No') ?>"| H["⏳ <?= __('Wait<br/>Max 24 hours') ?>"]
    H --> I{"<?= __('Token<br/>Expired?') ?>"}
    I -->|"<?= __('Yes') ?>"| J["❌ <?= __('Token Invalid<br/>Admin Must Resend') ?>"]
    I -->|"<?= __('No') ?>"| G
    G -->|"<?= __('Yes') ?>"| K["✅ <?= __('Validate Token') ?><br/><?= __('Mark') ?> is_used=1"]
    K --> L["🔄 <?= __('Update Status') ?><br/>email_verified"]
    L --> M["🔐 <?= __('Redirect to<br/>Set Password Page') ?>"]
    M --> N{"<?= __('Password<br/>Valid?') ?>"}
    N -->|"<?= __('No') ?>"| M
    N -->|"<?= __('Yes') ?>"| O["👤 <?= __('Create User Account') ?><br/>users<br/>role=lpk"]
    O --> P["🎉 <?= __('Update Status') ?><br/>active"]
    P --> Q["📧 <?= __('Send Welcome Email') ?>"]
    Q --> R["✅ <?= __('LPK Can Login') ?>"]

    style A fill:#e3f2fd
    style C fill:#fff9c4
    style D fill:#fff9c4
    style K fill:#c8e6c9
    style O fill:#fff9c4
    style P fill:#c8e6c9
    style R fill:#c8e6c9
    style J fill:#ffcdd2
    </div>
</div>

<!-- Database Relationships -->
<div class="flow-section">
    <h2><i class="fas fa-database"></i> <?= __('Database Relationships') ?></h2>
    
    <div class="mermaid">
erDiagram
    VOCATIONAL_TRAINING_INSTITUTIONS ||--o{ EMAIL_VERIFICATION_TOKENS : "has_many"
    VOCATIONAL_TRAINING_INSTITUTIONS ||--o| USERS : "creates"
    VOCATIONAL_TRAINING_INSTITUTIONS }o--|| MASTER_PROPINSIS : "belongs_to"
    VOCATIONAL_TRAINING_INSTITUTIONS }o--|| MASTER_KABUPATENS : "belongs_to"
    VOCATIONAL_TRAINING_INSTITUTIONS }o--|| MASTER_KECAMATANS : "belongs_to"
    VOCATIONAL_TRAINING_INSTITUTIONS }o--|| MASTER_KELURAHANS : "belongs_to"
    USERS }o--|| STAKEHOLDER_GROUPS : "belongs_to"
    
    VOCATIONAL_TRAINING_INSTITUTIONS {
        int id PK
        string name "<?= __('Required') ?>"
        string email "<?= __('Required, Unique') ?>"
        string director_name "<?= __('Required') ?>"
        string status "pending/email_verified/active"
        int master_propinsi_id FK
        int master_kabupaten_id FK
        datetime created
        datetime modified
    }
    
    EMAIL_VERIFICATION_TOKENS {
        int id PK
        string user_email FK
        string token "<?= __('64 chars, Unique') ?>"
        string token_type "email_verification"
        boolean is_used "<?= __('Default: 0') ?>"
        datetime expires_at "<?= __('Now + 24 hours') ?>"
        datetime created
    }
    
    USERS {
        int id PK
        string email FK
        string password "<?= __('Bcrypt hashed') ?>"
        string fullname
        int stakeholder_group_id FK
        boolean is_active "<?= __('Default: 1') ?>"
        datetime created
    }
    
    MASTER_PROPINSIS {
        int id PK
        string title
    }
    
    MASTER_KABUPATENS {
        int id PK
        string title
        int propinsi_id FK
    }
    </div>
</div>

<!-- Table Details: vocational_training_institutions -->
<div class="flow-section">
    <h2><i class="fas fa-table"></i> Table: vocational_training_institutions</h2>
    
    <div class="table-info">
        <h4>Database: <code>cms_lpk_candidates</code></h4>
        <p><strong>Purpose:</strong> <?= __('Master data for all Vocational Training Institutions (LPK)') ?></p>
        
        <h3 style="margin-top: 20px;">Key Fields:</h3>
        <div class="field-list">
            <div class="field-item required">
                <span class="field-name">id</span>
                <span class="field-type">INT</span>
                <span class="badge-custom badge-required">PK</span>
            </div>
            <div class="field-item required">
                <span class="field-name">name</span>
                <span class="field-type">VARCHAR(256)</span>
                <span class="badge-custom badge-required">Required</span>
            </div>
            <div class="field-item required">
                <span class="field-name">email</span>
                <span class="field-type">VARCHAR(256)</span>
                <span class="badge-custom badge-required">Required</span>
            </div>
            <div class="field-item required">
                <span class="field-name">director_name</span>
                <span class="field-type">VARCHAR(256)</span>
                <span class="badge-custom badge-required">Required</span>
            </div>
            <div class="field-item required">
                <span class="field-name">status</span>
                <span class="field-type">ENUM</span>
                <span class="badge-custom badge-required">Required</span>
            </div>
            <div class="field-item">
                <span class="field-name">address</span>
                <span class="field-type">TEXT</span>
                <span class="badge-custom badge-optional">Optional</span>
            </div>
            <div class="field-item foreign-key">
                <span class="field-name">master_propinsi_id</span>
                <span class="field-type">INT</span>
                <span class="badge-custom badge-fk">FK</span>
            </div>
            <div class="field-item foreign-key">
                <span class="field-name">master_kabupaten_id</span>
                <span class="field-type">INT</span>
                <span class="badge-custom badge-fk">FK</span>
            </div>
            <div class="field-item foreign-key">
                <span class="field-name">master_kecamatan_id</span>
                <span class="field-type">INT</span>
                <span class="badge-custom badge-fk">FK</span>
            </div>
            <div class="field-item foreign-key">
                <span class="field-name">master_kelurahan_id</span>
                <span class="field-type">INT</span>
                <span class="badge-custom badge-fk">FK</span>
            </div>
        </div>
        
        <h3>Associations (BelongsTo):</h3>
        <ul class="association-list">
            <li>
                <i class="fas fa-link"></i>
                <strong>MasterPropinsis</strong> 
                <code>master_propinsi_id</code> → <code>master_propinsis.id</code>
                <span class="database-indicator">cms_masters</span>
            </li>
            <li>
                <i class="fas fa-link"></i>
                <strong>MasterKabupatens</strong> 
                <code>master_kabupaten_id</code> → <code>master_kabupatens.id</code>
                <span class="database-indicator">cms_masters</span>
            </li>
            <li>
                <i class="fas fa-link"></i>
                <strong>MasterKecamatans</strong> 
                <code>master_kecamatan_id</code> → <code>master_kecamatans.id</code>
                <span class="database-indicator">cms_masters</span>
            </li>
            <li>
                <i class="fas fa-link"></i>
                <strong>MasterKelurahans</strong> 
                <code>master_kelurahan_id</code> → <code>master_kelurahans.id</code>
                <span class="database-indicator">cms_masters</span>
            </li>
        </ul>
        
        <h3><?= __('Status Values:') ?></h3>
        <div class="field-list">
            <div class="field-item" style="border-left-color: #fbbf24;">
                <span class="field-name">pending_verification</span>
                <span class="field-type"><?= __('Initial state after admin creates record') ?></span>
            </div>
            <div class="field-item" style="border-left-color: #60a5fa;">
                <span class="field-name">email_verified</span>
                <span class="field-type"><?= __('After LPK clicks verification link') ?></span>
            </div>
            <div class="field-item" style="border-left-color: #34d399;">
                <span class="field-name">active</span>
                <span class="field-type"><?= __('After password setup - can login') ?></span>
            </div>
            <div class="field-item" style="border-left-color: #f87171;">
                <span class="field-name">inactive</span>
                <span class="field-type"><?= __('Admin disabled the account') ?></span>
            </div>
            <div class="field-item" style="border-left-color: #ef4444;">
                <span class="field-name">suspended</span>
                <span class="field-type"><?= __('Temporarily blocked') ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Table Details: email_verification_tokens -->
<div class="flow-section">
    <h2><i class="fas fa-table"></i> Table: email_verification_tokens</h2>
    
    <div class="table-info">
        <h4>Database: <code>cms_authentication_authorization</code></h4>
        <p><strong>Purpose:</strong> <?= __('Store one-time verification tokens for email validation') ?></p>
        
        <h3 style="margin-top: 20px;">Key Fields:</h3>
        <div class="field-list">
            <div class="field-item required">
                <span class="field-name">id</span>
                <span class="field-type">INT</span>
                <span class="badge-custom badge-required">PK</span>
            </div>
            <div class="field-item required">
                <span class="field-name">user_email</span>
                <span class="field-type">VARCHAR(255)</span>
                <span class="badge-custom badge-required">Required</span>
            </div>
            <div class="field-item required">
                <span class="field-name">token</span>
                <span class="field-type">VARCHAR(255)</span>
                <span class="badge-custom badge-required">Unique</span>
            </div>
            <div class="field-item required">
                <span class="field-name">token_type</span>
                <span class="field-type">VARCHAR(50)</span>
                <span class="badge-custom badge-required">Required</span>
            </div>
            <div class="field-item required">
                <span class="field-name">is_used</span>
                <span class="field-type">TINYINT(1)</span>
                <span class="badge-custom badge-required">Default: 0</span>
            </div>
            <div class="field-item required">
                <span class="field-name">expires_at</span>
                <span class="field-type">DATETIME</span>
                <span class="badge-custom badge-required">Required</span>
            </div>
        </div>
        
        <h3><?= __('Token Lifecycle:') ?></h3>
        <div class="workflow-steps">
            <div class="workflow-step" style="border-left-color: #34d399;">
                <span class="step-number" style="background: #34d399;">✓</span>
                <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                    <div class="step-title">Generated</div>
                    <div class="step-description">
                        Token created with 64 random characters<br>
                        <code>is_used = 0</code>, <code>expires_at = NOW() + 24 hours</code>
                    </div>
                </div>
            </div>
            
            <div class="workflow-step" style="border-left-color: #60a5fa;">
                <span class="step-number" style="background: #60a5fa;">→</span>
                <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                    <div class="step-title">Used (Valid)</div>
                    <div class="step-description">
                        User clicks link within 24 hours<br>
                        <code>is_used = 1</code>, <code>used_at = NOW()</code>
                    </div>
                </div>
            </div>
            
            <div class="workflow-step" style="border-left-color: #f87171;">
                <span class="step-number" style="background: #f87171;">✗</span>
                <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                    <div class="step-title">Expired</div>
                    <div class="step-description">
                        24 hours passed without use<br>
                        <code>NOW() > expires_at</code> - Token rejected, new token needed
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Details: users -->
<div class="flow-section">
    <h2><i class="fas fa-table"></i> Table: users</h2>
    
    <div class="table-info">
        <h4>Database: <code>cms_authentication_authorization</code></h4>
        <p><strong>Purpose:</strong> <?= __('User accounts for authentication and authorization') ?></p>
        
        <h3 style="margin-top: 20px;">Key Fields:</h3>
        <div class="field-list">
            <div class="field-item required">
                <span class="field-name">id</span>
                <span class="field-type">INT</span>
                <span class="badge-custom badge-required">PK</span>
            </div>
            <div class="field-item required">
                <span class="field-name">email</span>
                <span class="field-type">VARCHAR(255)</span>
                <span class="badge-custom badge-required">Unique</span>
            </div>
            <div class="field-item required">
                <span class="field-name">password</span>
                <span class="field-type">VARCHAR(255)</span>
                <span class="badge-custom badge-required">Bcrypt</span>
            </div>
            <div class="field-item required">
                <span class="field-name">fullname</span>
                <span class="field-type">VARCHAR(255)</span>
                <span class="badge-custom badge-required">Required</span>
            </div>
            <div class="field-item foreign-key">
                <span class="field-name">stakeholder_group_id</span>
                <span class="field-type">INT</span>
                <span class="badge-custom badge-fk">FK</span>
            </div>
            <div class="field-item required">
                <span class="field-name">is_active</span>
                <span class="field-type">TINYINT(1)</span>
                <span class="badge-custom badge-required">Default: 1</span>
            </div>
        </div>
        
        <h3>Associations:</h3>
        <ul class="association-list">
            <li>
                <i class="fas fa-link"></i>
                <strong>StakeholderGroups</strong> 
                <code>stakeholder_group_id</code> → <code>stakeholder_groups.id</code>
                <span class="database-indicator">cms_authentication_authorization</span>
                <br><small><?= __('Determines user role and permissions (admin, lpk, candidate, etc.)') ?></small>
            </li>
        </ul>
    </div>
</div>

<!-- Important Notes -->
<div class="flow-section">
    <h2><i class="fas fa-exclamation-triangle"></i> <?= __('Important Guidelines') ?></h2>
    
    <div class="table-info" style="border-left-color: #f59e0b;">
        <h4>⚠️ Data Entry Rules:</h4>
        <ul style="line-height: 2;">
            <li><?= __('<strong>Email Uniqueness:</strong> Each LPK must have unique email address') ?></li>
            <li><?= __('<strong>Required Fields:</strong> Name, Email, Director Name must be filled') ?></li>
            <li><?= __('<strong>Geographic Cascade:</strong> Province → City → District → Village (optional but recommended)') ?></li>
            <li><?= __('<strong>Token Expiry:</strong> Verification links expire after 24 hours') ?></li>
            <li><?= __('<strong>One-Time Use:</strong> Each token can only be used once') ?></li>
            <li><?= __('<strong>Status Flow:</strong> pending_verification → email_verified → active (cannot skip steps)') ?></li>
        </ul>
    </div>
    
    <div class="table-info" style="border-left-color: #10b981;">
        <h4>✅ Security Features:</h4>
        <ul style="line-height: 2;">
            <li><?= __('<strong>Password Encryption:</strong> Bcrypt hashing with salt') ?></li>
            <li><?= __('<strong>Token Security:</strong> 64-character random tokens (URL-safe)') ?></li>
            <li><?= __('<strong>Email Verification:</strong> Ensures valid email ownership') ?></li>
            <li><?= __('<strong>CSRF Protection:</strong> All forms protected against cross-site attacks') ?></li>
            <li><?= __('<strong>Session Management:</strong> Secure session handling with timeout') ?></li>
        </ul>
    </div>
</div>

<!-- Cross-Database Relationships -->
<div class="flow-section">
    <h2><i class="fas fa-network-wired"></i> <?= __('Cross-Database Architecture') ?></h2>
    
    <div class="alert-info-custom">
        <i class="fas fa-info-circle"></i>
        This system uses <strong>multi-database architecture</strong> with tables distributed across 3 databases. All associations use <code>'strategy' => 'select'</code> for cross-database compatibility.
    </div>
    
    <div class="mermaid">
graph LR
    subgraph "cms_lpk_candidates"
        A[vocational_training_institutions]
    end
    
    subgraph "cms_authentication_authorization"
        B[users]
        C[email_verification_tokens]
        D[stakeholder_groups]
    end
    
    subgraph "cms_masters"
        E[master_propinsis]
        F[master_kabupatens]
        G[master_kecamatans]
        H[master_kelurahans]
    end
    
    A -.->|"<?= __('email matches') ?>"| B
    A -->|"<?= __('creates') ?>"| C
    B --> D
    A --> E
    A --> F
    A --> G
    A --> H
    
    style A fill:#fff9c4
    style B fill:#c8e6c9
    style C fill:#c8e6c9
    style E fill:#e1bee7
    style F fill:#e1bee7
    </div>
</div>
