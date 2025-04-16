<?php
require_once '../auth/auth.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Check if user has appropriate role
checkRole(['waiter', 'kasir', 'owner']);

// Get parameters
$type = $_GET['type'] ?? '';
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Initialize dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'helvetica');

$dompdf = new Dompdf($options);

// Generate HTML content
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: helvetica; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; }
        .period { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #e6e6e6; padding: 8px; text-align: center; font-weight: bold; }
        td { padding: 8px; border: 1px solid #000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .total-row { font-weight: bold; }
    </style>
</head>
<body>';

// Generate report based on type
switch($type) {
    case 'sales':
        $html .= generateSalesReportHTML($start_date, $end_date);
        break;
    case 'products':
        $html .= generateProductsReportHTML($start_date, $end_date);
        break;
    case 'waiters':
        $html .= generateWaitersReportHTML($start_date, $end_date);
        break;
    default:
        die('Invalid report type');
}

$html .= '</body></html>';

// Load HTML to dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF
$dompdf->stream('Laporan_' . $type . '_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);

function generateSalesReportHTML($start_date, $end_date) {
    global $conn;
    
    $html = '<div class="header">
        <div class="title">Laporan Penjualan</div>
        <div class="period">Periode: ' . date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date)) . '</div>
    </div>
    <table>
        <tr>
            <th>Tanggal</th>
            <th>Total Order</th>
            <th>Total Penjualan</th>
        </tr>';

    $stmt = $conn->prepare("
        SELECT 
            DATE(t.created_at) as date,
            COUNT(DISTINCT t.order_id) as total_orders,
            SUM(t.total_amount) as total_sales
        FROM transactions t
        WHERE t.created_at BETWEEN ? AND ?
        GROUP BY DATE(t.created_at)
        ORDER BY date DESC
    ");
    $stmt->execute([$start_date, $end_date]);
    
    $total_sales = 0;
    while($row = $stmt->fetch()) {
        $html .= '<tr>
            <td class="text-center">' . date('d/m/Y', strtotime($row['date'])) . '</td>
            <td class="text-center">' . $row['total_orders'] . '</td>
            <td class="text-right">Rp ' . number_format($row['total_sales'], 0, ',', '.') . '</td>
        </tr>';
        $total_sales += $row['total_sales'];
    }

    $html .= '<tr class="total-row">
        <td colspan="2" class="text-right">Total Penjualan</td>
        <td class="text-right">Rp ' . number_format($total_sales, 0, ',', '.') . '</td>
    </tr></table>';

    return $html;
}

function generateProductsReportHTML($start_date, $end_date) {
    global $conn;
    
    $html = '<div class="header">
        <div class="title">Laporan Produk</div>
        <div class="period">Periode: ' . date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date)) . '</div>
    </div>
    <table>
        <tr>
            <th>Produk</th>
            <th>Total Terjual</th>
            <th>Total Pendapatan</th>
        </tr>';

    $stmt = $conn->prepare("
        SELECT 
            p.name,
            SUM(oi.quantity) as total_quantity,
            SUM(oi.quantity * oi.price) as total_revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        JOIN transactions t ON o.id = t.order_id
        WHERE t.created_at BETWEEN ? AND ?
        GROUP BY p.id
        ORDER BY total_quantity DESC
    ");
    $stmt->execute([$start_date, $end_date]);
    
    $total_revenue = 0;
    while($row = $stmt->fetch()) {
        $html .= '<tr>
            <td class="text-left">' . $row['name'] . '</td>
            <td class="text-center">' . $row['total_quantity'] . '</td>
            <td class="text-right">Rp ' . number_format($row['total_revenue'], 0, ',', '.') . '</td>
        </tr>';
        $total_revenue += $row['total_revenue'];
    }

    $html .= '<tr class="total-row">
        <td colspan="2" class="text-right">Total Pendapatan</td>
        <td class="text-right">Rp ' . number_format($total_revenue, 0, ',', '.') . '</td>
    </tr></table>';

    return $html;
}

function generateWaitersReportHTML($start_date, $end_date) {
    global $conn;
    
    $html = '<div class="header">
        <div class="title">Laporan Waiter</div>
        <div class="period">Periode: ' . date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date)) . '</div>
    </div>
    <table>
        <tr>
            <th>Waiter</th>
            <th>Total Order</th>
            <th>Total Penjualan</th>
        </tr>';

    $stmt = $conn->prepare("
        SELECT 
            u.username as waiter_name,
            COUNT(DISTINCT o.id) as total_orders,
            SUM(t.total_amount) as total_sales
        FROM orders o
        JOIN users u ON o.waiter_id = u.id
        JOIN transactions t ON o.id = t.order_id
        WHERE t.created_at BETWEEN ? AND ?
        GROUP BY u.id
        ORDER BY total_sales DESC
    ");
    $stmt->execute([$start_date, $end_date]);
    
    $total_sales = 0;
    while($row = $stmt->fetch()) {
        $html .= '<tr>
            <td class="text-left">' . $row['waiter_name'] . '</td>
            <td class="text-center">' . $row['total_orders'] . '</td>
            <td class="text-right">Rp ' . number_format($row['total_sales'], 0, ',', '.') . '</td>
        </tr>';
        $total_sales += $row['total_sales'];
    }

    $html .= '<tr class="total-row">
        <td colspan="2" class="text-right">Total Penjualan</td>
        <td class="text-right">Rp ' . number_format($total_sales, 0, ',', '.') . '</td>
    </tr></table>';

    return $html;
}
?>