<?php
// Load required models
require_once __DIR__ . '/models/user.php';
require_once __DIR__ . '/models/userDB.php';

echo "=== Running Discount Logic Path-Based Tests ===\n\n";

// Run Discount Logic Path-Based Tests
echo "Testing different discount paths based on spending thresholds and age ranges...\n";
require_once __DIR__ . '/models/unitTestDiscounts.php';
echo "Discount Logic Path-Based Tests Completed.\n\n";

// Run Order Flow Path-Based Tests
echo "Testing complete order flow with different discount scenarios...\n";
require_once __DIR__ . '/models/unitTestOrderFlow.php';
echo "Order Flow Path-Based Tests Completed.\n\n";

echo "=== All Tests Completed ===\n";
echo "Check the error log for detailed test results.\n";
