<?php
require_once 'sales.php'; 

class SalesTest {
    public function testSales() {
        $sales = new Sales(0, 0, 0, 0); 

        error_log('Initial Values:');
        error_log('Sales ID: ' . $sales->getSalesId());
        error_log('Cost: ' . $sales->getCost());
        error_log('User ID: ' . $sales->getUserId());
        error_log('Product ID: ' . $sales->getProductId());

        error_log('Setting New Values:');
        $sales->setSalesId(1001);
        $sales->setCost(99.99);
        $sales->setUserId(123);
        $sales->setProductId(456);

        error_log('Updated Values:');
        error_log('Sales ID: ' . $sales->getSalesId());
        error_log('Cost: ' . $sales->getCost());
        error_log('User ID: ' . $sales->getUserId());
        error_log('Product ID: ' . $sales->getProductId());
    }
}

$test = new SalesTest();
$test->testSales();