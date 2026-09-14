<?php
/**
 * Styling and the mermaid loader every process-flow page needs.
 *
 * These pages used to render in the standalone 'process_flow' layout, which
 * supplied eight classes (flow-section, alert-info-custom, workflow-steps,
 * workflow-step, step-number, step-title, step-description, mermaid) and the
 * mermaid script. Each template declares only its own language switcher, so
 * moving a page into the 'elegant' layout - the one carrying the application
 * menu - without this element leaves it unstyled and its diagrams blank.
 *
 * Dashboard/process_flow.ctp already worked that way, with its own copy of the
 * same rules; this is that copy made shareable, so the other ninety templates
 * need one line rather than ninety duplicated stylesheets.
 *
 * append(), not start(): another template or element may already have written
 * to these blocks, and start() would discard what is there.
 *
 * The blocks land in <head> (elegant fetches 'css' and 'script' there), so a
 * template's own <style> - further down, in the body - still wins wherever the
 * two overlap. That is the order the old layout had, which is why the pages
 * look the same as before apart from gaining the menu.
 *
 * Usage, once, near the top of a process_flow.ctp template:
 *     <?= $this->element('process_flow_assets') ?>
 *
 * @var \App\View\AppView $this
 */

$this->append('css');
?>
<style>
    /* Sections */
    .flow-section {
        margin-bottom: 40px;
        padding: 30px;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 5px solid #667eea;
    }

    /* The old layout coloured every h2 on the page; scoped here so the rule
       cannot reach headings on the rest of the application. */
    .flow-section h2 {
        color: #764ba2;
        font-weight: 600;
        margin-top: 0;
        margin-bottom: 20px;
    }

    .alert-info-custom {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border-left: 4px solid #667eea;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }

    /* Numbered steps */
    .workflow-steps {
        margin: 30px 0;
    }

    .workflow-step {
        background: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 5px solid #764ba2;
    }

    .step-number {
        display: inline-block;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        text-align: center;
        line-height: 40px;
        font-weight: bold;
        font-size: 18px;
        margin-right: 15px;
    }

    .step-title {
        font-size: 18px;
        font-weight: 600;
        color: #764ba2;
        margin-bottom: 10px;
    }

    .step-description {
        color: #666;
        line-height: 1.8;
    }

    /* Diagrams */
    .mermaid {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        margin: 20px 0;
        text-align: center;
    }

    @media (max-width: 768px) {
        .flow-section {
            padding: 20px;
        }
    }
</style>
<?php
$this->end();

$this->append('script');
?>
<!-- Mermaid: startOnLoad waits for DOMContentLoaded, so loading it here in the
     head still finds every .mermaid block in the page below. -->
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
    if (window.mermaid) {
        mermaid.initialize({
            startOnLoad: true,
            theme: 'default',
            themeVariables: {
                primaryColor: '#667eea',
                primaryTextColor: '#fff',
                primaryBorderColor: '#764ba2',
                lineColor: '#667eea',
                secondaryColor: '#764ba2',
                tertiaryColor: '#f3e5f5'
            }
        });
    }
</script>
<?php
$this->end();
