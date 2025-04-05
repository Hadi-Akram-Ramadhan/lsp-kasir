<?php
function exportToCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Write headers
    if (!empty($data)) {
        fputcsv($output, array_keys($data[0]));
    }
    
    // Write data
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

function formatReportData($data) {
    $formatted = [];
    foreach ($data as $row) {
        $formatted[] = array_map(function($value) {
            if (is_numeric($value) && strpos($value, '.') !== false) {
                return number_format($value, 2);
            }
            return $value;
        }, $row);
    }
    return $formatted;
} 