<?php
// Load required models
require_once __DIR__ . '/models/user.php';
require_once __DIR__ . '/models/userDB.php';
require_once __DIR__ . '/models/comment.php';
require_once __DIR__ . '/models/commentDB.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/ProductDB.php';
require_once __DIR__ . '/models/wishlist.php';
require_once __DIR__ . '/models/wishlistDB.php';

echo "=== Running All Unit Tests ===\n\n";

// Run User Tests
echo "Running User Tests...\n";
require_once __DIR__ . '/models/unitTestUser.php';
echo "User Tests Completed.\n\n";

// Run Comment Tests
echo "Running Comment Tests...\n";
require_once __DIR__ . '/models/unitTestComment.php';
echo "Comment Tests Completed.\n\n";

// Run Product Tests
echo "Running Product Tests...\n";
require_once __DIR__ . '/models/unitTestProduct.php';
echo "Product Tests Completed.\n\n";

// Run ProductDB Tests
echo "Running ProductDB Tests...\n";
require_once __DIR__ . '/models/unitTestProductDB.php';
echo "ProductDB Tests Completed.\n\n";

// Run Image Upload Tests
echo "Running Image Upload Tests...\n";
require_once __DIR__ . '/models/unitTestImageUpload.php';
echo "Image Upload Tests Completed.\n\n";

echo "=== All Tests Completed ===\n";
echo "Check the error log for detailed test results.\n";
?>
