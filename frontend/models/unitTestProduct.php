<?php

class unitTestProduct
{
    public function __construct()
    {
        // Test creating a product with constructor
        $testProduct = new Product([
            'title' => 'Test Product',
            'description' => 'This is a test product description',
            'price' => 99.99,
            'image' => 'test-image.jpg',
            'seller_id' => 1
        ]);
        
        // Test getters
        error_log('Testing Product Getters:');
        error_log('Title: ' . $testProduct->getTitle());
        error_log('Description: ' . $testProduct->getDescription());
        error_log('Price: ' . $testProduct->getPrice());
        error_log('Image: ' . $testProduct->getImage());
        error_log('Seller ID: ' . $testProduct->getSellerId());
        
        // Test setters
        error_log('Testing Product Setters:');
        $testProduct->setId(1);
        $testProduct->setTitle('Updated Test Product');
        $testProduct->setDescription('This is an updated test product description');
        $testProduct->setPrice(149.99);
        $testProduct->setImage('updated-test-image.jpg');
        $testProduct->setSellerId(2);
        
        // Verify setters worked
        error_log('ID: ' . $testProduct->getId());
        error_log('Updated Title: ' . $testProduct->getTitle());
        error_log('Updated Description: ' . $testProduct->getDescription());
        error_log('Updated Price: ' . $testProduct->getPrice());
        error_log('Updated Image: ' . $testProduct->getImage());
        error_log('Updated Seller ID: ' . $testProduct->getSellerId());
        
        // Test creating a product with empty constructor
        $emptyProduct = new Product();
        error_log('Empty Product Test:');
        error_log('Empty Title: ' . ($emptyProduct->getTitle() ?: 'null'));
        error_log('Empty Description: ' . ($emptyProduct->getDescription() ?: 'null'));
        error_log('Empty Price: ' . ($emptyProduct->getPrice() ?: 'null'));
        
        // Test setting values on empty product
        $emptyProduct->setTitle('New Product');
        $emptyProduct->setDescription('New product description');
        $emptyProduct->setPrice(199.99);
        error_log('New Title: ' . $emptyProduct->getTitle());
        error_log('New Description: ' . $emptyProduct->getDescription());
        error_log('New Price: ' . $emptyProduct->getPrice());
    }
}

// Run the test
$test = new unitTestProduct();
?>
