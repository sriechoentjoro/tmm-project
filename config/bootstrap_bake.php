<?php
/**
 * Bootstrap file for Bake plugin configuration
 * This ensures custom templates are loaded from src/Template/Bake/
 */

use Cake\Event\EventManager;

/**
 * Configure Bake to use application templates
 */
EventManager::instance()->on('Bake.initialize', function ($event) {
    /** @var \Bake\View\BakeView $view */
    $view = $event->getSubject();
    
    // Add custom template paths for Bake plugin
    // This allows bake to use templates from src/Template/Bake/ instead of vendor
    $view->set('_paths', [
        ROOT . DS . 'src' . DS . 'Template' . DS . 'Bake' . DS,
        CAKE . 'Template' . DS . 'Bake' . DS
    ]);
    
    echo "\n";
    echo "============================================\n";
    echo "Bake Custom Templates Path Set!\n";
    echo "Primary: " . ROOT . DS . 'src' . DS . 'Template' . DS . 'Bake' . DS . "\n";
    echo "============================================\n";
    echo "\n";
});
