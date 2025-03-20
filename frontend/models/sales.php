<?php

class Sales {
    public $salesId;
    public $cost;
    public $userId;
    public $productId;

    public function __construct($salesId = null, $cost = null, $userId = null, $productId = null) {
        $this->salesId = $salesId;
        $this->cost = $cost;
        $this->userId = $userId;
        $this->productId = $productId;
    }

    public function getSalesId() {
        return $this->salesId;
    }

    public function setSalesId($salesId) {
        $this->salesId = $salesId;
    }

    public function getCost() {
        return $this->cost;
    }

    public function setCost($cost) {
        $this->cost = $cost;
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
        $stmt = $db->prepare("INSERT INTO sales (sales_id, cost, user_id, product_id) VALUES (?, ?, ?, ?)");
        $stmt->bindValue(1, $this->salesId, SQLITE3_TEXT);
        $stmt->bindValue(2, $this->cost, SQLITE3_TEXT);
        $stmt->bindValue(3, $this->userId, SQLITE3_TEXT);
        $stmt->bindValue(4, $this->productId, SQLITE3_TEXT);
        return $stmt->execute();
    }
}


?>
