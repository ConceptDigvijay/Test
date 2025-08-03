<?php
/**
 * Create a basic receipt template PDF
 */

require_once 'lib/fpdf/fpdf.php';

class ReceiptTemplate extends FPDF
{
    function Header()
    {
        // Set font
        $this->SetFont('Arial', 'B', 16);
        
        // Title
        $this->Cell(0, 10, 'GANPATI DONATION RECEIPT', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'GANAPATIBAPPA MORAYA', 0, 1, 'C');
        $this->Ln(10);
    }
    
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Thank you for your generous donation!', 0, 0, 'C');
    }
}

// Create template
$pdf = new ReceiptTemplate();
$pdf->AddPage();

// Add template content
$pdf->SetFont('Arial', '', 12);

// Receipt fields placeholders
$pdf->Cell(50, 10, 'Receipt No:', 0, 0);
$pdf->Cell(0, 10, '________________', 0, 1);
$pdf->Ln(5);

$pdf->Cell(50, 10, 'Date:', 0, 0);
$pdf->Cell(0, 10, '________________', 0, 1);
$pdf->Ln(5);

$pdf->Cell(50, 10, 'Name:', 0, 0);
$pdf->Cell(0, 10, '________________________________', 0, 1);
$pdf->Ln(5);

$pdf->Cell(50, 10, 'Flat No:', 0, 0);
$pdf->Cell(0, 10, '________________', 0, 1);
$pdf->Ln(5);

$pdf->Cell(50, 10, 'Amount:', 0, 0);
$pdf->Cell(0, 10, 'Rs. ________________', 0, 1);
$pdf->Ln(5);

$pdf->Cell(50, 10, 'In Words:', 0, 0);
$pdf->Ln(5);
$pdf->Cell(0, 10, '____________________________________________', 0, 1);
$pdf->Cell(0, 10, '____________________________________________', 0, 1);
$pdf->Ln(5);

$pdf->Cell(50, 10, 'Payment Mode:', 0, 0);
$pdf->Cell(0, 10, '________________', 0, 1);
$pdf->Ln(20);

$pdf->Cell(50, 10, 'Signature:', 0, 0);
$pdf->Cell(0, 10, '________________', 0, 1);

// Save template
$pdf->Output('F', 'templates/receipt_template.pdf');
echo "Template created successfully!";
?>