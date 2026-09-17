<?php
/**
 * The HTML body of an email written in the email_templates table.
 *
 * The body is authored as HTML by an administrator and has already had its
 * {{variables}} substituted by EmailTemplate::render(), so it is echoed
 * unchanged. h() would print the markup rather than render it.
 *
 * Not Email/html/default.ctp, which wraps every line of $content in <p> - that
 * is for plain text arriving through the html view, and it mangles real markup.
 *
 * Whether the branded letterhead goes around this is decided before rendering,
 * by EmailComponent, from EmailTemplatesTable::wrapsInLayout().
 *
 * @var \App\View\AppView $this
 * @var string $content
 */
echo $content;
