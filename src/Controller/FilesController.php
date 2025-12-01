<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Network\Exception\NotFoundException;

/**
 * FilesController
 *
 * Lightweight controller to securely serve files from webroot.
 * Usage: /files/.../path/to/file.ext (will call FilesController::view with passed path segments)
 */
class FilesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        // No view rendering for file responses
        $this->autoRender = false;
    }

    /**
     * View action - serves files from webroot
     * Example URL: /files/apprenticeshipOrder/filename.pdf
     */
    public function view()
    {
        $passed = isset($this->request->params['pass']) ? $this->request->params['pass'] : [];
        if (empty($passed)) {
            throw new NotFoundException('File not specified');
        }

        // Build relative path from passed segments
        $relative = implode(DS, $passed);

        // Ensure the path is under WWW_ROOT to avoid traversal
        $fullPath = WWW_ROOT . $relative;
        $real = realpath($fullPath);

        if ($real === false) {
            throw new NotFoundException('File not found');
        }

        $rootReal = realpath(WWW_ROOT);
        if ($rootReal === false || strpos($real, $rootReal) !== 0) {
            // Attempt to access outside webroot
            throw new NotFoundException('Invalid file path');
        }

        if (!file_exists($real) || !is_readable($real)) {
            throw new NotFoundException('File not found or not accessible');
        }

        // Serve file without forcing download; browser will decide how to render
        $this->response->file($real, ['download' => false, 'name' => basename($real)]);
        return $this->response;
    }

    /**
     * Process Flow Documentation
     */
    public function processFlow()
    {
        $this->viewBuilder()->setLayout('process_flow');
    }
}