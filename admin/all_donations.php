<?php
/**
 * All Donations View
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once '../db.php';

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
$search_params = [];

if (!empty($search)) {
    $search_condition = " WHERE name LIKE ? OR flat_number LIKE ? OR receipt_number LIKE ?";
    $search_params = ["%$search%", "%$search%", "%$search%"];
}

try {
    // Get total count
    $total_count_query = "SELECT COUNT(*) as count FROM donations" . $search_condition;
    $total_count = $db->fetchOne($total_count_query, $search_params)['count'];
    $total_pages = ceil($total_count / $per_page);
    
    // Get donations for current page
    $donations_query = "SELECT * FROM donations" . $search_condition . " ORDER BY date_created DESC LIMIT $per_page OFFSET $offset";
    $donations = $db->fetchAll($donations_query, $search_params);
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Donations - गणपती वर्गणी</title>
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
                <h1 class="text-2xl font-bold">All Donations</h1>
                <p class="text-orange-100">Total: <?php echo number_format($total_count); ?> donations</p>
            </div>
            <div class="space-x-4">
                <a href="dashboard.php" class="bg-orange-700 hover:bg-orange-800 px-4 py-2 rounded transition duration-200">
                    Dashboard
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

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search by name, flat number, or receipt number..."
                        value="<?php echo htmlspecialchars($search); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    >
                </div>
                <button 
                    type="submit" 
                    class="bg-orange-600 text-white px-6 py-2 rounded-md hover:bg-orange-700 transition duration-200"
                >
                    Search
                </button>
                <?php if (!empty($search)): ?>
                    <a 
                        href="all_donations.php" 
                        class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-200 text-center"
                    >
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Donations Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
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
                        <?php if (empty($donations)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    <?php echo empty($search) ? 'No donations found' : 'No donations match your search'; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($donations as $donation): ?>
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
                                            class="text-orange-600 hover:text-orange-900"
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

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                                   class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing 
                                    <span class="font-medium"><?php echo $offset + 1; ?></span>
                                    to 
                                    <span class="font-medium"><?php echo min($offset + $per_page, $total_count); ?></span>
                                    of 
                                    <span class="font-medium"><?php echo number_format($total_count); ?></span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                                           class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            Previous
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                                           class="relative inline-flex items-center px-4 py-2 border text-sm font-medium
                                                  <?php echo $i == $page ? 'z-10 bg-orange-50 border-orange-500 text-orange-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                                           class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            Next
                                        </a>
                                    <?php endif; ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>