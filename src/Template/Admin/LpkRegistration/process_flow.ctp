<?php
/**
 * LPK Registration Process Flow
 * 
 * Visual documentation of the 3-step LPK registration workflow
 * and database relationships
 */

$this->layout = 'process_flow';
?>

<!-- Process Overview -->
<div class="flow-section">
    <h2><i class="fas fa-clipboard-list"></i> Registration Process Overview</h2>
    
    <div class="alert-info-custom">
        <i class="fas fa-info-circle"></i>
        <strong>LPK Registration</strong> is a 3-step wizard process to register new Vocational Training Institutions (Lembaga Pelatihan Kerja) into the system with email verification and secure account creation.
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
                    <strong>Who:</strong> System Administrator<br>
                    <strong>Action:</strong> Fill out LPK registration form with institution details<br>
                    <strong>Database:</strong> Creates record in <code>vocational_training_institutions</code> table with status = <code>pending_verification</code><br>
                    <strong>Trigger:</strong> Generates verification token in <code>email_verification_tokens</code> table<br>
                    <strong>Email:</strong> Sends verification email to LPK email address
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
                    <strong>Who:</strong> LPK Staff (via email link)<br>
                    <strong>Action:</strong> Click verification link in email<br>
                    <strong>Database:</strong> Marks token as <code>is_used = 1</code> in <code>email_verification_tokens</code><br>
                    <strong>Update:</strong> Changes status to <code>email_verified</code> in <code>vocational_training_institutions</code><br>
                    <strong>Redirect:</strong> To password setup page
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
                    <strong>Who:</strong> LPK Staff<br>
                    <strong>Action:</strong> Create password for login<br>
                    <strong>Database:</strong> Creates user account in <code>users</code> table with role = <code>lpk</code><br>
                    <strong>Update:</strong> Changes status to <code>active</code> in <code>vocational_training_institutions</code><br>
                    <strong>Email:</strong> Sends welcome email with login instructions<br>
                    <strong>Result:</strong> LPK can now login to the system
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Flow Diagram -->
<div class="flow-section">
    <h2><i class="fas fa-sitemap"></i> Visual Process Flow</h2>
    
    <div class="mermaid">
graph TD
    A[👤 Admin Opens<br/>LPK Registration Form] --> B{Fill Form<br/>Valid?}
    B -->|No| A
    B -->|Yes| C[💾 Save to<br/>vocational_training_institutions<br/>status=pending_verification]
    C --> D[🔑 Generate Token<br/>email_verification_tokens<br/>64 random chars]
    D --> E[📧 Send Verification Email<br/>to LPK Email]
    E --> F[📬 LPK Receives Email]
    F --> G{LPK Clicks<br/>Verification Link?}
    G -->|No| H[⏳ Wait<br/>Max 24 hours]
    H --> I{Token<br/>Expired?}
    I -->|Yes| J[❌ Token Invalid<br/>Admin Must Resend]
    I -->|No| G
    G -->|Yes| K[✅ Validate Token<br/>Mark is_used=1]
    K --> L[🔄 Update Status<br/>email_verified]
    L --> M[🔐 Redirect to<br/>Set Password Page]
    M --> N{Password<br/>Valid?}
    N -->|No| M
    N -->|Yes| O[👤 Create User Account<br/>users table<br/>role=lpk]
    O --> P[🎉 Update Status<br/>active]
    P --> Q[📧 Send Welcome Email]
    Q --> R[✅ LPK Can Login]
    
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
    <h2><i class="fas fa-database"></i> Database Relationships</h2>
    
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
        string name "Required"
        string email "Required, Unique"
        string director_name "Required"
        string status "pending/email_verified/active"
        int master_propinsi_id FK
        int master_kabupaten_id FK
        datetime created
        datetime modified
    }
    
    EMAIL_VERIFICATION_TOKENS {
        int id PK
        string user_email FK
        string token "64 chars, Unique"
        string token_type "email_verification"
        boolean is_used "Default: 0"
        datetime expires_at "Now + 24 hours"
        datetime created
    }
    
    USERS {
        int id PK
        string email FK
        string password "Bcrypt hashed"
        string fullname
        int stakeholder_group_id FK
        boolean is_active "Default: 1"
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
        <p><strong>Purpose:</strong> Master data for all Vocational Training Institutions (LPK)</p>
        
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
        
        <h3>Status Values:</h3>
        <div class="field-list">
            <div class="field-item" style="border-left-color: #fbbf24;">
                <span class="field-name">pending_verification</span>
                <span class="field-type">Initial state after admin creates record</span>
            </div>
            <div class="field-item" style="border-left-color: #60a5fa;">
                <span class="field-name">email_verified</span>
                <span class="field-type">After LPK clicks verification link</span>
            </div>
            <div class="field-item" style="border-left-color: #34d399;">
                <span class="field-name">active</span>
                <span class="field-type">After password setup - can login</span>
            </div>
            <div class="field-item" style="border-left-color: #f87171;">
                <span class="field-name">inactive</span>
                <span class="field-type">Admin disabled the account</span>
            </div>
            <div class="field-item" style="border-left-color: #ef4444;">
                <span class="field-name">suspended</span>
                <span class="field-type">Temporarily blocked</span>
            </div>
        </div>
    </div>
</div>

<!-- Table Details: email_verification_tokens -->
<div class="flow-section">
    <h2><i class="fas fa-table"></i> Table: email_verification_tokens</h2>
    
    <div class="table-info">
        <h4>Database: <code>cms_authentication_authorization</code></h4>
        <p><strong>Purpose:</strong> Store one-time verification tokens for email validation</p>
        
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
        
        <h3>Token Lifecycle:</h3>
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
        <p><strong>Purpose:</strong> User accounts for authentication and authorization</p>
        
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
                <br><small>Determines user role and permissions (admin, lpk, candidate, etc.)</small>
            </li>
        </ul>
    </div>
</div>

<!-- Important Notes -->
<div class="flow-section">
    <h2><i class="fas fa-exclamation-triangle"></i> Important Guidelines</h2>
    
    <div class="table-info" style="border-left-color: #f59e0b;">
        <h4>⚠️ Data Entry Rules:</h4>
        <ul style="line-height: 2;">
            <li><strong>Email Uniqueness:</strong> Each LPK must have unique email address</li>
            <li><strong>Required Fields:</strong> Name, Email, Director Name must be filled</li>
            <li><strong>Geographic Cascade:</strong> Province → City → District → Village (optional but recommended)</li>
            <li><strong>Token Expiry:</strong> Verification links expire after 24 hours</li>
            <li><strong>One-Time Use:</strong> Each token can only be used once</li>
            <li><strong>Status Flow:</strong> pending_verification → email_verified → active (cannot skip steps)</li>
        </ul>
    </div>
    
    <div class="table-info" style="border-left-color: #10b981;">
        <h4>✅ Security Features:</h4>
        <ul style="line-height: 2;">
            <li><strong>Password Encryption:</strong> Bcrypt hashing with salt</li>
            <li><strong>Token Security:</strong> 64-character random tokens (URL-safe)</li>
            <li><strong>Email Verification:</strong> Ensures valid email ownership</li>
            <li><strong>CSRF Protection:</strong> All forms protected against cross-site attacks</li>
            <li><strong>Session Management:</strong> Secure session handling with timeout</li>
        </ul>
    </div>
</div>

<!-- Cross-Database Relationships -->
<div class="flow-section">
    <h2><i class="fas fa-network-wired"></i> Cross-Database Architecture</h2>
    
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
    
    A -.->|email matches| B
    A -->|creates| C
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
