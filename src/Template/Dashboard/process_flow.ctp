<?php
/**
 * System documentation - the landing page after signing in.
 *
 * Reached from the leftmost menu tab, which elegant_menu draws outside
 * role_menus so every role can come back to it.
 *
 * The document is the system_process_guide element, shared with Users::guide().
 * What this page adds is what only a signed-in reader can have: the application
 * menu around it, and $userRoles, which marks the phases they actually work in.
 *
 * process_flow_assets supplies the mermaid loader and the flow-section styling
 * that the standalone process_flow layout would otherwise have provided - this
 * page renders in 'elegant', which has neither.
 *
 * @var \App\View\AppView $this
 * @var array $userRoles
 */
?>
<?= $this->element('process_flow_assets') ?>

<style>
.sysdoc-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border-radius: 14px;
    padding: 24px 28px;
    margin-bottom: 24px;
}
.sysdoc-header h1 { margin: 0 0 8px; font-size: 26px; }
.sysdoc-header p { margin: 0; opacity: .9; }
.language-switcher { text-align: center; margin-bottom: 24px; }
.lang-btn {
    display: inline-block; padding: 7px 18px; margin: 0 4px;
    border: 2px solid #667eea; border-radius: 20px;
    color: #667eea; text-decoration: none; font-weight: 600; font-size: 14px;
}
.lang-btn.active, .lang-btn:hover { background: #667eea; color: #fff; text-decoration: none; }
</style>

<?php $currentLang = $this->request->getSession()->read('Config.language') ?: 'ind'; ?>

<div class="sysdoc-header">
    <h1><i class="fas fa-sitemap"></i> <?= __('TMM System Documentation') ?></h1>
    <p><?= __('The whole process, phase by phase, with a link to every screen that does the work.') ?></p>
</div>

<div class="language-switcher">
    <a href="?lang=ind" class="lang-btn <?= $currentLang === 'ind' ? 'active' : '' ?>">🇮🇩 Indonesia</a>
    <a href="?lang=eng" class="lang-btn <?= $currentLang === 'eng' ? 'active' : '' ?>">🇬🇧 English</a>
    <a href="?lang=jpn" class="lang-btn <?= $currentLang === 'jpn' ? 'active' : '' ?>">🇯🇵 日本語</a>
</div>

<?= $this->element('system_process_guide') ?>
