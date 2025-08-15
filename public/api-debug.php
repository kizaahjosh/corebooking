<?php
// Simple debug script to test if the server can handle API requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$response = [
    'message' => 'Direct PHP API test working',
    'method' => $_SERVER['REQUEST_METHOD'],
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown'
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
