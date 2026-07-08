<?php
/**
 * @var \App\View\AppView $this
 * @var array $sections
 * @var string $pageTitle
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <h2 style="margin: 0;"><?= h($pageTitle) ?></h2>
</div>

<div style="display: flex; flex-wrap: wrap; gap: 20px;">
    <?php foreach ($sections as $section): ?>
    <a href="<?= $this->Url->build(['controller' => $section['controller'], 'action' => isset($section['action']) ? $section['action'] : 'index']) ?>"
       style="flex: 1 1 260px; max-width: 340px; text-decoration: none; color: inherit;">
        <div class="card" style="padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: #fff; transition: transform 0.2s; border-left: 4px solid #00BCD4;">
            <i class="fas <?= h($section['icon']) ?>" style="font-size: 28px; color: #00BCD4;"></i>
            <h4 style="margin: 12px 0 6px;"><?= h($section['title']) ?></h4>
            <p style="margin: 0; color: #6c757d; font-size: 14px;"><?= h($section['description']) ?></p>
        </div>
    </a>
    <?php endforeach; ?>
</div>
