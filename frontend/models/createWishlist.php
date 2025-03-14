<?php

class CreateWishlist {
    private $userId;
    private $productId;

    // Constructor to initialize properties
    public function __construct($userId = null, $productId = null) {
        $this->userId = $userId;
        $this->productId = $productId;
    }

    // Getter for userId
    public function getUserId() {
        return $this->userId;
    }

    // Setter for userId
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    // Getter for productId
    public function getProductId() {
        return $this->productId;
    }

    // Setter for productId
    public function setProductId($productId) {
        $this->productId = $productId;
    }
}

?>
