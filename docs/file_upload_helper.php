<?php
/**
 * File Upload Helper Method
 * 
 * Paste this code into your AppController or specific Controller
 * untuk handle file upload secara otomatis
 * 
 * Usage di add() atau edit():
 *   $data = $this->handleFileUploads($data, ['image_url', 'file_path']);
 */

/**
 * Handle file/image uploads for multiple fields
 * 
 * @param array $data Request data from $this->request->getData()
 * @param array $fileFields Array of field names yang contain file/image
 * @param \App\Model\Entity\YourEntity|null $existingEntity For edit: existing entity to delete old files
 * @return array Modified data with file paths
 */
protected function handleFileUploads(array $data, array $fileFields, $existingEntity = null)
{
    foreach ($fileFields as $fieldName) {
        if (!empty($data[$fieldName]) && is_object($data[$fieldName])) {
            $file = $data[$fieldName];
            
            // Check if file was uploaded successfully
            if ($file->getError() === UPLOAD_ERR_OK) {
                // Delete old file if editing
                if ($existingEntity && !empty($existingEntity->get($fieldName))) {
                    $oldFile = $existingEntity->get($fieldName);
                    if (file_exists(WWW_ROOT . $oldFile)) {
                        @unlink(WWW_ROOT . $oldFile);
                    }
                }
                
                // Get file information
                $fileName = $file->getClientFilename();
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $baseName = pathinfo($fileName, PATHINFO_FILENAME);
                $timestamp = date('YmdHis');
                
                // Generate unique filename
                $uniqueFileName = $baseName . '_' . $timestamp . '.' . $extension;
                
                // Determine upload directory based on field type
                if (preg_match('/(image|photo|gambar|foto)/i', $fieldName)) {
                    $uploadPath = WWW_ROOT . 'img' . DS . 'uploads' . DS;
                    $webPath = 'img/uploads/' . $uniqueFileName;
                } else {
                    $uploadPath = WWW_ROOT . 'files' . DS . 'uploads' . DS;
                    $webPath = 'files/uploads/' . $uniqueFileName;
                }
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move uploaded file
                try {
                    $file->moveTo($uploadPath . $uniqueFileName);
                    $data[$fieldName] = $webPath; // Store relative path in database
                    
                    $this->log("File uploaded successfully: {$webPath}", 'info');
                } catch (\Exception $e) {
                    $this->Flash->error(__('Failed to upload {0}: {1}', $fieldName, $e->getMessage()));
                    $data[$fieldName] = null;
                }
            } else {
                $data[$fieldName] = null;
            }
        } else {
            // If editing and no new file uploaded, keep old value
            if ($existingEntity) {
                unset($data[$fieldName]); // Don't overwrite existing value
            } else {
                $data[$fieldName] = null;
            }
        }
    }
    
    return $data;
}
