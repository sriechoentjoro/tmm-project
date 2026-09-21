<?php
/**
 * The guide behind the "?" button for this module.
 *
 * The content lives in config/page_guides/LpkRegistration.php; Element/page_guide.ctp
 * renders it. The 250-line template that used to sit here was one of 89
 * identical copies whose only module-specific word was the entity name.
 *
 * @var \App\View\AppView $this
 */
echo $this->element('page_guide', ['module' => 'LpkRegistration']);
