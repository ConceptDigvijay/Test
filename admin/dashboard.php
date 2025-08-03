<?php
/**
 * Admin Dashboard
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once '../db.php';

// Get statistics
try {
    $total_donations = $db->fetchOne("SELECT COUNT(*) as count FROM donations")['count'];
    $total_amount = $db->fetchOne("SELECT SUM(amount) as total FROM donations")['total'] ?? 0;
    $today_donations = $db->fetchOne("SELECT COUNT(*) as count FROM donations WHERE DATE(date_created) = CURDATE()")['count'];
    $today_amount = $db->fetchOne("SELECT SUM(amount) as total FROM donations WHERE DATE(date_created) = CURDATE()")['total'] ?? 0;
    
    // Get recent donations
    $recent_donations = $db->fetchAll(
        "SELECT * FROM donations ORDER BY date_created DESC LIMIT 10"
    );
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - गणपती वर्गणी</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Khand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Khand', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-orange-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">गणपती वर्गणी - Admin Dashboard</h1>
                <p class="text-orange-100">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
            </div>
            <div class="space-x-4">
                <a href="all_donations.php" class="bg-orange-700 hover:bg-orange-800 px-4 py-2 rounded transition duration-200">
                    All Donations
                </a>
                <a href="logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded transition duration-200">
                    Logout
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                Error: <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Donations -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="text-3xl text-blue-600 mr-4">📊</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Total Donations</h3>
                        <p class="text-2xl font-bold text-blue-600"><?php echo number_format($total_donations); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="text-3xl text-green-600 mr-4">💰</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Total Amount</h3>
                        <p class="text-2xl font-bold text-green-600">₹<?php echo number_format($total_amount, 2); ?></p>
                    </div>
                </div>
            </div>

            <!-- Today's Donations -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="text-3xl text-orange-600 mr-4">📈</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Today's Donations</h3>
                        <p class="text-2xl font-bold text-orange-600"><?php echo number_format($today_donations); ?></p>
                    </div>
                </div>
            </div>

            <!-- Today's Amount -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="text-3xl text-purple-600 mr-4">💵</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Today's Amount</h3>
                        <p class="text-2xl font-bold text-purple-600">₹<?php echo number_format($today_amount, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Recent Donations</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flat No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Mode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($recent_donations)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No donations found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_donations as $donation): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($donation['receipt_number']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($donation['name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($donation['flat_number']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₹<?php echo number_format($donation['amount'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $donation['payment_mode'] == 'Cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                            <?php echo htmlspecialchars($donation['payment_mode']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('d-m-Y H:i', strtotime($donation['date_created'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a 
                                            href="../receipt.php?id=<?php echo $donation['id']; ?>" 
                                            class="text-orange-600 hover:text-orange-900 mr-3"
                                            target="_blank"
                                        >
                                            Download Receipt
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($recent_donations) >= 10): ?>
                <div class="p-4 border-t border-gray-200 text-center">
                    <a 
                        href="all_donations.php" 
                        class="text-orange-600 hover:text-orange-800 font-medium"
                    >
                        View All Donations →
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>