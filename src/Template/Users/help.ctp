<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="users help content">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">
                <i class="fa fa-question-circle"></i> Stakeholder Management - User Guide
            </h3>
        </div>
        <div class="card-body">
            
            <!-- Introduction -->
            <div class="alert alert-info">
                <h5><i class="fa fa-info-circle"></i> Welcome to Stakeholder Management</h5>
                <p>This guide will help you understand how to manage users and roles in the TMM Apprentice Management System.</p>
            </div>

            <!-- User Management Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-user"></i> User Management
                </h4>
                
                <h5 class="mt-4">How to Add a New User</h5>
                <ol>
                    <li>Navigate to <strong>Stakeholder Management → Users</strong></li>
                    <li>Click the <span class="badge badge-primary">New User</span> button</li>
                    <li>Fill in the required information:
                        <ul>
                            <li><strong>Username:</strong> Unique login identifier</li>
                            <li><strong>Password:</strong> Secure password (will be hashed automatically)</li>
                            <li><strong>Email:</strong> Valid email address</li>
                            <li><strong>Full Name:</strong> User's complete name</li>
                            <li><strong>Is Active:</strong> Check to enable the account</li>
                            <li><strong>Roles:</strong> Select one or more roles (hold Ctrl/Cmd for multiple)</li>
                        </ul>
                    </li>
                    <li>If the user is associated with an institution (LPK):
                        <ul>
                            <li><strong>Institution Type:</strong> e.g., "VocationalTrainingInstitution"</li>
                            <li><strong>Institution ID:</strong> The ID number of the institution</li>
                        </ul>
                    </li>
                    <li>Click <span class="badge badge-primary">Submit</span> to save</li>
                </ol>

                <h5 class="mt-4">How to Edit an Existing User</h5>
                <ol>
                    <li>Go to <strong>Stakeholder Management → Users</strong></li>
                    <li>Find the user in the list</li>
                    <li>Click the <span class="badge badge-warning">Edit</span> button</li>
                    <li>Modify the necessary fields</li>
                    <li><strong>Note:</strong> Leave the password field blank to keep the existing password</li>
                    <li>Click <span class="badge badge-primary">Submit</span> to save changes</li>
                </ol>

                <h5 class="mt-4">How to Deactivate a User</h5>
                <ol>
                    <li>Edit the user (see above)</li>
                    <li>Uncheck the <strong>Is Active</strong> checkbox</li>
                    <li>Save the changes</li>
                    <li><em>Note: Deactivated users cannot log in but their data is preserved</em></li>
                </ol>

                <h5 class="mt-4">How to Delete a User</h5>
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Warning:</strong> Deleting a user is permanent and cannot be undone!
                </div>
                <ol>
                    <li>Go to <strong>Stakeholder Management → Users</strong></li>
                    <li>Find the user in the list</li>
                    <li>Click the <span class="badge badge-danger">Delete</span> button</li>
                    <li>Confirm the deletion when prompted</li>
                </ol>
            </div>

            <!-- Role Management Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-id-badge"></i> Role Management
                </h4>
                
                <h5 class="mt-4">Understanding Roles</h5>
                <p>Roles define what users can do in the system. Each role has specific permissions:</p>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Role</th>
                            <th>Description</th>
                            <th>Permissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>administrator</strong></td>
                            <td>System Administrator</td>
                            <td>Full access to all features and data</td>
                        </tr>
                        <tr>
                            <td><strong>management</strong></td>
                            <td>Management/Director</td>
                            <td>Read-only access to all data for reporting</td>
                        </tr>
                        <tr>
                            <td><strong>tmm-recruitment</strong></td>
                            <td>TMM Recruitment Staff</td>
                            <td>Manage candidates and apprentice orders</td>
                        </tr>
                        <tr>
                            <td><strong>tmm-training</strong></td>
                            <td>TMM Training Staff</td>
                            <td>Manage trainees and training data</td>
                        </tr>
                        <tr>
                            <td><strong>tmm-documentation</strong></td>
                            <td>TMM Documentation Staff</td>
                            <td>Manage documents and ticketing</td>
                        </tr>
                        <tr>
                            <td><strong>lpk-penyangga</strong></td>
                            <td>LPK Institution User</td>
                            <td>Manage candidates for their institution only</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-4">How to Create a New Role</h5>
                <ol>
                    <li>Navigate to <strong>Stakeholder Management → Roles</strong></li>
                    <li>Click the <span class="badge badge-primary">New Role</span> button</li>
                    <li>Enter:
                        <ul>
                            <li><strong>Name:</strong> Role identifier (lowercase, use hyphens)</li>
                            <li><strong>Description:</strong> Brief explanation of the role's purpose</li>
                        </ul>
                    </li>
                    <li>Click <span class="badge badge-primary">Submit</span></li>
                </ol>

                <h5 class="mt-4">How to Edit a Role</h5>
                <ol>
                    <li>Go to <strong>Stakeholder Management → Roles</strong></li>
                    <li>Find the role and click <span class="badge badge-warning">Edit</span></li>
                    <li>Modify the name or description</li>
                    <li>Click <span class="badge badge-primary">Submit</span></li>
                </ol>

                <h5 class="mt-4">How to View Users with a Specific Role</h5>
                <ol>
                    <li>Go to <strong>Stakeholder Management → Roles</strong></li>
                    <li>Click <span class="badge badge-info">View</span> on the desired role</li>
                    <li>Scroll down to see the "Related Users" section</li>
                    <li>All users assigned to this role will be listed</li>
                </ol>
            </div>

            <!-- Best Practices -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-lightbulb-o"></i> Best Practices
                </h4>
                <ul>
                    <li><strong>Use Strong Passwords:</strong> Require users to create passwords with at least 8 characters, including uppercase, lowercase, numbers, and symbols</li>
                    <li><strong>Principle of Least Privilege:</strong> Only assign the minimum roles necessary for users to perform their job</li>
                    <li><strong>Regular Audits:</strong> Periodically review user accounts and deactivate those no longer needed</li>
                    <li><strong>Institution Linking:</strong> For LPK users, always link them to their institution to ensure proper data filtering</li>
                    <li><strong>Multiple Roles:</strong> Users can have multiple roles if they perform different functions</li>
                    <li><strong>Deactivate Instead of Delete:</strong> When possible, deactivate users instead of deleting them to preserve audit trails</li>
                </ul>
            </div>

            <!-- Troubleshooting -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-wrench"></i> Troubleshooting
                </h4>
                
                <h5 class="mt-3">User Cannot Log In</h5>
                <ul>
                    <li>Verify the account is marked as <strong>Active</strong></li>
                    <li>Check that the username and password are correct</li>
                    <li>Ensure the user has at least one role assigned</li>
                </ul>

                <h5 class="mt-3">User Cannot Access Certain Features</h5>
                <ul>
                    <li>Review the user's assigned roles</li>
                    <li>Verify the role has the necessary permissions</li>
                    <li>For LPK users, ensure institution_id and institution_type are set correctly</li>
                </ul>

                <h5 class="mt-3">Cannot Delete a Role</h5>
                <ul>
                    <li>Roles that are assigned to users cannot be deleted</li>
                    <li>First remove the role from all users, then delete the role</li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="alert alert-success">
                <h5><i class="fa fa-link"></i> Quick Links</h5>
                <p class="mb-2">
                    <?= $this->Html->link('<i class="fa fa-user"></i> Manage Users', ['controller' => 'Users', 'action' => 'index'], ['class' => 'btn btn-sm btn-primary mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-id-badge"></i> Manage Roles', ['controller' => 'Roles', 'action' => 'index'], ['class' => 'btn btn-sm btn-primary mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-user-plus"></i> Add New User', ['controller' => 'Users', 'action' => 'add'], ['class' => 'btn btn-sm btn-success mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> Add New Role', ['controller' => 'Roles', 'action' => 'add'], ['class' => 'btn btn-sm btn-success', 'escape' => false]) ?>
                </p>
            </div>

        </div>
    </div>
</div>
