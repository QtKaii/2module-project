<?php

class createUser
{
    private $db;
    public function __construct($db) 
    {
        $this->db = $db;
        $this->makeTable();

        $user = $this->getUserByName('user');
        if (!$user) {
            $this->makeUser('user', 'User one', 'user@gmail.com', '00-00-0000', 'user', 0);
        }

        $admin = $this->getUserByName('admin');
        if (!$admin) {
            $this->makeUser('admin', 'Administrator', 'admin@gmail.com', '00-00-0000', 'admin', 0);
            $this->setAdmin('admin');
        }

        $seller = $this->getUserByName('seller');
        if (!$seller) {
            $this->makeUser('seller', 'Seller one', 'seller@gmail.com', '00-00-0000', 'seller', 1);
        }
    }

    private function makeTable()
    {
        $query = 
            "CREATE TABLE IF NOT EXISTS Users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL, 
                fullname TEXT NOT NULL, 
                email TEXT NOT NULL, 
                dob date NOT NULL, 
                password TEXT NOT NULL,
                is_seller BOOLEAN NOT NULL,
                is_admin BOOLEAN DEFAULT 0
            )";
        $this->db->exec($query);
    }


    private function setAdmin($username)
    {
        $query = "UPDATE Users SET is_admin = 1 WHERE username= ?";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $username, SQLITE3_TEXT);

        $stmt->execute();
    }

    public function makeUser($username, $fullname, $email, $dob, $password, $is_seller)
    {
        $userObj = new User($username, $fullname, $email, $dob, $password, $is_seller);

        $query =
            "INSERT INTO Users (username, fullname, email, dob, password, is_seller) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $userObj.$username, SQLITE3_TEXT);
        $stmt->bindValue(2, $userObj.$fullname, SQLITE3_TEXT);
        $stmt->bindValue(3, $userObj.$email, SQLITE3_TEXT);
        $stmt->bindValue(4, $userObj.$dob, SQLITE3_TEXT);
        $stmt->bindValue(5, password_hash($userObj.$password, PASSWORD_DEFAULT), SQLITE3_TEXT);
        $stmt->bindValue(6, $$userObj.$is_seller, SQLITE3_TEXT);

        $stmt->execute();

        return $this->db->lastInsertRowID();
    }

    public function getUserByName($str)
    {
        $query = "SELECT * FROM Users WHERE username= :username";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(":username", $str, SQLITE3_TEXT);

        $result = $stmt->execute();
        return $result->fetchArray();
    }

    public function getUserByID($id)
    {
        $query = "SELECT * FROM Users WHERE id= :id";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(":id", $id, SQLITE3_TEXT);

        $result = $stmt->execute();
        return $result->fetchArray();
    }

    public function login($username, $password)
    {
        $user = $this->getUserByName($username);
        if ($user) {
            if (password_verify($password, $user["password"])) {
                return $this->getUserByName($username);
            } else {
                return null;
            }
        } else {
            return 'User doesnt exist';
        }
    }
}

?>