<?php

class CreateWishlist {
    public $userId;
    public $productId;

    
    public function __construct($userId = null, $productId = null) {
        $this->userId = $userId;
        $this->productId = $productId;
    }

    
    public function getUserId() {
        return $this->userId;
    }

    
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    
    public function getProductId() {
        return $this->productId;
    }

    
    public function setProductId($productId) {
        $this->productId = $productId;
    }
}

?>
