<?php
/**
 * Form Confirmation Element
 * Include this in add.ctp and edit.ctp to enable confirmation before save
 * 
 * Usage:
 * <?= $this->element('form_confirm_script') ?>
 */

echo $this->Html->script('form-confirm', ['block' => true]);
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Find the form
    const form = document.querySelector('form');
    if (!form) return;
    
    // Add data attribute to enable confirmation
    form.setAttribute('data-confirm', 'true');
    form.setAttribute('id', 'main-form');
    
    // Initialize FormConfirmation
    new FormConfirmation('#main-form');
});
