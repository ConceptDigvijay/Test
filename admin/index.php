<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - गणपती वर्गणी</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Khand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Khand', sans-serif; }
    </style>
</head>
<body class="bg-orange-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-orange-800 mb-2">🔐 Admin Login</h1>
            <h2 class="text-xl font-semibold text-orange-700">गणपती वर्गणी सिस्टम</h2>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">';
                echo $_SESSION['error'];
                echo '</div>';
                unset($_SESSION['error']);
            }
            ?>
            
            <form action="login_process.php" method="POST" class="space-y-6">
                <!-- Username Field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        placeholder="Enter username"
                        value="<?php echo isset($_SESSION['old_username']) ? htmlspecialchars($_SESSION['old_username']) : ''; ?>"
                    >
                    <?php unset($_SESSION['old_username']); ?>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        placeholder="Enter password"
                    >
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="w-full bg-orange-600 text-white py-3 px-4 rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 font-semibold text-lg transition duration-200"
                    >
                        Login
                    </button>
                </div>
            </form>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a 
                href="../index.php" 
                class="text-orange-600 hover:text-orange-800 text-sm"
            >
                ← Back to Donation Form
            </a>
        </div>

        <!-- Default Credentials Note -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <p class="text-sm text-blue-800">
                <strong>Default Login:</strong><br>
                Username: admin<br>
                Password: admin123
            </p>
        </div>
    </div>
</body>
</html>