<?php
/**
 * One candidate's curriculum vitae.
 *
 * The document itself lives in Element/Candidates/cv_body.ctp, because the
 * live preview beside the registration wizard renders the same element: two
 * copies of a bilingual CV would be two copies to keep in step.
 *
 * Rendered in the elegant layout (/candidates/cv/{id}) and in the print
 * layout (/candidates/export-cv-pdf/{id}), so all styling is self-contained.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate $candidate
 */
?>
<?= $this->element('Candidates/cv_body', [
    'candidate' => $candidate,
    'showToolbar' => true,
]) ?>
