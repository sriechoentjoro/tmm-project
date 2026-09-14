<!-- src/Template/Error/error400.ctp -->
<?php
/**
 * Client errors: 404 Not Found, 403 Forbidden, and the rest of the 4xx family.
 *
 * Without this file CakePHP falls back to error500.ctp - ExceptionRenderer
 * line 378 - so every broken link in the application announced itself as
 * "500 Internal Server Error". A page that does not exist and a server that
 * crashed call for different responses from whoever is reading the screen, and
 * the wrong label sent at least one investigation after the wrong thing.
 *
 * $message is deliberately not printed. For a MissingControllerException it
 * names the PHP class that was looked for, which no visitor needs to see; the
 * detail belongs in logs/error.log, where it already is.
 *
 * @var \App\View\AppView $this
 * @var int $code
 * @var string $url Already escaped by ExceptionRenderer.
 */
$code = isset($code) ? (int)$code : 404;

if ($code === 403) {
    $heading = __('403 - Access Denied');
    $detail = __('You do not have permission to open this page. Ask an administrator if you think you should.');
} else {
    $heading = __('404 - Page Not Found');
    $detail = __('The page you are looking for does not exist, or has been moved.');
}
?>
<div class="error-page" style="padding: 40px; text-align: center; font-family: Arial, sans-serif;">
    <h1 style="font-size: 48px; color: #e67e22; margin-bottom: 20px;"><?= h($heading) ?></h1>
    <p style="font-size: 18px; color: #333;"><?= h($detail) ?></p>

    <?php if (!empty($url)): ?>
        <p style="font-size: 14px; color: #777; margin-top: 20px;">
            <?= __('Address requested:') ?>
            <code style="color: #555;"><?= $url ?></code>
        </p>
    <?php endif; ?>

    <p style="margin-top: 30px;">
        <?= $this->Html->link(__('Back to the dashboard'), '/', ['style' => 'color: #00838F;']) ?>
    </p>
</div>
