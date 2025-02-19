<?php
require_once __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Ensure proper content type
header('Content-Type: text/html; charset=utf-8');

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create cache directory if it doesn't exist
$cacheDir = __DIR__ . '/cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

// Initialize Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    'cache' => $cacheDir,
    'auto_reload' => true,
    'debug' => true
]);

// Simple router
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Sample product data (in real app, this would come from a database)
$products = [
    ['id' => 1, 'title' => 'Vintage Camera - As Is', 'description' => 'Old camera, untested. Sold as is.', 'price' => '45.50'],
    ['id' => 2, 'title' => 'Antique Pocket Watch', 'description' => 'A beautiful antique pocket watch. Minor wear.', 'price' => '120.00'],
    ['id' => 3, 'title' => 'Retro Video Game Console', 'description' => 'Classic console with one controller. Some scratches.', 'price' => '75.00'],
    ['id' => 4, 'title' => 'Collectible Coin Set', 'description' => 'Rare coin set in protective case. Great condition.', 'price' => '250.00'],
];

try {
    // Basic routing
    switch ($path) {
        case '/':
        case '/index':
            echo $twig->render('pages/index.html.twig', [
                'current_page' => 'home',
                'products' => $products
            ]);
            break;

        case '/login':
            echo $twig->render('pages/login.html.twig', [
                'current_page' => 'login'
            ]);
            break;

        case '/register':
            echo $twig->render('pages/register.html.twig', [
                'current_page' => 'register'
            ]);
            break;

        default:
            // Check if it's a product page
            if (preg_match('/^\/product\/(\d+)$/', $path, $matches)) {
                $productId = (int)$matches[1];
                $product = array_filter($products, function($p) use ($productId) {
                    return $p['id'] === $productId;
                });
                
                if (!empty($product)) {
                    $product = reset($product);
                    echo $twig->render('pages/product.html.twig', [
                        'product' => $product
                    ]);
                } else {
                    http_response_code(404);
                    echo $twig->render('pages/404.html.twig');
                }
            } else {
                http_response_code(404);
                echo $twig->render('pages/404.html.twig');
            }
            break;
    }
} catch (Exception $e) {
    // Log error
    error_log($e->getMessage());
    
    // Show error page in production, show actual error in development
    if (getenv('ENVIRONMENT') === 'production') {
        http_response_code(500);
        echo $twig->render('pages/error.html.twig', [
            'message' => 'An unexpected error occurred.'
        ]);
    } else {
        throw $e;
    }
}