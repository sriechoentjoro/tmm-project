<?php
/**
 * @var \App\View\AppView $this
 * @var int $step
 * @var array $wizardData
 */
?>
<div class="candidates wizard content">
    <div class="row">
        <div class="col-md-12">
            <!-- Wizard Progress Bar -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h3 class="text-center mb-4">
                        <i class="fa fa-user-plus"></i> Candidate Registration Wizard
                    </h3>
                    
                    <div class="progress" style="height: 35px;">
                        <div class="progress-bar <?= $step >= 1 ? 'bg-success' : 'bg-secondary' ?>" style="width: 11.11%">
                            <small>1. ID</small>
                        </div>
                        <div class="progress-bar <?= $step >= 2 ? 'bg-success' : 'bg-secondary' ?>" style="width: 11.11%">
                            <small>2. Photo</small>
                        </div>
                        <div class="progress-bar <?= $step >= 3 ? 'bg-success' : 'bg-secondary' ?>" style="width: 11.11%">
                            <small>3. Info</small>
                        </div>
                        <div class="progress-bar <?= $step >= 4 ? 'bg-success' : 'bg-secondary' ?>" style="width: 11.11%">
                            <small>4. Edu</small>
                        </div>
                        <div class="progress-bar <?= $step >= 5 ? 'bg-success' : 'bg-secondary' ?>" style="width: 11.11%">
                            <small>5. Exp</small>
                        </div>
                        <div class="progress-bar <?= $step >= 6 ? 'bg-success' : 'bg-secondary' ?>" style="width: 11.11%">
                            <small>6. Family</small>
                        </div>
                        <div class="progress-bar <?= $step >= 7 ? 'bg-success' : 'bg-secondary' ?>" style="width: 11.11%">
                            <small>7. Cert</small>
                        </div>
                        <div class="progress-bar <?= $step >= 8 ? 'bg-success' : 'bg-secondary' ?>" style="width: 11.11%">
                            <small>8. Course</small>
                        </div>
                        <div class="progress-bar <?= $step >= 9 ? 'bg-success' : 'bg-secondary' ?>" style="width: 11.11%">
                            <small>9. Review</small>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <strong>Step <?= $step ?> of 9</strong>
                    </div>
                </div>
            </div>

            <!-- Step Content -->
            <?php if ($step == 1): ?>
                <!-- ========== STEP 1: Identity Number Check ========== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h5 class="m-0"><i class="fa fa-id-card"></i> Step 1: Identity Number Verification</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Duplicate Prevention</h5>
                            <p class="mb-0">Enter the candidate's <strong>Identity Number (NIK/KTP)</strong> to check if they are already registered in the system.</p>
                        </div>

                        <?= $this->Form->create(null, ['url' => ['action' => 'wizard', '?' => ['step' => 1]]]) ?>
                        
                        <div class="form-group">
                            <label for="identity-number">Identity Number (NIK/KTP) <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="identity_number" 
                                   id="identity-number" 
                                   class="form-control form-control-lg" 
                                   placeholder="Enter 16-digit NIK" 
                                   maxlength="16"
                                   pattern="[0-9]{16}"
                                   value="<?= h($wizardData['identity_number'] ?? '') ?>"
                                   required>
                            <small class="form-text text-muted">
                                <i class="fa fa-check-circle text-success"></i> Must be exactly 16 digits<br>
                                <i class="fa fa-check-circle text-success"></i> Numbers only
                            </small>
                        </div>

                        <?php if (isset($existingCandidate)): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fa fa-exclamation-triangle"></i> Duplicate Found!</h5>
                                <p><strong>This identity number is already registered:</strong></p>
                                <table class="table table-sm table-bordered bg-white">
                                    <tr>
                                        <th width="30%">Name</th>
                                        <td><?= h($existingCandidate->name) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td><?= h($existingCandidate->birth_date) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Registered</th>
                                        <td><?= h($existingCandidate->created) ?></td>
                                    </tr>
                                </table>
                                <?= $this->Html->link('View Candidate Details', ['action' => 'view', $existingCandidate->id], ['class' => 'btn btn-info']) ?>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <?= $this->Form->button(__('<i class="fa fa-check"></i> Check & Continue'), ['class' => 'btn btn-primary btn-lg', 'escape' => false]) ?>
                            <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>

            <?php elseif ($step == 2): ?>
                <!-- ========== STEP 2: Photo Upload & Crop ========== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h5 class="m-0"><i class="fa fa-camera"></i> Step 2: Upload Passport Photo</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Photo Requirements</h5>
                            <ul class="mb-0">
                                <li><strong>Format:</strong> JPG, PNG, or GIF</li>
                                <li><strong>Size:</strong> Maximum 5MB</li>
                                <li><strong>Style:</strong> Passport photo (square, 1:1 aspect ratio)</li>
                                <li><strong>Quality:</strong> Clear, well-lit, facing camera</li>
                            </ul>
                        </div>

                        <?= $this->Form->create(null, ['url' => ['action' => 'wizard', '?' => ['step' => 2]], 'id' => 'photo-upload-form']) ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">1. Select Photo</h5>
                                <div class="form-group">
                                    <label for="photo-input">Choose Photo File</label>
                                    <input type="file" 
                                           id="photo-input" 
                                           class="form-control-file" 
                                           accept="image/*">
                                    <small class="form-text text-muted">
                                        <i class="fa fa-lightbulb"></i> Select a clear passport-style photo
                                    </small>
                                </div>
                                
                                <div id="image-container" style="max-width: 100%; display: none;">
                                    <img id="image-preview" style="max-width: 100%;">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">2. Crop Photo (Square)</h5>
                                <div id="cropped-preview-container" style="display: none;">
                                    <p class="text-muted"><i class="fa fa-scissors"></i> Cropped Preview:</p>
                                    <div style="width: 200px; height: 200px; border: 2px solid #ddd; overflow: hidden; margin: 0 auto;">
                                        <img id="cropped-preview" style="width: 100%;">
                                    </div>
                                    <p class="text-center mt-2">
                                        <small class="text-success"><i class="fa fa-check-circle"></i> Square passport photo ready</small>
                                    </p>
                                </div>
                                <div id="crop-instructions" class="alert alert-warning">
                                    <i class="fa fa-hand-pointer"></i> <strong>Instructions:</strong>
                                    <ol class="mb-0">
                                        <li>Select a photo file from your device</li>
                                        <li>Drag the crop box to position</li>
                                        <li>Resize to include face clearly</li>
                                        <li>Click "Crop & Continue" when ready</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="image_photo_cropped" id="cropped-image-data">

                        <div class="mt-4">
                            <?= $this->Html->link(__('<i class="fa fa-arrow-left"></i> Back'), ['action' => 'wizard', '?' => ['step' => 1]], ['class' => 'btn btn-secondary', 'escape' => false]) ?>
                            <button type="button" id="crop-button" class="btn btn-success btn-lg" disabled>
                                <i class="fa fa-crop"></i> Crop & Continue
                            </button>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>

                <!-- Cropper.js CSS & JS -->
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
                
                <script>
                let cropper;
                const photoInput = document.getElementById('photo-input');
                const imageContainer = document.getElementById('image-container');
                const imagePreview = document.getElementById('image-preview');
                const croppedPreview = document.getElementById('cropped-preview');
                const croppedPreviewContainer = document.getElementById('cropped-preview-container');
                const croppedImageData = document.getElementById('cropped-image-data');
                const cropButton = document.getElementById('crop-button');
                const photoForm = document.getElementById('photo-upload-form');

                photoInput.addEventListener('change', function(e) {
                    const files = e.target.files;
                    if (files && files.length > 0) {
                        const file = files[0];
                        
                        // Validate file size (5MB max)
                        if (file.size > 5 * 1024 * 1024) {
                            alert('File size must be less than 5MB');
                            return;
                        
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            imagePreview.src = event.target.result;
                            imageContainer.style.display = 'block';
                            
                            // Destroy previous cropper if exists
                            if (cropper) {
                                cropper.destroy();
                            
                            // Initialize Cropper.js with square aspect ratio
                            cropper = new Cropper(imagePreview, {
                                aspectRatio: 1, // Square (1:1)
                                viewMode: 1,
                                autoCropArea: 0.8,
                                responsive: true,
                                guides: true,
                                center: true,
                                highlight: true,
                                cropBoxResizable: true,
                                cropBoxMovable: true,
                                crop: function(event) {
                                    // Update preview on crop
                                    updateCroppedPreview();
                            });
                            
                            cropButton.disabled = false;
                            croppedPreviewContainer.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                });

                function updateCroppedPreview() {
                    if (cropper) {
                        const canvas = cropper.getCroppedCanvas({
                            width: 400,
                            height: 400
                        });
                        croppedPreview.src = canvas.toDataURL();

                cropButton.addEventListener('click', function() {
                    if (cropper) {
                        const canvas = cropper.getCroppedCanvas({
                            width: 400,
                            height: 400
                        });
                        
                        // Convert to base64
                        const croppedData = canvas.toDataURL('image/jpeg', 0.9);
                        croppedImageData.value = croppedData;
                        
                        // Submit form
                        photoForm.submit();
                    } else {
                        alert('Please select and crop a photo first');
                });
                </script>

            <?php elseif ($step == 3): ?>
                <!-- ========== STEP 3: Complete Candidate Information ========== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h5 class="m-0"><i class="fa fa-user-edit"></i> Step 3: Complete Candidate Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Required Fields</h5>
                            <p class="mb-0">Fields marked with <span class="text-danger">*</span> are required. Please fill all information accurately.</p>
                        </div>

                        <?php if (isset($validationErrors) && !empty($validationErrors)): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fa fa-exclamation-triangle"></i> Please fix the following errors:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($validationErrors as $error): ?>
                                        <li><?= h($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?= $this->Form->create(null, ['url' => ['action' => 'wizard', '?' => ['step' => 3]]]) ?>
                        
                        <!-- Basic Information -->
                        <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-user"></i> Basic Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="<?= h($wizardData['name'] ?? '') ?>" required>
                                    <small class="text-muted"><i class="fa fa-check-circle text-success"></i> As per identity card</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name (Katakana)</label>
                                    <input type="text" name="name_katakana" class="form-control" value="<?= h($wizardData['name_katakana'] ?? '') ?>">
                                    <small class="text-muted">Japanese katakana format (optional)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Place of Birth <span class="text-danger">*</span></label>
                                    <input type="text" name="birth_place" class="form-control" value="<?= h($wizardData['birth_place'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Birth Place (Katakana)</label>
                                    <input type="text" name="birth_place_katakana" class="form-control" value="<?= h($wizardData['birth_place_katakana'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="birth_date" class="form-control" value="<?= h($wizardData['birth_date'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Gender <span class="text-danger">*</span></label>
                                    <select name="master_gender_id" class="form-control" required>
                                        <option value="">Select Gender</option>
                                        <?php foreach ($masterGenders as $id => $name): ?>
                                            <option value="<?= $id ?>" <?= (isset($wizardData['master_gender_id']) && $wizardData['master_gender_id'] == $id) ? 'selected' : '' ?>><?= h($name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Blood Type</label>
                                    <select name="master_blood_type_id" class="form-control">
                                        <option value="">Select Blood Type</option>
                                        <?php foreach ($masterBloodTypes as $id => $name): ?>
                                            <option value="<?= $id ?>" <?= (isset($wizardData['master_blood_type_id']) && $wizardData['master_blood_type_id'] == $id) ? 'selected' : '' ?>><?= h($name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Religion <span class="text-danger">*</span></label>
                                    <select name="master_religion_id" class="form-control" required>
                                        <option value="">Select Religion</option>
                                        <?php foreach ($masterReligions as $id => $name): ?>
                                            <option value="<?= $id ?>" <?= (isset($wizardData['master_religion_id']) && $wizardData['master_religion_id'] == $id) ? 'selected' : '' ?>><?= h($name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Marriage Status <span class="text-danger">*</span></label>
                                    <select name="master_marriage_status_id" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <?php foreach ($masterMarriageStatuses as $id => $name): ?>
                                            <option value="<?= $id ?>" <?= (isset($wizardData['master_marriage_status_id']) && $wizardData['master_marriage_status_id'] == $id) ? 'selected' : '' ?>><?= h($name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-phone"></i> Contact Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile Phone</label>
                                    <input type="tel" name="telephone_mobile" class="form-control" value="<?= h($wizardData['telephone_mobile'] ?? '') ?>" maxlength="12">
                                    <small class="text-muted">e.g., 081234567890</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Emergency Contact <span class="text-danger">*</span></label>
                                    <input type="tel" name="telephone_emergency" class="form-control" value="<?= h($wizardData['telephone_emergency'] ?? '') ?>" required>
                                    <small class="text-muted"><i class="fa fa-check-circle text-success"></i> Family or close contact</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= h($wizardData['email'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-map-marker"></i> Address</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Province</label>
                                    <select name="master_propinsi_id" id="master-propinsi-id" class="form-control location-select">
                                        <option value="">Select Province</option>
                                        <?php foreach ($masterPropinsis as $id => $name): ?>
                                            <option value="<?= $id ?>" <?= (isset($wizardData['master_propinsi_id']) && $wizardData['master_propinsi_id'] == $id) ? 'selected' : '' ?>><?= h($name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kabupaten / City</label>
                                    <select name="master_kabupaten_id" id="master-kabupaten-id" class="form-control location-select" disabled>
                                        <option value="">Select Kabupaten</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <select name="master_kecamatan_id" id="master-kecamatan-id" class="form-control location-select" disabled>
                                        <option value="">Select Kecamatan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelurahan</label>
                                    <select name="master_kelurahan_id" id="master-kelurahan-id" class="form-control location-select" disabled>
                                        <option value="">Select Kelurahan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Postal Code</label>
                                    <input type="text" name="post_code" class="form-control" value="<?= h($wizardData['post_code'] ?? '') ?>" maxlength="5">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Address <span class="text-danger">*</span></label>
                                    <textarea name="address" id="address-field" class="form-control" rows="3" required><?= h($wizardData['address'] ?? '') ?></textarea>
                                    <small class="text-muted"><i class="fa fa-check-circle text-success"></i> Street, RT/RW</small>
                                </div>
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const propinsiSelect = document.getElementById('master-propinsi-id');
                            if (!propinsiSelect) return; // Exit if not on Step 3

                            const kabupatenSelect = document.getElementById('master-kabupaten-id');
                            const kecamatanSelect = document.getElementById('master-kecamatan-id');
                            const kelurahanSelect = document.getElementById('master-kelurahan-id');
                            const addressField = document.getElementById('address-field');

                            // Pre-selected values from session
                            const selectedKabupaten = "<?= $wizardData['master_kabupaten_id'] ?? '' ?>";
                            const selectedKecamatan = "<?= $wizardData['master_kecamatan_id'] ?? '' ?>";
                            const selectedKelurahan = "<?= $wizardData['master_kelurahan_id'] ?? '' ?>";

                            // Helper to fetch and populate dropdowns
                            function loadDropdown(url, targetSelect, selectedValue, callback) {
                                targetSelect.disabled = true;
                                targetSelect.innerHTML = '<option value="">Loading...</option>';
                                
                                fetch(url)
                                    .then(response => response.json())
                                    .then(data => {
                                        targetSelect.innerHTML = '<option value="">Select Option</option>';
                                        for (const [id, name] of Object.entries(data)) {
                                            const option = document.createElement('option');
                                            option.value = id;
                                            option.textContent = name;
                                            if (id == selectedValue) {
                                                option.selected = true;
                                            targetSelect.appendChild(option);
                                        targetSelect.disabled = false;
                                        if (callback) callback();
                                        updateAddress();
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        targetSelect.innerHTML = '<option value="">Error loading data</option>';
                                    });

                            // Event Listeners
                            propinsiSelect.addEventListener('change', function() {
                                const id = this.value;
                                kabupatenSelect.innerHTML = '<option value="">Select Kabupaten</option>';
                                kecamatanSelect.innerHTML = '<option value="">Select Kecamatan</option>';
                                kelurahanSelect.innerHTML = '<option value="">Select Kelurahan</option>';
                                kabupatenSelect.disabled = true;
                                kecamatanSelect.disabled = true;
                                kelurahanSelect.disabled = true;

                                if (id) {
                                    loadDropdown('<?= $this->Url->build(['controller' => 'Candidates', 'action' => 'getKabupaten']) ?>?propinsi_id=' + id, kabupatenSelect);
                                updateAddress();
                            });

                            kabupatenSelect.addEventListener('change', function() {
                                const id = this.value;
                                kecamatanSelect.innerHTML = '<option value="">Select Kecamatan</option>';
                                kelurahanSelect.innerHTML = '<option value="">Select Kelurahan</option>';
                                kecamatanSelect.disabled = true;
                                kelurahanSelect.disabled = true;

                                if (id) {
                                    loadDropdown('<?= $this->Url->build(['controller' => 'Candidates', 'action' => 'getKecamatan']) ?>?kabupaten_id=' + id, kecamatanSelect);
                                updateAddress();
                            });

                            kecamatanSelect.addEventListener('change', function() {
                                const id = this.value;
                                kelurahanSelect.innerHTML = '<option value="">Select Kelurahan</option>';
                                kelurahanSelect.disabled = true;

                                if (id) {
                                    loadDropdown('<?= $this->Url->build(['controller' => 'Candidates', 'action' => 'getKelurahan']) ?>?kecamatan_id=' + id, kelurahanSelect);
                                updateAddress();
                            });

                            kelurahanSelect.addEventListener('change', function() {
                                updateAddress();
                            });

                            // Auto-update Address Field
                            function updateAddress() {
                                const propinsi = propinsiSelect.options[propinsiSelect.selectedIndex]?.text;
                                const kabupaten = kabupatenSelect.options[kabupatenSelect.selectedIndex]?.text;
                                const kecamatan = kecamatanSelect.options[kecamatanSelect.selectedIndex]?.text;
                                const kelurahan = kelurahanSelect.options[kelurahanSelect.selectedIndex]?.text;

                                let addressParts = [];
                                // Only add if selected and not default/loading
                                if (propinsi && propinsi !== 'Select Province' && propinsi !== 'Loading...') addressParts.push('Propinsi ' + propinsi);
                                if (kabupaten && kabupaten !== 'Select Kabupaten' && kabupaten !== 'Loading...') addressParts.push('Kabupaten ' + kabupaten);
                                if (kecamatan && kecamatan !== 'Select Kecamatan' && kecamatan !== 'Loading...') addressParts.push('Kecamatan ' + kecamatan);
                                if (kelurahan && kelurahan !== 'Select Kelurahan' && kelurahan !== 'Loading...') addressParts.push('Kelurahan ' + kelurahan);

                                const locationString = addressParts.join(', ');
                                
                                // Get current address without the generated part
                                let currentAddress = addressField.value;
                                // Regex to remove existing generated part (starts with Propinsi, matches until end or new line sequence)
                                // We look for the pattern "Propinsi ..." and remove it along with any preceding newlines
                                currentAddress = currentAddress.replace(/(\n\n)?Propinsi .*$/s, '').trim();

                                if (locationString) {
                                    addressField.value = currentAddress + (currentAddress ? '\n\n' : '') + locationString;
                                } else {
                                    addressField.value = currentAddress;

                            // Initial Load if values exist
                            if (propinsiSelect.value) {
                                loadDropdown('<?= $this->Url->build(['controller' => 'Candidates', 'action' => 'getKabupaten']) ?>?propinsi_id=' + propinsiSelect.value, kabupatenSelect, selectedKabupaten, function() {
                                    if (selectedKabupaten) {
                                        loadDropdown('<?= $this->Url->build(['controller' => 'Candidates', 'action' => 'getKecamatan']) ?>?kabupaten_id=' + selectedKabupaten, kecamatanSelect, selectedKecamatan, function() {
                                            if (selectedKecamatan) {
                                                loadDropdown('<?= $this->Url->build(['controller' => 'Candidates', 'action' => 'getKelurahan']) ?>?kecamatan_id=' + selectedKecamatan, kelurahanSelect, selectedKelurahan);
                                        });
                                });
                        });
                        </script>


                        <!-- Personal Attributes -->
                        <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-heartbeat"></i> Personal Attributes</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Body Weight (kg)</label>
                                    <input type="number" step="0.1" name="body_weight" class="form-control" value="<?= h($wizardData['body_weight'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Body Height (cm)</label>
                                    <input type="number" step="0.1" name="body_height" class="form-control" value="<?= h($wizardData['body_height'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="is_right_handed" value="1" class="form-check-input" <?= !empty($wizardData['is_right_handed']) ? 'checked' : '' ?>>
                                        <label class="form-check-label">Right Handed</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="is_wear_eye_glasses" value="1" class="form-check-input" <?= !empty($wizardData['is_wear_eye_glasses']) ? 'checked' : '' ?>>
                                    <label class="form-check-label">Wears Eye Glasses</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="is_color_blind" value="1" class="form-check-input" <?= !empty($wizardData['is_color_blind']) ? 'checked' : '' ?>>
                                    <label class="form-check-label">Color Blind</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="is_smoking" value="1" class="form-check-input" <?= !empty($wizardData['is_smoking']) ? 'checked' : '' ?>>
                                    <label class="form-check-label">Smoking</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="is_drinking_alcohol" value="1" class="form-check-input" <?= !empty($wizardData['is_drinking_alcohol']) ? 'checked' : '' ?>>
                                    <label class="form-check-label">Drinking Alcohol</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="is_tattooed" value="1" class="form-check-input" <?= !empty($wizardData['is_tattooed']) ? 'checked' : '' ?>>
                                    <label class="form-check-label">Has Tattoo</label>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-info"></i> Additional Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Strengths</label>
                                    <textarea name="strengths" class="form-control" rows="2"><?= h($wizardData['strengths'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Weaknesses</label>
                                    <textarea name="weaknesses" class="form-control" rows="2"><?= h($wizardData['weaknesses'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Hobby</label>
                                    <input type="text" name="hobby" class="form-control" value="<?= h($wizardData['hobby'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <?= $this->Html->link(__('<i class="fa fa-arrow-left"></i> Back'), ['action' => 'wizard', '?' => ['step' => 2]], ['class' => 'btn btn-secondary', 'escape' => false]) ?>
                            <?= $this->Form->button(__('<i class="fa fa-save"></i> Save & Continue'), ['class' => 'btn btn-primary btn-lg', 'escape' => false]) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>

            <?php elseif ($step == 4): ?>
                <!-- ========== STEP 4: Education History ========== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h5 class="m-0"><i class="fa fa-graduation-cap"></i> Step 4: Education History</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Education Requirements</h5>
                            <p class="mb-0"><strong>At least 1 education entry is required.</strong> Add all your educational background starting from the highest level.</p>
                        </div>

                        <?= $this->Form->create(null, ['url' => ['action' => 'wizard', '?' => ['step' => 4]]]) ?>
                        
                        <div id="education-entries">
                            <!-- Initial entry will be added by JavaScript -->
                        </div>

                        <button type="button" class="btn btn-success mb-3" onclick="addEducationEntry()">
                            <i class="fa fa-plus"></i> Add More Education
                        </button>

                        <div class="mt-4">
                            <?= $this->Html->link(__('<i class="fa fa-arrow-left"></i> Back'), ['action' => 'wizard', '?' => ['step' => 3]], ['class' => 'btn btn-secondary', 'escape' => false]) ?>
                            <?= $this->Form->button(__('<i class="fa fa-save"></i> Save & Continue'), ['class' => 'btn btn-primary btn-lg', 'escape' => false]) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
                
                <script src="<?= $this->Url->build('/js/wizard-dynamic-forms.js') ?>"></script>
                <script>
                // Add initial education entry
                document.addEventListener('DOMContentLoaded', function() {
                    addEducationEntry();
                });
                </script>

            <?php elseif ($step == 5): ?>
                <!-- ========== STEP 5: Work Experience ========== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h5 class="m-0"><i class="fa fa-briefcase"></i> Step 5: Work Experience</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Work Experience (Optional)</h5>
                            <p class="mb-0">Add your work experience if applicable. You can skip this step if you have no work experience.</p>
                        </div>

                        <?= $this->Form->create(null, ['url' => ['action' => 'wizard', '?' => ['step' => 5]]]) ?>
                        
                        <div id="experience-entries">
                            <!-- Entries will be added by JavaScript -->
                        </div>

                        <button type="button" class="btn btn-success mb-3" onclick="addExperienceEntry()">
                            <i class="fa fa-plus"></i> Add Work Experience
                        </button>

                        <div class="mt-4">
                            <?= $this->Html->link(__('<i class="fa fa-arrow-left"></i> Back'), ['action' => 'wizard', '?' => ['step' => 4]], ['class' => 'btn btn-secondary', 'escape' => false]) ?>
                            <?= $this->Form->button(__('<i class="fa fa-arrow-right"></i> Continue'), ['class' => 'btn btn-primary btn-lg', 'escape' => false]) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
                
                <script src="<?= $this->Url->build('/js/wizard-dynamic-forms.js') ?>"></script>

            <?php elseif ($step == 6): ?>
                <!-- ========== STEP 6: Family Information ========== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h5 class="m-0"><i class="fa fa-users"></i> Step 6: Family Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <h5><i class="fa fa-exclamation-triangle"></i> Recommendation</h5>
                            <p class="mb-0">It is <strong>recommended to add at least 2 family members</strong> (Father & Mother). This information is important for emergency contacts.</p>
                        </div>

                        <?= $this->Form->create(null, ['url' => ['action' => 'wizard', '?' => ['step' => 6]]]) ?>
                        
                        <div id="family-entries">
                            <!-- Entries will be added by JavaScript -->
                        </div>

                        <button type="button" class="btn btn-success mb-3" onclick="addFamilyEntry()">
                            <i class="fa fa-plus"></i> Add Family Member
                        </button>

                        <div class="mt-4">
                            <?= $this->Html->link(__('<i class="fa fa-arrow-left"></i> Back'), ['action' => 'wizard', '?' => ['step' => 5]], ['class' => 'btn btn-secondary', 'escape' => false]) ?>
                            <?= $this->Form->button(__('<i class="fa fa-arrow-right"></i> Continue'), ['class' => 'btn btn-primary btn-lg', 'escape' => false]) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
                
                <script src="<?= $this->Url->build('/js/wizard-dynamic-forms.js') ?>"></script>
                <script>
                // Add initial 2 family entries (Father & Mother)
                document.addEventListener('DOMContentLoaded', function() {
                    addFamilyEntry();
                    addFamilyEntry();
                });
                </script>

            <?php elseif ($step == 7): ?>
                <!-- ========== STEP 7: Certifications ========== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h5 class="m-0"><i class="fa fa-certificate"></i> Step 7: Certifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Certifications (Optional)</h5>
                            <p class="mb-0">Add any professional certifications, licenses, or skill certificates you have obtained.</p>
                        </div>

                        <?= $this->Form->create(null, ['url' => ['action' => 'wizard', '?' => ['step' => 7]]]) ?>
                        
                        <div id="certification-entries">
                            <!-- Entries will be added by JavaScript -->
                        </div>

                        <button type="button" class="btn btn-success mb-3" onclick="addCertificationEntry()">
                            <i class="fa fa-plus"></i> Add Certification
                        </button>

                        <div class="mt-4">
                            <?= $this->Html->link(__('<i class="fa fa-arrow-left"></i> Back'), ['action' => 'wizard', '?' => ['step' => 6]], ['class' => 'btn btn-secondary', 'escape' => false]) ?>
                            <?= $this->Form->button(__('<i class="fa fa-arrow-right"></i> Continue'), ['class' => 'btn btn-primary btn-lg', 'escape' => false]) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
                
                <script src="<?= $this->Url->build('/js/wizard-dynamic-forms.js') ?>"></script>

            <?php elseif ($step == 8): ?>
                <!-- ========== STEP 8: Courses/Training ========== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h5 class="m-0"><i class="fa fa-book"></i> Step 8: Courses & Training</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Training Courses (Optional)</h5>
                            <p class="mb-0">Add any training courses, workshops, or professional development programs you have completed.</p>
                        </div>

                        <?= $this->Form->create(null, ['url' => ['action' => 'wizard', '?' => ['step' => 8]]]) ?>
                        
                        <div id="course-entries">
                            <!-- Entries will be added by JavaScript -->
                        </div>

                        <button type="button" class="btn btn-success mb-3" onclick="addCourseEntry()">
                            <i class="fa fa-plus"></i> Add Course
                        </button>

                        <div class="mt-4">
                            <?= $this->Html->link(__('<i class="fa fa-arrow-left"></i> Back'), ['action' => 'wizard', '?' => ['step' => 7]], ['class' => 'btn btn-secondary', 'escape' => false]) ?>
                            <?= $this->Form->button(__('<i class="fa fa-arrow-right"></i> Continue to Review'), ['class' => 'btn btn-primary btn-lg', 'escape' => false]) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
                
                <script src="<?= $this->Url->build('/js/wizard-dynamic-forms.js') ?>"></script>

            <?php elseif ($step == 9): ?>
                <!-- ========== STEP 9: Review & Submit ========== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-success text-white">
                        <h5 class="m-0"><i class="fa fa-check-circle"></i> Step 9: Review & Submit</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h5><i class="fa fa-check-circle"></i> Final Review</h5>
                            <p class="mb-0">Please review all information carefully before submitting. You can go back to any step to make changes.</p>
                        </div>

                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fa fa-exclamation-triangle"></i> Validation Errors:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $field => $fieldErrors): ?>
                                        <?php foreach ($fieldErrors as $error): ?>
                                            <li><strong><?= h($field) ?>:</strong> <?= h($error) ?></li>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Photo Preview -->
                        <?php if (!empty($wizardData['image_photo_cropped'])): ?>
                            <h4 class="border-bottom pb-2 mb-3"><i class="fa fa-camera"></i> Photo</h4>
                            <div class="text-center mb-4">
                                <img src="<?= h($wizardData['image_photo_cropped']) ?>" alt="Candidate Photo" style="width: 200px; height: 200px; border: 2px solid #ddd;">
                            </div>
                        <?php endif; ?>

                        <!-- Basic Information -->
                        <h4 class="border-bottom pb-2 mb-3"><i class="fa fa-user"></i> Basic Information</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Identity Number</th>
                                <td><?= h($wizardData['identity_number'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th>Full Name</th>
                                <td><?= h($wizardData['name'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th>Birth Place & Date</th>
                                <td><?= h($wizardData['birth_place'] ?? '') ?>, <?= h($wizardData['birth_date'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td><?= isset($wizardData['master_gender_id']) && isset($masterGenders[$wizardData['master_gender_id']]) ? h($masterGenders[$wizardData['master_gender_id']]) : '' ?></td>
                            </tr>
                            <tr>
                                <th>Religion</th>
                                <td><?= isset($wizardData['master_religion_id']) && isset($masterReligions[$wizardData['master_religion_id']]) ? h($masterReligions[$wizardData['master_religion_id']]) : '' ?></td>
                            </tr>
                            <tr>
                                <th>Marriage Status</th>
                                <td><?= isset($wizardData['master_marriage_status_id']) && isset($masterMarriageStatuses[$wizardData['master_marriage_status_id']]) ? h($masterMarriageStatuses[$wizardData['master_marriage_status_id']]) : '' ?></td>
                            </tr>
                        </table>

                        <!-- Contact Information -->
                        <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-phone"></i> Contact Information</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Mobile Phone</th>
                                <td><?= h($wizardData['telephone_mobile'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th>Emergency Contact</th>
                                <td><?= h($wizardData['telephone_emergency'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?= h($wizardData['email'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td><?= h($wizardData['address'] ?? '') ?></td>
                            </tr>
                        </table>

                        <!-- Education History -->
                        <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-graduation-cap"></i> Education History (<?= count($wizardData['educations'] ?? []) ?>)</h4>
                        <?php if (!empty($wizardData['educations'])): ?>
                            <?php foreach ($wizardData['educations'] as $index => $edu): ?>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <strong><?= $index + 1 ?>. <?= h($edu['institution_name'] ?? '') ?></strong><br>
                                        Level: <?= h($edu['level'] ?? 'N/A') ?> | Year: <?= h($edu['year_graduated'] ?? 'N/A') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No education entries</p>
                        <?php endif; ?>

                        <!-- Work Experience -->
                        <?php if (!empty($wizardData['experiences'])): ?>
                            <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-briefcase"></i> Work Experience (<?= count($wizardData['experiences']) ?>)</h4>
                            <?php foreach ($wizardData['experiences'] as $index => $exp): ?>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <strong><?= $index + 1 ?>. <?= h($exp['position'] ?? '') ?></strong> at <?= h($exp['company_name'] ?? '') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Family Information -->
                        <?php if (!empty($wizardData['families'])): ?>
                            <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-users"></i> Family Information (<?= count($wizardData['families']) ?>)</h4>
                            <?php foreach ($wizardData['families'] as $index => $fam): ?>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <strong><?= $index + 1 ?>. <?= h($fam['full_name'] ?? '') ?></strong> (<?= h($fam['relationship'] ?? '') ?>)
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Certifications -->
                        <?php if (!empty($wizardData['certifications'])): ?>
                            <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-certificate"></i> Certifications (<?= count($wizardData['certifications']) ?>)</h4>
                            <?php foreach ($wizardData['certifications'] as $index => $cert): ?>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <strong><?= $index + 1 ?>. <?= h($cert['certification_name'] ?? '') ?></strong> by <?= h($cert['issuing_organization'] ?? '') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Courses -->
                        <?php if (!empty($wizardData['courses'])): ?>
                            <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-book"></i> Courses & Training (<?= count($wizardData['courses']) ?>)</h4>
                            <?php foreach ($wizardData['courses'] as $index => $course): ?>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <strong><?= $index + 1 ?>. <?= h($course['course_name'] ?? '') ?></strong> at <?= h($course['training_institution'] ?? '') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?= $this->Form->create(null, ['url' => ['action' => 'wizard', '?' => ['step' => 9]]]) ?>
                        <div class="mt-4">
                            <?= $this->Html->link(__('<i class="fa fa-arrow-left"></i> Back to Edit'), ['action' => 'wizard', '?' => ['step' => 8]], ['class' => 'btn btn-secondary', 'escape' => false]) ?>
                            <?= $this->Form->button(__('<i class="fa fa-check"></i> Submit Registration'), ['class' => 'btn btn-success btn-lg', 'escape' => false]) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- Fallback for invalid steps -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-danger text-white">
                        <h5 class="m-0"><i class="fa fa-exclamation-triangle"></i> Invalid Step</h5>
                    </div>
                    <div class="card-body">
                        <p>The requested step is invalid. Please start from the beginning.</p>
                        <?= $this->Html->link(__('Start Over'), ['action' => 'wizard', '?' => ['step' => 1]], ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
.progress-bar {
    font-size: 11px;
    line-height: 35px;
.cropper-view-box,
.cropper-face {
    border-radius: 0;
</style>

<!-- Global Input Handling Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Global Input Handling Script Loaded');

    // Event Delegation for Input Handling
    document.body.addEventListener('input', function(e) {
        const target = e.target;
        
        // 1. Capitalize text inputs and textareas (excluding email and kana fields)
        if (target.matches('input[type="text"], textarea') && !target.matches('input[name*="kana"], textarea[name*="kana"]')) {
            const start = target.selectionStart;
            const end = target.selectionEnd;
            target.value = target.value.toUpperCase();
            try {
                target.setSelectionRange(start, end);
            } catch (err) {
                // Ignore errors for types that don't support selection

        // 2. Lowercase email inputs and kana fields
        if (target.matches('input[type="email"]') || target.matches('input[name*="kana"], textarea[name*="kana"]')) {
            target.value = target.value.toLowerCase();
            // Note: setSelectionRange is not supported on email inputs in some browsers

        // 3. Katakana Conversion (for fields with 'kana' in name)
        // We handle this in 'input' but check for composition to avoid breaking IME
        if (target.matches('input[name*="kana"], textarea[name*="kana"]')) {
            if (e.isComposing) return; // Skip if currently composing via IME
            
            const original = target.value;
            const converted = original.replace(/[\u3041-\u3096]/g, function(ch) {
                return String.fromCharCode(ch.charCodeAt(0) + 0x60);
            });
            
            if (original !== converted) {
                target.value = converted;
    });

    // Handle IME Composition End (for Katakana conversion)
    document.body.addEventListener('compositionend', function(e) {
        const target = e.target;
        if (target.matches('input[name*="kana"], textarea[name*="kana"]')) {
            const original = target.value;
            const converted = original.replace(/[\u3041-\u3096]/g, function(ch) {
                return String.fromCharCode(ch.charCodeAt(0) + 0x60);
            });
            
            if (original !== converted) {
                target.value = converted;
    });

    // 3. Force Datepicker for 'date' fields (Run once on load)
    const dateInputs = document.querySelectorAll('input[name*="date"]');
    console.log('Found date inputs:', dateInputs.length);
    dateInputs.forEach(input => {
        if (input.getAttribute('type') !== 'date') {
            input.setAttribute('type', 'date');
            console.log('Converted to date:', input.name);
    });
});
</script>

