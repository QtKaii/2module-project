<?php

class user
{
    private $db;
    private $username;
    private $fullname;
    private$email;
    private $dob;
    private$password;
    private$is_seller;
    private $is_admin=0;

    public function __construct($_POSTDATA)
    {
        $this->username=$_POSTDATA['username'];
        $this->fullname=$_POSTDATA['name'];
        $this->email=$_POSTDATA['email'];
        $this->dob=$_POSTDATA['dob'];
        $this->password=$_POSTDATA['password'];
        $this->is_seller=$_POSTDATA['seller-toggle'];
    }

    public function getByUsername($str)
    {
        if($str==$this->username)
        {
            return true;
        }
        else
        {
            error_log('user by name doesnt exist, user.php');
            return false;
        }
    }
    
    public function setAdmin()
    {
        $this->is_admin=1;
    }

    public function getisAdmin()
    {
        return $this->is_admin;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getDob()
    {
        return $this->dob;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getIsSeller()
    {
        return $this->is_seller;
    }

}

?>