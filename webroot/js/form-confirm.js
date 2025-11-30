/**
 * Form Confirmation with Preview Modal
 * Auto-detects forms with data-confirm="true" attribute
 */
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Find all forms with data-confirm attribute
        const forms = document.querySelectorAll('form[data-confirm="true"]');
        
        forms.forEach(function(form) {
            // Named function to avoid arguments.callee in strict mode
            function handleFormSubmit(e) {
                e.preventDefault();
                
                // Collect form data
                const formData = new FormData(form);
                let previewHtml = '<table class="table table-bordered"><tbody>';
                
                // Build preview from form fields
                const inputs = form.querySelectorAll('input:not([type="file"]):not([type="submit"]):not([type="hidden"]), select, textarea');
                inputs.forEach(function(input) {
                    const label = getFieldLabel(input);
                    let value = getFieldValue(input);
                    
                    if (value && label !== '_Token') {
                        previewHtml += '<tr><th style="width: 30%">' + escapeHtml(label) + '</th><td>' + escapeHtml(value) + '</td></tr>';
                    }
                });
                
                // Handle file inputs separately
                const fileInputs = form.querySelectorAll('input[type="file"]');
                fileInputs.forEach(function(input) {
                    if (input.files.length > 0) {
                        const label = getFieldLabel(input);
                        const fileName = input.files[0].name;
                        const fileSize = formatFileSize(input.files[0].size);
                        previewHtml += '<tr><th style="width: 30%">' + escapeHtml(label) + '</th><td>' + escapeHtml(fileName) + ' (' + fileSize + ')</td></tr>';
                    }
                });
                
                previewHtml += '</tbody></table>';
                
                // Show modal
                showConfirmModal(previewHtml, function() {
                    // User confirmed - submit the form
                    form.removeEventListener('submit', handleFormSubmit);
                    form.submit();
                });
            }
            
            form.addEventListener('submit', handleFormSubmit);
        });
    }); // End DOMContentLoaded
    
    // Helper functions
    function getFieldLabel(input) {
        // Try to find label
        const id = input.id;
        if (id) {
            const label = document.querySelector('label[for="' + id + '"]');
            if (label) return label.textContent.trim();
        }
        
        // Try parent label
        const parentLabel = input.closest('label');
        if (parentLabel) return parentLabel.textContent.trim();
        
        // Fallback to name
        return input.name || 'Unknown';
    }
    
    function getFieldValue(input) {
        if (input.tagName === 'SELECT') {
            return input.options[input.selectedIndex]?.text || '';
        } else if (input.type === 'checkbox') {
            return input.checked ? 'Yes' : 'No';
        } else if (input.type === 'radio') {
            if (input.checked) return input.value;
            return '';
        }
        return input.value;
    }
    
    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function showConfirmModal(content, onConfirm) {
        // Create modal HTML
        const modalHtml = `
            <div id="formConfirmModal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                <div style="
                    background: white;
                    padding: 30px;
                    border-radius: 8px;
                    max-width: 600px;
                    max-height: 80vh;
                    overflow-y: auto;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                ">
                    <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                        Konfirmasi Data
                    </h3>
                    <div style="margin: 20px 0;">
                        ${content}
                    </div>
                    <div style="text-align: right; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px;">
                        <button id="modalCancel" type="button" style="
                            background: #6c757d;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 4px;
                            cursor: pointer;
                            margin-right: 10px;
                            font-size: 14px;
                        ">Batal</button>
                        <button id="modalConfirm" type="button" style="
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 14px;
                            font-weight: bold;
                        ">Simpan Data</button>
                    </div>
                </div>
            </div>
        `;
        
        // Insert modal
        const modalContainer = document.createElement('div');
        modalContainer.innerHTML = modalHtml;
        document.body.appendChild(modalContainer);
        
        // Add event listeners
        const confirmBtn = document.getElementById('modalConfirm');
        const cancelBtn = document.getElementById('modalCancel');
        
        confirmBtn.addEventListener('click', function() {
            // Disable button and show loading state
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            cancelBtn.disabled = true;
            
            // Show full screen overlay
            const overlay = document.createElement('div');
            overlay.className = 'form-overlay';
            overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center; color: white; flex-direction: column;';
            overlay.innerHTML = '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); color: #333; text-align: center;"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><div class="mt-2">Saving data, please wait...</div></div>';
            document.body.appendChild(overlay);
            
            // Remove modal after a short delay to allow overlay to appear
            setTimeout(function() {
                document.body.removeChild(modalContainer);
                onConfirm();
            }, 100);
        });
        
        cancelBtn.addEventListener('click', function() {
            document.body.removeChild(modalContainer);
        });
        
        // Close on backdrop click
        document.getElementById('formConfirmModal').addEventListener('click', function(e) {
            if (e.target.id === 'formConfirmModal') {
                document.body.removeChild(modalContainer);
            }
        });
    }
})();
