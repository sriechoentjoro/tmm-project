<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Inventory $inventory
 */
?>
<!-- EXAMPLE Enhanced View Template for Inventories -->
<!-- Copy this structure and modify for other tables -->

<div class="github-container">
    <!-- Page Header with Hamburger Menu -->
    <div class="github-page-header">
        <div class="github-header-content">
            <div class="github-title-row">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <!-- Hamburger Dropdown Menu -->
                    <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" 
                            data-bs-toggle="dropdown" aria-expanded="false" 
                            style="padding: 6px 8px; font-size: 18px;">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?= $this->Html->link(
                            '<i class="fas fa-list"></i> ' . __('List Inventories'),
                            ['action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-plus"></i> ' . __('New Inventory'),
                            ['action' => 'add'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                        <div class="dropdown-divider"></div>
                        <?= $this->Html->link(
                            '<i class="fas fa-warehouse"></i> ' . __('List Storages'),
                            ['controller' => 'Storages', 'action' => 'index'],
                            ['class' => 'dropdown-item', 'escape' => false]
                        ) ?>
                    </div>
                    
                    <h1 style="margin: 0;"><?= h($inventory->title) ?></h1>
                </div>
                
                <div class="github-header-actions">
                    <?= $this->Html->link(
                        '<i class="fas fa-edit"></i> ' . __('Edit'),
                        ['action' => 'edit', $inventory->id],
                        ['class' => 'btn-export-light', 'escape' => false]
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-list"></i> ' . __('List'),
                        ['action' => 'index'],
                        ['class' => 'btn-export-light', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="view-tabs-container" style="margin-top: 20px;">
        <ul class="view-tabs-nav" role="tablist" style="display: flex; list-style: none; padding: 0; border-bottom: 1px solid #d0d7de;">
            <li class="view-tab-item">
                <a href="#tab-detail" class="view-tab-link active" data-tab="tab-detail" 
                   style="padding: 8px 16px; display: block; text-decoration: none; border-bottom: 2px solid #fd8c73;">
                    📄 <?= __('Detail') ?>
                </a>
            </li>
            <?php if (!empty($inventory->stock_incomings)): ?>
            <li class="view-tab-item">
                <a href="#tab-incoming" class="view-tab-link" data-tab="tab-incoming" 
                   style="padding: 8px 16px; display: block; text-decoration: none;">
                    📦 <?= __('Stock Incoming') ?>
                    <span style="background: #667eea; color: white; padding: 2px 6px; border-radius: 10px; font-size: 12px;">
                        <?= count($inventory->stock_incomings) ?>
                    </span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Tab Contents -->
        <div class="view-tabs-content">
            <!-- Detail Tab -->
            <div id="tab-detail" class="view-tab-pane active" style="padding: 20px; background: white;">
                <table class="table" style="width: 100%;">
                    <tr>
                        <th style="width: 200px;"><?= __('Code') ?></th>
                        <td><?= h($inventory->code) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Title') ?></th>
                        <td><?= h($inventory->title) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Storage') ?></th>
                        <td><?= $inventory->has('storage') ? $this->Html->link($inventory->storage->title, ['controller' => 'Storages', 'action' => 'view', $inventory->storage->id]) : '' ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Rack') ?></th>
                        <td><?= h($inventory->rack) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Stock') ?></th>
                        <td><?= $this->Number->format($inventory->stock) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Price') ?></th>
                        <td><?= $this->Number->currency($inventory->price) ?></td>
                    </tr>
                    <?php if (!empty($inventory->image_url)): ?>
                    <tr>
                        <th><?= __('Image') ?></th>
                        <td>
                            <?= $this->Html->image($inventory->image_url, [
                                'style' => 'max-width: 200px; cursor: pointer;',
                                'onclick' => "document.getElementById('imageModal').style.display='block'"
                            ]) ?>
                            <!-- Simple Modal -->
                            <div id="imageModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8);" onclick="this.style.display='none'">
                                <?= $this->Html->image($inventory->image_url, ['style' => 'margin: auto; display: block; max-width: 90%; max-height: 90%; margin-top: 50px;']) ?>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?= __('Created') ?></th>
                        <td><?= h($inventory->created) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Modified') ?></th>
                        <td><?= h($inventory->modified) ?></td>
                    </tr>
                </table>

                <?php if (!empty($inventory->description)): ?>
                <div style="margin-top: 20px;">
                    <h4><?= __('Description') ?></h4>
                    <div style="background: #f6f8fa; padding: 16px; border-radius: 6px;">
                        <?= $this->Text->autoParagraph(h($inventory->description)) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Stock Incoming Tab -->
            <?php if (!empty($inventory->stock_incomings)): ?>
            <div id="tab-incoming" class="view-tab-pane" style="display: none; padding: 20px;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?= __('Date') ?></th>
                            <th><?= __('Quantity') ?></th>
                            <th><?= __('Note') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($inventory->stock_incomings as $incoming): ?>
                        <tr>
                            <td><?= h($incoming->date_incoming) ?></td>
                            <td><?= $this->Number->format($incoming->quantity) ?></td>
                            <td><?= h($incoming->note) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'StockIncomings', 'action' => 'view', $incoming->id]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.github-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 20px;
}
.github-page-header {
    padding: 16px 0;
    border-bottom: 1px solid #d0d7de;
    margin-bottom: 16px;
}
.github-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.btn-export-light {
    padding: 5px 16px;
    margin-left: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
}
.btn-export-light:hover {
    opacity: 0.9;
}
.view-tab-link {
    color: #24292f;
}
.view-tab-link.active {
    color: #667eea;
    font-weight: 600;
}
.dropdown-menu {
    display: none;
    position: absolute;
    background: white;
    border: 1px solid #d0d7de;
    border-radius: 6px;
    box-shadow: 0 8px 24px rgba(140,149,159,0.2);
    min-width: 200px;
    z-index: 1000;
}
#dropdownMenuButton:focus + .dropdown-menu,
.dropdown-menu:hover {
    display: block;
}
.dropdown-item {
    padding: 8px 16px;
    display: block;
    text-decoration: none;
    color: #24292f;
}
.dropdown-item:hover {
    background: #f6f8fa;
}
</style>

<script>
// Simple tab switching
document.querySelectorAll('.view-tab-link').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('data-tab');
        
        // Hide all tabs
        document.querySelectorAll('.view-tab-pane').forEach(pane => {
            pane.style.display = 'none';
        });
        document.querySelectorAll('.view-tab-link').forEach(link => {
            link.classList.remove('active');
            link.style.borderBottom = 'none';
        });
        
        // Show target tab
        document.getElementById(targetId).style.display = 'block';
        this.classList.add('active');
        this.style.borderBottom = '2px solid #fd8c73';
    });
});

// Dropdown toggle
document.getElementById('dropdownMenuButton').addEventListener('click', function(e) {
    e.stopPropagation();
    const menu = this.nextElementSibling;
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
});

// Close dropdown when clicking outside
document.addEventListener('click', function() {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
    });
});
</script>
