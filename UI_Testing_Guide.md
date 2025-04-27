# PHP-Based UI Testing Guide for Ebaí E-commerce Platform

## Introduction

This document outlines how to implement effective UI testing for the Ebaí e-commerce platform using PHP. Our approach focuses on creating structured PHP test scripts that verify the UI functions correctly, is intuitive to use, and provides a consistent experience across different user roles and scenarios.

## PHP UI Testing Framework

We'll implement a simple, custom PHP testing framework for UI testing that follows these principles:

1. **Test Classes**: Each test suite is a PHP class with methods for specific test cases
2. **Error Logging**: Use PHP's `error_log()` function to record test results
3. **Test Runner**: A main script that executes all UI tests and reports results
4. **Visual Verification**: Screenshots or descriptions of expected UI states

## UI Testing Checklist

### 1. Consistency Testing

Verify that UI elements maintain consistency across the application:

- [ ] **Color Scheme**: Check that colors are consistent with the design system
- [ ] **Typography**: Ensure fonts and text sizes are consistent
- [ ] **Button Styles**: Verify that buttons of the same type look identical
- [ ] **Form Elements**: Check that form inputs, labels, and validation messages are consistent
- [ ] **Navigation**: Ensure navigation elements appear in the same location across pages
- [ ] **Responsive Behavior**: Test that the UI responds consistently across different screen sizes

### 2. Path-Based UI Testing

Test common user journeys through the application:

#### Buyer Journey
- [ ] **Registration**: Complete the registration process as a buyer
- [ ] **Login**: Log in with buyer credentials
- [ ] **Browse Products**: Navigate through product listings
- [ ] **Search**: Use the search functionality to find products
- [ ] **Add to Cart**: Add products to the shopping cart
- [ ] **Checkout**: Complete the checkout process
- [ ] **View Orders**: Check order history and details

#### Seller Journey
- [ ] **Registration**: Complete the registration process as a seller
- [ ] **Login**: Log in with seller credentials
- [ ] **Create Listing**: Create a new product listing
- [ ] **Manage Listings**: Edit and delete existing listings
- [ ] **View Sales**: Check sales history and details

#### Admin Journey
- [ ] **Login**: Log in with admin credentials
- [ ] **User Management**: View, edit, and delete user accounts
- [ ] **Product Management**: View, edit, and delete product listings

### 3. Role-Based UI Testing

Verify that UI elements are appropriate for different user roles:

- [ ] **Buyer UI**: Ensure buyers only see appropriate actions (add to cart, wishlist, etc.)
- [ ] **Seller UI**: Verify sellers have access to product creation and management
- [ ] **Admin UI**: Confirm admins have access to all administrative functions
- [ ] **Guest UI**: Check that non-logged-in users see appropriate content and prompts

### 4. Accessibility Testing

Ensure the UI is accessible to all users:

- [ ] **Keyboard Navigation**: Verify all functionality is accessible via keyboard
- [ ] **Screen Reader Compatibility**: Test with screen readers (NVDA, VoiceOver)
- [ ] **Color Contrast**: Ensure text has sufficient contrast against backgrounds
- [ ] **Text Sizing**: Verify the UI works when text is enlarged
- [ ] **Alternative Text**: Check that images have appropriate alt text

### 5. Error Handling Testing

Test how the UI handles errors and edge cases:

- [ ] **Form Validation**: Verify that forms provide clear error messages
- [ ] **Empty States**: Check how the UI displays when no data is available
- [ ] **Server Errors**: Test how the UI handles server-side errors
- [ ] **Offline Mode**: Verify behavior when internet connection is lost

## Practical PHP Implementation for UI Testing

### How to Test UI with PHP

Testing UI with PHP involves several practical approaches:

1. **DOM Parsing**: Use PHP's DOM functions to parse HTML and verify elements exist
2. **HTTP Requests**: Simulate user interactions by making HTTP requests to pages
3. **Session Simulation**: Test UI changes based on different user sessions
4. **Output Buffering**: Capture rendered HTML to verify UI components
5. **Visual Regression**: Compare screenshots of UI before and after changes

### Directory Structure

```bash
frontend/
├── models/
│   ├── UITester.php         # Core UI testing functionality
│   ├── UITestBuyer.php      # Buyer-specific UI tests
│   ├── UITestSeller.php     # Seller-specific UI tests
│   └── UITestAdmin.php      # Admin-specific UI tests
├── tests/
│   ├── ui/                  # UI test files
│   │   ├── expected/        # Expected HTML snippets
│   │   └── screenshots/     # UI screenshots for comparison
└── runUITests.php           # Test runner script
```

