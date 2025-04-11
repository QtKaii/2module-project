<?php

class ProductDB {
    private $db;

    public function __construct() {
        $this->db = new SQLite3(__DIR__ . '/../db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $this->makeTable();
        $this->addInitialProducts();
    }

    private function makeTable() {
        $query = "CREATE TABLE IF NOT EXISTS Products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            image TEXT,
            seller_id INTEGER,
            FOREIGN KEY (seller_id) REFERENCES Users(id)
        )";
        $this->db->exec($query);
    }

    private function addInitialProducts() {
        // Check if products exist first
        $result = $this->db->query("SELECT COUNT(*) as count FROM Products");
        $count = $result->fetchArray()['count'];
        
        if ($count === 0) {
            $initialProducts = [
                [
                    'title' => 'Vintage 1964 Silver Dollar',
                    'description' => 'Rare mint condition 1964 Peace Silver Dollar. Great addition to any coin collection. Minor toning but excellent detail preservation.',
                    'price' => 129.99,
                    'image' => ''
                ],
                [
                    'title' => 'Antique Victorian Pocket Watch',
                    'description' => 'Beautiful brass pocket watch from the late Victorian era. Working condition with original chain. Some patina adds to its authentic vintage charm.',
                    'price' => 249.99,
                    'image' => ''
                ],
                [
                    'title' => 'Retro 1950s Diner Clock',
                    'description' => 'Authentic 1950s chrome wall clock from an American diner. Neon lighting still works! Perfect for vintage home decor or restaurant.',
                    'price' => 189.99,
                    'image' => ''
                ],
                [
                    'title' => 'Vintage Camera Collection',
                    'description' => 'Set of three vintage cameras from the 1960s-70s. Includes Kodak Brownie, Polaroid Land Camera, and a Minolta SRT 101. All in working condition.',
                    'price' => 299.99,
                    'image' => ''
                ]
            ];

            foreach ($initialProducts as $product) {
                $productObj = new Product([
                    'title' => $product['title'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'image' => $product['image']
                ]);
                $this->createProduct($productObj);
            }
        }
    }

    public function createProduct($product) {
        $query = "INSERT INTO Products (title, description, price, image, seller_id) 
                 VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $product->getTitle(), SQLITE3_TEXT);
        $stmt->bindValue(2, $product->getDescription(), SQLITE3_TEXT);
        $stmt->bindValue(3, $product->getPrice(), SQLITE3_FLOAT);
        $stmt->bindValue(4, $product->getImage(), SQLITE3_TEXT);
        $stmt->bindValue(5, $product->getSellerId(), SQLITE3_INTEGER);

        $stmt->execute();
        return $this->db->lastInsertRowID();
    }

    public function getProductById($id) {
        $query = "SELECT * FROM Products WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function getAllProducts() {
        $query = "SELECT * FROM Products";
        $result = $this->db->query($query);
        $products = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $products[] = $row;
        }
        
        return $products;
    }

    public function updateProduct($product) {
        $query = "UPDATE Products 
                 SET title = ?, description = ?, price = ?, image = ?, seller_id = ? 
                 WHERE id = ?";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $product->getTitle(), SQLITE3_TEXT);
        $stmt->bindValue(2, $product->getDescription(), SQLITE3_TEXT);
        $stmt->bindValue(3, $product->getPrice(), SQLITE3_FLOAT);
        $stmt->bindValue(4, $product->getImage(), SQLITE3_TEXT);
        $stmt->bindValue(5, $product->getSellerId(), SQLITE3_INTEGER);
        $stmt->bindValue(6, $product->getId(), SQLITE3_INTEGER);

        return $stmt->execute();
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM Products WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }

    public function getProductsBySeller($sellerId) {
        $query = "SELECT * FROM Products WHERE seller_id = :seller_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':seller_id', $sellerId, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        $products = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $products[] = $row;
        }
        
        return $products;
    }

    public function getProductsByIds($ids) {
        if (empty($ids)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $query = "SELECT * FROM Products WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($query);
        
        foreach ($ids as $i => $id) {
            $stmt->bindValue($i + 1, $id, SQLITE3_INTEGER);
        }
        
        $result = $stmt->execute();
        $products = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $products[] = $row;
        }
        
        return $products;
    }
}
