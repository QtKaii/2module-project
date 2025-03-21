<?php

class wishlist {
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

    public function saveToDatabase($db) {
        $stmt = $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->bindValue(1, $this->userId, SQLITE3_TEXT);
        $stmt->bindValue(2, $this->productId, SQLITE3_TEXT);
        return $stmt->execute();
    }
}


?>
