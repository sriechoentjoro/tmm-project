<!-- Image Upload with 4:6 Crop Feature -->
<?php
$fieldName = isset($fieldName) ? $fieldName : 'image_photo';
$currentImage = isset($currentImage) ? $currentImage : '';
$label = isset($label) ? $label : 'Photo';
$required = isset($required) ? $required : false;
?>

<div class="image-crop-container">
    <label class="form-label">
        <?= __($label) ?>
        <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
    </label>
    
    <!-- File Input -->
    <input type="file" 
           id="<?= h($fieldName) ?>_input" 
           name="<?= h($fieldName) ?>" 
           class="form-control mb-2" 
           accept="image/jpeg,image/jpg,image/png"
           <?= $required ? 'required' : '' ?>>
    
    <!-- Current Image Preview -->
    <?php if (!empty($currentImage)): ?>
    <div class="current-image-preview mb-3">
        <label class="form-label small text-muted"><?= __('Current Photo') ?></label>
        <div>
            <img src="<?= $this->Url->build('/' . $currentImage) ?>" 
                 alt="Current Photo" 
                 class="img-thumbnail" 
                 style="max-height: 200px;">
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Crop Modal -->
    <div id="<?= h($fieldName) ?>_crop_modal" class="crop-modal" style="display:none;">
        <div class="crop-modal-content">
            <div class="crop-modal-header">
                <h5><?= __('Crop Photo (4:6 Ratio - Passport Size)') ?></h5>
                <button type="button" class="crop-modal-close">&times;</button>
            </div>
            <div class="crop-modal-body">
                <div class="crop-container">
                    <img id="<?= h($fieldName) ?>_crop_image" style="max-width:100%;display:block;">
                </div>
            </div>
            <div class="crop-modal-footer">
                <button type="button" class="btn btn-secondary crop-cancel"><?= __('Cancel') ?></button>
                <button type="button" class="btn btn-primary crop-apply"><?= __('Apply Crop') ?></button>
            </div>
        </div>
    </div>
    
    <!-- Cropped Preview -->
    <div id="<?= h($fieldName) ?>_preview" class="cropped-preview" style="display:none;">
        <label class="form-label small text-success"><?= __('Cropped Preview (will be uploaded)') ?></label>
        <div>
            <img id="<?= h($fieldName) ?>_preview_image" class="img-thumbnail" style="max-height: 200px;">
        </div>
        <button type="button" class="btn btn-sm btn-warning mt-2 recrop-btn">
            <i class="fas fa-crop"></i> <?= __('Re-crop') ?>
        </button>
    </div>
    
    <!-- Hidden input for cropped data -->
    <input type="hidden" id="<?= h($fieldName) ?>_cropped" name="<?= h($fieldName) ?>_cropped">
</div>

<style>
.crop-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
.crop-modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
.crop-modal-header {
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
.crop-modal-header h5 {
    margin: 0;
.crop-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
.crop-modal-close:hover {
    color: #333;
.crop-modal-body {
    padding: 20px;
    flex: 1;
    overflow: auto;
.crop-container {
    max-height: 500px;
    display: flex;
    justify-content: center;
    align-items: center;
.crop-modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #ddd;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
</style>

<script>
(function(){
var fieldName = '<?= h($fieldName) ?>';
var input = document.getElementById(fieldName + '_input');
var modal = document.getElementById(fieldName + '_crop_modal');
var cropImage = document.getElementById(fieldName + '_crop_image');
var preview = document.getElementById(fieldName + '_preview');
var previewImage = document.getElementById(fieldName + '_preview_image');
var croppedInput = document.getElementById(fieldName + '_cropped');
var cropper = null;

// Load Cropper.js if not loaded
if (typeof Cropper === 'undefined') {
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css';
    document.head.appendChild(link);
    
    var script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js';
    document.head.appendChild(script);

// File input change
input.addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (!file || !file.type.match('image.*')) {
        alert('Please select an image file (JPEG or PNG)');
        return;
    
    var reader = new FileReader();
    reader.onload = function(event) {
        cropImage.src = event.target.result;
        modal.style.display = 'flex';
        
        // Wait for Cropper.js to load
        var initCropper = function() {
            if (typeof Cropper !== 'undefined') {
                if (cropper) {
                    cropper.destroy();
                cropper = new Cropper(cropImage, {
                    aspectRatio: 4 / 6, // Passport photo ratio
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    guides: true,
                    center: true,
                    highlight: true,
                    cropBoxResizable: true,
                    cropBoxMovable: true,
                    toggleDragModeOnDblclick: false,
                });
            } else {
                setTimeout(initCropper, 100);
        };
        initCropper();
    };
    reader.readAsDataURL(file);
});

// Apply crop
modal.querySelector('.crop-apply').addEventListener('click', function() {
    if (!cropper) return;
    
    var canvas = cropper.getCroppedCanvas({
        width: 400,  // 4:6 ratio - width 400px
        height: 600, // height 600px
        imageSmoothingQuality: 'high'
    });
    
    canvas.toBlob(function(blob) {
        var url = URL.createObjectURL(blob);
        previewImage.src = url;
        preview.style.display = 'block';
        
        // Convert to base64 for upload
        canvas.toBlob(function(blob) {
            var reader = new FileReader();
            reader.onloadend = function() {
                croppedInput.value = reader.result;
            };
            reader.readAsDataURL(blob);
        }, 'image/jpeg', 0.9);
        
        modal.style.display = 'none';
        if (cropper) {
            cropper.destroy();
            cropper = null;
    }, 'image/jpeg', 0.9);
});

// Cancel crop
modal.querySelector('.crop-cancel').addEventListener('click', function() {
    modal.style.display = 'none';
    input.value = '';
    if (cropper) {
        cropper.destroy();
        cropper = null;
});

// Close button
modal.querySelector('.crop-modal-close').addEventListener('click', function() {
    modal.style.display = 'none';
    input.value = '';
    if (cropper) {
        cropper.destroy();
        cropper = null;
});

// Re-crop button
document.querySelector('#' + fieldName + '_preview').querySelector('.recrop-btn').addEventListener('click', function() {
    input.click();
});
})();
</script>

