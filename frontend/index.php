<?php
// Get current request URI without query parameters
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route to the corresponding view
if ($requestUri === '/' || $requestUri === '/index') {
    include __DIR__ . '/views/index.html';
} elseif ($requestUri === '/login') {
    include __DIR__ . '/views/login.html';
} elseif ($requestUri === '/register.html') {
    include __DIR__ . '/views/register.html';
} elseif (preg_match('#^/product/[^/]+$#', $requestUri)) {
    include __DIR__ . '/views/product.html';
} else {
    http_response_code(404);
    echo '404 Not Found';
}
?>