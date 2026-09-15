<?php
/**
 * The plain-text body of an email written in the email_templates table.
 *
 * body_text if the author wrote one; otherwise EmailComponent passes the HTML
 * body with its tags stripped, so a client that prefers text/plain gets the
 * words rather than an empty message.
 *
 * @var \App\View\AppView $this
 * @var string $textContent
 */
echo $textContent;
