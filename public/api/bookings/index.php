<?php
// Direct API endpoint for bookings - bypasses Laravel routing issues
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include Laravel bootstrap
require_once __DIR__ . '/../../../vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/../../../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Create a request
    $request = Illuminate\Http\Request::capture();
    
    // Handle different HTTP methods
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Get all bookings or specific booking
            $controller = new App\Http\Controllers\BookingController();
            
            // Check if there's an ID in the path
            $pathInfo = $_SERVER['PATH_INFO'] ?? '';
            $segments = array_filter(explode('/', $pathInfo));
            
            if (count($segments) > 0 && end($segments) !== 'index.php') {
                $id = end($segments);
                $response = $controller->show($id);
            } else {
                $response = $controller->index();
            }
            break;
            
        case 'POST':
            // Create new booking
            $controller = new App\Http\Controllers\BookingController();
            $response = $controller->store($request);
            break;
            
        case 'DELETE':
            // Delete booking
            $pathInfo = $_SERVER['PATH_INFO'] ?? '';
            $segments = array_filter(explode('/', $pathInfo));
            
            if (count($segments) > 0) {
                $id = end($segments);
                $controller = new App\Http\Controllers\BookingController();
                $response = $controller->destroy($id);
            } else {
                $response = response()->json(['message' => 'ID required for delete'], 400);
            }
            break;
            
        default:
            $response = response()->json(['message' => 'Method not allowed'], 405);
    }
    
    // Send the response
    http_response_code($response->getStatusCode());
    echo $response->getContent();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Server error',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
