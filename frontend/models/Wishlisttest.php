<?php
require_once 'wishlist.php'; 

class wishlistTest {
    public function testWishlist() {
        $wishlist = new Wishlist(0, 0); 

        error_log('Initial Values:');
        error_log($wishlist->getUserId());
        error_log($wishlist->getProductId());

        error_log('Setting New Values:');
        $wishlist->setUserId(123);
        $wishlist->setProductId(456);

        error_log('Updated Values:');
        error_log( $wishlist->getUserId());
        error_log( $wishlist->getProductId());

    }
}

$test = new WishlistTest();
$test->testWishlist();
?>
