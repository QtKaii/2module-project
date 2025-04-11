<?php

class unitTestImageUpload
{
    public function __construct()
    {
        error_log('Starting Image Upload Test');
        
        // Test the image path handling in Product class
        $testProduct = new Product([
            'title' => 'Test Product with Image',
            'description' => 'This is a test product with an image',
            'price' => 99.99,
            'image' => '/uploads/test_image.jpg',
            'seller_id' => 1
        ]);
        
        // Test getters
        error_log('Image path: ' . $testProduct->getImage());
        
        // Test setters
        $testProduct->setImage('/uploads/updated_test_image.jpg');
        error_log('Updated image path: ' . $testProduct->getImage());
        
        // Test with ProductDB
        $productDB = new ProductDB();
        $productId = $productDB->createProduct($testProduct);
        error_log('Created product with ID: ' . $productId);
        
        // Retrieve the product and check the image path
        $retrievedProduct = $productDB->getProductById($productId);
        error_log('Retrieved product image path: ' . $retrievedProduct['image']);
        
        // Clean up
        $productDB->deleteProduct($productId);
        error_log('Image Upload Test Completed');
    }
}

// Run the test
$test = new unitTestImageUpload();
?>
