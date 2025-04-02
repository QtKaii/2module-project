<?php

class unitTestComment
{
    public function __construct()
    {
        $test = new comment(1,1,"ijefnviernfvijn");

        error_log($test->getcomment());
        error_log($test->getproductID());
        error_log($test->getuserID());
    }
}

?>