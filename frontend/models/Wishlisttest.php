<?php
require_once 'wishlist.php'; 

class WishlistTest {
    public function testWishlist() {
        $wishlist = new Wishlist(0, 0); 

        error_log('Initial Values:');
        error_log('UserId: ' . ($wishlist->getUserId() ?? 'null'));
        error_log('ProductId: ' . ($wishlist->getProductId() ?? 'null'));

        error_log('Setting New Values:');
        $wishlist->setUserId(123);
        $wishlist->setProductId(456);

        error_log('Updated Values:');
        error_log('UserId: ' . $wishlist->getUserId());
        error_log('ProductId: ' . $wishlist->getProductId());

        error_log('Testing with specific values:');
        $wishlist->setUserId(789);
        $wishlist->setProductId(101112);
        error_log('UserId: ' . $wishlist->getUserId());
        error_log('ProductId: ' . $wishlist->getProductId());
    }
}

$test = new WishlistTest();
$test->testWishlist();
?>
