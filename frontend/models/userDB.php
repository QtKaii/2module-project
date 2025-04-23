<?php

class userDB
{
    private $db;
    public function __construct() 
    {

        $this->db = new SQLite3(__DIR__ . '/../db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
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
    public function getUserNameByID($id)
    {
        $query = "SELECT username FROM Users WHERE id= :id";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(":id", $id, SQLITE3_NUM);

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

    public function getUserByIDobj($id)
    {
        $query = "SELECT * FROM Users WHERE id= :id";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(":id", $id, SQLITE3_INTEGER);

        $result = $stmt->execute();

        if ($result->fetchArray(SQLITE3_ASSOC)) 
        {
            return new user($result->fetchArray(SQLITE3_ASSOC)); 
        } 
        else 
        {
            return false; 
        }
    }

    public function login($username, $password)
    {
        $user = $this->getUserByName($username);
        if ($user) 
        {
            error_log($user["password"]);
            if (password_verify($password, $user["password"])) 
            {
                error_log("pass good");
                return $this->getUserByName($username);
            } 
            else 
            {
                error_log(message: "pass wrong");
                return null;
            }
        } else 
        {
            error_log(message: "user wrong");
            return false;
        }
    }

    public function getAllUsers()
    {
        $query = 'SELECT * FROM Users';
        $result = $this->db->query($query);
        $users = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) 
        {
            $users[] = $row;
        }
        error_log('Displaying all users');
        return $users;
    }

    public function updateUser($user, $_POSTDATA)
    {
        /*
        error_log(print_r($user, true));
        error_log($_POSTDATA['fullname']);
        error_log($_POSTDATA['email']);
        error_log($_POSTDATA['dob']);
        */

        $user->setUsername($_POSTDATA['username']);
        $user->setFullname($_POSTDATA['fullname']);
        $user->setEmail($_POSTDATA['email']);
        $user->setDOB($_POSTDATA['dob']);

        /*
        error_log($user->getFullname());
        error_log($user->getEmail());
        error_log($user->getDob());
        error_log($_POSTDATA['id']);
        */

        $query = "UPDATE Users SET username = ?, fullname = ?, email = ?, dob = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindValue(1, $user->getUsername(), SQLITE3_TEXT);
        $stmt->bindValue(2, $user->getFullname(), SQLITE3_TEXT);
        $stmt->bindValue(3, $user->getEmail(), SQLITE3_TEXT);
        $stmt->bindValue(4, $user->getDob(), SQLITE3_TEXT);
        $stmt->bindValue(5, $_POSTDATA['id'], SQLITE3_INTEGER); 

        $result = $stmt->execute();

        if ($result) 
        {
            error_log('User updated successfully');
        } 
        else 
        {
            error_log('User update failed');
        }   

        //error_log(new userDB->getUserByID($user->getId()));
            
    }

    public function deleteUser($userID)
    {
        $query = "DELETE FROM Users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $userID, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result;
    }

    
   
}

?>