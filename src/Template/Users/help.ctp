<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="users help content">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">
                <i class="fa fa-question-circle"></i> <?= __('Stakeholder Management - User Guide') ?>
            </h3>
        </div>
        <div class="card-body">
            
            <!-- Introduction -->
            <div class="alert alert-info">
                <h5><i class="fa fa-info-circle"></i> <?= __('Welcome to Stakeholder Management') ?></h5>
                <p><?= __('This guide will help you understand how to manage users and roles in the TMM Apprentice Management System.') ?></p>
            </div>

            <!-- User Management Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-user"></i> <?= __('User Management') ?>
                </h4>
                
                <h5 class="mt-4"><?= __('How to Add a New User') ?></h5>
                <ol>
                    <li><?= __('Navigate to <strong>Stakeholder Management → Users</strong>') ?></li>
                    <li><?= __('Click the <span class="badge badge-primary">New User</span> button') ?></li>
                    <li><?= __('Fill in the required information:') ?>
                        <ul>
                            <li><?= __('<strong>Username:</strong> Unique login identifier') ?></li>
                            <li><?= __('<strong>Password:</strong> Secure password (will be hashed automatically)') ?></li>
                            <li><?= __('<strong>Email:</strong> Valid email address') ?></li>
                            <li><?= __('<strong>Full Name:</strong> User\'s complete name') ?></li>
                            <li><?= __('<strong>Is Active:</strong> Check to enable the account') ?></li>
                            <li><?= __('<strong>Roles:</strong> Select one or more roles (hold Ctrl/Cmd for multiple)') ?></li>
                        </ul>
                    </li>
                    <li><?= __('If the user is associated with an institution (LPK):') ?>
                        <ul>
                            <li><?= __('<strong>Institution Type:</strong> e.g., "VocationalTrainingInstitution"') ?></li>
                            <li><?= __('<strong>Institution ID:</strong> The ID number of the institution') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Click <span class="badge badge-primary">Submit</span> to save') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('How to Edit an Existing User') ?></h5>
                <ol>
                    <li><?= __('Go to <strong>Stakeholder Management → Users</strong>') ?></li>
                    <li><?= __('Find the user in the list') ?></li>
                    <li><?= __('Click the <span class="badge badge-warning">Edit</span> button') ?></li>
                    <li><?= __('Modify the necessary fields') ?></li>
                    <li><?= __('<strong>Note:</strong> Leave the password field blank to keep the existing password') ?></li>
                    <li><?= __('Click <span class="badge badge-primary">Submit</span> to save changes') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('How to Deactivate a User') ?></h5>
                <ol>
                    <li><?= __('Edit the user (see above)') ?></li>
                    <li><?= __('Uncheck the <strong>Is Active</strong> checkbox') ?></li>
                    <li><?= __('Save the changes') ?></li>
                    <li><?= __('<em>Note: Deactivated users cannot log in but their data is preserved</em>') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('How to Delete a User') ?></h5>
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <?= __('<strong>Warning:</strong> Deleting a user is permanent and cannot be undone!') ?>
                </div>
                <ol>
                    <li><?= __('Go to <strong>Stakeholder Management → Users</strong>') ?></li>
                    <li><?= __('Find the user in the list') ?></li>
                    <li><?= __('Click the <span class="badge badge-danger">Delete</span> button') ?></li>
                    <li><?= __('Confirm the deletion when prompted') ?></li>
                </ol>
            </div>

            <!-- Role Management Section -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-id-badge"></i> <?= __('Role Management') ?>
                </h4>
                
                <h5 class="mt-4"><?= __('Understanding Roles') ?></h5>
                <p><?= __('Roles define what users can do in the system. Each role has specific permissions:') ?></p>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th><?= __('Role') ?></th>
                            <th><?= __('Description') ?></th>
                            <th><?= __('Permissions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>administrator</strong></td>
                            <td><?= __('System Administrator') ?></td>
                            <td><?= __('Full access to all features and data') ?></td>
                        </tr>
                        <tr>
                            <td><strong>management</strong></td>
                            <td><?= __('Management/Director') ?></td>
                            <td><?= __('Read-only access to all data for reporting') ?></td>
                        </tr>
                        <tr>
                            <td><strong>tmm-recruitment</strong></td>
                            <td><?= __('TMM Recruitment Staff') ?></td>
                            <td><?= __('Manage candidates and apprentice orders') ?></td>
                        </tr>
                        <tr>
                            <td><strong>tmm-training</strong></td>
                            <td><?= __('TMM Training Staff') ?></td>
                            <td><?= __('Manage trainees and training data') ?></td>
                        </tr>
                        <tr>
                            <td><strong>tmm-documentation</strong></td>
                            <td><?= __('TMM Documentation Staff') ?></td>
                            <td><?= __('Manage documents and ticketing') ?></td>
                        </tr>
                        <tr>
                            <td><strong>lpk-penyangga</strong></td>
                            <td><?= __('LPK Institution User') ?></td>
                            <td><?= __('Manage candidates for their institution only') ?></td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-4"><?= __('How to Create a New Role') ?></h5>
                <ol>
                    <li><?= __('Navigate to <strong>Stakeholder Management → Roles</strong>') ?></li>
                    <li><?= __('Click the <span class="badge badge-primary">New Role</span> button') ?></li>
                    <li><?= __('Enter:') ?>
                        <ul>
                            <li><?= __('<strong>Name:</strong> Role identifier (lowercase, use hyphens)') ?></li>
                            <li><?= __('<strong>Description:</strong> Brief explanation of the role\'s purpose') ?></li>
                        </ul>
                    </li>
                    <li><?= __('Click <span class="badge badge-primary">Submit</span>') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('How to Edit a Role') ?></h5>
                <ol>
                    <li><?= __('Go to <strong>Stakeholder Management → Roles</strong>') ?></li>
                    <li><?= __('Find the role and click <span class="badge badge-warning">Edit</span>') ?></li>
                    <li><?= __('Modify the name or description') ?></li>
                    <li><?= __('Click <span class="badge badge-primary">Submit</span>') ?></li>
                </ol>

                <h5 class="mt-4"><?= __('How to View Users with a Specific Role') ?></h5>
                <ol>
                    <li><?= __('Go to <strong>Stakeholder Management → Roles</strong>') ?></li>
                    <li><?= __('Click <span class="badge badge-info">View</span> on the desired role') ?></li>
                    <li><?= __('Scroll down to see the "Related Users" section') ?></li>
                    <li><?= __('All users assigned to this role will be listed') ?></li>
                </ol>
            </div>

            <!-- Best Practices -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-lightbulb-o"></i> <?= __('Best Practices') ?>
                </h4>
                <ul>
                    <li><?= __('<strong>Use Strong Passwords:</strong> Require users to create passwords with at least 8 characters, including uppercase, lowercase, numbers, and symbols') ?></li>
                    <li><?= __('<strong>Principle of Least Privilege:</strong> Only assign the minimum roles necessary for users to perform their job') ?></li>
                    <li><?= __('<strong>Regular Audits:</strong> Periodically review user accounts and deactivate those no longer needed') ?></li>
                    <li><?= __('<strong>Institution Linking:</strong> For LPK users, always link them to their institution to ensure proper data filtering') ?></li>
                    <li><?= __('<strong>Multiple Roles:</strong> Users can have multiple roles if they perform different functions') ?></li>
                    <li><?= __('<strong>Deactivate Instead of Delete:</strong> When possible, deactivate users instead of deleting them to preserve audit trails') ?></li>
                </ul>
            </div>

            <!-- Troubleshooting -->
            <div class="mb-5">
                <h4 class="text-primary border-bottom pb-2">
                    <i class="fa fa-wrench"></i> <?= __('Troubleshooting') ?>
                </h4>
                
                <h5 class="mt-3"><?= __('User Cannot Log In') ?></h5>
                <ul>
                    <li><?= __('Verify the account is marked as <strong>Active</strong>') ?></li>
                    <li><?= __('Check that the username and password are correct') ?></li>
                    <li><?= __('Ensure the user has at least one role assigned') ?></li>
                </ul>

                <h5 class="mt-3"><?= __('User Cannot Access Certain Features') ?></h5>
                <ul>
                    <li><?= __('Review the user\'s assigned roles') ?></li>
                    <li><?= __('Verify the role has the necessary permissions') ?></li>
                    <li><?= __('For LPK users, ensure institution_id and institution_type are set correctly') ?></li>
                </ul>

                <h5 class="mt-3"><?= __('Cannot Delete a Role') ?></h5>
                <ul>
                    <li><?= __('Roles that are assigned to users cannot be deleted') ?></li>
                    <li><?= __('First remove the role from all users, then delete the role') ?></li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="alert alert-success">
                <h5><i class="fa fa-link"></i> <?= __('Quick Links') ?></h5>
                <p class="mb-2">
                    <?= $this->Html->link('<i class="fa fa-user"></i> ' . __('Manage Users'), ['controller' => 'Users', 'action' => 'index'], ['class' => 'btn btn-sm btn-primary mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-id-badge"></i> ' . __('Manage Roles'), ['controller' => 'Roles', 'action' => 'index'], ['class' => 'btn btn-sm btn-primary mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-user-plus"></i> ' . __('Add New User'), ['controller' => 'Users', 'action' => 'add'], ['class' => 'btn btn-sm btn-success mr-2', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add New Role'), ['controller' => 'Roles', 'action' => 'add'], ['class' => 'btn btn-sm btn-success', 'escape' => false]) ?>
                </p>
            </div>

        </div>
    </div>
</div>
