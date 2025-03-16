<?php
// load required dependencies
require_once __DIR__ . '/vendor/autoload.php';

// import twig classes for templating
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/models/user.php';
require_once __DIR__ . '/models/userDB.php';


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
            header('Location: /login');
            break;

        case '/api/user/login':
            $username =$_POST['username'];
            $password =$_POST['password'];

            $userDB= new userDB();
            $userlogin = $userDB->login($username, $password);

            if ($userlogin)
            {
                $_SESSION['user']=$user;
                header('Location: /');
                echo json_encode($user);
            }
            else
            {
                echo $twig->render('pages/trial.php');
            }
            break;
            
        case '/update/users':
            $query = 'SELECT * FROM Users';
            $result = $db->query($query);
    
            $users = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC))
            {
                $users[] = $row;
            }
            error_log('displaying table');
            echo $twig->render('pages/updateUsers.html.twig',['user'=> $users,'users'=>$users]);
            break;

        case '/update/single/edit':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userID'])) 
            {
                $userID = (int)$_POST['user_id'];
                $user =$userAPI->getUserByID($userID);
                echo $twig->render('pages/editUsers.html.twigt',['user'=> $users]);
                header("Location: /update/single/edit/$userID");
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
                $userID = (int)$_GET['user_id'];  
        
                $stmr = $db->prepare('SELECT * FROM Users WHERE id = :id');
                $stmr->bindValue(':id', $userID, SQLITE3_INTEGER);
                $result = $stmr->execute();
                $user = $result->fetchArray(SQLITE3_ASSOC);
        
                if ($user) {
                    echo $twig->render('pages/editUser.html.twig', [
                        'user' => $user  
                    ]);
                } 
                else {
                    http_response_code(404);
                    echo $twig->render('pages/cart.html.twig');
                }
            }
            break;

        case '/update/single/delete':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userID'])) 
            {
                $userID = (int)$_POST['user_id'];    

                $stmr = $db->prepare('DELETE FROM Users WHERE id = :id');
                $stmr->bindValue(':id', $userID, SQLITE3_INTEGER);
                $result = $stmr->execute();
                if ($result) 
                {
                    error_log('Did delete');
                    header('Location: /update/users');
                    
                } 
                else 
                {
                    error_log('cant delete');
                    header('Location: /error');
                    exit;
                }
            }
            break;
            

        case '/logout':
            unset($_SESSION['user']);
            header('Location: /');
            break;
        
        case '/update':
            echo $twig->render('pages/update.html.twig',array('user'=>$user));
            break;

        case '/profile':
            echo $twig->render('pages/profile.html.twig');
            break;

        case '/':
        case '/index':
            // render home page with product list
            echo $twig->render('pages/index.html.twig', [
                'current_page' => 'home',
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
            // if error header exists pass to page

            echo $twig->render('pages/register.html.twig', [
                'current_page' => 'register',
                'error' => isset($_SESSION['ERROR']) ? $_SESSION['ERROR'] : null
            ]);

            if (isset($_SESSION['ERROR'])) { 
                unset($_SESSION['ERROR']);
            } 

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