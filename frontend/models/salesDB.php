<?php

class SalesDB {
    private $db;

    public function __construct() {
        $this->db = new SQLite3(__DIR__ . '/../db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $this->makeTable();
    }

    private function makeTable() {
        $query = "
        CREATE TABLE IF NOT EXISTS sales (
            salesid INTEGER PRIMARY KEY AUTOINCREMENT,
            cost REAL NOT NULL,
            userid INTEGER NOT NULL,
            productid INTEGER NOT NULL,
            FOREIGN KEY (userid) REFERENCES users(userid),
            FOREIGN KEY (productid) REFERENCES products(productid)
        )";
        $this->db->exec($query);
    }

    public function insertSale($cost, $userId, $productId) {
        $query = "
        INSERT INTO sales (cost, userid, productid)
        VALUES (:cost, :userId, :productId)
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':cost', $cost, SQLITE3_FLOAT);
        $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
        $stmt->bindValue(':productId', $productId, SQLITE3_INTEGER);

        $stmt->execute();
        return $this->db->lastInsertRowID();
    }

    public function getSalesByUser($userId) {
        $query = "SELECT * FROM sales WHERE userid = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);

        $result = $stmt->execute();
        $sales = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $sales[] = $row;
        }

        return $sales;
    }

    public function getSaleById($salesId) {
        $query = "SELECT * FROM sales WHERE salesid = :salesId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':salesId', $salesId, SQLITE3_INTEGER);

        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function updateSaleCost($salesId, $newCost) {
        $query = "
        UPDATE sales
        SET cost = :cost
        WHERE salesid = :salesId
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':cost', $newCost, SQLITE3_FLOAT);
        $stmt->bindValue(':salesId', $salesId, SQLITE3_INTEGER);

        return $stmt->execute();
    }

    public function deleteSale($salesId) {
        $query = "DELETE FROM sales WHERE salesid = :salesId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':salesId', $salesId, SQLITE3_INTEGER);

        return $stmt->execute();
    }
}
