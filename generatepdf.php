<?php
require('includes/db.php');
$sql = "SELECT * FROM sensor_readings2 ORDER BY reading_date DESC, reading_time DESC LIMIT 0,10";
require('fpdf/fpdf.php');

// Get current date and time
$currentDateTime = date('Y-m-d H:i:s');
$pdf = new FPDF('P','mm','A4');
$pdf->SetTitle('Flood Monitoring Record');
$pdf->SetAutoPageBreak(true, 10);

$pdf->AddPage();

// Header
$pdf->SetFont('Arial','B',16);
$pdf->Cell(190, 10, 'Flood Monitoring Record', 0, 1, 'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(190, 10, 'Generated on: ' . $currentDateTime, 0, 1, 'C');
$pdf->Ln(10);

// Table Header
$pdf->SetFont('Arial','B',12);
$pdf->SetFillColor(79, 129, 189); 
$pdf->SetTextColor(255);
$pdf->SetLeftMargin(35);

// Column widths
$width_date = 40;
$width_time = 40;
$width_level = 50;
$width_risk = 50;

// Header cells
// $pdf->Cell($width_date, 10, 'Date', 1, 0, 'C', true);
$pdf->Cell($width_time, 10, 'Time', 1, 0, 'C', true);
$pdf->Cell($width_level, 10, 'Water Level (m)', 1, 0, 'C', true);
$pdf->Cell($width_risk, 10, 'Status', 1, 1, 'C', true);


// Data rows
$pdf->SetFont('Arial','',11);
$pdf->SetTextColor(0);
$pdf->SetFillColor(240, 240, 240); 

$fill = false;
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Determine risk level
        $water_level = $row['water_level'];
        if ($water_level < 0.5) {
            $risk_level = 'Normal';
            $risk_color = array(46, 204, 113); // Green
        } elseif ($water_level >= 0.5 && $water_level < 1.0) {
            $risk_level = 'Warning';
            $risk_color = array(243, 156, 18); // Orange
        } else {
            $risk_level = 'Danger';
            $risk_color = array(231, 76, 60); // Red
        }
        
        // Date cell
        // $pdf->Cell($width_date, 10, $row['reading_date'], 1, 0, 'C', $fill);
        $formatted_time = date('H:i:s', strtotime($row['reading_time']));
        $pdf->Cell($width_time, 10, $formatted_time, 1, 0, 'C', $fill);
        $pdf->Cell($width_level, 10, number_format($water_level, 2), 1, 0, 'C', $fill);
        
        //cell with color
        $pdf->SetFillColor($risk_color[0], $risk_color[1], $risk_color[2]);
        $pdf->SetTextColor(255); // White text for risk cells
        $pdf->Cell($width_risk, 10, $risk_level, 1, 1, 'C', true);
        
        // Reset colors for next row
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetTextColor(0);
        
        $fill = !$fill; // Alternate row background
    }
} else {
    $pdf->SetFillColor(255);
    $pdf->Cell($width_date + $width_time + $width_level + $width_risk, 10, 'No data available', 1, 1, 'C', true);
}

// Add some space
$pdf->Ln(15);

// Add summary information
$pdf->SetFont('Arial','B',12);
$pdf->Cell(40, 10, 'Risk Level Guide:', 0, 1);
$pdf->SetFont('Arial','',11);

// Normal risk
$pdf->SetFillColor(46, 204, 113);
$pdf->SetTextColor(255);
$pdf->Cell(15, 8, '', 0, 0, 'C', true);
$pdf->SetTextColor(0);
$pdf->Cell(60, 8, ' Normal: < 0.5m', 0, 1);

// Warning risk
$pdf->SetFillColor(243, 156, 18);
$pdf->SetTextColor(255);
$pdf->Cell(15, 8, '', 0, 0, 'C', true);
$pdf->SetTextColor(0);
$pdf->Cell(60, 8, ' Warning: 0.5m - 1.0m', 0, 1);

// Danger risk
$pdf->SetFillColor(231, 76, 60);
$pdf->SetTextColor(255);
$pdf->Cell(15, 8, '', 0, 0, 'C', true);
$pdf->SetTextColor(0);
$pdf->Cell(60, 8, ' Danger: >= 1.0m', 0, 1);

// Add footer with generation info
// $pdf->SetY(-30);
// $pdf->SetFont('Arial','I',10);
// $pdf->Cell(0, 10, 'Document generated on: ' . $currentDateTime, 0, 0, 'C');

$pdf->Output('Flood_Monitoring_Record.pdf', 'I');
?>