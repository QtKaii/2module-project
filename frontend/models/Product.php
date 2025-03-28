<?php

class Product {
    private $id;
    private $title;
    private $description;
    private $price;
    private $image;
    private $seller_id;

    public function __construct($_POSTDATA = null) {
        if ($_POSTDATA) {
            $this->title = $_POSTDATA['title'];
            $this->description = $_POSTDATA['description'];
            $this->price = $_POSTDATA['price'];
            $this->image = isset($_POSTDATA['image']) ? $_POSTDATA['image'] : '';
            $this->seller_id = isset($_POSTDATA['seller_id']) ? $_POSTDATA['seller_id'] : null;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function getSellerId() {
        return $this->seller_id;
    }

    public function setSellerId($seller_id) {
        $this->seller_id = $seller_id;
    }
}
