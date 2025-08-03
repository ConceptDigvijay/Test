<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>गणपती वर्गणी - Ganpati Donation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Khand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Khand', sans-serif; }
    </style>
</head>
<body class="bg-orange-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-orange-800 mb-2">🙏 गणपती बाप्पा मोरया 🙏</h1>
            <h2 class="text-2xl font-semibold text-orange-700">वर्गणी फॉर्म / Donation Form</h2>
            <p class="text-orange-600 mt-2">कृपया खालील माहिती भरा / Please fill the information below</p>
        </div>

        <!-- Donation Form -->
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <form action="save.php" method="POST" class="space-y-6">
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        नाव / Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        placeholder="तुमचे नाव लिहा / Enter your name"
                    >
                </div>

                <!-- Flat Number Field -->
                <div>
                    <label for="flat_number" class="block text-sm font-medium text-gray-700 mb-2">
                        फ्लॅट क्रमांक / Flat Number <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="flat_number" 
                        name="flat_number" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        placeholder="जसे: A101, B205"
                    >
                </div>

                <!-- Amount Field -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        रक्कम / Amount (₹) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="amount" 
                        name="amount" 
                        required 
                        min="1" 
                        step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        placeholder="रक्कम लिहा / Enter amount"
                    >
                </div>

                <!-- Payment Mode Field -->
                <div>
                    <label for="payment_mode" class="block text-sm font-medium text-gray-700 mb-2">
                        पेमेंट पद्धत / Payment Mode <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="payment_mode" 
                        name="payment_mode" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    >
                        <option value="">निवडा / Select</option>
                        <option value="Cash">रोख / Cash</option>
                        <option value="UPI">UPI</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="w-full bg-orange-600 text-white py-3 px-4 rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 font-semibold text-lg transition duration-200"
                    >
                        वर्गणी जमा करा / Submit Donation
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600">
            <p class="text-sm">
                🙏 गणपती बाप्पा मोरया, मंगलमूर्ती मोरया 🙏
            </p>
            <p class="text-xs mt-2">
                <a href="admin/" class="text-orange-600 hover:text-orange-800">Admin Login</a>
            </p>
        </div>
    </div>

    <script>
        // Auto-format flat number input
        document.getElementById('flat_number').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            e.target.value = value;
        });

        // Validate amount
        document.getElementById('amount').addEventListener('input', function(e) {
            let value = parseFloat(e.target.value);
            if (value < 0) {
                e.target.value = '';
            }
        });
    </script>
</body>
</html>