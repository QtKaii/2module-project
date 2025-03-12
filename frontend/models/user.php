<?php

class user
{
    private $db;

    private function makeTable()
    {
        $query = 
        "
            CREATE TABLE IF NOT EXISTS Users
            (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL, 
                fullname TEXT NOT NULL, 
                email TEXT NOT NULL, 
                dob date NOT NULL, 
                password TEXT NOT NULL,
                is_seller BOOLEAN NOT NULL,
                is_admin BOOLEAN DEFAULT 0
            )
        ";
        $this->db->exec($query);
    }

    public function constructDB($db)
    {
        $this->db = $db;
        $this->makeTable();

        $user= $this->getUserName('user');
        if (!$user)
        {
            $this->makeUser('user','User one','user@gmail.com','00-00-0000','user',0);
        }

        $admin= $this->getUserName('admin');
        if (!$admin)
        {
            $this->makeUser('admin','Administrator','admin@gmail.com','00-00-0000','admin',0);
            $this->setAdmin('admin');
        }

        $seller= $this->getUserName('seller');
        if (!$seller)
        {
            $this->makeUser('seller','Seller one','seller@gmail.com','00-00-0000','seller',1);
        }

    }

    private function setAdmin($username)
    {
        $query = 
        "
            UPDATE Users
            SET is_admin = 1
            WHERE username= ?
        ";
        $stmt= $this->db->prepare($query);

        $stmt->bindValue(1, $username, SQLITE3_TEXT);

        $stmt->execute();
    }

    public function makeUser($username,$fullname,$email,$dob,$password,$is_seller)
    {
        $query = 
        "
            INSERT INTO Users (username, fullname, email, dob, password, is_seller)
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $username,SQLITE3_TEXT);
        $stmt->bindValue(2, $fullname,SQLITE3_TEXT);
        $stmt->bindValue(3, $email,SQLITE3_TEXT);
        $stmt->bindValue(4, $dob,SQLITE3_TEXT);
        $stmt->bindValue(5, $password,SQLITE3_TEXT);
        $stmt->bindValue(6, $is_seller,SQLITE3_TEXT);

        $stmt->execute();

        return $this->db->lastInsertRowID();
    }

    public function getUserName($str)
    {
        $query= "SELECT * FROM Users WHERE username= ?";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $str,SQLITE3_TEXT);

        $result = $stmt->execute();
        return $result->fetchArray();
    }

    public function login($username, $password)
    {
        $user= $this->getUserName($username);
        if ($user) 
        {
            if ($password== $user['password']) 
            {
                error_log('password verified');
                return $this->getUserName($username);
            } 
            else 
            {
                error_log('password not verified');
                return false; 
            }
        } 
        else 
        {
            error_log('user doesnt exist');
            return 'User doesnt exist'; 
        }
    }
}

?>