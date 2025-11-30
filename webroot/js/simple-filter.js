<script>
jQuery(document).ready(function($) {
    console.log('Filter script loaded!');
    
    // Find all tables
    $('table').each(function() {
        var $table = $(this);
        var $thead = $table.find('thead');
        
        if ($thead.length === 0) {
            console.log('No thead found in table');
            return;
        }
        
        var $headerRow = $thead.find('tr').first();
        if ($headerRow.length === 0) {
            console.log('No header row found');
            return;
        }
        
        // Check if filter row already exists
        if ($thead.find('tr.filter-row').length > 0) {
            console.log('Filter row already exists');
            return;
        }
        
        console.log('Creating filter row...');
        
        // Create filter row
        var $filterRow = $('<tr class="filter-row"></tr>');
        $filterRow.css({
            'background-color': '#f8f9fa',
            'border-bottom': '2px solid #dee2e6'
        });
        
        // Add filter input for each header
        $headerRow.find('th').each(function(index) {
            var $th = $(this);
            var $filterCell = $('<th></th>');
            $filterCell.css('padding', '8px 12px');
            
            // Don't add filter to Actions column
            if ($th.text().toLowerCase().includes('action')) {
                $filterRow.append($filterCell);
                return;
            }
            
            // Create filter input
            var $input = $('<input type="text" class="form-control form-control-sm ajax-filter" placeholder="Filter...">');
            $input.css({
                'width': '100%',
                'padding': '4px 8px',
                'font-size': '13px',
                'border': '1px solid #ced4da',
                'border-radius': '4px'
            });
            
            // Store column index
            $input.data('column-index', index);
            
            // Add filter event
            $input.on('keyup', function() {
                var filterValue = $(this).val().toLowerCase();
                var colIndex = $(this).data('column-index');
                
                console.log('Filtering column', colIndex, 'with value:', filterValue);
                
                // Filter rows
                $table.find('tbody tr').each(function() {
                    var $row = $(this);
                    var cellText = $row.find('td').eq(colIndex).text().toLowerCase();
                    
                    if (cellText.includes(filterValue) || filterValue === '') {
                        $row.show();
                    } else {
                        $row.hide();
                    }
                });
            });
            
            $filterCell.append($input);
            $filterRow.append($filterCell);
        });
        
        // Insert filter row after header row
        $headerRow.after($filterRow);
        console.log('Filter row created successfully!');
    });
});
</script>
