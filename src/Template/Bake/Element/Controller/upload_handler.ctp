<%
/**
 * Upload Handler Template
 * Generates upload code for file and image fields
 */

use Cake\Utility\Inflector;

$modelObj = $this->Bake->getModel($modelClass);
$fields = $modelObj->getSchema()->columns();
$singularName = Inflector::underscore($singularName);

foreach ($fields as $field):
    $columnType = $modelObj->getSchema()->getColumnType($field);
    
    // Detect file fields
    if (preg_match('/(file|filename|document|attachment)/i', $field)):
%>
        // Handle <%= $field %> upload
        if ($this->request->getData('<%= $field %>') && is_object($this->request->getData('<%= $field %>'))) {
            $this->uploadFile('<%= $currentModelName %>', '<%= $field %>', '<%= $singularName %>', $this->request->getData('name') ?? '');
        } else {
            $this->request = $this->request->withoutData('<%= $field %>');
        
<%
    // Detect image fields
    elseif (preg_match('/(image|photo|gambar|foto|picture)/i', $field)):
%>
        // Handle <%= $field %> upload
        if ($this->request->getData('<%= $field %>') && is_object($this->request->getData('<%= $field %>'))) {
            $this->uploadImage('<%= $currentModelName %>', '<%= $field %>', '<%= $singularName %>', $this->request->getData('name') ?? '');
        } else {
            $this->request = $this->request->withoutData('<%= $field %>');
        
<%
    endif;
endforeach;
%>

