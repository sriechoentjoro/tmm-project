<?php
namespace App\Controller;

use Cake\Http\Response;
use Cake\I18n\FrozenTime;

/**
 * Export Trait
 * Provides export functionality (Print, CSV, Excel, PDF) for all controllers
 * 
 * Usage in controller:
 * use ExportTrait;
 * 
 * Then call:
 * - $this->doExportCsv($query, $filename, $headers, $fields)
 * - $this->doExportExcel($query, $filename, $headers, $fields)
 * - $this->doExportPrint($query, $title, $headers, $fields)
 */
trait ExportTrait
{
    /**
     * Export data to CSV format
     *
     * @param \Cake\ORM\Query $query The query object
     * @param string $filename Filename without extension
     * @param array $headers Column headers
     * @param array $fields Field names to export
     * @return \Cake\Http\Response
     */
    public function doExportCsv($query, $filename, array $headers, array $fields)
    {
        $filename = $filename . '_' . date('Y-m-d_His') . '.csv';
        
        $response = $this->response->withType('text/csv')
            ->withCharset('UTF-8')
            ->withDownload($filename);
        
        // Create CSV content
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($output, $headers);
        
        // Write data
        foreach ($query as $row) {
            $rowData = [];
            foreach ($fields as $field) {
                $value = $this->getNestedValue($row, $field);
                $rowData[] = $this->formatCsvValue($value);
            }
            fputcsv($output, $rowData);
        }
        
        fclose($output);
        
        return $response;
    }
    
    /**
     * Export data to Excel format using PhpSpreadsheet
     *
     * @param \Cake\ORM\Query $query The query object
     * @param string $filename Filename without extension
     * @param array $headers Column headers
     * @param array $fields Field names to export
     * @return \Cake\Http\Response
     */
    public function doExportExcel($query, $filename, array $headers, array $fields)
    {
        $filename = $filename . '_' . date('Y-m-d_His') . '.xlsx';
        
        // Check if PhpSpreadsheet is available
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            // Fallback to CSV with .xlsx extension
            return $this->doExportCsv($query, str_replace('.xlsx', '', $filename), $headers, $fields);
        }
        
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set purple gradient header style
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => ['rgb' => '667eea'],
                    'endColor' => ['rgb' => '764ba2']
                ],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ];
            
            // Write headers
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
                $sheet->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }
            
            // Write data
            $row = 2;
            foreach ($query as $dataRow) {
                $col = 'A';
                foreach ($fields as $field) {
                    $value = $this->getNestedValue($dataRow, $field);
                    $sheet->setCellValue($col . $row, $this->formatCsvValue($value));
                    $col++;
                }
                $row++;
            }
            
            // Enable auto-filter.
            //
            // The last column used to be chr(64 + count($headers)), which is
            // right up to Z and then walks off the alphabet: twenty-seven
            // columns asks for '[' and PhpSpreadsheet throws. That was caught
            // below and turned into a CSV named .xlsx, so a wide table's
            // "Export Excel" quietly handed over something Excel would
            // complain about. Now that the columns come from the table rather
            // than being four every time, plenty of them are wide.
            $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                max(1, count($headers))
            );
            $sheet->setAutoFilter('A1:' . $lastColumn . '1');
            
            // Write to output
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);
            
            // Read file content
            $content = file_get_contents($tempFile);
            unlink($tempFile);
            
            // Return response
            $response = $this->response
                ->withType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->withDownload($filename)
                ->withStringBody($content);
            
            return $response;
            
        } catch (\Exception $e) {
            // Fallback to CSV if Excel generation fails
            return $this->doExportCsv($query, str_replace('.xlsx', '', $filename), $headers, $fields);
        }
    }
    
    /**
     * Generate print view
     *
     * @param \Cake\ORM\Query $query The query object
     * @param string $title Report title
     * @param array $headers Column headers
     * @param array $fields Field names to display
     * @return \Cake\Http\Response|null
     */
    public function doExportPrint($query, $title, array $headers, array $fields)
    {
        $data = $query->all()->toArray();

        // The cells are laid out here rather than in the template, which used
        // to reach into each row itself and name only FrozenTime - so a date
        // printed as the locale's short form, 5/10/26, while the same column
        // in the CSV read 2026-05-10, and a value behind a dot never went
        // through any formatting at all. Three exports of one list should not
        // disagree about what is in it.
        $rows = [];
        foreach ($data as $row) {
            $cells = [];
            foreach ($fields as $field) {
                $cells[] = $this->formatCsvValue($this->getNestedValue($row, $field));
            }
            $rows[] = $cells;
        }
        
        // Force print layout (override AppController default)
        $this->viewBuilder()
            ->setLayout('print')
            ->setTemplatePath('Element')
            ->setTemplate('export_print')
            ->disableAutoLayout();
        
        $this->set(compact('data', 'rows', 'title', 'headers', 'fields'));
        
        // Re-enable layout with print
        $this->viewBuilder()->enableAutoLayout();
        $this->viewBuilder()->setLayout('print');
        
        return $this->render();
    }
    
    /**
     * Get nested value from object/array
     *
     * @param mixed $data Data object or array
     * @param string $field Field name (supports dot notation)
     * @return mixed
     */
    private function getNestedValue($data, $field)
    {
        if (strpos($field, '.') !== false) {
            $parts = explode('.', $field);
            $value = $data;
            foreach ($parts as $part) {
                if (is_object($value) && isset($value->$part)) {
                    $value = $value->$part;
                } elseif (is_array($value) && isset($value[$part])) {
                    $value = $value[$part];
                } else {
                    return '';
                }
            }
            return $value;
        }
        
        if (is_object($data) && isset($data->$field)) {
            return $data->$field;
        } elseif (is_array($data) && isset($data[$field])) {
            return $data[$field];
        }
        
        return '';
    }
    
    /**
     * Format value for CSV export
     *
     * @param mixed $value Value to format
     * @return string
     */
    private function formatCsvValue($value)
    {
        // Every date and time, however it is carried.
        //
        // FrozenTime was the only one named, and neither it nor FrozenDate is
        // a \DateTime - both extend DateTimeImmutable - so a date column fell
        // through to (string), which gives the locale's own short form:
        // 5/10/26. Nobody can tell the tenth of May from the fifth of October
        // in that, and no spreadsheet reads it as a date at all.
        // Both the immutable date and the mutable one, since which of the two
        // arrives depends on how the application was bootstrapped.
        if ($value instanceof \Cake\Chronos\Date || $value instanceof \Cake\Chronos\MutableDate) {
            return $value->format('Y-m-d');
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        
        if (is_null($value)) {
            return '';
        }
        
        return (string) $value;
    }
}
