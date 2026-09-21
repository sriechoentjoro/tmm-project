<?php
/**
 * The guide behind the "?" button for this module.
 *
 * Content lives in config/page_guides/TraineeSubmissionDocuments.php; Element/page_guide.ctp
 * renders it. This module had no guide page at all before - no action, no
 * template, and no "?" button on its screens.
 *
 * @var \App\View\AppView $this
 */
echo $this->element('page_guide', ['module' => 'TraineeSubmissionDocuments']);
