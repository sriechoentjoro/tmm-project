<?php
/**
 * The guide behind the "?" button for this module.
 *
 * Content lives in config/page_guides/EmailTemplates.php; Element/page_guide.ctp
 * renders it.
 *
 * @var \App\View\AppView $this
 */
echo $this->element('page_guide', ['module' => 'EmailTemplates']);
