
    /**
     * Export Excel method with images
     *
     * @return \Cake\Http\Response|null
     */
    public function exportExcel()
    {
        // Check if PhpSpreadsheet is installed
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $this->Flash->error(__('PhpSpreadsheet library is not installed. Please run: composer require phpoffice/phpspreadsheet'));
            return $this->redirect(['action' => 'index']);

        $query = $this-><%= $currentModelName %>;
<%
    $associations = array_merge(
        $this->Bake->aliasExtractor($modelObj, 'BelongsTo'),
        $this->Bake->aliasExtractor($modelObj, 'BelongsToMany')
    );
    if (!empty($associations)) {
        echo "\n        \$query = \$query->contain([";
        $assocList = [];
        foreach ($associations as $assoc) {
            $assocList[] = "'{$assoc}'";
        echo implode(', ', $assocList);
        echo "]);\n";
%>
        
        // Apply filters from query parameters
        if ($this->request->getQuery('filter')) {
            $filters = $this->request->getQuery('filter');
            foreach ($filters as $field => $value) {
                if (!empty($value)) {
                    $query = $query->where(["<%= $currentModelName %>.{$field} LIKE" => "%{$value}%"]);
        
        $data = $query->all();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('PT Asahi Family - Trucking Management')
            ->setTitle('<%= $pluralHumanName %> Export')
            ->setSubject('<%= $singularHumanName %> Data')
            ->setDescription('Export of <%= strtolower($pluralHumanName) %> data with images');
        
        // Set header row
        $headers = [
<%
    if ($firstImageField) {
        echo "            'Image',\n";
    
    $headerCount = 0;
    foreach ($fields as $field) {
        if (in_array($field, $schema->primaryKey())) {
            continue;
        
        // Check if it's a foreign key
        $headerLabel = Cake\Utility\Inflector::humanize($field);
        if (!empty($associations)) {
            foreach ($this->Bake->aliasExtractor($modelObj, 'BelongsTo') as $alias => $details) {
                if (isset($details['foreignKey']) && $field === $details['foreignKey']) {
                    $headerLabel = $alias;
                    break;
        
        echo "            __('{$headerLabel}')";
        if ($headerCount < count($fields) - 2) {
            echo ",\n";
        $headerCount++;
%>
        ];
        $sheet->fromArray($headers, NULL, 'A1');
        
        // Style header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
                'name' => 'Mulish'
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['rgb' => '667eea'],
                'endColor' => ['rgb' => '764ba2']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
        $lastColumn = chr(65 + count($headers) - 1);
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);
        
        // Set column widths
<%
    $colIndex = 65; // ASCII 'A'
    if ($firstImageField) {
        echo "        \$sheet->getColumnDimension('" . chr($colIndex) . "')->setWidth(15); // Image\n";
        $colIndex++;
    
    foreach ($fields as $field) {
        if (in_array($field, $schema->primaryKey())) {
            continue;
        
        $width = 15;
        if (strpos($field, 'id') !== false) {
            $width = 8;
        } elseif (strpos($field, 'description') !== false || strpos($field, 'title') !== false || strpos($field, 'name') !== false) {
            $width = 30;
        } elseif (in_array($schema->getColumnType($field), ['date', 'datetime', 'timestamp'])) {
            $width = 18;
        } elseif (in_array($schema->getColumnType($field), ['integer', 'float', 'decimal'])) {
            $width = 10;
        
        echo "        \$sheet->getColumnDimension('" . chr($colIndex) . "')->setWidth({$width});\n";
        $colIndex++;
%>
        
        // Add data rows with images
        $row = 2;
        foreach ($data as $item) {
<%
    if ($firstImageField) {
        echo "            // Add image if exists\n";
        echo "            if (!empty(\$item->{$firstImageField})) {\n";
        echo "                \$imagePath = WWW_ROOT . \$item->{$firstImageField};\n";
        echo "                if (file_exists(\$imagePath)) {\n";
        echo "                    try {\n";
        echo "                        \$drawing = new \\PhpOffice\\PhpSpreadsheet\\Worksheet\\Drawing();\n";
        echo "                        \$drawing->setName('Image');\n";
        echo "                        \$drawing->setDescription('Image');\n";
        echo "                        \$drawing->setPath(\$imagePath);\n";
        echo "                        \$drawing->setHeight(60);\n";
        echo "                        \$drawing->setCoordinates('A' . \$row);\n";
        echo "                        \$drawing->setOffsetX(5);\n";
        echo "                        \$drawing->setOffsetY(5);\n";
        echo "                        \$drawing->setWorksheet(\$sheet);\n";
        echo "                        \$sheet->getRowDimension(\$row)->setRowHeight(65);\n";
        echo "                    } catch (\\Exception \$e) {\n";
        echo "                        // Continue without image\n";
        echo "                    }\n";
        echo "                }\n";
        echo "            }\n\n";
%>
            // Add data
<%
    $colIndex = 65; // ASCII 'A'
    if ($firstImageField) {
        $colIndex++; // Skip image column
    
    foreach ($fields as $field) {
        if (in_array($field, $schema->primaryKey())) {
            continue;
        
        $column = chr($colIndex);
        
        // Check if it's a foreign key
        $isForeignKey = false;
        if (!empty($associations)) {
            foreach ($this->Bake->aliasExtractor($modelObj, 'BelongsTo') as $alias => $details) {
                if (isset($details['foreignKey']) && $field === $details['foreignKey']) {
                    echo "            \$sheet->setCellValue('{$column}' . \$row, isset(\$item->{$alias}) ? \$item->{$alias}->" . $details['displayField'] . " : '');\n";
                    $isForeignKey = true;
                    break;
        
        if (!$isForeignKey) {
            if (isset($schema) && in_array($schema->getColumnType($field), ['date', 'datetime', 'timestamp'])) {
                echo "            \$sheet->setCellValue('{$column}' . \$row, \$item->{$field} ? \$item->{$field}->format('Y-m-d H:i:s') : '');\n";
            } else {
                echo "            \$sheet->setCellValue('{$column}' . \$row, \$item->{$field});\n";
        
        $colIndex++;
%>
            
            // Center align all cells
            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            
            $row++;
        
        // Auto-filter
        $sheet->setAutoFilter('A1:' . $lastColumn . ($row - 1));
        
        // Freeze header row
        $sheet->freezePane('A2');
        
        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Output to browser
        $filename = '<%= strtolower($pluralVar) %>_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;

