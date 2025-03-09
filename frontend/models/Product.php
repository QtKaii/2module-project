<?php

class Product
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->createTable();
    }

    private function createTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT NOT NULL,
        sale_type TEXT NOT NULL,
        base_price REAL NOT NULL
    )";

        $query2 = "CREATE TABLE IF NOT EXISTS auctions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
       
        start_date TEXT NOT NULL,
        end_date TEXT NOT NULL,

        FOREIGN KEY (product_id) REFERENCES products(id)
    )";

        $query3 = "CREATE TABLE IF NOT EXISTS bids (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        auction_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        price REAL NOT NULL,
        date TEXT NOT NULL,

        FOREIGN KEY (auction_id) REFERENCES auctions(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";

        $this->db->exec($query);
        $this->db->exec($query2);
        $this->db->exec($query3);
    }

    public function createProduct($name, $description, $sale_type, $base_price)
    {
        $query = "INSERT INTO products (name, description, sale_type, base_price) VALUES (:name, :description, :sale_type, :base_price)";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
        $stmt->bindValue(':sale_type', $sale_type, SQLITE3_TEXT);
        $stmt->bindValue(':base_price', $base_price, SQLITE3_FLOAT);

        $stmt->execute();

        return $this->db->lastInsertRowID();
    }

    public function getAllProducts() {
        $query = "SELECT * FROM products";
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute();

        $products = [];
        while ($row = $result->fetchArray()) {
            $products[] = $row;
        }

        return $products;
    }

    public function getProductById($id) {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        $result = $stmt->execute();

        return $result->fetchArray();
    }

}
