<?php

class createSales {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->makeTable();
    }

    // Method to create the Sales table
    private function makeTable() {
        $query = "
        CREATE TABLE IF NOT EXISTS sales (
            salesid INTEGER PRIMARY KEY AUTOINCREMENT,
            cost REAL NOT NULL,
            userid INTEGER NOT NULL,
            productid INTEGER NOT NULL,
            FOREIGN KEY (userid) REFERENCES users(id),
            FOREIGN KEY (productid) REFERENCES products(productid)
        );
        ";
        $this->db->exec($query);
    }

    public function insertValues($cost, $userid, $productid) {
        $query = "
        INSERT INTO sales (cost, userid, productid)
        VALUES (:cost, :userid, :productid)
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':cost', $cost, SQLITE3_FLOAT);
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);
        $stmt->bindValue(':productid', $productid, SQLITE3_INTEGER);

        $stmt->execute();
        return $this->db->lastInsertRowID();
    }

    public function getSalesById($salesid) {
        $query = "SELECT * FROM sales WHERE salesid = :salesid";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':salesid', $salesid, SQLITE3_INTEGER);

        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function updateCost($salesid, $newCost) {
        $query = "
        UPDATE sales
        SET cost = :cost
        WHERE salesid = :salesid
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':cost', $newCost, SQLITE3_FLOAT);
        $stmt->bindValue(':salesid', $salesid, SQLITE3_INTEGER);

        return $stmt->execute();
    }

    public function deleteSale($salesid) {
        $query = "DELETE FROM sales WHERE salesid = :salesid";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':salesid', $salesid, SQLITE3_INTEGER);

        return $stmt->execute();
    }
}
