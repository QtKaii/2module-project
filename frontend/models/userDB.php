<?php

class userDB
{
    private $db;
    public function __construct() 
    {
        $db= new SQLite3(__DIR__ . '/db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

        $this->db = $db;
        $this->makeTable();

        $user = new user([
            'username' => 'user',
            'name' => 'User one',
            'email' => 'user@gmail.com',
            'dob' => '00-00-0000',
            'password' => 'user', 
            'seller-toggle' => 0
        ]);

        $userExist = $this->getUserByName('user');
        if (!$userExist) 
        {
            $this->makeUser($user);
        }

        $admin = new user([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'dob' => '00-00-0000',
            'password' => 'admin',
            'seller-toggle' => 0
        ]);

        $adminExist = $this->getUserByName('admin');
        if (!$adminExist) 
        {
            $this->makeUser($admin);
            $this->setAdmin('admin');
        }

        $seller = new user([
            'username' => 'seller',
            'name' => 'Seller one',
            'email' => 'seller@gmail.com',
            'dob' => '00-00-0000',
            'password' => 'seller', 
            'seller-toggle' => 1
        ]);

        $sellerExist = $this->getUserByName('seller');
        if (!$sellerExist) 
        {
            $this->makeUser($seller);
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

    public function getUserByName($str)
    {
        $query = "SELECT * FROM Users WHERE username= :username";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(":username", $str, SQLITE3_TEXT);

        $result = $stmt->execute();
        return $result->fetchArray();
    }
    private function setAdmin($username)
    {
        $query = "UPDATE Users SET is_admin = 1 WHERE username= ?";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $username, SQLITE3_TEXT);

        $stmt->execute();
    }

    public function makeUser($user)
    {
        $query =
            "INSERT INTO Users (username, fullname, email, dob, password, is_seller) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $user->getUsername(), SQLITE3_TEXT);
        $stmt->bindValue(2, $user->getFullname(), SQLITE3_TEXT);
        $stmt->bindValue(3, $user->getEmail(), SQLITE3_TEXT);
        $stmt->bindValue(4, $user->getDob(), SQLITE3_TEXT);
        $stmt->bindValue(5, password_hash($user->getPassword(), PASSWORD_DEFAULT), SQLITE3_TEXT);
        $stmt->bindValue(6, $user->getIsSeller(), SQLITE3_TEXT);

        $stmt->execute();

        return $this->db->lastInsertRowID();
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