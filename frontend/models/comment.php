<?php

class comment
{
    private $comment_id;
    private $user_id;
    private $product_id;

    public function __construct($userID, $commentID, $productID)
    {
        $this->user_id = $userID;
        $this->comment_id = $commentID;
        $this->product_id = $productID;
    }
    public function get_comment_id()
    {
        return $this->comment_id;
    }
    public function get_user_id()
    {
        return $this->user_id;
    }
    public function get_product_id()
    {
        return $this->product_id;
    }
}

?>