<?php
// load required dependencies
require_once __DIR__ . '/vendor/autoload.php';

// import twig classes for templating
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// ensure proper content type
header('Content-Type: text/html; charset=utf-8');

// enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// create cache directory for twig templates
$cacheDir = __DIR__ . '/cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

// setup twig template engine with configuration
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    'cache' => $cacheDir,
    'auto_reload' => true,
    'debug' => true
]);

// get the current request url
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// mock product data for demonstration
$products = [
    ['id' => 1, 'title' => 'Vintage Camera - As Is', 'description' => 'Old camera, untested. Sold as is.', 'price' => '45.50', 'image' => '/images/vintage.jpg'],
    ['id' => 2, 'title' => 'Antique Pocket Watch', 'description' => 'A beautiful antique pocket watch. Minor wear.', 'price' => '120.00', 'image' => '/images/pocketwatch.jpg'],
    ['id' => 3, 'title' => 'Retro NES Video Game Console', 'description' => 'Classic NES with one controller. Some scratches.', 'price' => '75.00', 'image' => '/images/retro.jpg'],
    ['id' => 4, 'title' => 'Collectible Coin Set', 'description' => 'Rare coin set in protective case. Great condition.', 'price' => '250.00', 'image' => '/images/coins.jpg'],
];

try {
    // handle different page routes
    switch ($path) {
        case '/':
        case '/index':
            // render home page with product list
            echo $twig->render('pages/index.html.twig', [
                'current_page' => 'home',
                'products' => $products
            ]);
            break;
        case '/cart':
            // render shopping cart page
            echo $twig->render('pages/cart.html.twig', [
                'current_page' => 'cart'
            ]);
            break;
        case '/wishlist':
            // render wishlist page
            echo $twig->render('pages/wishlist.html.twig', [
                'current_page' => 'wishlist'
            ]);
            break;

        case '/login':
            // render login page
            echo $twig->render('pages/login.html.twig', [
                'current_page' => 'login'
            ]);
            break;

        case '/register':
            // render registration page
            echo $twig->render('pages/register.html.twig', [
                'current_page' => 'register'
            ]);
            break;

        case '/order':
            // render order summary page
            echo $twig->render('pages/ordersummary.html.twig', [
                'current_page' => 'order'
            ]);
            break;

        case '/create':
            // render create product page
            echo $twig->render('pages/productcreation.html.twig', [
                'current_page' => 'productcreation'
            ]);
            break;

        case '/created':
            // render product created confirmation page
            echo $twig->render('pages/productMade.html.twig', [
                'current_page' => 'prodectConfirm'
            ]);
            break;

        default:
            // check if url matches product detail pattern
            if (preg_match('/^\/product\/(\d+)$/', $path, $matches)) {
                $productId = (int)$matches[1];
                // find product by id
                $product = array_filter($products, function($p) use ($productId) {
                    return $p['id'] === $productId;
                });
                
                if (!empty($product)) {
                    // render product detail page if found
                    $product = reset($product);
                    echo $twig->render('pages/product.html.twig', [
                        'product' => $product
                    ]);
                } else {
                    // show 404 if product not found
                    http_response_code(404);
                    echo $twig->render('pages/404.html.twig');
                }
            } else {
                // show 404 for invalid routes
                http_response_code(404);
                echo $twig->render('pages/404.html.twig');
            }
            break;
    }
} catch (Exception $e) {
    // log any errors that occur
    error_log($e->getMessage());
    
    // handle errors differently in production vs development
    if (getenv('ENVIRONMENT') === 'production') {
        http_response_code(500);
        echo $twig->render('pages/error.html.twig', [
            'message' => 'An unexpected error occurred.'
        ]);
    } else {
        throw $e;
    }
}