### Core UI Testing Class

```php
<?php
class UITester
{
    private $baseUrl;
    private $cookies = [];

    public function __construct($baseUrl = 'http://localhost')
    {
        $this->baseUrl = $baseUrl;
        error_log('UI Tester initialized with base URL: ' . $this->baseUrl);
    }

    /**
     * Make an HTTP request to a page and return the response
     */
    public function requestPage($path, $method = 'GET', $data = [])
    {
        $url = $this->baseUrl . $path;
        $ch = curl_init($url);

        // Set up the request
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        // Set cookies if any
        if (!empty($this->cookies)) {
            $cookieStr = '';
            foreach ($this->cookies as $name => $value) {
                $cookieStr .= $name . '=' . $value . '; ';
            }
            curl_setopt($ch, CURLOPT_COOKIE, $cookieStr);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        // Execute the request
        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        // Extract cookies from response
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches);
        foreach ($matches[1] as $cookie) {
            $parts = explode('=', $cookie, 2);
            if (count($parts) == 2) {
                $this->cookies[trim($parts[0])] = trim($parts[1]);
            }
        }

        curl_close($ch);
        return $body;
    }

    /**
     * Login as a specific user type
     */
    public function login($username, $password)
    {
        $response = $this->requestPage('/api/user/login', 'POST', [
            'username' => $username,
            'password' => $password
        ]);

        // Check if login was successful
        if (strpos($response, 'Login failed') !== false) {
            error_log("Login failed for user: $username");
            return false;
        }

        error_log("Successfully logged in as: $username");
        return true;
    }

    /**
     * Check if an element exists in the HTML
     */
    public function elementExists($html, $selector, $attribute = null, $value = null)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html); // @ to suppress warnings for malformed HTML
        $xpath = new DOMXPath($dom);

        // Build XPath query
        $query = '//' . $selector;
        if ($attribute && $value) {
            $query .= "[@$attribute='$value']";
        }

        $elements = $xpath->query($query);
        return $elements->length > 0;
    }

    /**
     * Count elements matching a selector
     */
    public function countElements($html, $selector)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $elements = $xpath->query('//' . $selector);
        return $elements->length;
    }

    /**
     * Get element text content
     */
    public function getElementText($html, $selector, $index = 0)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $elements = $xpath->query('//' . $selector);

        if ($elements->length > $index) {
            return $elements->item($index)->textContent;
        }

        return null;
    }
}
?>
```

### Example: Testing Navigation Elements

```php
<?php
class UITestNavigation extends UITester
{
    public function __construct($baseUrl = 'http://localhost')
    {
        parent::__construct($baseUrl);
        $this->testNavigationElements();
    }

    private function testNavigationElements()
    {
        error_log('Testing Navigation Elements');

        // Get the home page
        $homePage = $this->requestPage('/');

        // Check if navigation elements exist
        $hasHomeLink = $this->elementExists($homePage, 'a', 'href', '/');
        $hasProductsLink = $this->elementExists($homePage, 'a', 'href', '/products');
        $hasCartLink = $this->elementExists($homePage, 'a', 'href', '/cart');
        $hasLoginLink = $this->elementExists($homePage, 'a', 'href', '/login');

        // Log results
        error_log("Home link exists: " . ($hasHomeLink ? 'YES' : 'NO'));
        error_log("Products link exists: " . ($hasProductsLink ? 'YES' : 'NO'));
        error_log("Cart link exists: " . ($hasCartLink ? 'YES' : 'NO'));
        error_log("Login link exists: " . ($hasLoginLink ? 'YES' : 'NO'));

        // Test navigation functionality
        $productsPage = $this->requestPage('/products');
        $hasProductElements = $this->countElements($productsPage, 'div[class*="product"]');
        error_log("Products page has $hasProductElements product elements");
    }
}
?>
```

### Example: Testing Buyer User Journey

