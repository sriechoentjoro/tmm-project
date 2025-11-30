/**
 * File Upload Preview with Real-time Preview
 * Automatically shows preview when file is selected
 * 
 * Usage:
 * 1. Add class 'file-upload-preview' to file input
 * 2. Add data-preview-container="container-id" attribute
 * 3. Or use default IDs: file-preview-container, file-preview-content, file-info
 */

(function($) {
    'use strict';
    
    /**
     * Preview file function
     * @param {HTMLInputElement} input - File input element
     * @param {string} containerId - Optional custom container ID
     */
    window.previewFile = function(input, containerId) {
        const previewContainer = document.getElementById(containerId || 'file-preview-container');
        const previewContent = document.getElementById('file-preview-content');
        const fileInfo = document.getElementById('file-info');
        
        if (!previewContainer || !previewContent || !fileInfo) {
            console.warn('File preview elements not found');
            return;
        }
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileName = file.name;
            const fileSize = (file.size / 1024).toFixed(2); // KB
            const fileType = file.type;
            const fileExt = fileName.split('.').pop().toLowerCase();
            
            // Show preview container with animation
            previewContainer.style.display = 'block';
            previewContainer.style.animation = 'slideInUp 0.3s ease-out';
            
            // Display file info
            fileInfo.innerHTML = `
                <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 8px; background: #f8f9fa; border-radius: 4px; margin-top: 10px;">
                    <span><strong>📄 Name:</strong> <span style="color: #495057;">${fileName}</span></span>
                    <span><strong>📦 Size:</strong> <span style="color: #495057;">${fileSize} KB</span></span>
                    <span><strong>🏷️ Type:</strong> <span style="color: #495057; text-transform: uppercase;">${fileExt}</span></span>
                </div>
            `;
            
            // Preview based on file type
            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(fileExt)) {
                // Image preview with FileReader
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContent.innerHTML = `
                        <div style="position: relative; display: inline-block;">
                            <img src="${e.target.result}" 
                                 alt="Preview" 
                                 style="max-width: 100%; max-height: 400px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: transform 0.3s;"
                                 onmouseover="this.style.transform='scale(1.02)'" 
                                 onmouseout="this.style.transform='scale(1)'">
                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6); color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                                ✓ Image Ready
                            </div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
                
            } else if (fileExt === 'pdf') {
                // PDF preview icon
                previewContent.innerHTML = `
                    <div style="background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%); padding: 40px; border-radius: 8px; border: 2px dashed #E74C3C;">
                        <i class="fas fa-file-pdf" style="font-size: 72px; color: #E74C3C; animation: pulse 2s infinite;"></i>
                        <p style="margin-top: 20px; color: #495057; font-weight: 600;"><strong>📕 PDF Document</strong></p>
                        <p style="font-size: 13px; color: #6c757d; margin-top: 10px;">
                            Preview will be available after upload<br>
                            <span style="font-size: 11px; color: #999;">Click save to upload this document</span>
                        </p>
                    </div>
                `;
                
            } else if (['doc', 'docx'].includes(fileExt)) {
                // Word document icon
                previewContent.innerHTML = `
                    <div style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 40px; border-radius: 8px; border: 2px dashed #2980B9;">
                        <i class="fas fa-file-word" style="font-size: 72px; color: #2980B9; animation: pulse 2s infinite;"></i>
                        <p style="margin-top: 20px; color: #495057; font-weight: 600;"><strong>📘 Word Document</strong></p>
                        <p style="font-size: 13px; color: #6c757d; margin-top: 10px;">
                            Preview will be available after upload<br>
                            <span style="font-size: 11px; color: #999;">Click save to upload this document</span>
                        </p>
                    </div>
                `;
                
            } else if (['xls', 'xlsx'].includes(fileExt)) {
                // Excel document icon
                previewContent.innerHTML = `
                    <div style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); padding: 40px; border-radius: 8px; border: 2px dashed #27AE60;">
                        <i class="fas fa-file-excel" style="font-size: 72px; color: #27AE60; animation: pulse 2s infinite;"></i>
                        <p style="margin-top: 20px; color: #495057; font-weight: 600;"><strong>📗 Excel Spreadsheet</strong></p>
                        <p style="font-size: 13px; color: #6c757d; margin-top: 10px;">
                            Preview will be available after upload<br>
                            <span style="font-size: 11px; color: #999;">Click save to upload this spreadsheet</span>
                        </p>
                    </div>
                `;
                
            } else if (['zip', 'rar', '7z', 'tar', 'gz'].includes(fileExt)) {
                // Archive file icon
                previewContent.innerHTML = `
                    <div style="background: linear-gradient(135deg, #fafafa 0%, #e0e0e0 100%); padding: 40px; border-radius: 8px; border: 2px dashed #95A5A6;">
                        <i class="fas fa-file-archive" style="font-size: 72px; color: #95A5A6; animation: pulse 2s infinite;"></i>
                        <p style="margin-top: 20px; color: #495057; font-weight: 600;"><strong>🗜️ Archive File</strong></p>
                        <p style="font-size: 13px; color: #6c757d; margin-top: 10px;">
                            Compressed file ready for upload<br>
                            <span style="font-size: 11px; color: #999;">Contains multiple files in compressed format</span>
                        </p>
                    </div>
                `;
                
            } else if (['txt', 'log', 'csv'].includes(fileExt)) {
                // Text file with content preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const textContent = e.target.result;
                    const preview = textContent.substring(0, 500); // First 500 chars
                    previewContent.innerHTML = `
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px dashed #7F8C8D;">
                            <i class="fas fa-file-alt" style="font-size: 48px; color: #7F8C8D;"></i>
                            <p style="margin: 15px 0 10px; color: #495057; font-weight: 600;"><strong>📄 Text File Preview</strong></p>
                            <div style="text-align: left; margin-top: 15px; padding: 15px; background: white; border-radius: 4px; max-height: 200px; overflow-y: auto; font-family: monospace; font-size: 12px; color: #333; white-space: pre-wrap; word-break: break-word;">
${preview}${textContent.length > 500 ? '\n\n... (truncated)' : ''}
                            </div>
                        </div>
                    `;
                };
                reader.readAsText(file);
                
            } else {
                // Generic file icon
                previewContent.innerHTML = `
                    <div style="background: linear-gradient(135deg, #eceff1 0%, #cfd8dc 100%); padding: 40px; border-radius: 8px; border: 2px dashed #34495E;">
                        <i class="fas fa-file" style="font-size: 72px; color: #34495E; animation: pulse 2s infinite;"></i>
                        <p style="margin-top: 20px; color: #495057; font-weight: 600;"><strong>📎 File Selected</strong></p>
                        <p style="font-size: 13px; color: #6c757d; margin-top: 10px;">
                            Ready for upload<br>
                            <span style="font-size: 11px; color: #999;">Click save to upload this file</span>
                        </p>
                    </div>
                `;
            }
            
        } else {
            // Hide preview if no file selected
            previewContainer.style.display = 'none';
        }
    };
    
    // Auto-initialize on file inputs with class 'file-upload-preview'
    $(document).ready(function() {
        $('.file-upload-preview').on('change', function() {
            const containerId = $(this).data('preview-container') || 'file-preview-container';
            previewFile(this, containerId);
        });
    });
    
    // Add CSS animation keyframes
    if (!document.getElementById('file-preview-animations')) {
        const style = document.createElement('style');
        style.id = 'file-preview-animations';
        style.textContent = `
            @keyframes slideInUp {
                from {
                    transform: translateY(20px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
            @keyframes pulse {
                0%, 100% {
                    transform: scale(1);
                    opacity: 1;
                }
                50% {
                    transform: scale(1.05);
                    opacity: 0.8;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
})(jQuery);
