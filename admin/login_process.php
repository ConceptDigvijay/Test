<?php
/**
 * Admin login process
 */

session_start();
require_once '../db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'कृपया सर्व फील्ड भरा / Please fill all fields';
    $_SESSION['old_username'] = $username;
    header('Location: index.php');
    exit;
}

try {
    // Check admin credentials
    $admin = $db->fetchOne(
        "SELECT * FROM admins WHERE username = ?", 
        [$username]
    );
    
    if ($admin && password_verify($password, $admin['password'])) {
        // Login successful
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_logged_in'] = true;
        
        header('Location: dashboard.php');
        exit;
    } else {
        // Login failed
        $_SESSION['error'] = 'अवैध यूजरनेम किंवा पासवर्ड / Invalid username or password';
        $_SESSION['old_username'] = $username;
        header('Location: index.php');
        exit;
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'लॉगिन त्रुटी: ' . $e->getMessage() . ' / Login error: ' . $e->getMessage();
    $_SESSION['old_username'] = $username;
    header('Location: index.php');
    exit;
}
?>