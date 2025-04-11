<?php

class unitTestDiscounts
{
    public function __construct()
    {
        error_log('Starting Discount Logic Path-Based Tests');
        
        // Test spending threshold discounts
        $this->testSpendingThresholdDiscounts();
        
        // Test age-based discounts
        $this->testAgeBasedDiscounts();
        
        // Test combined discounts (only larger one should apply)
        $this->testCombinedDiscounts();
        
        error_log('Discount Logic Path-Based Tests Completed');
    }
    
    private function testSpendingThresholdDiscounts()
    {
        error_log('Testing Spending Threshold Discounts:');
        
        // Test case 1: No discount (under $50)
        $subtotal = 49.99;
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        error_log("Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $0.00 | Actual Discount: $" . number_format($spendingDiscount, 2));
        
        // Test case 2: 5% discount ($50-$99.99)
        $subtotal = 75.00;
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        error_log("Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $3.75 | Actual Discount: $" . number_format($spendingDiscount, 2));
        
        // Test case 3: 10% discount ($100-$199.99)
        $subtotal = 150.00;
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        error_log("Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $15.00 | Actual Discount: $" . number_format($spendingDiscount, 2));
        
        // Test case 4: 15% discount ($200+)
        $subtotal = 250.00;
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        error_log("Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $37.50 | Actual Discount: $" . number_format($spendingDiscount, 2));
        
        // Test case 5: Boundary case - exactly $50
        $subtotal = 50.00;
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        error_log("Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $2.50 | Actual Discount: $" . number_format($spendingDiscount, 2));
        
        // Test case 6: Boundary case - exactly $100
        $subtotal = 100.00;
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        error_log("Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $10.00 | Actual Discount: $" . number_format($spendingDiscount, 2));
        
        // Test case 7: Boundary case - exactly $200
        $subtotal = 200.00;
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        error_log("Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $30.00 | Actual Discount: $" . number_format($spendingDiscount, 2));
    }
    
    private function calculateSpendingDiscount($subtotal)
    {
        $spendingDiscount = 0;
        
        // Spending threshold discount
        if ($subtotal >= 200) {
            $spendingDiscount = $subtotal * 0.15; // 15% discount for orders over $200
        } elseif ($subtotal >= 100) {
            $spendingDiscount = $subtotal * 0.10; // 10% discount for orders over $100
        } elseif ($subtotal >= 50) {
            $spendingDiscount = $subtotal * 0.05; // 5% discount for orders over $50
        }
        
        return $spendingDiscount;
    }
    
    private function testAgeBasedDiscounts()
    {
        error_log('Testing Age-Based Discounts:');
        
        // Test case 1: No discount (age 26-64)
        $subtotal = 100.00;
        $age = 40;
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        error_log("Age: $age | Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $0.00 | Actual Discount: $" . number_format($ageDiscount, 2));
        
        // Test case 2: Youth discount (age <= 25)
        $subtotal = 100.00;
        $age = 20;
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        error_log("Age: $age | Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $5.00 | Actual Discount: $" . number_format($ageDiscount, 2));
        
        // Test case 3: Senior discount (age >= 65)
        $subtotal = 100.00;
        $age = 70;
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        error_log("Age: $age | Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $10.00 | Actual Discount: $" . number_format($ageDiscount, 2));
        
        // Test case 4: Boundary case - exactly 25
        $subtotal = 100.00;
        $age = 25;
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        error_log("Age: $age | Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $5.00 | Actual Discount: $" . number_format($ageDiscount, 2));
        
        // Test case 5: Boundary case - exactly 26
        $subtotal = 100.00;
        $age = 26;
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        error_log("Age: $age | Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $0.00 | Actual Discount: $" . number_format($ageDiscount, 2));
        
        // Test case 6: Boundary case - exactly 64
        $subtotal = 100.00;
        $age = 64;
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        error_log("Age: $age | Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $0.00 | Actual Discount: $" . number_format($ageDiscount, 2));
        
        // Test case 7: Boundary case - exactly 65
        $subtotal = 100.00;
        $age = 65;
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        error_log("Age: $age | Subtotal: $" . number_format($subtotal, 2) . " | Expected Discount: $10.00 | Actual Discount: $" . number_format($ageDiscount, 2));
    }
    
    private function calculateAgeDiscount($subtotal, $age)
    {
        $ageDiscount = 0;
        
        // Age-based discount
        if ($age >= 65) {
            $ageDiscount = $subtotal * 0.10; // 10% senior discount
        } elseif ($age <= 25) {
            $ageDiscount = $subtotal * 0.05; // 5% youth discount
        }
        
        return $ageDiscount;
    }
    
    private function testCombinedDiscounts()
    {
        error_log('Testing Combined Discounts (Only Larger One Should Apply):');
        
        // Test case 1: Spending discount > Age discount
        $subtotal = 250.00; // 15% discount = $37.50
        $age = 20; // 5% discount = $12.50
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        $totalDiscount = max($spendingDiscount, $ageDiscount);
        error_log("Subtotal: $" . number_format($subtotal, 2) . " | Age: $age | Spending Discount: $" . 
                 number_format($spendingDiscount, 2) . " | Age Discount: $" . number_format($ageDiscount, 2) . 
                 " | Applied Discount: $" . number_format($totalDiscount, 2));
        
        // Test case 2: Age discount > Spending discount
        $subtotal = 75.00; // 5% discount = $3.75
        $age = 70; // 10% discount = $7.50
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        $totalDiscount = max($spendingDiscount, $ageDiscount);
        error_log("Subtotal: $" . number_format($subtotal, 2) . " | Age: $age | Spending Discount: $" . 
                 number_format($spendingDiscount, 2) . " | Age Discount: $" . number_format($ageDiscount, 2) . 
                 " | Applied Discount: $" . number_format($totalDiscount, 2));
        
        // Test case 3: Equal discounts (should still work)
        $subtotal = 100.00; // 10% discount = $10.00
        $age = 70; // 10% discount = $10.00
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        $totalDiscount = max($spendingDiscount, $ageDiscount);
        error_log("Subtotal: $" . number_format($subtotal, 2) . " | Age: $age | Spending Discount: $" . 
                 number_format($spendingDiscount, 2) . " | Age Discount: $" . number_format($ageDiscount, 2) . 
                 " | Applied Discount: $" . number_format($totalDiscount, 2));
    }
}

// Run the test
$test = new unitTestDiscounts();
?>
