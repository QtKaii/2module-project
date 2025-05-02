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
require_once __DIR__ . '/models/unitTestComment.php';
require_once __DIR__ . '/models/unitTestUser.php';
require_once __DIR__ . '/models/userSanit.php';


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
            $DATA=sanit($_POST);
            $state=false;
            //username 4–20 characters
            if (strlen($DATA['username']) < 4 || strlen($DATA['username'])  > 20 )
            {
                $usernameErr='username shpould be 4 to 20 characters';
                $state=true;
            }
            //fullname only letters and spaces, 2–40 characters
            if (!preg_match('/^[A-Za-z ]+$/', $DATA['name']) || strlen($DATA['name']) < 2 || strlen($DATA['name'])  > 40 )
            {
                $fullnameErr='fullname only letters and spaces, 2 to 40 characters';
                $state=true;
            }
            // password 6-30 characters
            if (strlen($DATA['password']) < 6 || strlen($DATA['password'])  > 30)
            {
                $passwordErr='password should be 6-30 characters';
                //error_log($passwordErr);
                $state=true;
            }
            //should be older than 13
            $userDOB=new DateTime($DATA['dob']);
            $currentDate= new DateTime();
            $age= $currentDate->diff($userDOB)->y;
            if($age<13)
            {
                $dobErr='should be older than 13';
                $state=true;
            }
            //check if username is unique
            $userDB= new userDB();
            $usernameExists=$userDB->getUserByName($DATA['username']);
            if($usernameExists)
            {
                $usernameErr="Username already exists";
                $state=true;
            }

            $err= [
                'username'=> $usernameErr ?? null,
                'fullname'=> $fullnameErr ?? null,
                'password'=> $passwordErr ?? null,
                'dob'=> $dobErr ?? null
            ];

            if ($state==true)
            {
                echo $twig->render('pages/registerErr.html.twig', [
                    'err' => $err,
                    'old' => $DATA
                ]);
            }
            //make user obj then add to db
            else
            {
                $user= new user($DATA);
                $userDB->makeUser($user);
                header('Location: /login');
            }
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

        case '/admin/test':
            error_log('Entered case');
            error_log('Comment unit Test');
            $test= new unitTestComment();
            error_log('Comment unit Test done');
            error_log('User unit Test');
            $test= new unitTestUser();
            error_log('User unit Test done');
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
            $userDB = new userDB();

            // Get all cart items before clearing
            $cartItems = $createSales->getSalesByUserId($userId);

            // Add product details to each cart item
            foreach ($cartItems as &$item) {
                $product = $productDB->getProductById($item['productid']);
                $item['product'] = $product;
            }

            // Calculate subtotal
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['product']['price'];
            }

            // Get user information for age-based discount
            $user = $userDB->getUserByID($userId);
            $userDob = $user['dob'];

            // Calculate user age
            $dobDate = DateTime::createFromFormat('d-m-Y', $userDob);
            if (!$dobDate) {
                // Try alternative format if the first one fails
                $dobDate = DateTime::createFromFormat('Y-m-d', $userDob);
            }

            $now = new DateTime();
            $age = 0;

            if ($dobDate) {
                $age = $now->diff($dobDate)->y;
            }

            // Calculate discounts
            $spendingDiscount = 0;
            $ageDiscount = 0;
            $totalDiscount = 0;

            // Spending threshold discount
            if ($subtotal >= 200) {
                $spendingDiscount = $subtotal * 0.15; // 15% discount for orders over $200
            } elseif ($subtotal >= 100) {
                $spendingDiscount = $subtotal * 0.10; // 10% discount for orders over $100
            } elseif ($subtotal >= 50) {
                $spendingDiscount = $subtotal * 0.05; // 5% discount for orders over $50
            }

            // Age-based discount
            if ($age >= 65) {
                $ageDiscount = $subtotal * 0.10; // 10% senior discount
            } elseif ($age <= 25) {
                $ageDiscount = $subtotal * 0.05; // 5% youth discount
            }

            // Apply the larger of the two discounts
            $totalDiscount = max($spendingDiscount, $ageDiscount);

            // Calculate final total
            $total = $subtotal - $totalDiscount;

            // render order summary page with cart items and discount information
            echo $twig->render('pages/ordersummary.html.twig', [
                'current_page' => 'order',
                'cartItems' => $cartItems,
                'subtotal' => $subtotal,
                'spendingDiscount' => $spendingDiscount,
                'ageDiscount' => $ageDiscount,
                'totalDiscount' => $totalDiscount,
                'total' => $total,
                'userAge' => $age
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

        case '/update/product/edit':
            if (isset($_SESSION['user']) && ($_SESSION['user']['username'] == 'admin' || $_SESSION['user']['is_seller'] == 1)) {
                $productId = $_POST['edit'];
                $productDB = new ProductDB();
                $product = $productDB->getProductById($productId);

                if ($product) {
                    echo $twig->render('pages/editProduct.html.twig', ['product' => $product]);
                } else {
                    header('Location: /error');
                }
            } else {
                header('Location: /error');
            }
            break;

        case '/update/product/save':
            if (!isset($_SESSION['user']) || ($_SESSION['user']['username'] != 'admin' && $_SESSION['user']['is_seller'] != 1)) {
                header('Location: /error');
                break;
            }

            // Get the product ID from the form
            $productId = $_POST['id'];
            $productDB = new ProductDB();

            // Get the existing product
            $existingProduct = $productDB->getProductById($productId);
            if (!$existingProduct) {
                header('Location: /error');
                break;
            }

            // Create a product object with the updated data
            $product = new Product($_POST);
            $product->setId($productId);

            // Keep the existing image if no new one is uploaded
            if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
                $product->setImage($existingProduct['image']);
            } else {
                // Handle new image upload
                $uploadDir = __DIR__ . '/uploads/';

                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate a unique filename
                $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $uniqueFilename = uniqid('product_') . '.' . $fileExtension;
                $uploadPath = $uploadDir . $uniqueFilename;

                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    // Set the image path in the product object
                    $product->setImage('/uploads/' . $uniqueFilename);
                    error_log('Image uploaded successfully: ' . $uploadPath);
                } else {
                    error_log('Failed to upload image');
                    $product->setImage($existingProduct['image']);
                }
            }

            // Update the product in the database
            $result = $productDB->updateProduct($product);

            if ($result) {
                // If admin, redirect to products admin page, otherwise redirect to product page
                if ($_SESSION['user']['username'] == 'admin') {
                    header('Location: /update/products');
                } else {
                    header('Location: /product/' . $productId);
                }
            } else {
                header('Location: /error');
            }
            break;

        case '/update/product/delete':
            if (isset($_SESSION['user']) && ($_SESSION['user']['username'] == 'admin' || $_SESSION['user']['is_seller'] == 1)) {
                $productId = $_POST['delete'];
                $productDB = new ProductDB();
                $result = $productDB->deleteProduct($productId);

                if ($result) {
                    header('Location: /update/products');
                } else {
                    header('Location: /error');
                }
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

        case '/productcreation':
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                break;
            }

            // Create a new product from form data
            $product = new Product($_POST);

            $errors=[];

            if(strlen($product->getTitle())<3 || strlen($product->getTitle())>30)
            {
                $errors['title']="Product name should be 3-30 characters long";
                error_log($errors['title']);
            }
            if(strlen($product->getDescription())<10 || strlen($product->getTitle())>100)
            {
                $errors['description']="Product description should be 10-100 characters long";
                error_log($errors['description']);
            }
            if($product->getPrice()>1100)
            {
                $errors['price']="Cannot sell items for more than 1100";
                error_log($errors['price']);
            }
            if(empty($_FILES['image']['name']))
            {
                $errors['image']="Need product image to list product";
                error_log($errors['image']);
            }
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = __DIR__ . '/uploads/';

                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate a unique filename
                $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $uniqueFilename = uniqid('product_') . '.' . $fileExtension;
                $uploadPath = $uploadDir . $uniqueFilename;

                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    // Set the image path in the product object
                    $product->setImage('/uploads/' . $uniqueFilename);
                    error_log('Image uploaded successfully: ' . $uploadPath);
                } else {
                    error_log('Failed to upload image');
                }
            }

            if($errors)
            {
                echo $twig->render('pages/productcreation.html.twig',
                [
                    'current_page'=>'productcreation',
                    'err'=> $errors
                ]);
                exit;
            }
            $productDB = new ProductDB();
            $productId = $productDB->createProduct($product);

            if ($productId) {
                header('Location: /created');
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
                    $userDB = new userDB();
                    $usernames=[];
                    /*
                    foreach ($comments as $comment)
                    {
                       // $commentUserID=$comment[$userID];
                        //$comment['username']=$userDB->getUserNameByID($comment['userID']);
                        //error_log(print_r($comment));
                        //$usernames[$userID] = $user['name'];
                        //$commentUserName=$userDB->getUserByID($indiComment.$userID);
                        //$commentUserID=$indiComment.$userID;
                    }
                    error_log(print_r($comments));
                    */
                    // Check if the current user is the seller of this product
                    $isProductSeller = false;
                    if (isset($_SESSION['user'])) {
                        $isProductSeller = ($_SESSION['user']['id'] == $product['seller_id']) ||
                                          ($_SESSION['user']['username'] == 'admin');
                    }

                    echo $twig->render('pages/product.html.twig', [
                        'product' => $product,
                        'comments' => $comments,
                        'isProductSeller' => $isProductSeller
                        /*tried to make two arrays of username and user id, in twig page use comment id to match to userid array,
                        get index and find matching index in username to show name
                        then tried the simplier method of adding to the comments useing comment['username], didnt get to work
                        */
                        //'username'=> $commentUserName,
                        //'usernameUserID' => $commentUserID
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
