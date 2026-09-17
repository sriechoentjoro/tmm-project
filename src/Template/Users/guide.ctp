<?php
/**
 * The TMM process guide, as shown to someone who is not signed in.
 *
 * Reachable from the login page, so it renders in the standalone process_flow
 * layout - that layout carries its own container, language switcher, back
 * button and mermaid loader, which is why none of them appear here.
 *
 * The document itself is the system_process_guide element, shared with
 * Dashboard::processFlow(). This page used to carry its own copy, and the two
 * drifted: this one already described sharing an order with an LPK while no
 * such code existed, and it knew nothing of how an institution is onboarded.
 *
 * No $userRoles is passed, and there is nothing to pass: nobody is signed in,
 * so no phase is marked as the reader's own.
 *
 * @var \App\View\AppView $this
 */
$this->assign('title', __('System Process Guide'));
?>
<?= $this->element('system_process_guide') ?>
