<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="users settings">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-cog"></i> <?= __('Account Settings') ?>
                    </h3>
                </div>
                
                <?= $this->Form->create($user, ['type' => 'file']) ?>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= __('Note: You cannot change your roles or institution assignment. Contact administrator for these changes.') ?>
                    </div>
                    
                    <fieldset>
                        <legend><?= __('Personal Information') ?></legend>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <?= __('Full Name') ?> 
                                <span class="text-danger">*</span>
                                <small class="text-muted">(Max 256 characters)</small>
                            </label>
                            <?= $this->Form->control('fullname', [
                                'class' => 'form-control',
                                'label' => false,
                                'required' => true,
                                'maxlength' => 256,
                                'placeholder' => __('Enter your full name')
                            ]) ?>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Your full name as it appears on official documents.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <?= __('Username') ?>
                                <small class="text-muted">(Cannot be changed)</small>
                            </label>
                            <?= $this->Form->control('username', [
                                'class' => 'form-control',
                                'label' => false,
                                'disabled' => true,
                                'value' => $user->username
                            ]) ?>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Username cannot be changed after account creation.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <?= __('Email') ?>
                                <small class="text-muted">(Optional - Valid email format)</small>
                            </label>
                            <?= $this->Form->control('email', [
                                'type' => 'email',
                                'class' => 'form-control',
                                'label' => false,
                                'placeholder' => __('user@example.com')
                            ]) ?>
                            <small class="form-text text-muted">
                                <i class="fas fa-envelope"></i> Enter a valid email address for notifications.
                            </small>
                        </div>
                    </fieldset>
                    
                    <fieldset class="mt-4">
                        <legend><?= __('Profile Photo') ?></legend>
                        
                        <?php if (!empty($user->photo)): ?>
                            <div class="mb-3">
                                <img src="<?= h($user->photo) ?>" alt="Current Photo" class="img-thumbnail" style="max-width: 200px;">
                                <p class="text-muted mt-2">
                                    <small><?= __('Current profile photo') ?></small>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <?= __('Upload New Photo') ?>
                                <small class="text-muted">(Optional - JPG, PNG, max 2MB)</small>
                            </label>
                            <?= $this->Form->control('photo_file', [
                                'type' => 'file',
                                'class' => 'form-control-file',
                                'label' => false,
                                'accept' => 'image/jpeg,image/png,image/jpg'
                            ]) ?>
                            <small class="form-text text-muted">
                                <i class="fas fa-camera"></i> Upload a clear photo of yourself (JPG or PNG format).
                            </small>
                        </div>
                    </fieldset>
                </div>
                
                <div class="card-footer">
                    <div class="form-actions">
                        <?= $this->Form->button(
                            '<i class="fas fa-save"></i> ' . __('Save Changes'),
                            ['class' => 'btn btn-primary', 'escape' => false]
                        ) ?>
                        
                        <?= $this->Html->link(
                            '<i class="fas fa-times"></i> ' . __('Cancel'),
                            ['action' => 'profile'],
                            ['class' => 'btn btn-secondary', 'escape' => false]
                        ) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<style>
.form-actions {
    display: flex;
    gap: 10px;
}
</style>
