<?php
// load required dependencies
require_once __DIR__ . '/vendor/autoload.php';

// import twig classes for templating
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

  // handle user api requests
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Product.php';

session_start();


$db = new SQLite3(__DIR__ . '/db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

// create user api instance
$userApi = new User($db);
$productApi = new Product($db);

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
$twig->addGlobal('session', $_SESSION);

// get the current request url
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

try {
    // handle different page routes
    switch ($path) {

        case '/api/product/create':
            $name = $_POST['name'];
            $description = $_POST['description'];
            $sale_type = $_POST['sale_type'];
            $base_price = $_POST['base_price'];
            $productApi->createProduct($name, $description, $sale_type, $base_price);
            break;

        case '/api/product/read':
            break;
        
        case '/api/product/update':
            break;
   
        case '/api/product/delete':
            break;


        case '/api/user/create':
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $userApi->createUser($username, $password, $email);
            break;

        case '/api/user/read':
            break;

        case '/api/user/update':
            break;

        case '/api/user/delete':
            break;

        case '/api/user/login':
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = $userApi->login($username, $password);
            if ($user) {
                $_SESSION['user'] = $user;
                // redirect to /
                header('Location: /');
                echo json_encode($user);
            } else {
                http_response_code(401);
            }
            break;
        case '/api/user/logout':
            unset($_SESSION['user']);
            // redirect to /
            header('Location: /');
            break;

        case '/admin/createproduct':
            $user = $_SESSION['user'];

            if (!$user || !$user['is_admin']) {
                http_response_code(403);
                return;
            }
            
            // render create product page
            echo $twig->render('pages/createproduct.html.twig', [
                'current_page' => 'createproduct'
            ]);
            break;



        case '/profile':

            // render profile page
            echo $twig->render('pages/profile.html.twig', [
                'current_page' => 'profile'
            ]);
            break;


        case '/':
        case '/index':
            // get user from session
            // check if undefined first
            $user = $_SESSION['user'] ?? null;


            // render home page with product list
            echo $twig->render('pages/index.html.twig', [
                'current_page' => 'home',
                'user' => $user
            ]);
            break;


        case '/cart':
            // render shopping cart page
            echo $twig->render('pages/cart.html.twig', [
                'current_page' => 'cart',
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