<?php
/**
 * Per-page guide, the thing the floating "?" button opens.
 *
 * What it replaces
 * ----------------
 * Every module had its own process_flow.ctp: about 250 lines, of which ~150
 * were a copy of the same stylesheet, ~40 a copy of the same language
 * switcher, and the rest three sections of text so general they were true of
 * any screen in the system - one workflow step called "Input Data", and a
 * mermaid diagram reading Input -> Validation -> Valid? -> Save -> Done that
 * was byte-for-byte identical in 89 files. Each still carried the comment
 * "TODO: Customize this template". Nobody could have customised 89 copies of a
 * 250-line file, which is why nobody did.
 *
 * So the content moves out of the template and into a definition: one small
 * array per module in config/page_guides/<Controller>.php, and one renderer
 * here. A module's guide becomes a file you can read in a minute and correct
 * in a line.
 *
 * Language
 * --------
 * The old templates carried every sentence three times, in ind/eng/jpn
 * if-else blocks. AppController::beforeFilter already calls I18n::setLocale()
 * from Config.language, so __() resolves to the reader's language on its own.
 * Writing the guides with __() means the id and ja catalogues hold the
 * translations, bin/i18n-coverage.php can see them - it could not see a word
 * of the old templates - and a correction is made once rather than three
 * times.
 *
 * A definition is an array with these keys, all optional except title and
 * lead: icon, title, subtitle, lead, actors, before, steps, diagram, triggers,
 * cautions. config/page_guides/LpkRegistration.php is the worked example -
 * a real file is a better specification than a sketch, and it cannot drift out
 * of date the way a comment can.
 *
 * A module with no definition file renders a short notice saying its guide has
 * not been written yet, and points at the system documentation. That is
 * deliberate: the guides are written one workflow phase at a time, and a
 * missing one should say so rather than show something generic.
 *
 * @var \App\View\AppView $this
 * @var string $module Controller name, e.g. 'VocationalTrainingInstitutions'.
 */

$module = $module ?? $this->request->getParam('controller');
$file = CONFIG . 'page_guides' . DS . $module . '.php';
$guide = is_file($file) ? include $file : null;

$currentLang = $this->request->getSession()->read('Config.language') ?: 'ind';
?>
<?= $this->element('process_flow_assets') ?>
<?php $this->append('css'); ?>
<style>
.guide-wrap { max-width: 1100px; margin: 0 auto; padding: 8px 0 40px; }

.guide-head {
    display: flex; align-items: center; gap: 16px;
    padding: 22px 26px; margin-bottom: 22px; border-radius: 14px;
    color: #fff; background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%);
    box-shadow: 0 4px 16px rgba(0, 151, 167, .25);
}
.guide-head .guide-icon {
    width: 48px; height: 48px; border-radius: 14px; flex: 0 0 auto;
    background: rgba(255,255,255,.18);
    display: flex; align-items: center; justify-content: center; font-size: 22px;
}
.guide-head h1 { margin: 0; font-size: 22px; font-weight: 700; }
.guide-head p { margin: 4px 0 0; font-size: 13.5px; opacity: .93; }

