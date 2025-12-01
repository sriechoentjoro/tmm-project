<%
/**
 * Custom Bake Template for Forms with Smart Field Detection
 * Features:
 * - Auto-detect file/image fields -> multipart form
 * - Date fields -> datePicker
 * - Email fields -> email validation
 * - Katakana/Hiragana fields -> kana.js
 * - Address fields -> custom address selector
 */
use Cake\Utility\Inflector;

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return !in_array($schema->getColumnType($field), ['binary']);
    });

// Auto-detect if form needs multipart/form-data
$hasFileUpload = false;
foreach ($fields as $field) {
    if (preg_match('/(file|image)/i', $field)) {
        $hasFileUpload = true;
        break;
$formOptions = $hasFileUpload ? ", ['type' => 'file']" : '';
%>
<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\<%= $entityClass %>|null $<%= $singularVar %>
 */
use Cake\Utility\Inflector;

// Dynamic host detection for static assets (CORS-friendly)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$staticAssetsUrl = $protocol . '://' . $host . '/static-assets';
?>
<!-- Load CSS/JS from static location (workaround for .htaccess issue) -->
<?= $this->Html->script('form-confirm.js?v=2.0') ?>
<?= $this->Html->css('datepicker-fix.css') ?>

<!-- Mobile CSS now loaded globally from layout - mobile-responsive.css -->

<!-- Actions Sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List <%= $pluralHumanName %>'), ['action' => 'index'], ['escape' => false]) ?></li>
<%
    $done = [];
    foreach ($associations as $type => $data):
        foreach ($data as $alias => $details):
            if (!empty($details['navLink']) && $details['controller'] !== $this->name && !in_array($details['controller'], $done)):
                $controller = $details['controller'];
                $singularController = Inflector::humanize(Inflector::singularize($controller));
%>
        <li><?= $this->Html->link('<i class="fas fa-list"></i> ' . __('List <%= $controller %>'), ['controller' => '<%= $controller %>', 'action' => 'index'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New <%= $singularController %>'), ['controller' => '<%= $controller %>', 'action' => 'add'], ['escape' => false]) ?></li>
<%
                $done[] = $controller;
            endif;
        endforeach;
    endforeach;
%>
    </ul>
</nav>

<!-- Main Content -->
<div class="<%= $pluralVar %> form content">
    <div class="card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fas fa-edit"></i>
                <?= __(Inflector::humanize($this->request->getParam("action")) . ' <%= $singularHumanName %>') ?>
            </h3>
        </div>

        <div class="card-body">
            <?= $this->Form->create($<%= $singularVar %><% if ($hasFileUpload) ?>
            <?php if (!empty($template->id)): ?>
                <?= $this->Form->hidden('id') ?>
            <?php endif; ?>


<!-- Process Flow Help Button -->
<?= $this->element('process_flow_help') ?>
