<?php
$db= new SQLite3(__DIR__ . '/db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

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

    public function __construct($username, $fullname, $email, $dob, $password, $is_seller)
    {
        $this->username=$username;
        $this->fullname=$fullname;
        $this->email=$email;
        $this->dob=$dob;
        $this->password=$password;
        $this->is_seller=$is_seller;
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