<?php

class unitTestOrderFlow
{
    public function __construct()
    {
        error_log('Starting Order Flow Path-Based Tests');
        
        // Test complete order flow with different discount scenarios
        $this->testOrderFlowWithDiscounts();
        
        error_log('Order Flow Path-Based Tests Completed');
    }
    
    private function testOrderFlowWithDiscounts()
    {
        error_log('Testing Order Flow with Different Discount Scenarios:');
        
        // Scenario 1: Order with no discount (subtotal < $50, age 30-64)
        $this->simulateOrderFlow(45.00, 35);
        
        // Scenario 2: Order with spending discount only (subtotal >= $50, age 30-64)
        $this->simulateOrderFlow(75.00, 35);
        
        // Scenario 3: Order with spending discount only (subtotal >= $100, age 30-64)
        $this->simulateOrderFlow(150.00, 35);
        
        // Scenario 4: Order with spending discount only (subtotal >= $200, age 30-64)
        $this->simulateOrderFlow(250.00, 35);
        
        // Scenario 5: Order with age discount only (subtotal < $50, age <= 25)
        $this->simulateOrderFlow(45.00, 20);
        
        // Scenario 6: Order with age discount only (subtotal < $50, age >= 65)
        $this->simulateOrderFlow(45.00, 70);
        
        // Scenario 7: Order with both discounts, spending larger (subtotal >= $200, age <= 25)
        $this->simulateOrderFlow(250.00, 20);
        
        // Scenario 8: Order with both discounts, age larger (subtotal >= $50, age >= 65)
        $this->simulateOrderFlow(75.00, 70);
        
        // Scenario 9: Order with equal discounts (subtotal = $100, age >= 65)
        $this->simulateOrderFlow(100.00, 70);
    }
    
    private function simulateOrderFlow($subtotal, $age)
    {
        error_log("Simulating Order Flow - Subtotal: $" . number_format($subtotal, 2) . ", Age: $age");
        
        // Calculate spending discount
        $spendingDiscount = $this->calculateSpendingDiscount($subtotal);
        
        // Calculate age discount
        $ageDiscount = $this->calculateAgeDiscount($subtotal, $age);
        
        // Apply the larger discount
        $totalDiscount = max($spendingDiscount, $ageDiscount);
        
        // Calculate final total
        $total = $subtotal - $totalDiscount;
        
        // Log the results
        error_log("  Subtotal: $" . number_format($subtotal, 2));
        
        if ($spendingDiscount > 0) {
            $discountPercent = $this->getSpendingDiscountPercent($subtotal);
            error_log("  Spending Discount ($discountPercent% off): -$" . number_format($spendingDiscount, 2));
        } else {
            error_log("  Spending Discount: None");
        }
        
        if ($ageDiscount > 0) {
            $discountPercent = $this->getAgeDiscountPercent($age);
            error_log("  Age Discount ($discountPercent% off): -$" . number_format($ageDiscount, 2));
        } else {
            error_log("  Age Discount: None");
        }
        
        error_log("  Applied Discount: -$" . number_format($totalDiscount, 2));
        error_log("  Final Total: $" . number_format($total, 2));
        error_log("  Path: " . $this->determineDiscountPath($subtotal, $age, $spendingDiscount, $ageDiscount));
        error_log("");
    }
    
    private function calculateSpendingDiscount($subtotal)
    {
        $spendingDiscount = 0;
        
        if ($subtotal >= 200) {
            $spendingDiscount = $subtotal * 0.15; // 15% discount for orders over $200
        } elseif ($subtotal >= 100) {
            $spendingDiscount = $subtotal * 0.10; // 10% discount for orders over $100
        } elseif ($subtotal >= 50) {
            $spendingDiscount = $subtotal * 0.05; // 5% discount for orders over $50
        }
        
        return $spendingDiscount;
    }
    
    private function calculateAgeDiscount($subtotal, $age)
    {
        $ageDiscount = 0;
        
        if ($age >= 65) {
            $ageDiscount = $subtotal * 0.10; // 10% senior discount
        } elseif ($age <= 25) {
            $ageDiscount = $subtotal * 0.05; // 5% youth discount
        }
        
        return $ageDiscount;
    }
    
    private function getSpendingDiscountPercent($subtotal)
    {
        if ($subtotal >= 200) {
            return 15;
        } elseif ($subtotal >= 100) {
            return 10;
        } elseif ($subtotal >= 50) {
            return 5;
        }
        return 0;
    }
    
    private function getAgeDiscountPercent($age)
    {
        if ($age >= 65) {
            return 10;
        } elseif ($age <= 25) {
            return 5;
        }
        return 0;
    }
    
    private function determineDiscountPath($subtotal, $age, $spendingDiscount, $ageDiscount)
    {
        // Determine which discount path was taken
        $path = "Path: ";
        
        // Spending threshold path
        if ($subtotal < 50) {
            $path .= "Subtotal < $50 (No spending discount)";
        } elseif ($subtotal < 100) {
            $path .= "Subtotal $50-$99.99 (5% spending discount)";
        } elseif ($subtotal < 200) {
            $path .= "Subtotal $100-$199.99 (10% spending discount)";
        } else {
            $path .= "Subtotal >= $200 (15% spending discount)";
        }
        
        $path .= " AND ";
        
        // Age discount path
        if ($age <= 25) {
            $path .= "Age <= 25 (5% youth discount)";
        } elseif ($age >= 65) {
            $path .= "Age >= 65 (10% senior discount)";
        } else {
            $path .= "Age 26-64 (No age discount)";
        }
        
        $path .= " => ";
        
        // Final discount applied
        if ($spendingDiscount > $ageDiscount) {
            $path .= "Spending discount applied";
        } elseif ($ageDiscount > $spendingDiscount) {
            $path .= "Age discount applied";
        } elseif ($spendingDiscount > 0) {
            $path .= "Equal discounts (either applied)";
        } else {
            $path .= "No discount applied";
        }
        
        return $path;
    }
}

// Run the test
$test = new unitTestOrderFlow();
?>
