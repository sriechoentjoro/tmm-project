<div class="users form login-container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm mt-5 mx-auto" style="max-width: 1000px;">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-sign-in-alt"></i> Login System
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Login Form -->
                        <div class="col-md-6 border-end">
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-user-lock"></i> Login Form
                            </h5>
                            <?= $this->Flash->render() ?>
                            <?= $this->Form->create(null, [
                                'autocomplete' => 'off'
                            ]) ?>
                            <div class="form-group mb-3">
                                <?= $this->Form->control('username', [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Username',
                                    'label' => 'Username',
                                    'required' => true,
                                    'autofocus' => true,
                                    'autocomplete' => 'off',
                                    'id' => 'username-field'
                                ]) ?>
                            </div>
                            <div class="form-group mb-4">
                                <?= $this->Form->control('password', [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Password',
                                    'label' => 'Password',
                                    'required' => true,
                                    'autocomplete' => 'new-password',
                                    'id' => 'password-field'
                                ]) ?>
                            </div>
                            <div class="d-grid gap-2">
                                <?= $this->Form->button(__('Login'), [
                                    'class' => 'btn btn-primary btn-block btn-lg'
                                ]) ?>
                            </div>
                            <?= $this->Form->end() ?>
                        </div>

                        <!-- Test Credentials -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-success">
                                <i class="fas fa-users-cog"></i> Test Credentials
                            </h5>
                            <div class="alert alert-info">
                                <small><i class="fas fa-info-circle"></i> Click username to auto-fill login form</small>
                            </div>
                            <div class="credentials-list" style="max-height: 400px; overflow-y: auto;">
                                <!-- Administrator -->
                                <div class="credential-item mb-3 p-3 border rounded bg-light" onclick="fillLogin('admin', 'admin123')">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-danger">
                                                <i class="fas fa-user-shield"></i> Administrator
                                            </h6>
                                            <div><small class="text-muted">Username:</small> <code>admin</code></div>
                                            <div><small class="text-muted">Password:</small> <code>admin123</code></div>
                                            <small class="text-muted d-block mt-1">Full Access</small>
                                        </div>
                                        <span class="badge bg-danger">ADMIN</span>
                                    </div>
                                </div>

                                <!-- Management -->
                                <div class="credential-item mb-3 p-3 border rounded bg-light" onclick="fillLogin('director', 'director123')">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-primary">
                                                <i class="fas fa-user-tie"></i> Director
                                            </h6>
                                            <div><small class="text-muted">Username:</small> <code>director</code></div>
                                            <div><small class="text-muted">Password:</small> <code>director123</code></div>
                                            <small class="text-muted d-block mt-1">Management Level</small>
                                        </div>
                                        <span class="badge bg-primary">MGT</span>
                                    </div>
                                </div>

                                <div class="credential-item mb-3 p-3 border rounded bg-light" onclick="fillLogin('manager', 'manager123')">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-primary">
                                                <i class="fas fa-user-tie"></i> General Manager
                                            </h6>
                                            <div><small class="text-muted">Username:</small> <code>manager</code></div>
                                            <div><small class="text-muted">Password:</small> <code>manager123</code></div>
                                            <small class="text-muted d-block mt-1">Management Level</small>
                                        </div>
                                        <span class="badge bg-primary">MGT</span>
                                    </div>
                                </div>

                                <!-- TMM Recruitment -->
                                <div class="credential-item mb-3 p-3 border rounded bg-light" onclick="fillLogin('recruitment1', 'recruit123')">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-success">
                                                <i class="fas fa-user-plus"></i> Recruitment Officer 1
                                            </h6>
                                            <div><small class="text-muted">Username:</small> <code>recruitment1</code></div>
                                            <div><small class="text-muted">Password:</small> <code>recruit123</code></div>
                                            <small class="text-muted d-block mt-1">Recruitment Department</small>
                                        </div>
                                        <span class="badge bg-success">RECRUIT</span>
                                    </div>
                                </div>

                                <!-- TMM Training -->
                                <div class="credential-item mb-3 p-3 border rounded bg-light" onclick="fillLogin('training1', 'training123')">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-warning">
                                                <i class="fas fa-chalkboard-teacher"></i> Training Coordinator 1
                                            </h6>
                                            <div><small class="text-muted">Username:</small> <code>training1</code></div>
                                            <div><small class="text-muted">Password:</small> <code>training123</code></div>
                                            <small class="text-muted d-block mt-1">Training Department</small>
                                        </div>
                                        <span class="badge bg-warning text-dark">TRAINING</span>
                                    </div>
                                </div>

                                <!-- TMM Documentation -->
                                <div class="credential-item mb-3 p-3 border rounded bg-light" onclick="fillLogin('documentation1', 'doc123')">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-info">
                                                <i class="fas fa-file-alt"></i> Documentation Officer 1
                                            </h6>
                                            <div><small class="text-muted">Username:</small> <code>documentation1</code></div>
                                            <div><small class="text-muted">Password:</small> <code>doc123</code></div>
                                            <small class="text-muted d-block mt-1">Documentation Department</small>
                                        </div>
                                        <span class="badge bg-info">DOC</span>
                                    </div>
                                </div>

                                <!-- LPK Penyangga -->
                                <div class="credential-item mb-3 p-3 border rounded bg-light" onclick="fillLogin('lpk_semarang', 'lpk123')">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-secondary">
                                                <i class="fas fa-school"></i> LPK Semarang
                                            </h6>
                                            <div><small class="text-muted">Username:</small> <code>lpk_semarang</code></div>
                                            <div><small class="text-muted">Password:</small> <code>lpk123</code></div>
                                            <small class="text-muted d-block mt-1">Vocational Training Institution</small>
                                        </div>
                                        <span class="badge bg-secondary">LPK</span>
                                    </div>
                                </div>

                                <div class="credential-item mb-3 p-3 border rounded bg-light" onclick="fillLogin('lpk_bekasi', 'lpk123')">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-secondary">
                                                <i class="fas fa-school"></i> LPK Bekasi
                                            </h6>
                                            <div><small class="text-muted">Username:</small> <code>lpk_bekasi</code></div>
                                            <div><small class="text-muted">Password:</small> <code>lpk123</code></div>
                                            <small class="text-muted d-block mt-1">Vocational Training Institution</small>
                                        </div>
                                        <span class="badge bg-secondary">LPK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>&copy; <?= date('Y') ?> TMM System - Test Environment</small>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .login-container {
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .login-container .card {
        width: 100%;
    }
    
    .login-container .form-control {
        padding: 12px;
        font-size: 16px;
    }
    
    .login-container .btn-lg {
        padding: 12px;
        font-size: 18px;
    }

    .credential-item {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .credential-item:hover {
        background-color: #e3f2fd !important;
        border-color: #2196F3 !important;
        box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
        transform: translateY(-2px);
    }

    .credential-item code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 13px;
        color: #d63384;
    }

    .credentials-list::-webkit-scrollbar {
        width: 8px;
    }

    .credentials-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .credentials-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .credentials-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Prevent browser password autofill styling */
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px white inset !important;
        -webkit-text-fill-color: #000 !important;
    }
