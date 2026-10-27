<?php
/**
 * The CV fragment the live preview swaps in.
 *
 * Rendered in the ajax layout, so it is the document and nothing else: the
 * same element /candidates/cv/{id} renders, built from what is on the
 * registration form at that moment.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate $cvCandidate
 */
?>
<?= $this->element('Candidates/cv_body', [
    'candidate' => $cvCandidate,
    'showToolbar' => false,
]) ?>
