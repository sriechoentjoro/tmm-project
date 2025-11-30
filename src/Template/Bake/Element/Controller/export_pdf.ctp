
    /**
     * Export PDF method with images
     *
     * @return \Cake\Http\Response|null
     */
    public function exportPdf()
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
        
        // Set page setup for PDF
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        
        // Set margins
        $sheet->getPageMargins()
            ->setTop(0.5)
            ->setRight(0.5)
            ->setLeft(0.5)
            ->setBottom(0.5);
        
        // Set header/footer
        $sheet->getHeaderFooter()
            ->setOddHeader('&C&B&16Asahi <%= $pluralHumanName %> Report')
            ->setOddFooter('&L' . date('Y-m-d H:i') . '&RPage &P of &N');
        
        // Set header row (fewer columns for PDF to fit page)
        $headers = [
<%
    if ($firstImageField) {
        echo "            'Image',\n";
    
    $pdfFields = [];
    $headerCount = 0;
    
    // Limit to most important fields for PDF
    foreach ($fields as $field) {
        if (in_array($field, $schema->primaryKey())) {
            continue;
        
        // Skip less important fields for PDF
        if (strpos($field, 'created') !== false || 
            strpos($field, 'modified') !== false || 
            strpos($field, 'rack_cell') !== false ||
            strpos($field, 'description') !== false) {
            continue;
        
        $pdfFields[] = $field;
        
        // Check if it's a foreign key
        $headerLabel = Cake\Utility\Inflector::humanize($field);
        if (!empty($associations)) {
            foreach ($this->Bake->aliasExtractor($modelObj, 'BelongsTo') as $alias => $details) {
                if (isset($details['foreignKey']) && $field === $details['foreignKey']) {
                    $headerLabel = $alias;
                    break;
        
        // Limit to first 8 columns for PDF
        if (count($pdfFields) <= 8) {
            echo "            __('{$headerLabel}')";
            if ($headerCount < min(count($fields) - 2, 7)) {
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
                'size' => 11,
                'name' => 'Mulish'
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '667eea']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
        $lastColumn = chr(65 + count($headers) - 1);
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);
        
        // Set column widths for PDF (narrower)
<%
    $colIndex = 65; // ASCII 'A'
    if ($firstImageField) {
        echo "        \$sheet->getColumnDimension('" . chr($colIndex) . "')->setWidth(12); // Image\n";
        $colIndex++;
    
    $pdfColCount = 0;
    foreach ($fields as $field) {
        if (in_array($field, $schema->primaryKey())) {
            continue;
        
        // Skip same fields as header
        if (strpos($field, 'created') !== false || 
            strpos($field, 'modified') !== false || 
            strpos($field, 'rack_cell') !== false ||
            strpos($field, 'description') !== false) {
            continue;
        
        if ($pdfColCount >= 8) break;
        
        $width = 12;
        if (strpos($field, 'id') !== false) {
            $width = 6;
        } elseif (strpos($field, 'title') !== false || strpos($field, 'name') !== false) {
            $width = 25;
        } elseif (in_array($schema->getColumnType($field), ['integer', 'float', 'decimal'])) {
            $width = 8;
        
        echo "        \$sheet->getColumnDimension('" . chr($colIndex) . "')->setWidth({$width});\n";
        $colIndex++;
        $pdfColCount++;
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
        echo "                        \$drawing->setHeight(45);\n";
        echo "                        \$drawing->setCoordinates('A' . \$row);\n";
        echo "                        \$drawing->setOffsetX(3);\n";
        echo "                        \$drawing->setOffsetY(3);\n";
        echo "                        \$drawing->setWorksheet(\$sheet);\n";
        echo "                        \$sheet->getRowDimension(\$row)->setRowHeight(50);\n";
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
    
    $pdfColCount = 0;
    foreach ($fields as $field) {
        if (in_array($field, $schema->primaryKey())) {
            continue;
        
        // Skip same fields as header
        if (strpos($field, 'created') !== false || 
            strpos($field, 'modified') !== false || 
            strpos($field, 'rack_cell') !== false ||
            strpos($field, 'description') !== false) {
            continue;
        
        if ($pdfColCount >= 8) break;
        
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
                echo "            \$sheet->setCellValue('{$column}' . \$row, \$item->{$field} ? \$item->{$field}->format('Y-m-d') : '');\n";
            } else {
                echo "            \$sheet->setCellValue('{$column}' . \$row, \$item->{$field});\n";
        
        $colIndex++;
        $pdfColCount++;
%>
            
            // Style data rows
            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setSize(9);
            
            // Zebra striping
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('f9f9f9');
            
            $row++;
        
        // Add borders
        $sheet->getStyle('A1:' . $lastColumn . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        // Create PDF using Mpdf
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        
        // Output to browser
        $filename = '<%= strtolower($pluralVar) %>_' . date('Ymd_His') . '.pdf';
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;

