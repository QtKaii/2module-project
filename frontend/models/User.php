<?php

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->createTable();
    
        // Add a default admin user
        $admin = $this->getUserByUsername('admin');
        if (!$admin) {
            $this->createUser('admin', 'admin', 'admin@admin.com');
            $this->db->exec("UPDATE users SET is_admin = 1 WHERE username = 'admin'");
        }

        // add dummy users
        $user = $this->getUserByUsername('user');
        if (!$user) {
            $this->createUser('user', 'user', 'user@user.com');
        }

        $user = $this->getUserByUsername('user2');
        if (!$user) {
            $this->createUser('user2', 'user2', 'user2@user.com');
        }

        $user = $this->getUserByUsername('user3');
        if (!$user) {
            $this->createUser('user3', 'user3', 'user3@user.com');
        }


    }

    private function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            password TEXT NOT NULL,
            username TEXT NOT NULL,
            email TEXT,
            is_admin BOOLEAN DEFAULT 0,
            is_seller BOOLEAN DEFAULT 0
        )";
        $this->db->exec($query);
    }

    public function createUser($username, $password, $email) {
        $query = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
        $stmt = $this->db->prepare($query);

        // bind -> ":username" to $username, ":password" to $password, and ":email" to $email
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), SQLITE3_TEXT);    
        $stmt->bindValue(':email', $email,  SQLITE3_TEXT);

        // execute the query
        $stmt->execute();

        // return the id of the inserted user
        return $this->db->lastInsertRowID();
    }

    public function getUserByUsername($username) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($query);

        // bind ":username" to $username
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);

        // execute the query
        $result = $stmt->execute();

        // return the user
        return $result->fetchArray();
    }

    public function verifyPassword($username, $password) {
        $user = $this->getUserByUsername($username);

        // if the user does not exist, return false
        if (!$user) {
            return false;
        }

        // verify the password
        return password_verify($password, $user['password']);
    }

    public function deleteUser($person, $username) {
        // person is the user deleting the account
        // if the person is not an admin, or themselves, return false

        $query = "DELETE FROM users WHERE username = :username";
        $stmt = $this->db->prepare($query);

        // bind ":username" to $username
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);

        // execute the query
        $stmt->execute();

        // return the number of rows affected
        return $this->db->changes();
    }

    public function login($username, $password) {
        if ($this->verifyPassword($username, $password)) {
            return $this->getUserByUsername($username);
        } else {
            return false;
        }
    }
}
?>