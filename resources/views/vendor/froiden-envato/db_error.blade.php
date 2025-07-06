<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-12">
        <div class="flex justify-center mb-10">
            <img src="{{ asset('img/logo.png') }}" class="h-16 hover:scale-110 transition-transform duration-300" alt="Home"/>
        </div>

        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="bg-red-600 px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-database text-white text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-white">Database Connection Failed</h2>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-700 font-medium">Unable to establish database connection</p>
                            <p class="text-red-600 mt-1">The application could not connect to the database using the provided credentials.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-800 mb-2">Possible causes:</h4>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>Incorrect database credentials in your .env file</li>
                            <li>Database server is not running</li>
                            <li>Database does not exist</li>
                            <li>Network connectivity issues</li>
                        </ul>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-medium text-blue-800 mb-2">Recommended actions:</h4>
                        <ol class="list-decimal list-inside text-blue-600 space-y-2">
                            <li>Verify your database credentials in the .env file</li>
                            <li>Check if your database server is running</li>
                            <li>Ensure the specified database exists</li>
                            <li>Contact your system administrator if the problem persists</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>