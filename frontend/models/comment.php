<?php

class comment
{
    private $usercomment;
    private $userID;
    private $productID;

    public function __construct($userID, $productID, $usercomment)
    {
        $this->userID = $userID;
        $this->productID = $productID;
        $this->usercomment = $usercomment;
    }
    public function getcomment()
    {
        return $this->usercomment;
    }
    public function getuserID()
    {
        return $this->userID;
    }
    public function getproductID()
    {
        return $this->productID;
    }
    
}

?>