</style>

<script>
    // Function to fill login credentials
    function fillLogin(username, password) {
        document.getElementById('username-field').value = username;
        document.getElementById('password-field').value = password;
        
        // Focus on login button
        document.querySelector('.btn-primary').focus();
        
        // Show visual feedback
        const usernameField = document.getElementById('username-field');
        const passwordField = document.getElementById('password-field');
        
        usernameField.classList.add('border-success');
        passwordField.classList.add('border-success');
        
        setTimeout(() => {
            usernameField.classList.remove('border-success');
            passwordField.classList.remove('border-success');
        }, 1000);
    }

    // Prevent browser from saving passwords
    document.addEventListener('DOMContentLoaded', function() {
        // Disable autocomplete on page load
        const form = document.querySelector('form');
        if (form) {
            form.setAttribute('autocomplete', 'off');
        }
        
        // Clear form fields on page load
        const usernameField = document.getElementById('username-field');
        const passwordField = document.getElementById('password-field');
        
        if (usernameField) usernameField.value = '';
        if (passwordField) passwordField.value = '';
        
        // Prevent password manager from auto-filling
        setTimeout(() => {
            if (usernameField) usernameField.value = '';
            if (passwordField) passwordField.value = '';
        }, 100);
    });

    // Clear fields on browser back button
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            document.getElementById('username-field').value = '';
            document.getElementById('password-field').value = '';
        }
    });
</script>