```php
<?php
class UITestBuyer extends UITester
{
    public function __construct($baseUrl = 'http://localhost')
    {
        parent::__construct($baseUrl);
        $this->testBuyerJourney();
    }

    private function testBuyerJourney()
    {
        error_log('Testing Buyer User Journey');

        // Step 1: Login as a buyer
        $loggedIn = $this->login('user', 'user');
        if (!$loggedIn) {
            error_log("FAILED: Could not log in as buyer");
            return;
        }

        // Step 2: Browse products
        $productsPage = $this->requestPage('/');
        $productCount = $this->countElements($productsPage, 'div[class*="product"]');
        error_log("Found $productCount products on the home page");

        if ($productCount === 0) {
            error_log("FAILED: No products found on home page");
            return;
        }

        // Step 3: View a product
        // For this example, we'll assume the first product has a link with a specific pattern
        $productLink = $this->getElementAttribute($productsPage, 'a[href*="/product/"]', 'href');
        if (!$productLink) {
            error_log("FAILED: Could not find product link");
            return;
        }

        $productPage = $this->requestPage($productLink);
        $hasAddToCartButton = $this->elementExists($productPage, 'button', 'id', 'add-to-cart');
        error_log("Product page has Add to Cart button: " . ($hasAddToCartButton ? 'YES' : 'NO'));

        if (!$hasAddToCartButton) {
            error_log("FAILED: Add to Cart button not found on product page");
            return;
        }

        // Step 4: Add to cart (simulate button click)
        $cartResponse = $this->requestPage('/api/cart/add', 'POST', [
            'product_id' => $this->extractProductId($productLink)
        ]);

        // Step 5: View cart
        $cartPage = $this->requestPage('/cart');
        $cartItemCount = $this->countElements($cartPage, 'div[class*="cart-item"]');
        error_log("Cart has $cartItemCount items");

        if ($cartItemCount === 0) {
            error_log("FAILED: No items in cart after adding product");
            return;
        }

        // Step 6: Proceed to checkout
        $checkoutButton = $this->elementExists($cartPage, 'a', 'href', '/checkout');
        error_log("Cart page has Checkout button: " . ($checkoutButton ? 'YES' : 'NO'));

        if (!$checkoutButton) {
            error_log("FAILED: Checkout button not found on cart page");
            return;
        }

        error_log("PASSED: Buyer journey test completed successfully");
    }

    private function extractProductId($productLink)
    {
        // Extract product ID from URL like /product/123
        preg_match('/\/product\/(\d+)/', $productLink, $matches);
        return isset($matches[1]) ? $matches[1] : 1; // Default to 1 if not found
    }

    private function getElementAttribute($html, $selector, $attribute, $index = 0)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $elements = $xpath->query('//' . $selector);

        if ($elements->length > $index) {
            return $elements->item($index)->getAttribute($attribute);
        }

        return null;
    }
}
?>
```

### Test Runner Script

```php
<?php
// Load required models
require_once __DIR__ . '/models/UITester.php';
require_once __DIR__ . '/models/UITestNavigation.php';
require_once __DIR__ . '/models/UITestBuyer.php';
require_once __DIR__ . '/models/UITestSeller.php';
require_once __DIR__ . '/models/UITestAdmin.php';

echo "=== Running UI Tests ===\n\n";

// Set base URL for testing
$baseUrl = 'http://localhost';

// Run navigation tests
echo "Running Navigation UI Tests...\n";
$navigationTests = new UITestNavigation($baseUrl);
echo "Navigation UI Tests Completed.\n\n";

// Run buyer journey tests
echo "Running Buyer Journey UI Tests...\n";
$buyerTests = new UITestBuyer($baseUrl);
echo "Buyer Journey UI Tests Completed.\n\n";

// Run seller journey tests
echo "Running Seller Journey UI Tests...\n";
$sellerTests = new UITestSeller($baseUrl);
echo "Seller Journey UI Tests Completed.\n\n";

// Run admin journey tests
echo "Running Admin Journey UI Tests...\n";
$adminTests = new UITestAdmin($baseUrl);
echo "Admin Journey UI Tests Completed.\n\n";

echo "=== All UI Tests Completed ===\n";
echo "Check the error log for detailed test results.\n";
?>
```

## Best Practices for PHP UI Testing

1. **Isolate Tests**: Each test method should focus on a specific UI aspect
2. **Descriptive Logging**: Use clear, descriptive messages in error_log() calls
3. **Expected vs. Actual**: Always compare expected UI state with actual state
4. **Test Independence**: Tests should not depend on the results of other tests
5. **Comprehensive Coverage**: Test all major UI components and user paths
6. **Regular Testing**: Run UI tests after any significant UI changes

## Conclusion

This PHP-based approach to UI testing provides a structured way to verify the Ebaí platform's user interface without requiring external testing frameworks. By implementing these test classes and running them regularly, you can ensure a consistent, intuitive, and error-free user experience across all aspects of the application.
