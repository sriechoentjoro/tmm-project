
    /**
     * Filter search method for AJAX
     *
     * @return \Cake\Http\Response|null
     */
    public function filter()
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json');
        
        $filters = $this->request->getQuery();
        
        $query = $this-><%= $currentModelName %>->find()<% if (!empty($associations['BelongsTo'])): %>->contain([<%
            $i = 0;
            foreach ($associations['BelongsTo'] as $assoc):
                if ($i > 0) echo ', ';
                echo "'" . $assoc['alias'] . "'";
                $i++;
            endforeach;
        %>])<% endif; %>;
        
        // Apply filters with proper field matching
        foreach ($filters as $field => $value) {
            if (!empty($value) && $field !== '_') {
<%
        // Add matching logic for each BelongsTo association
        if (!empty($associations['BelongsTo'])):
            foreach ($associations['BelongsTo'] as $alias => $details):
                $assocLower = strtolower($alias);
%>
                // Handle <%= $alias %> search
                if ($field === '<%= $assocLower %>') {
                    $query->matching('<%= $alias %>', function ($q) use ($value) {
                        return $q->where(['<%= $alias %>.<%= $details['displayField'] %> LIKE' => "%{$value}%"]);
                    });
                } else<%
            endforeach;
        endif;
%> if ($field === 'id' || strpos($field, '_id') !== false) {
                    // Exact match for ID fields
                    $query->where(["<%= $currentModelName %>.{$field}" => $value]);
                } else {
                    // LIKE search for text fields
                    $query->where(["<%= $currentModelName %>.{$field} LIKE" => "%{$value}%"]);
        
        $results = $query->all();
        
        // Generate HTML rows
        $html = '';
        foreach ($results as $<%= $singularVar %>) {
            $html .= '<tr class="table-row-with-actions">';
            
<%
    // Thumbnail column if exists
    if ($firstImageField && $primaryKey[0]):
%>
            // Thumbnail
            $html .= '<td class="thumbnail-cell">';
            if (!empty($<%= $singularVar %>-><%= $firstImageField %>)) {
                $html .= '<img src="/asahi_v3/' . h($<%= $singularVar %>-><%= $firstImageField %>) . '" alt="Thumbnail" class="table-thumbnail" style="max-width:50px; max-height:50px;" onerror="this.src=\'/asahi_v3/img/no-image.png\'">';
            } else {
                $html .= '<span class="no-thumbnail"><i class="fas fa-image"></i></span>';
            $html .= '</td>';
            
<%
    endif;
    
    // ID column (always first after thumbnail)
    if (!empty($primaryKey[0])):
%>
            // ID
            $html .= '<td>' . h($<%= $singularVar %>-><%= $primaryKey[0] %>) . '</td>';
            
<%
    endif;
    
    // Build array of foreign keys for lookup
    $foreignKeys = [];
    if (!empty($associations['BelongsTo'])):
        foreach ($associations['BelongsTo'] as $alias => $details):
            $foreignKeys[$details['foreignKey']] = [
                'alias' => $alias,
                'details' => $details
            ];
        endforeach;
    endif;
    
    // Generate columns in SCHEMA order (excluding primary key and image thumbnail)
    foreach ($fields as $field):
        // Skip primary key (already rendered) and image thumbnail
        if (in_array($field, [$primaryKey[0], $firstImageField])) {
            continue;
        
        // Check if this is a foreign key field
        if (isset($foreignKeys[$field])):
            $alias = $foreignKeys[$field]['alias'];
            $details = $foreignKeys[$field]['details'];
            $otherSingularVar = Inflector::variable($alias);
%>
            // <%= $alias %> (Foreign Key: <%= $field %>)
            $html .= '<td>';
            if ($<%= $singularVar %>-><%= $otherSingularVar %>) {
                $html .= '<a href="/asahi_v3/<%= $details['controller'] %>/view/' . $<%= $singularVar %>-><%= $otherSingularVar %>-><%= $details['primaryKey'][0] %> . '">' . h($<%= $singularVar %>-><%= $otherSingularVar %>-><%= $details['displayField'] %>) . '</a>';
            $html .= '</td>';
            
<%
        else:
            // Regular field
            // Check if file/image field
            $isFileField = (strpos($field, 'image') !== false || 
                           strpos($field, 'photo') !== false || 
                           strpos($field, 'file') !== false || 
                           strpos($field, 'attachment') !== false ||
                           strpos($field, 'document') !== false);
            
            if ($isFileField):
%>
            // <%= $field %> (File/Image)
            $html .= '<td class="file-cell">';
            if (!empty($<%= $singularVar %>-><%= $field %>)) {
                $fileUrl = $<%= $singularVar %>-><%= $field %>;
                $extension = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                
                // Icon based on extension
                $iconClass = 'fa-file';
                $iconColor = '#6c757d';
                if ($isImage) {
                    $iconClass = 'fa-file-image';
                    $iconColor = '#28a745';
                } elseif ($extension === 'pdf') {
                    $iconClass = 'fa-file-pdf';
                    $iconColor = '#dc3545';
                } elseif (in_array($extension, ['doc', 'docx'])) {
                    $iconClass = 'fa-file-word';
                    $iconColor = '#2b579a';
                } elseif (in_array($extension, ['xls', 'xlsx'])) {
                    $iconClass = 'fa-file-excel';
                    $iconColor = '#217346';
                } elseif (in_array($extension, ['zip', 'rar', '7z'])) {
                    $iconClass = 'fa-file-archive';
                    $iconColor = '#fd7e14';
                
                $isImageStr = $isImage ? 'true' : 'false';
                $html .= '<a href="javascript:void(0)" onclick="previewFile(\'/asahi_v3/' . h($fileUrl) . '\', \'' . $extension . '\', ' . $isImageStr . ')" title="Click to preview" style="color: ' . $iconColor . '; font-size: 1.5rem;">';
                $html .= '<i class="fas ' . $iconClass . '"></i>';
                $html .= '</a>';
            } else {
                $html .= '<span class="text-muted"><i class="fas fa-minus"></i></span>';
            $html .= '</td>';
            
<%
            else:
                // Check if numeric field for formatting
                $fieldType = isset($schema) ? $schema->getColumnType($field) : '';
                $isNumeric = in_array($fieldType, ['integer', 'biginteger', 'float', 'decimal']);
%>
            // <%= $field %>
            $html .= '<td>';
<%          if ($isNumeric): %>
            if ($<%= $singularVar %>-><%= $field %> !== null) {
                $html .= number_format($<%= $singularVar %>-><%= $field %>, 2);
<%          else: %>
            $html .= h($<%= $singularVar %>-><%= $field %>);
<%          endif; %>
            $html .= '</td>';
            
<%
            endif;
        endif;
    endforeach;
%>
            // Actions
            $html .= '<td style="position: relative;">';
            $html .= '<div class="action-buttons-hover">';
            $html .= '<a href="/asahi_v3/<%= $pluralVar %>/view/' . $<%= $singularVar %>-><%= $primaryKey[0] %> . '" class="btn-action-icon btn-view-icon" title="View"><i class="fas fa-expand"></i></a>';
            $html .= '<a href="/asahi_v3/<%= $pluralVar %>/edit/' . $<%= $singularVar %>-><%= $primaryKey[0] %> . '" class="btn-action-icon btn-edit-icon" title="Edit"><i class="fas fa-edit"></i></a>';
            $html .= '<form method="post" action="/asahi_v3/<%= $pluralVar %>/delete/' . $<%= $singularVar %>-><%= $primaryKey[0] %> . '" style="display:inline;" onsubmit="return confirm(\'Are you sure?\');">';
            $html .= '<input type="hidden" name="_csrfToken" value="' . $this->request->getAttribute('csrfToken') . '">';
            $html .= '<button type="submit" class="btn-action-icon btn-delete-icon" title="Delete"><i class="fas fa-trash"></i></button>';
            $html .= '</form>';
            $html .= '</div></td>';
            
            $html .= '</tr>';
        
        if (empty($html)) {
            $html = '<tr><td colspan="100" class="text-center">No results found</td></tr>';
        
        return $this->response->withStringBody(json_encode([
            'success' => true,
            'html' => $html,
            'count' => $results->count()
        ]));

