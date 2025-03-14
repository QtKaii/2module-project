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
}

?>