.guide-langs { text-align: center; margin-bottom: 22px; }
.guide-lang {
    display: inline-block; padding: 7px 18px; margin: 0 4px;
    border: 2px solid #00BCD4; border-radius: 20px;
    color: #00838f; text-decoration: none; font-weight: 600; font-size: 14px;
}
.guide-lang.active, .guide-lang:hover { background: #00BCD4; color: #fff; text-decoration: none; }

.guide-lead {
    padding: 18px 20px; margin-bottom: 26px;
    background: #f1fbfd; border-left: 5px solid #00BCD4; border-radius: 10px;
    font-size: 15px; line-height: 1.65; color: #35495e;
}

.guide-actors { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; }
.guide-actor {
    display: flex; align-items: baseline; gap: 8px;
    padding: 10px 14px; border: 1px solid #e6ecf1; border-radius: 10px;
    background: #fff; font-size: 13.5px; color: #55677a; flex: 1 1 260px;
}
.guide-actor code {
    background: #eef6f8; color: #00697a; padding: 2px 8px; border-radius: 20px;
    font-size: 12px; font-weight: 700; white-space: nowrap;
}

/* process_flow_assets never defined .database-indicator, so the table name a
   step writes to ran on from the step title as if it were part of the
   sentence. It is a chip. */
.guide-wrap .database-indicator {
    display: inline-block;
    margin-left: 10px;
    padding: 2px 10px;
    border-radius: 20px;
    background: #eef6f8;
    color: #00697a;
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-size: 11.5px;
    font-weight: 600;
    vertical-align: middle;
    white-space: nowrap;
}

.guide-step-screen {
    display: inline-flex; align-items: center; gap: 6px; margin-top: 8px;
    padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 600;
    background: #00BCD4; color: #fff !important; text-decoration: none !important;
}
.guide-step-screen:hover { background: #0097A7; }

.guide-step-note {
    display: block; margin-top: 8px; padding: 8px 12px;
    background: #fff8e1; border-left: 3px solid #ffc107; border-radius: 6px;
    font-size: 13px; color: #7a5b00;
}

.guide-trigger {
    display: flex; gap: 12px; align-items: flex-start;
    padding: 12px 14px; margin-bottom: 10px;
    background: #fff; border: 1px solid #e6ecf1; border-left: 4px solid #7e57c2;
    border-radius: 10px; font-size: 13.5px; line-height: 1.55; color: #46586b;
}
.guide-trigger i { color: #7e57c2; margin-top: 3px; }
.guide-trigger a { font-weight: 600; white-space: nowrap; }

.guide-caution {
    padding: 12px 14px; margin-bottom: 10px;
    background: #fff4f2; border: 1px solid #f3c2b8; border-left: 4px solid #e5533d;
    border-radius: 10px; font-size: 13.5px; line-height: 1.55; color: #8a2c1b;
}

.guide-missing {
    padding: 18px 20px; border-radius: 10px;
    background: #fff8e1; border-left: 5px solid #ffc107; color: #7a5b00;
}

@media (max-width: 768px) {
    .guide-head { flex-wrap: wrap; padding: 16px; }
    .guide-head h1 { font-size: 19px; }
    .flow-section { padding: 18px; }
    .guide-wrap .database-indicator { display: block; margin: 6px 0 0; white-space: normal; }
}
</style>
<?php $this->end(); ?>

<div class="guide-wrap">

    <div class="guide-langs">
        <a href="?lang=ind" class="guide-lang <?= $currentLang === 'ind' ? 'active' : '' ?>">🇮🇩 Indonesia</a>
        <a href="?lang=eng" class="guide-lang <?= $currentLang === 'eng' ? 'active' : '' ?>">🇬🇧 English</a>
        <a href="?lang=jpn" class="guide-lang <?= $currentLang === 'jpn' ? 'active' : '' ?>">🇯🇵 日本語</a>
    </div>

<?php if (!$guide) : ?>
    <div class="guide-missing">
        <strong><?= __('This page has no guide yet.') ?></strong>
        <p><?= __('The guides are being written one workflow phase at a time. Until this module is reached, the system documentation describes the process it belongs to.') ?></p>
        <?= $this->Html->link(__('Open the system documentation'), ['controller' => 'Dashboard', 'action' => 'processFlow', 'prefix' => false]) ?>
    </div>
<?php else : ?>

    <div class="guide-head">
        <div class="guide-icon"><i class="fas <?= h($guide['icon'] ?? 'fa-book-open') ?>"></i></div>
        <div>
            <h1><?= h($guide['title']) ?></h1>
            <?php if (!empty($guide['subtitle'])) : ?>
                <p><?= h($guide['subtitle']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="guide-lead"><?= h($guide['lead']) ?></div>

    <?php if (!empty($guide['actors'])) : ?>
        <div class="flow-section">
            <h2><i class="fas fa-users"></i> <?= __('Who works on this screen') ?></h2>
            <div class="guide-actors">
                <?php foreach ($guide['actors'] as $actor) : ?>
                    <div class="guide-actor">
                        <code><?= h($actor['role']) ?></code>
                        <span><?= h($actor['can']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($guide['before'])) : ?>
        <div class="flow-section">
            <h2><i class="fas fa-sign-in-alt"></i> <?= __('What has to exist first') ?></h2>
            <?php foreach ($guide['before'] as $item) : ?>
                <div class="guide-trigger">
                    <i class="fas fa-arrow-right"></i>
                    <div>
                        <?= h($item['note']) ?>
                        <?php if (!empty($item['url'])) : ?>
                            — <?= $this->Html->link(h($item['label']), $item['url']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($guide['steps'])) : ?>
        <div class="flow-section">
            <h2><i class="fas fa-list-ol"></i> <?= __('Step by step') ?></h2>
            <div class="workflow-steps">
                <?php foreach ($guide['steps'] as $i => $step) : ?>
                    <div class="workflow-step">
                        <span class="step-number"><?= $i + 1 ?></span>
                        <div style="display:inline-block; vertical-align:top; width:calc(100% - 60px);">
                            <div class="step-title">
                                <?= h($step['title']) ?>
                                <?php if (!empty($step['data'])) : ?>
                                    <span class="database-indicator"><?= h($step['data']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="step-description">
                                <?php if (!empty($step['who'])) : ?>
                                    <strong><?= __('Who') ?>:</strong> <?= h($step['who']) ?><br>
                                <?php endif; ?>
                                <?php if (!empty($step['do'])) : ?>
                                    <strong><?= __('Action') ?>:</strong> <?= h($step['do']) ?><br>
                                <?php endif; ?>
                                <?php if (!empty($step['result'])) : ?>
                                    <strong><?= __('Result') ?>:</strong> <?= h($step['result']) ?>
                                <?php endif; ?>
                                <?php if (!empty($step['screen'])) : ?>
                                    <br><?= $this->Html->link(
                                        '<i class="fas fa-external-link-alt"></i> ' . h($step['screen'][1]),
                                        $step['screen'][0],
                                        ['class' => 'guide-step-screen', 'escape' => false]
                                    ) ?>
                                <?php endif; ?>
                                <?php if (!empty($step['note'])) : ?>
                                    <span class="guide-step-note"><i class="fas fa-exclamation-triangle"></i> <?= h($step['note']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($guide['diagram'])) : ?>
        <div class="flow-section">
            <h2><i class="fas fa-project-diagram"></i> <?= __('Flow of this screen') ?></h2>
            <div class="mermaid"><?= $guide['diagram'] ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($guide['triggers'])) : ?>
        <div class="flow-section">
            <h2><i class="fas fa-bolt"></i> <?= __('What this screen sets off elsewhere') ?></h2>
            <?php foreach ($guide['triggers'] as $trigger) : ?>
                <div class="guide-trigger">
                    <i class="fas <?= h($trigger['icon'] ?? 'fa-bolt') ?>"></i>
                    <div>
                        <?= h($trigger['what']) ?>
                        <?php if (!empty($trigger['url'])) : ?>
                            — <?= $this->Html->link(h($trigger['label']), $trigger['url']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($guide['cautions'])) : ?>
        <div class="flow-section">
            <h2><i class="fas fa-exclamation-triangle"></i> <?= __('Things that go wrong here') ?></h2>
            <?php foreach ($guide['cautions'] as $caution) : ?>
                <div class="guide-caution"><?= h($caution) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

</div>
