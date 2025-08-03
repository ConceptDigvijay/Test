<?php
/**
 * Save donation and generate receipt
 */

require_once 'db.php';
require_once 'lib/marathi_converter.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Validate and sanitize input
$name = trim($_POST['name'] ?? '');
$flat_number = trim($_POST['flat_number'] ?? '');
$amount = floatval($_POST['amount'] ?? 0);
$payment_mode = $_POST['payment_mode'] ?? '';

// Validation
$errors = array();

if (empty($name)) {
    $errors[] = 'नाव आवश्यक आहे / Name is required';
}

if (empty($flat_number)) {
    $errors[] = 'फ्लॅट क्रमांक आवश्यक आहे / Flat number is required';
}

if ($amount <= 0) {
    $errors[] = 'वैध रक्कम प्रविष्ट करा / Please enter a valid amount';
}

if (!in_array($payment_mode, ['Cash', 'UPI'])) {
    $errors[] = 'वैध पेमेंट पद्धत निवडा / Please select a valid payment mode';
}

if (!empty($errors)) {
    $error_message = implode('<br>', $errors);
    include 'error_page.php';
    exit;
}

try {
    // Generate receipt number
    $receipt_number = 'GD' . date('Y') . sprintf('%06d', rand(1, 999999));
    
    // Check if receipt number already exists
    $check_receipt = $db->fetchOne(
        "SELECT id FROM donations WHERE receipt_number = ?", 
        [$receipt_number]
    );
    
    // If exists, generate a new one
    while ($check_receipt) {
        $receipt_number = 'GD' . date('Y') . sprintf('%06d', rand(1, 999999));
        $check_receipt = $db->fetchOne(
            "SELECT id FROM donations WHERE receipt_number = ?", 
            [$receipt_number]
        );
    }
    
    // Convert amount to Marathi words
    $amount_in_words = MarathiNumberConverter::convertToWords($amount);
    
    // Insert donation record
    $donation_id = $db->insert(
        "INSERT INTO donations (receipt_number, name, flat_number, amount, amount_in_words, payment_mode) VALUES (?, ?, ?, ?, ?, ?)",
        [$receipt_number, $name, $flat_number, $amount, $amount_in_words, $payment_mode]
    );
    
    if ($donation_id) {
        // Redirect to receipt generation
        header("Location: receipt.php?id=" . $donation_id);
        exit;
    } else {
        throw new Exception('डेटा सेव्ह करण्यात अयशस्वी / Failed to save data');
    }
    
} catch (Exception $e) {
    $error_message = 'त्रुटी: ' . $e->getMessage() . ' / Error: ' . $e->getMessage();
    include 'error_page.php';
}
?>

<?php
// error_page.php content embedded
if (isset($error_message)):
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
            <?php echo $error_message; ?>
        </div>
        <a 
            href="index.php" 
            class="inline-block bg-orange-600 text-white py-2 px-6 rounded-md hover:bg-orange-700 transition duration-200"
        >
            परत जा / Go Back
        </a>
    </div>
</body>
</html>
<?php
endif;
?>