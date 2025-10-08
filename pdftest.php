<?php
    require('includes/db.php');
    require('fpdf/fpdf.php');
    $pdf=new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',12);
    $width_cell=array(10,30,20,30);
    $pdf->setfillcolor(193,229,252);
    $pdf->Cell($width_cell[0],10,'Date',1,0,'C',true);

    $pdf->output('Flood Monitoring Record.pdf','I');

?>