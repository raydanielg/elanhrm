<!DOCTYPE html>
<html>
<head>
    <title>Worksuite Not installed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<?php
if (file_exists(storage_path('installed'))) {
    print view('vendor.froiden-envato.db_error')->render();
    exit();
}

$laravelVersion = '9.0';

$reqList = [
    '9.0' => [
        'php' => '8.2',
        'mcrypt' => false,
        'openssl' => true,
        'pdo' => true,
        'mbstring' => true,
        'tokenizer' => true,
        'xml' => true,
        'ctype' => true,
        'json' => true,
        'curl' => true,
        'obs' => ''
    ]
];

$strOk = '<i class="fas fa-check-circle text-green-500 text-xl"></i>';
$strFail = '<i class="fas fa-times-circle text-red-500 text-xl"></i>';

$requirements = [
    'php_version' => version_compare(PHP_VERSION, $reqList[$laravelVersion]['php'], ">="),
    'openssl_enabled' => extension_loaded("openssl"),
    'pdo_enabled' => defined('PDO::ATTR_DRIVER_NAME'),
    'mbstring_enabled' => extension_loaded("mbstring"),
    'tokenizer_enabled' => extension_loaded("tokenizer"),
    'xml_enabled' => extension_loaded("xml"),
    'ctype_enabled' => extension_loaded("ctype"),
    'json_enabled' => extension_loaded("json"),
    'mcrypt_enabled' => extension_loaded("mcrypt_encrypt"),
    'curl_enabled' => extension_loaded("curl"),
    'mod_rewrite_enabled' => function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : null
];
?>

<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-center mb-8">
            <img src="{{ asset('img/worksuite-logo.png') }}" class="h-12 hover:scale-105 transition-transform duration-300" alt="Home"/>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 bg-red-100 p-3 rounded-full">
                    <i class="fas fa-exclamation-circle text-red-500 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-red-700 mb-1">
                        Worksuite Installation Required
                    </h3>
                    <p class="text-red-600">
                        The application needs to be installed before you can use it. Please proceed to the installer to complete the setup.
                    </p>
                    <a href="{{ url('/install')}}" class="inline-flex items-center mt-3 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-300">
                        <span>Launch Installer</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-lg font-medium text-gray-900 flex justify-between items-center">
                    <span><i class="fas fa-server mr-2"></i> Server Requirements</span>
                    <span class="text-sm flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-full">
                        <span class="font-medium">Current PHP Version:</span>
                        <span class="px-2 py-0.5 bg-white rounded text-blue-600 font-semibold">{{ phpversion() }}</span>
                        @if (version_compare(PHP_VERSION, '8.2') > 0)
                            <span class="flex items-center text-green-500">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span class="text-xs font-medium">Compatible</span>
                            </span>
                            {!! $strOk !!}
                        @else
                            <span class="flex items-center text-red-500">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <span class="text-xs font-medium">Upgrade Required</span>
                            </span>
                            {!! $strFail !!}
                        @endif
                    </span>
                </h3>
            </div>

            <div class="px-6 py-4">
                <div class="space-y-2">
                    <p class="flex items-center justify-between p-1 hover:bg-gray-50 rounded transition-colors duration-200">
                        <span><i class="fab fa-php mr-2 text-indigo-500"></i> PHP >= {{ $reqList[$laravelVersion]['php'] }}</span>
                        {!! $requirements['php_version'] ? $strOk : $strFail !!}
                    </p>

                    @if($reqList[$laravelVersion]['openssl'])
                    <p class="flex items-center justify-between p-1 hover:bg-gray-50 rounded transition-colors duration-200">
                        <span><i class="fas fa-lock mr-2 text-green-500"></i> OpenSSL PHP Extension</span>
                        {!! $requirements['openssl_enabled'] ? $strOk : $strFail !!}
                    </p>
                    @endif

                    @if($reqList[$laravelVersion]['pdo'])
                    <p class="flex items-center justify-between p-1 hover:bg-gray-50 rounded transition-colors duration-200">
                        <span><i class="fas fa-database mr-2 text-blue-500"></i> PDO PHP Extension</span>
                        {!! $requirements['pdo_enabled'] ? $strOk : $strFail !!}
                    </p>
                    @endif

                    @if($reqList[$laravelVersion]['mbstring'])
                    <p class="flex items-center justify-between p-1 hover:bg-gray-50 rounded transition-colors duration-200">
                        <span><i class="fas fa-font mr-2 text-purple-500"></i> Mbstring PHP Extension</span>
                        {!! $requirements['mbstring_enabled'] ? $strOk : $strFail !!}
                    </p>
                    @endif

                    @if($reqList[$laravelVersion]['tokenizer'])
                    <p class="flex items-center justify-between p-1 hover:bg-gray-50 rounded transition-colors duration-200">
                        <span><i class="fas fa-key mr-2 text-yellow-500"></i> Tokenizer PHP Extension</span>
                        {!! $requirements['tokenizer_enabled'] ? $strOk : $strFail !!}
                    </p>
                    @endif

                    @if($reqList[$laravelVersion]['xml'])
                    <p class="flex items-center justify-between p-1 hover:bg-gray-50 rounded transition-colors duration-200">
                        <span><i class="fas fa-code mr-2 text-red-500"></i> XML PHP Extension</span>
                        {!! $requirements['xml_enabled'] ? $strOk : $strFail !!}
                    </p>
                    @endif

                    @if($reqList[$laravelVersion]['ctype'])
                    <p class="flex items-center justify-between p-1 hover:bg-gray-50 rounded transition-colors duration-200">
                        <span><i class="fas fa-text-width mr-2 text-orange-500"></i> CTYPE PHP Extension</span>
                        {!! $requirements['ctype_enabled'] ? $strOk : $strFail !!}
                    </p>
                    @endif

                    @if($reqList[$laravelVersion]['json'])
                    <p class="flex items-center justify-between p-1 hover:bg-gray-50 rounded transition-colors duration-200">
                        <span><i class="fas fa-code mr-2 text-pink-500"></i> JSON PHP Extension</span>
                        {!! $requirements['json_enabled'] ? $strOk : $strFail !!}
                    </p>
                    @endif

                    @if($reqList[$laravelVersion]['mcrypt'])
                    <p class="flex items-center justify-between p-1 hover:bg-gray-50 rounded transition-colors duration-200">
                        <span><i class="fas fa-shield-alt mr-2 text-gray-500"></i> Mcrypt PHP Extension</span>
                        {!! $requirements['mcrypt_enabled'] ? $strOk : $strFail !!}
                    </p>
                    @endif

                    @if($reqList[$laravelVersion]['curl'])
                    <p class="flex items-center justify-between p-1 hover:bg-gray-50 rounded transition-colors duration-200">
                        <span><i class="fas fa-exchange-alt mr-2 text-teal-500"></i> Curl Extension</span>
                        {!! $requirements['curl_enabled'] ? $strOk : $strFail !!}
                    </p>
                    @endif

                    @if(!empty($reqList[$laravelVersion]['obs']))
                        <p class="text-sm text-gray-500 italic">{{ $reqList[$laravelVersion]['obs'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
