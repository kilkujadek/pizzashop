<?php
require_once 'vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;

define('PIZZA_PREP_TIME', 10);

// Database connection
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'pizza_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: 'root_pass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

$app = AppFactory::create();

// Helper to write JSON response
$jsonResponse = function (Response $response, $data, $status = 200) {
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Cache-Control', 'no-cache')
        ->withStatus($status);
};

// GET /api/items - Fetch menu
$app->get('/api/items', function (Request $request, Response $response) use ($pdo, $jsonResponse) {
    $items = $pdo->query("SELECT * FROM items")->fetchAll(PDO::FETCH_ASSOC);
    return $jsonResponse($response, $items);
});

// POST /api/orders - Create new order
$app->post('/api/orders', function (Request $request, Response $response) use ($pdo, $jsonResponse) {
    $data = json_decode($request->getBody()->getContents(), true);
    $email = trim($data['email'] ?? '');
    $address = trim($data['address'] ?? '');
    $itemsRequest = $data['items'] ?? [];
    
    if (empty($email) || empty($address) || empty($itemsRequest)) {
        return $jsonResponse($response, ['error' => 'Wszystkie pola są wymagane'], 400);
    }

    $total = 0; $quantity = 0; $validatedItems = [];
    foreach ($itemsRequest as $item) {
        $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->execute([$item['id']]);
        $dbItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($dbItem) {
            $qty = max(1, (int)($item['quantity'] ?? 1));
            $total += $dbItem['price'] * $qty;
            $quantity += $qty;
            $validatedItems[] = array_merge($dbItem, ['quantity' => $qty]);
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO orders (email, address, items, quantity) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $address, json_encode($validatedItems), $quantity]);
    
    return $jsonResponse($response, ['id' => $pdo->lastInsertId(), 'status' => 'pending', 'total' => $total]);
});

// GET /api/orders - Current queue status
$app->get('/api/orders', function (Request $request, Response $response) use ($pdo, $jsonResponse) {
    $orders = $pdo->query("SELECT * FROM orders WHERE status='pending' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    $orderCount = count($orders);
    $waitingMinutes = $orderCount * PIZZA_PREP_TIME;

    // Log delays if wait time > 60 minutes
    if ($waitingMinutes > 60) {
        $logEntry = date('Y-m-d H:i:s') . " - Alert: Kolejka przekroczyła 60 minut ($waitingMinutes min)\n";
        file_put_contents('delays.log', $logEntry, FILE_APPEND);
    }
    
    return $jsonResponse($response, [
        'orders' => $orders,
        'waiting_minutes' => $waitingMinutes,
        'pending_orders' => $orderCount
    ]);
});

// GET /api/cron/process - Simple worker endpoint
$app->get('/api/cron/process', function (Request $request, Response $response) use ($pdo, $jsonResponse) {
    $order = $pdo->query("SELECT id FROM orders WHERE status='pending' ORDER BY created_at ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($order) {
        $pdo->prepare("UPDATE orders SET status='completed' WHERE id=?")->execute([$order['id']]);
    }
    return $jsonResponse($response, ['success' => true, 'completed_order_id' => $order['id'] ?? null]);
});

$app->run();
