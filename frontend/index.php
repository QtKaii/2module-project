<?php
// load required dependencies
require_once __DIR__ . '/vendor/autoload.php';

// import twig classes for templating
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/models/user.php';
require_once __DIR__ . '/models/wishlist.php';
require_once __DIR__ . '/models/wishlistDB.php';
require_once __DIR__ . '/models/userDB.php';
require_once __DIR__ . '/models/comment.php';
require_once __DIR__ . '/models/commentDB.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/ProductDB.php';
require_once __DIR__ . '/models/createSales.php';


session_start();

$userDB= new userDB();
 

//$userAPI = new user($db);

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
    switch ($path) 
    {
        case '/api/user/create':
            $user= new user($_POST);
            $userDB= new userDB();
            $userDB->makeUser($user);
            header('Location: /login');
            break;

        case '/api/user/login':
            $username =$_POST['username'];
            $password =$_POST['password'];
            $userDB= new userDB();
            $userlogin = $userDB->login($username, $password);

            if ($userlogin)
            {
                $_SESSION['user'] = $userlogin;
                //error_log(json_encode($_SESSION['user']));
                header('Location: /profile');
            }
            else
            {
                header('Location: /error');
            }
            break;
            
        //shows users table
        case '/update/users':
            $userDB= new userDB();
            $users=$userDB->getAllUsers();
            echo $twig->render('pages/updateUsers.html.twig',['users'=>$users]);
            break;
            
        //shows one user
        case '/update/single/edit':
            error_log('insifr case');
            $username=$_POST['edit'];
            error_log($_POST['edit']);
            $userDB= new userDB();
            $user=$userDB->getUserByName($username);
            error_log(print_r($user, true));  
            echo $twig->render('pages/editUser.html.twig',['user'=>$user]);
            break;

        //shows one user
        case '/update/single/delete':
            error_log('inside delete case');
            $username=$_POST['delete'];
            $userDB= new userDB();
            $user=$userDB->getUserByName($username);
            echo $twig->render('pages/deleteUser.html.twig',['user'=>$user]);
            break;

        //update one user
        case '/update/single/save':
            $userDB = new userDB();
            $userID = $_POST['id'];
            $user= $userDB->getUserByIDobj($userID);
            
            //error_log(print_r($user, true));

            $userDB->updateUser($user, $_POST);
            header('Location: /profile');
            break;
        
        //delete one user
        case '/update/single/deleteUser':
            error_log('inside delerte single log user');
            $userDB = new userDB();
            $userID = $_POST['id'];
            $result = $userDB->deleteUser($userID);
            if ($result) 
            {
                error_log('delete record');
                header('Location: /profile');
            } 
            else 
            {
                error_log('wrong');
                header('Location: /cart');
            }
            break;


        case '/submit/comment':
            $usercomment= $_POST['comment'];
            $userID=$_SESSION['user']['id'];
            error_log(print_r($userID, true));
            $productID=$_POST['productID'];
            $comment = new comment($userID, $productID, $usercomment);
            $commentDB=new commentDB();
            $commentRecord= $commentDB->makeRecord($comment);
            header('Location: /');
            break;

        case '/logout':
            unset($_SESSION['user']);
            header('Location: /');
            break;
        
        case '/update':
            if (isset($_SESSION['user']) && $_SESSION['user']['username'] == 'admin') 
            {
                echo $twig->render('pages/update.html.twig');
            } 
            else 
            {
                header('Location: /error'); 
            }
            break;

        case '/wishlist':
            if (isset($_SESSION['user'])) 
            {
                $userId = $_SESSION['user']['id'];
                $wishlistDB = new wishlistDB();
                $productDB = new ProductDB();
                $wishlistItems = $wishlistDB->getWishlistByUser($userId);
                
                // Get product details for each wishlist item
                foreach ($wishlistItems as &$item) {
                    $product = $productDB->getProductById($item['productid']);
                    $item['product'] = $product;
                }
                
                echo $twig->render('pages/wishlist.html.twig', [
                    'wishlistItems' => $wishlistItems,
                    'current_page' => 'wishlist'
                ]);
            } 
            else 
            {
                header('Location: /login'); 
            }
            break;

        case '/wishlist/add':
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                break;
            }
            
            $userId = $_SESSION['user']['id'];
            $productId = $_POST['productId'];
            $wishlistDB = new wishlistDB();
            $wishlistDB->insertValues($productId, $userId);
            header('Location: /wishlist');
            break;

        case '/wishlist/remove':
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                break;
            }
            
            $wishlistId = $_POST['wishlistId'];
            $wishlistDB = new wishlistDB();
            $wishlistDB->deleteWishlistItem($wishlistId);
            header('Location: /wishlist');
            break;

        case '/cart/add':
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                break;
            }
            
            $userId = $_SESSION['user']['id'];
            $productId = $_POST['productId'];
            $productDB = new ProductDB();
            $product = $productDB->getProductById($productId);
            
            $createSales = new createSales();
            $createSales->insertValues($product['price'], $userId, $productId);
            header('Location: /cart');
            break;
            
 
        case '/profile':
            echo $twig->render('pages/profile.html.twig');
            break;

        case '/':
        case '/index':
            // render home page with product list from database
            $productDB = new ProductDB();
            $products = $productDB->getAllProducts();
            echo $twig->render('pages/index.html.twig', [
                'current_page' => 'home',
                'products' => $products
            ]);
            break;
        case '/cart':
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                break;
            }
            
            $userId = $_SESSION['user']['id'];
            $createSales = new createSales();
            $productDB = new ProductDB();

            // Get all sales for the user
            $cartItems = $createSales->getSalesByUserId($userId);
            
            // Add product details to each cart item
            foreach ($cartItems as &$item) {
                $product = $productDB->getProductById($item['productid']);
                $item['product'] = $product;
            }

            echo $twig->render('pages/cart.html.twig', [
                'current_page' => 'cart',
                'cartItems' => $cartItems
            ]);
            break;

        case '/cart/remove':
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                break;
            }
            
            $salesId = $_POST['salesId'];
            $createSales = new createSales();
            $createSales->deleteSale($salesId);
            header('Location: /cart');
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
                // if error header exists pass to page
                'current_page' => 'register',
                'error' => isset($_SESSION['ERROR']) ? $_SESSION['ERROR'] : null
            ]);

            if (isset($_SESSION['ERROR'])) 
            { 
                unset($_SESSION['ERROR']);
            } 

            break;

        case '/order':
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                break;
            }
            
            $userId = $_SESSION['user']['id'];
            $createSales = new createSales();
            $productDB = new ProductDB();

            // Get all cart items before clearing
            $cartItems = $createSales->getSalesByUserId($userId);
            
            // Add product details to each cart item
            foreach ($cartItems as &$item) {
                $product = $productDB->getProductById($item['productid']);
                $item['product'] = $product;
                
            }

            // render order summary page with cart items
            echo $twig->render('pages/ordersummary.html.twig', [
                'current_page' => 'order',
                'cartItems' => $cartItems
            ]);
            break;

        case '/update/products':
            if (isset($_SESSION['user']) && $_SESSION['user']['username'] == 'admin') {
                $productDB = new ProductDB();
                $products = $productDB->getAllProducts();
                echo $twig->render('pages/updateProducts.html.twig', ['products' => $products]);
            } else {
                header('Location: /error');
            }
            break;

        case (preg_match('/^\/update\/product\/sales\/(\d+)$/', $path, $matches) ? true : false):
            if (isset($_SESSION['user']) && $_SESSION['user']['username'] == 'admin') {
                // Get the product ID from the URL
                $productId = (int)$matches[1];
                $productDB = new ProductDB();
                $createSales = new createSales();

                $product = $productDB->getProductById($productId);
                $sales = $createSales->getSalesByProductId($productId);

                // Calculate totals
                $totalRevenue = 0;
                foreach ($sales as $sale) {
                    $totalRevenue += $sale['cost'];
                }
                $avgPrice = count($sales) > 0 ? $totalRevenue / count($sales) : 0;

                echo $twig->render('pages/productSales.html.twig', [
                    'product' => $product,
                    'sales' => $sales,
                    'totalRevenue' => $totalRevenue,
                    'avgPrice' => $avgPrice
                ]);
            } else {
                header('Location: /error');
            }
            break;
            
        case '/create':
            if (isset($_SESSION['user']) && ($_SESSION['user']['username'] == 'admin' || $_SESSION['user']['is_seller'] == 1)) {
                echo $twig->render('pages/productcreation.html.twig', [
                    'current_page' => 'productcreation'
                ]);
            } else {
                header('Location: /error');
            }
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
                $productDB = new ProductDB();
                $product = $productDB->getProductById($productId);
                
                if ($product) 
                {
                    $commentDB = new commentDB();
                    $comments = $commentDB->getCommentsByProductID($productId);
                    echo $twig->render('pages/product.html.twig', [
                        'product' => $product,
                        'comments' => $comments
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
