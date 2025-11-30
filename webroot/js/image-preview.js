/**
 * Image Preview with Crop Functionality
 * Uses Cropper.js for image cropping before upload
 * Automatically shows preview when user selects an image file
 */
(function () {
    'use strict';

    // Load Cropper.js CSS and JS dynamically if not already loaded
    if (typeof Cropper === 'undefined') {
        const cropperCSS = document.createElement('link');
        cropperCSS.rel = 'stylesheet';
        cropperCSS.href = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css';
        document.head.appendChild(cropperCSS);

        const cropperJS = document.createElement('script');
        cropperJS.src = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js';
        cropperJS.onload = initImagePreview;
        document.head.appendChild(cropperJS);
    } else {
        document.addEventListener('DOMContentLoaded', initImagePreview);
    }

    function initImagePreview() {
        // Find all file inputs that accept images
        const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');

        imageInputs.forEach(function (input) {
            let cropper = null;
            let originalFile = null;

            // Create preview container
            const previewContainer = document.createElement('div');
            previewContainer.className = 'image-preview-container';
            previewContainer.style.cssText = `
                margin-top: 15px;
                padding: 15px;
                background: #f8f9fa;
                border: 2px dashed #dee2e6;
                border-radius: 8px;
                text-align: center;
                display: none;
            `;

            const previewImg = document.createElement('img');
            previewImg.className = 'image-preview';
            previewImg.style.cssText = `
                max-width: 100%;
                max-height: 400px;
                border: 3px solid #fff;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                margin-bottom: 15px;
                display: block;
            `;

            const previewInfo = document.createElement('div');
            previewInfo.className = 'preview-info';
            previewInfo.style.cssText = `
                margin-bottom: 10px;
                color: #495057;
                font-size: 14px;
            `;

            const cropBtn = document.createElement('button');
            cropBtn.type = 'button';
            cropBtn.className = 'btn-crop-image';
            cropBtn.textContent = '✂️ Crop Gambar';
            cropBtn.style.cssText = `
                background: #28a745;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
                margin-right: 8px;
                font-size: 13px;
            `;

            const changeBtn = document.createElement('button');
            changeBtn.type = 'button';
            changeBtn.className = 'btn-change-image';
            changeBtn.textContent = '🔄 Ganti Gambar';
            changeBtn.style.cssText = `
                background: #007bff;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
                margin-right: 8px;
                font-size: 13px;
            `;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-remove-image';
            removeBtn.textContent = '❌ Hapus';
            removeBtn.style.cssText = `
                background: #dc3545;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 13px;
            `;

            previewContainer.appendChild(previewImg);
            previewContainer.appendChild(previewInfo);
            previewContainer.appendChild(cropBtn);
            previewContainer.appendChild(changeBtn);
            previewContainer.appendChild(removeBtn);

            // Insert after the file input
            input.parentNode.insertBefore(previewContainer, input.nextSibling);

            // Handle file selection
            input.addEventListener('change', function (e) {
                const file = e.target.files[0];

                if (file) {
                    // Validate file type
                    if (!file.type.match('image.*')) {
                        alert('File harus berupa gambar (JPG, PNG, GIF)');
                        input.value = '';
                        return;
                    }

                    // Validate file size (max 5MB)
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    if (file.size > maxSize) {
                        alert('Ukuran file terlalu besar! Maksimal 5MB.\\nUkuran file Anda: ' + formatFileSize(file.size));
                        input.value = '';
                        return;
                    }

                    originalFile = file;

                    // Read and display image
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImg.src = e.target.result;
                        previewInfo.innerHTML = `
                            <strong>✓ Gambar Terpilih:</strong><br>
                            ${escapeHtml(file.name)}<br>
                            <span style="color: #28a745; font-weight: bold;">${formatFileSize(file.size)}</span>
                        `;
                        previewContainer.style.display = 'block';

                        // Add success animation
                        previewContainer.style.animation = 'fadeIn 0.3s ease-in';
                        previewContainer.style.borderColor = '#28a745';

                        // Destroy previous cropper instance
                        if (cropper) {
                            cropper.destroy();
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Crop button - Initialize/Save cropper
            cropBtn.addEventListener('click', function () {
                if (!previewImg.src || previewImg.src.indexOf('data:') !== 0 && previewImg.src.indexOf('http') !== 0) {
                    alert('Pilih gambar terlebih dahulu!');
                    return;
                }

                // Check if we are currently cropping (based on button text/state)
                const isCropping = cropBtn.getAttribute('data-cropping') === 'true';

                if (!isCropping) {
                    // START CROPPING

                    // Destroy existing cropper if any
                    if (cropper) {
                        cropper.destroy();
                    }

                    // Initialize cropper
                    cropper = new Cropper(previewImg, {
                        aspectRatio: 1, // Square crop
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.8,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });

                    // Change button state
                    cropBtn.textContent = '✅ Selesai Crop';
                    cropBtn.style.background = '#17a2b8';
                    cropBtn.setAttribute('data-cropping', 'true');

                } else {
                    // FINISH CROPPING (SAVE)

                    if (!cropper) {
                        return;
                    }

                    // Get cropped canvas
                    const canvas = cropper.getCroppedCanvas({
                        width: 800,
                        height: 800,
                        imageSmoothingQuality: 'high'
                    });

                    if (!canvas) {
                        alert('Gagal memproses gambar. Silakan coba lagi.');
                        return;
                    }

                    // Convert canvas to blob
                    canvas.toBlob(function (blob) {
                        if (!blob) {
                            alert('Gagal menyimpan gambar.');
                            return;
                        }

                        // Create new file from cropped image
                        // Use original filename or default if not available
                        const fileName = originalFile ? originalFile.name : 'cropped-image.jpg';
                        const fileType = originalFile ? originalFile.type : 'image/jpeg';

                        const croppedFile = new File([blob], fileName, {
                            type: fileType,
                            lastModified: Date.now()
                        });

                        // Create new FileList with cropped file
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(croppedFile);
                        input.files = dataTransfer.files;

                        // Update preview
                        previewImg.src = canvas.toDataURL();

                        // Update info
                        const sizeStr = formatFileSize(blob.size);
                        previewInfo.innerHTML = `
                            <strong>✓ Gambar Ter-crop:</strong><br>
                            ${escapeHtml(fileName)}<br>
                            <span style="color: #17a2b8; font-weight: bold;">${sizeStr}</span>
                        `;

                        // Destroy cropper and restore button
                        cropper.destroy();
                        cropper = null;
                        cropBtn.textContent = '✂️ Crop Gambar';
                        cropBtn.style.background = '#28a745';
                        cropBtn.setAttribute('data-cropping', 'false');

                        alert('✅ Gambar berhasil di-crop!');
                    }, originalFile ? originalFile.type : 'image/jpeg');
                }
            });

            // Change image button
            changeBtn.addEventListener('click', function () {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                    cropBtn.textContent = '✂️ Crop Gambar';
                    cropBtn.style.background = '#28a745';
                    cropBtn.onclick = null;
                }
                input.click();
            });

            // Remove image button
            removeBtn.addEventListener('click', function () {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                input.value = '';
                previewContainer.style.display = 'none';
                previewImg.src = '';
                previewInfo.innerHTML = '';
                cropBtn.textContent = '✂️ Crop Gambar';
                cropBtn.style.background = '#28a745';
                cropBtn.onclick = null;
            });
        });
    } // End initImagePreview

    // Helper functions
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

    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-crop-image:hover {
            background: #218838 !important;
            transform: scale(1.05);
        }
        .btn-change-image:hover {
            background: #0056b3 !important;
            transform: scale(1.05);
        }
        .btn-remove-image:hover {
            background: #c82333 !important;
            transform: scale(1.05);
        }
        .cropper-view-box,
        .cropper-face {
            border-radius: 50%;
        }
    `;
    document.head.appendChild(style);
})();
