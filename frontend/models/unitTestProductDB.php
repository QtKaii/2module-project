<?php

class unitTestProductDB
{
    public function __construct()
    {
        error_log('Starting ProductDB Unit Tests');
        
        // Initialize ProductDB
        $productDB = new ProductDB();
        
        // Test creating a new product
        $testProduct = new Product([
            'title' => 'Unit Test Product',
            'description' => 'This product was created during unit testing',
            'price' => 123.45,
            'image' => 'test-product.jpg',
            'seller_id' => 1
        ]);
        
        $productId = $productDB->createProduct($testProduct);
        error_log('Created product with ID: ' . $productId);
        
        // Test retrieving the product by ID
        $retrievedProduct = $productDB->getProductById($productId);
        error_log('Retrieved product:');
        error_log('Title: ' . $retrievedProduct['title']);
        error_log('Description: ' . $retrievedProduct['description']);
        error_log('Price: ' . $retrievedProduct['price']);
        
        // Test updating the product
        $testProduct->setId($productId);
        $testProduct->setTitle('Updated Unit Test Product');
        $testProduct->setDescription('This product description was updated during unit testing');
        $testProduct->setPrice(199.99);
        
        $updateResult = $productDB->updateProduct($testProduct);
        error_log('Update result: ' . ($updateResult ? 'success' : 'failure'));
        
        // Verify the update
        $updatedProduct = $productDB->getProductById($productId);
        error_log('Updated product:');
        error_log('Title: ' . $updatedProduct['title']);
        error_log('Description: ' . $updatedProduct['description']);
        error_log('Price: ' . $updatedProduct['price']);
        
        // Test getting all products
        $allProducts = $productDB->getAllProducts();
        error_log('Total products: ' . count($allProducts));
        
        // Test getting products by seller
        $sellerProducts = $productDB->getProductsBySeller(1);
        error_log('Seller 1 products: ' . count($sellerProducts));
        
        // Test getting products by IDs
        $productsByIds = $productDB->getProductsByIds([$productId, 1, 2]);
        error_log('Products by IDs: ' . count($productsByIds));
        
        // Test deleting the product
        $deleteResult = $productDB->deleteProduct($productId);
        error_log('Delete result: ' . ($deleteResult ? 'success' : 'failure'));
        
        // Verify deletion
        $deletedProduct = $productDB->getProductById($productId);
        error_log('Product after deletion: ' . ($deletedProduct ? 'still exists' : 'successfully deleted'));
        
        error_log('ProductDB Unit Tests Completed');
    }
}

// Run the test
$test = new unitTestProductDB();
?>
