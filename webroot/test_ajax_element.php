<?php
// Quick test to verify element JavaScript syntax
$currentPage = 1;
$totalRecords = 100;
$limit = 50;
$totalPages = ceil($totalRecords / $limit);
$prevPage = $currentPage - 1;
$nextPage = $currentPage + 1;

$h = '<div class="ajax-pagination">';
$h .= '<div>';
$h .= '<button class="page-btn" data-page="1"' . ($currentPage === 1 ? ' disabled' : '') . '>First</button>';
$h .= '<button class="page-btn" data-page="' . $prevPage . '"' . ($currentPage === 1 ? ' disabled' : '') . '>Previous</button>';
$h .= '</div>';
$h .= '<div>Page ' . $currentPage . ' of ' . $totalPages . '</div>';
$h .= '<div>';
$h .= '<button class="page-btn" data-page="' . $nextPage . '"' . ($currentPage >= $totalPages ? ' disabled' : '') . '>Next</button>';
$h .= '<button class="page-btn" data-page="' . $totalPages . '"' . ($currentPage >= $totalPages ? ' disabled' : '') . '>Last</button>';
$h .= '</div></div>';

echo "Generated HTML:\n";
echo htmlspecialchars($h);
echo "\n\nRendered:\n";
echo $h;
?>
