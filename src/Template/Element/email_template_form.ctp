<?php
/**
 * The email template editor, with the message drawn beside it as it will arrive.
 *
 * Shared by add.ctp and edit.ctp, which differ only in their heading.
 *
 * The preview is not an impression of the email - it is the email's own
 * letterhead. EmailTemplatesController renders Layout/Email/html/email_branded
 * through the same view EmailComponent sends through, splits it where the body
 * goes, and hands both halves here. The body is dropped between them and the
 * result written into an iframe, which keeps the email's CSS from reaching the
 * admin page and the admin page's CSS from flattering the email.
 *
 * Whether the letterhead is used at all is the same question the sender asks:
 * a body that already opens an <html> document is a whole email and is shown
 * on its own. Get that wrong in either place and the preview becomes a picture
 * of an email nobody receives.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\EmailTemplate $emailTemplate
 * @var array{before: string, after: string} $previewChrome
 * @var array<string, string> $previewData
 */
?>
<style>
    .et-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 1100px) {
        .et-grid { grid-template-columns: minmax(0, 1fr); }
    }
    .et-panel {
        background: #fff;
        border: 1px solid #e3e8ee;
        border-radius: 10px;
        overflow: hidden;
    }
    .et-panel > header {
        padding: 10px 14px;
        background: #f6f8fa;
        border-bottom: 1px solid #e3e8ee;
        font-weight: 600;
        font-size: 14px;
        color: #33475b;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .et-panel > .et-body { padding: 14px; }
    .et-panel textarea { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 13px; }
    .et-panel textarea#body-html { min-height: 320px; }
    .et-panel textarea#body-text { min-height: 160px; }

    /* The preview pane follows while the body is edited further down. */
    .et-sticky { position: sticky; top: 16px; }
    .et-preview-frame {
        width: 100%;
        height: 620px;
        border: 0;
        display: block;
        background: #f4f4f4;
    }
    .et-subject {
        padding: 10px 14px;
        border-bottom: 1px solid #e3e8ee;
        background: #fff;
        font-size: 14px;
    }
    .et-subject .et-label { color: #8898aa; margin-right: 6px; }
    .et-chip {
        display: inline-block;
        padding: 2px 8px;
        margin: 0 4px 4px 0;
        border-radius: 12px;
        background: #eef2ff;
        color: #4c5bd4;
        border: 1px solid #dbe1ff;
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 12px;
        cursor: pointer;
    }
    .et-chip:hover { background: #4c5bd4; color: #fff; }
    .et-note { color: #8898aa; font-size: 12px; margin: 8px 0 0; }
    .et-warn {
        margin: 0;
        padding: 8px 14px;
        background: #fff8e6;
        border-bottom: 1px solid #f3e2b6;
        color: #8a6d1f;
        font-size: 13px;
    }
</style>

<div class="et-grid">
    <div class="et-panel">
        <header><?= __('Template') ?></header>
        <div class="et-body">
            <?= $this->Form->create($emailTemplate) ?>
            <?= $this->Form->control('template_key', [
                'label' => __('Template Key'),
                'help' => __('The key the application asks for. Changing it stops the template being found.'),
            ]) ?>
            <?= $this->Form->control('subject', ['label' => __('Subject'), 'id' => 'subject']) ?>
            <?= $this->Form->control('body_html', [
                'type' => 'textarea',
                'label' => __('Body (HTML)'),
                'id' => 'body-html',
            ]) ?>
            <?= $this->Form->control('body_text', [
                'type' => 'textarea',
                'label' => __('Body (plain text)'),
                'id' => 'body-text',
                'help' => __('Sent to mail clients that prefer plain text. Left empty, the HTML body is sent with its tags stripped.'),
            ]) ?>
            <?= $this->Form->control('variables', [
                'label' => __('Variables'),
                'id' => 'variables',
                'help' => __('Names the application supplies, as JSON or separated by commas. The preview gives a value to every {{name}} it finds in the text, declared here or not.'),
            ]) ?>
            <?= $this->Form->control('description', ['label' => __('Description')]) ?>
            <?= $this->Form->control('is_active', ['label' => __('Active')]) ?>

            <div style="margin-top: 14px; display: flex; gap: 8px;">
                <?= $this->Form->button(__('Save'), ['class' => 'btn btn-primary']) ?>
                <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>

    <div class="et-panel et-sticky">
        <header>
            <span><?= __('As it will arrive') ?></span>
            <span id="chrome-state" style="font-weight: 400; font-size: 12px; color: #8898aa;"></span>
        </header>
        <p class="et-warn" id="preview-warn" hidden></p>
        <div class="et-subject">
            <span class="et-label"><?= __('Subject') ?>:</span><strong id="preview-subject"></strong>
        </div>
        <iframe id="preview-frame" class="et-preview-frame" title="<?= h(__('Email preview')) ?>"></iframe>
        <div class="et-body" style="border-top: 1px solid #e3e8ee;">
            <div id="variable-chips"></div>
            <p class="et-note"><?= __('Click a name to insert it at the cursor. Values shown are samples.') ?></p>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    // Handed over as JSON rather than interpolated into the script: a template
    // body is arbitrary HTML written by an admin, and it contains </script> as
    // readily as anything else.
    var CHROME = <?= json_encode($previewChrome, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    var DATA = <?= json_encode($previewData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

    var subject = document.getElementById('subject');
    var bodyHtml = document.getElementById('body-html');
    var variables = document.getElementById('variables');
    var frame = document.getElementById('preview-frame');
    var subjectOut = document.getElementById('preview-subject');
    var chromeState = document.getElementById('chrome-state');
    var warn = document.getElementById('preview-warn');
    var chips = document.getElementById('variable-chips');

    if (!bodyHtml || !frame) {
        return;
    }

    /** Replace {{name}} with its sample value; leave unknown names visible. */
    function fill(text) {
        return String(text || '').replace(/\{\{\s*([A-Za-z0-9_]+)\s*\}\}/g, function (whole, name) {
            return Object.prototype.hasOwnProperty.call(DATA, name) ? DATA[name] : '[' + name + ']';
        });
    }

    // The same question EmailTemplatesTable::wrapsInLayout() asks in PHP. Both
    // have to answer it the same way or the preview stops being the email.
    function wrapsInLayout(html) {
        return String(html || '').toLowerCase().indexOf('<html') === -1;
    }

    function render() {
        var raw = bodyHtml.value;
        var filled = fill(raw);
        var wrap = wrapsInLayout(raw);

        frame.srcdoc = wrap ? (CHROME.before + filled + CHROME.after) : filled;

        chromeState.textContent = wrap
            ? <?= json_encode(__('with the standard letterhead')) ?>
            : <?= json_encode(__('sent as written - no letterhead')) ?>;

        if (subjectOut && subject) {
            subjectOut.textContent = fill(subject.value) || <?= json_encode(__('(no subject)')) ?>;
        }

        // A name in the text that the application never supplies arrives at the
        // reader as [name]. Worth saying out loud rather than leaving to be
        // spotted in the preview.
        var unknown = [];
        var seen = {};
        var re = /\{\{\s*([A-Za-z0-9_]+)\s*\}\}/g;
        var hay = raw + '\n' + (subject ? subject.value : '');
        var m;
        while ((m = re.exec(hay)) !== null) {
            if (!Object.prototype.hasOwnProperty.call(DATA, m[1]) && !seen[m[1]]) {
                seen[m[1]] = true;
                unknown.push(m[1]);
            }
        }
        if (unknown.length) {
            warn.textContent = <?= json_encode(__('Not supplied by the application, so it will arrive literally:')) ?>
                + ' ' + unknown.join(', ');
            warn.hidden = false;
        } else {
            warn.hidden = true;
        }
    }

    /** Insert {{name}} where the cursor is in the HTML body. */
    function insert(name) {
        var token = '{{' + name + '}}';
        var start = bodyHtml.selectionStart;
        var end = bodyHtml.selectionEnd;
        if (typeof start !== 'number') {
            bodyHtml.value += token;
        } else {
            bodyHtml.value = bodyHtml.value.slice(0, start) + token + bodyHtml.value.slice(end);
            bodyHtml.selectionStart = bodyHtml.selectionEnd = start + token.length;
        }
        bodyHtml.focus();
        render();
    }

    function drawChips() {
        chips.textContent = '';
        Object.keys(DATA).sort().forEach(function (name) {
            var chip = document.createElement('span');
            chip.className = 'et-chip';
            chip.textContent = '{{' + name + '}}';
            chip.title = DATA[name];
            chip.addEventListener('click', function () { insert(name); });
            chips.appendChild(chip);
        });
    }

    ['input', 'change'].forEach(function (ev) {
        bodyHtml.addEventListener(ev, render);
        if (subject) { subject.addEventListener(ev, render); }
        if (variables) { variables.addEventListener(ev, render); }
    });

    drawChips();
    render();
})();
</script>
