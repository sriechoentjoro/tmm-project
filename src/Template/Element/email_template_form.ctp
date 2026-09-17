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
            <?php
            /**
             * Every control says its own type and size.
             *
             * Both matter. FormHelper picks the control from the column type,
             * so a template_key declared TEXT rather than VARCHAR silently
             * becomes a five-row textarea for a one-word key - which is what
             * happened on a test schema and would happen on any install whose
             * columns drifted. And rows= gives each box a usable height even
             * where the stylesheet below never loads.
             *
             * There is no 'help' option in this version of FormHelper: unknown
             * options are passed through as HTML attributes, so the first
             * version of this form emitted help="..." inside the tags and
             * showed the reader nothing. The notes are written out by hand.
             */
            ?>
            <?= $this->Form->create($emailTemplate) ?>
            <?= $this->Form->control('template_key', [
                'type' => 'text',
                'label' => __('Template Key'),
            ]) ?>
            <small class="et-note"><?= __('The key the application asks for. Changing it stops the template being found.') ?></small>

            <?= $this->Form->control('subject', [
                'type' => 'text',
                'label' => __('Subject'),
                'id' => 'subject',
            ]) ?>

            <?= $this->Form->control('body_html', [
                'type' => 'textarea',
                'label' => __('Body (HTML)'),
                'id' => 'body-html',
                'rows' => 18,
            ]) ?>

            <?= $this->Form->control('body_text', [
                'type' => 'textarea',
                'label' => __('Body (plain text)'),
                'id' => 'body-text',
                'rows' => 8,
            ]) ?>
            <small class="et-note"><?= __('Sent to mail clients that prefer plain text. Left empty, the HTML body is sent with its tags stripped.') ?></small>

            <?= $this->Form->control('variables', [
                'type' => 'textarea',
                'label' => __('Variables'),
                'id' => 'variables',
                'rows' => 3,
            ]) ?>
            <small class="et-note"><?= __('Names the application supplies, as JSON or separated by commas. The preview gives a value to every {{name}} it finds in the text, declared here or not.') ?></small>

            <?= $this->Form->control('description', [
                'type' => 'text',
                'label' => __('Description'),
            ]) ?>
            <?php
            // Explicit for the same reason as the rest: is_active is a flag,
            // and a column declared INTEGER rather than TINYINT(1) turns it
            // into a number spinner asking the admin to type 1.
            ?>
            <?= $this->Form->control('is_active', ['type' => 'checkbox', 'label' => __('Active')]) ?>

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

    // JSON_HEX_TAG, and no closing script tag written literally anywhere in
    // this block - including in a comment.
    //
    // The first version of this file explained, in a comment here, that a
    // template body can contain a closing script tag as readily as anything
    // else. The comment spelled that tag out, and the HTML parser did what it
    // is supposed to do: it ended the script element mid-sentence. The rest of
    // the JavaScript rendered as text on the page and the email letterhead
    // rendered as markup below it.
    //
    // JSON_HEX_TAG turns every angle bracket into a unicode escape, so no
    // amount of markup in the letterhead or in an admin's body can end this
    // element either.
    var CHROME = <?= json_encode($previewChrome, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    var DATA = <?= json_encode($previewData, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

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
            ? <?= json_encode(__('with the standard letterhead'), JSON_HEX_TAG) ?>
            : <?= json_encode(__('sent as written - no letterhead'), JSON_HEX_TAG) ?>;

        if (subjectOut && subject) {
            subjectOut.textContent = fill(subject.value) || <?= json_encode(__('(no subject)'), JSON_HEX_TAG) ?>;
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
            warn.textContent = <?= json_encode(__('Not supplied by the application, so it will arrive literally:'), JSON_HEX_TAG) ?>
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
