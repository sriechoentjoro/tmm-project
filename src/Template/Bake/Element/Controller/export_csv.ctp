
    /**
     * Export CSV method
     *
     * @return \Cake\Http\Response|null Redirects on successful export.
     */
    public function exportCsv()
    {
        $query = $this-><%= $currentModelName %>;
<%
    $associations = array_merge(
        $this->Bake->aliasExtractor($modelObj, 'BelongsTo'),
        $this->Bake->aliasExtractor($modelObj, 'BelongsToMany')
    );
    if (!empty($associations)) {
        echo "\n        \$query = \$query->contain([";
        foreach ($associations as $i => $assoc) {
            echo "'{$assoc}'";
            if ($i < count($associations) - 1) {
                echo ", ";
        echo "]);\n";
%>
        // Apply filters from query parameters
        if ($this->request->getQuery('filter')) {
            $filters = $this->request->getQuery('filter');
            foreach ($filters as $field => $value) {
                if (!empty($value)) {
                    $query = $query->where(["<%= $currentModelName %>.{$field} LIKE" => "%{$value}%"]);
        
        $data = $query->all();
        
        // Set response type
        $this->response = $this->response->withType('csv');
        $this->response = $this->response->withDownload('<%= strtolower($pluralVar) %>_' . date('Ymd_His') . '.csv');
        
        // Create CSV content
        $output = fopen('php://output', 'w');
        
        // Write header row
        $headers = [
<%
    $i = 0;
    foreach ($fields as $field) {
        if (in_array($field, $schema->primaryKey())) {
            continue;
        echo "            __('" . Cake\Utility\Inflector::humanize($field) . "')";
        if ($i < count($fields) - 2) {
            echo ",\n";
        $i++;
%>
        ];
        fputcsv($output, $headers);
        
        // Write data rows
        foreach ($data as $row) {
            $csvRow = [
<%
    $i = 0;
    foreach ($fields as $field) {
        if (in_array($field, $schema->primaryKey())) {
            continue;
        
        // Check if it's a foreign key field
        $isForeignKey = false;
        if (!empty($associations)) {
            foreach ($this->Bake->aliasExtractor($modelObj, 'BelongsTo') as $alias => $details) {
                if (isset($details['foreignKey']) && $field === $details['foreignKey']) {
                    echo "                isset(\$row->{$alias}) ? \$row->{$alias}->" . $details['displayField'] . " : ''";
                    $isForeignKey = true;
                    break;
        
        if (!$isForeignKey) {
            if (isset($schema) && in_array($schema->getColumnType($field), ['date', 'datetime', 'timestamp'])) {
                echo "                \$row->{$field} ? \$row->{$field}->format('Y-m-d H:i:s') : ''";
            } else {
                echo "                \$row->{$field}";
        
        if ($i < count($fields) - 2) {
            echo ",\n";
        $i++;
%>
            ];
            fputcsv($output, $csvRow);
        
        fclose($output);
        
        return $this->response;

