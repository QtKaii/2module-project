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
            $this->makeUser('user','User one','user@gmail.com','user',false);
        }

        $admin= $this->getUserName('admin');
        if (!$user)
        {
            $this->makeUser('admin','Administrator','admin@gmail.com','admin',false);
            $admin->setAdmin('admin');
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

    public function makeUser($username,$fullname,$email,$password,$is_seller)
    {
        $query = 
        "
            INSERT INTO Users (username, fullname, email, password, is_seller)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $username,SQLITE3_TEXT);
        $stmt->bindValue(2, $fullname,SQLITE3_TEXT);
        $stmt->bindValue(3, $email,SQLITE3_TEXT);
        $stmt->bindValue(4, $password,SQLITE3_TEXT);
        $stmt->bindValue(5, $is_seller,SQLITE3_TEXT);

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
            if (password_verify($password, $user['password'])) 
            {
                return true;
            } 
            else 
            {
                return false; 
            }
        } 
        else 
        {
            return 'User doesnt exist'; 
        }
    }
}

?>