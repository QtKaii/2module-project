<?php

class user
{
    private $username;
    private $fullname; 
    private $email;
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
        $this->is_seller=isset($_POSTDATA['seller-toggle']) ? $_POSTDATA['seller-toggle'] : 0;
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

    public function getId($db) {
        $query = "SELECT id FROM Users WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':username', $this->username, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            return $row['id'];
        }
        
        return null; 
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

    public function setUsername($str)
    {
        $this->username = $str; 
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname($str)
    {
        $this->fullname=$str;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($str)
    {
        $this->email=$str;
    }

    public function getDob()
    {
        return $this->dob;
    }

    public function setDOB($str)
    {
        $this->dob=$str;
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
