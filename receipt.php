<?php
/**
 * Generate PDF receipt for donation
 */

require_once 'db.php';
require_once 'lib/fpdf/fpdf.php';

// Check if donation ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$donation_id = intval($_GET['id']);

try {
    // Fetch donation details
    $donation = $db->fetchOne(
        "SELECT * FROM donations WHERE id = ?", 
        [$donation_id]
    );
    
    if (!$donation) {
        throw new Exception('रसीद सापडली नाही / Receipt not found');
    }
    
    // Create PDF class for receipt
    class DonationReceipt extends FPDF
    {
        private $donation;
        
        function __construct($donation)
        {
            parent::__construct();
            $this->donation = $donation;
        }
        
        function Header()
        {
            // Set font for header
            $this->SetFont('Arial', 'B', 18);
            $this->SetTextColor(255, 100, 0); // Orange color
            
            // Title
            $this->Cell(0, 12, 'GANPATI DONATION RECEIPT', 0, 1, 'C');
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 8, 'GANAPATIBAPPA MORAYA', 0, 1, 'C');
            
            // Reset color
            $this->SetTextColor(0, 0, 0);
            $this->Ln(10);
            
            // Draw a line
            $this->Line(10, $this->GetY(), 200, $this->GetY());
            $this->Ln(5);
        }
        
        function Footer()
        {
            $this->SetY(-25);
            $this->Line(10, $this->GetY(), 200, $this->GetY());
            $this->Ln(3);
            $this->SetFont('Arial', 'I', 10);
            $this->SetTextColor(100, 100, 100);
            $this->Cell(0, 5, 'Thank you for your generous donation to Ganpati Celebration!', 0, 1, 'C');
            $this->Cell(0, 5, 'Generated on: ' . date('d-m-Y H:i:s'), 0, 0, 'C');
        }
        
        function GetY()
        {
            return $this->y;
        }
    }
    
    // Create PDF
    $pdf = new DonationReceipt($donation);
    $pdf->AddPage();
    
    // Set font for content
    $pdf->SetFont('Arial', '', 12);
    
    // Receipt details in a box
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Rect(15, $pdf->GetY(), 180, 120);
    
    $y_start = $pdf->GetY() + 10;
    $pdf->SetY($y_start);
    $pdf->SetX(20);
    
    // Receipt Number
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Receipt No:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, $donation['receipt_number'], 0, 1);
    $pdf->Ln(3);
    
    // Date
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Date:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, date('d-m-Y', strtotime($donation['date_created'])), 0, 1);
    $pdf->Ln(3);
    
    // Name
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Name:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, $donation['name'], 0, 1);
    $pdf->Ln(3);
    
    // Flat Number
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Flat No:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, $donation['flat_number'], 0, 1);
    $pdf->Ln(3);
    
    // Amount
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Amount:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, 'Rs. ' . number_format($donation['amount'], 2), 0, 1);
    $pdf->Ln(3);
    
    // Amount in words
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'In Words:', 0, 1);
    $pdf->SetX(20);
    $pdf->SetFont('Arial', '', 11);
    
    // Split long text into multiple lines
    $words = $donation['amount_in_words'];
    $lines = explode(' ', $words);
    $current_line = '';
    $max_width = 160;
    
    foreach ($lines as $word) {
        $test_line = $current_line . ' ' . $word;
        if ($pdf->GetStringWidth($test_line) > $max_width && $current_line != '') {
            $pdf->Cell(0, 6, trim($current_line), 0, 1);
            $pdf->SetX(20);
            $current_line = $word;
        } else {
            $current_line = $test_line;
        }
    }
    if ($current_line != '') {
        $pdf->Cell(0, 6, trim($current_line), 0, 1);
    }
    $pdf->Ln(5);
    
    // Payment Mode
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 8, 'Payment Mode:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, $donation['payment_mode'], 0, 1);
    
    // Add some space and signature area
    $pdf->Ln(20);
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(80, 8, 'Received By:', 0, 0);
    $pdf->Cell(80, 8, 'Signature:', 0, 1, 'R');
    $pdf->Ln(5);
    $pdf->SetX(20);
    $pdf->Line(20, $pdf->GetY(), 80, $pdf->GetY());
    $pdf->Line(130, $pdf->GetY(), 190, $pdf->GetY());
    
    // Output PDF
    $filename = 'receipt_' . $donation['receipt_number'] . '.pdf';
    $pdf->Output('D', $filename);
    
} catch (Exception $e) {
    // Show error page
    ?>
    <!DOCTYPE html>
    <html lang="hi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>त्रुटी - Error</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Khand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Khand', sans-serif; }
        </style>
    </head>
    <body class="bg-red-50 min-h-screen flex items-center justify-center">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="text-red-600 text-6xl mb-4">⚠️</div>
            <h1 class="text-2xl font-bold text-red-800 mb-4">त्रुटी / Error</h1>
            <div class="text-red-700 mb-6">
                <?php echo $e->getMessage(); ?>
            </div>
            <a 
                href="index.php" 
                class="inline-block bg-orange-600 text-white py-2 px-6 rounded-md hover:bg-orange-700 transition duration-200"
            >
                मुख्य पान / Home
            </a>
        </div>
    </body>
    </html>
    <?php
}
?>