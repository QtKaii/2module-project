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
            $user->setFullname($_POST['fullname']);
            $user->setEmail($_POST['email']);
            $user->setDOB($_POST['dob']);

            $userDB->updateUser($user);
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
            $username=$_SESSION['username'];
            $productID=$_POST['productID'];
            $comment = new comment($userID, $productID, $usercomment);
            $commentDB=new commentDB();
            $commentRecord= $commentDB->makeRecord($comment);
            break;

        case '/logout':
            unset($_SESSION['user']);
            header('Location: /');
            break;
        
        case '/update':
            if (isset($_SESSION['user']) && $_SESSION['user']['username'] == 'admin') 
            {
                echo $twig->render('pages/update.html.twig', array('user' => $user));
            } 
            else 
            {
                header('Location: /error'); // Redirect to an error page or another page if the user is not an admin
            }
            break;

        case '/wishlist':
            if (isset($_SESSION['user'])) 
            {
                $userId = $_SESSION['user']['id'];
                $wishlistDB = new wishlistDB();  
                $wishlistItems = $wishlistDB->getWishlistByUser($userId); 
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