<?php
namespace App\View\Helper;

use Cake\View\Helper;

/**
 * FileViewer Helper
 * 
 * Helper to display files with icons and modal preview
 */
class FileViewerHelper extends Helper
{
    public $helpers = ['Url'];
    
    /**
     * Display file with icon and modal preview
     * 
     * @param string $filePath Path to the file
     * @param array $options Options array
     *   - label: Display label (default: filename)
     *   - showIcon: Show icon (default: true)
     *   - showModal: Enable modal preview (default: true)
     * @return string HTML output
     */
    public function display($filePath, array $options = array())
    {
        if (empty($filePath)) {
            return '<span style="color: #999; font-style: italic;">No file</span>';
        }
        
        // Default options
        $defaults = array(
            'label' => basename($filePath),
            'showIcon' => true,
            'showModal' => true
        );
        $options = array_merge($defaults, $options);
        
        // Get file extension
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Get icon and color
        $icon = $this->getIcon($ext);
        $color = $this->getColor($ext);
        
        // Check if previewable
        $previewableTypes = array('pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt', 'html');
        $canPreview = in_array($ext, $previewableTypes);
        
        // Build file URL
        $fileUrl = $this->Url->build('/' . $filePath, array('fullBase' => false));
        
        // Generate unique modal ID
        $modalId = 'fileModal_' . md5($filePath . microtime());
        
        // Build HTML
        $html = '<div class="file-viewer-wrapper" style="display: inline-flex; align-items: center; gap: 8px;">';
        
        // Icon
        if ($options['showIcon']) {
            $html .= '<span class="file-icon" style="color: ' . $color . '; display: inline-flex; align-items: center;">';
            $html .= $icon;
            $html .= '</span>';
        }
        
        // Link
        if ($options['showModal'] && $canPreview) {
            $html .= '<a href="javascript:void(0);" data-toggle="modal" data-target="#' . $modalId . '" class="file-link" style="color: ' . $color . '; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">';
            $html .= '<span>' . h($options['label']) . '</span>';
            $html .= '<small style="opacity: 0.7;">[.' . $ext . ']</small>';
            $html .= '</a>';
        } else {
            $html .= '<a href="' . $fileUrl . '" target="_blank" download class="file-link" style="color: ' . $color . '; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">';
            $html .= '<span>' . h($options['label']) . '</span>';
            $html .= '<small style="opacity: 0.7;">[.' . $ext . ']</small>';
            $html .= $this->getDownloadIcon();
            $html .= '</a>';
        }
        
        $html .= '</div>';
        
        // Modal
        if ($options['showModal'] && $canPreview) {
            $html .= $this->buildModal($modalId, $fileUrl, $options['label'], $ext, $icon, $color);
        }
        
        return $html;
    }
    
    /**
     * Get icon SVG for file extension
     * 
     * @param string $ext File extension
     * @return string SVG icon
     */
    protected function getIcon($ext)
    {
        $icons = array(
            'pdf' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M10 12h4"></path><path d="M10 16h4"></path></svg>',
            'doc' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M16 13H8"></path><path d="M16 17H8"></path><path d="M10 9H8"></path></svg>',
            'xls' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><line x1="12" y1="13" x2="12" y2="17"></line></svg>',
            'jpg' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>',
            'zip' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>',
        );
        
        // Add aliases
        $icons['docx'] = $icons['doc'];
        $icons['xlsx'] = $icons['xls'];
        $icons['jpeg'] = $icons['jpg'];
        $icons['png'] = $icons['jpg'];
        $icons['gif'] = $icons['jpg'];
        $icons['rar'] = $icons['zip'];
        
        return isset($icons[$ext]) ? $icons[$ext] : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';
    }
    
    /**
     * Get color for file extension
     * 
     * @param string $ext File extension
     * @return string Color hex code
     */
    protected function getColor($ext)
    {
        $colors = array(
            'pdf' => '#E74C3C',
            'doc' => '#2980B9',
            'docx' => '#2980B9',
            'xls' => '#27AE60',
            'xlsx' => '#27AE60',
            'jpg' => '#9B59B6',
            'jpeg' => '#9B59B6',
            'png' => '#9B59B6',
            'gif' => '#9B59B6',
            'zip' => '#95A5A6',
            'rar' => '#95A5A6',
            'txt' => '#7F8C8D',
        );
        
        return isset($colors[$ext]) ? $colors[$ext] : '#34495E';
    }
    
    /**
     * Get download icon SVG
     * 
     * @return string SVG icon
     */
    protected function getDownloadIcon()
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.5;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>';
    }
    
    /**
     * Build modal HTML
     * 
     * @param string $modalId Modal ID
     * @param string $fileUrl File URL
     * @param string $label Label
     * @param string $ext File extension
     * @param string $icon Icon SVG
     * @param string $color Color
     * @return string Modal HTML
     */
    protected function buildModal($modalId, $fileUrl, $label, $ext, $icon, $color)
    {
        $isImage = in_array($ext, array('jpg', 'jpeg', 'png', 'gif'));
        
        $html = '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog">';
        $html .= '<div class="modal-dialog modal-lg" role="document" style="max-width: 90%; margin: 1.75rem auto;">';
        $html .= '<div class="modal-content">';
        $html .= '<div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">';
        $html .= '<h5 class="modal-title"><span style="color: ' . $color . '; margin-right: 8px;">' . $icon . '</span>' . h($label) . '</h5>';
        $html .= '<button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.9;"><span>&times;</span></button>';
        $html .= '</div>';
        $html .= '<div class="modal-body" style="padding: 0; height: 80vh;">';
        
        if ($isImage) {
            $html .= '<img src="' . $fileUrl . '" alt="' . h($label) . '" style="width: 100%; height: 100%; object-fit: contain; background: #f8f9fa;">';
        } else {
            $html .= '<iframe src="' . $fileUrl . '" style="width: 100%; height: 100%; border: none;"></iframe>';
        }
        
        $html .= '</div>';
        $html .= '<div class="modal-footer">';
        $html .= '<a href="' . $fileUrl . '" target="_blank" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">Open in New Tab</a>';
        $html .= '<a href="' . $fileUrl . '" download class="btn btn-success btn-sm">Download</a>';
        $html .= '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}
