<?php
/**
 * Layout for the public pages an LPK reaches from its verification email.
 *
 * LpkRegistration::verifyEmail() and ::setPassword() have asked for this layout
 * since they were written, but the file did not exist, so both answered 500 -
 * clicking the link in the email could never work.
 *
 * It cannot reuse the process_flow layout: that one hard-codes an <h1> reading
 * "Process Flow Help", which would head a password form with the wrong title.
 * The visitor here is not logged in and has no navigation, so this carries the
 * language switcher and nothing else.
 *
 * @var \App\View\AppView $this
 */

// Read the same way the other standalone layouts do. A template renders before
// its layout, so this cannot be set from one.
$currentLang = $this->request->getSession()->read('Config.language') ?: 'ind';
$htmlLang = $currentLang === 'ind' ? 'id' : ($currentLang === 'eng' ? 'en' : 'ja');
?>
<!DOCTYPE html>
<html lang="<?= $htmlLang ?>">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php // Read by JavaScript that POSTs without a form; empty when the CSRF
          // middleware is not active, which is the correct value in that case. ?>
    <meta name="csrfToken" content="<?= h($this->request->getAttribute('csrfToken')) ?>">
    <title><?= $this->fetch('title') ?> - <?= __('TMM Apprentice Management System') ?></title>
    <?= $this->Html->meta('icon') ?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .lang-bar {
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
            padding: 16px 12px 0;
        }
        .lang-bar a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            padding: 6px 12px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
        }
        .lang-bar a.active,
        .lang-bar a:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        .panel {
            max-width: 640px;
            margin: 24px auto 48px;
            padding: 0 16px;
        }
        .panel .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.18);
        }
        .brand {
            text-align: center;
            color: #fff;
            margin-top: 20px;
        }
        .brand h1 {
            font-size: 22px;
            font-weight: 600;
            margin: 8px 0 0;
        }
    </style>
</head>
<body>
    <?php // ?lang= rather than the Users::changeLanguage route: whoever opens
          // these pages came from an email and is not logged in, and that action
          // requires authentication, so linking to it would bounce them to the
          // login form and lose their verification link. The controller reads
          // this query string, the same way the other public pages do. ?>
    <div class="lang-bar">
        <?php foreach (['ind' => '🇮🇩 Indonesia', 'eng' => '🇬🇧 English', 'jpn' => '🇯🇵 日本語'] as $code => $label): ?>
            <a href="?lang=<?= $code ?>" class="<?= $currentLang === $code ? 'active' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <div class="brand">
        <i class="fas fa-graduation-cap fa-2x"></i>
        <h1><?= __('TMM Apprentice Management System') ?></h1>
    </div>

    <div class="panel">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->fetch('script') ?>
</body>
</html>
