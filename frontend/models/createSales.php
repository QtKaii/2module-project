<?php

class createSales {
    protected $db;

    public function __construct() {
        $this->db = new SQLite3(__DIR__ . '/../db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $this->makeTable();
        $this->addInitialSales();
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

    private function addInitialSales() {
        // Check if sales exist first
        $result = $this->db->query("SELECT COUNT(*) as count FROM sales");
        $count = $result->fetchArray()['count'];
        
        if ($count === 0) {
            $initialSales = [
                ['cost' => 129.99, 'userid' => 1, 'productid' => 1],
                ['cost' => 249.99, 'userid' => 2, 'productid' => 2],
                ['cost' => 189.99, 'userid' => 3, 'productid' => 3]
            ];

            foreach ($initialSales as $sale) {
                $this->insertValues($sale['cost'], $sale['userid'], $sale['productid']);
            }
        }
    }

    public function getSalesByUserId($userId) {
        $query = "SELECT * FROM sales WHERE userid = :userid";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userid', $userId, SQLITE3_INTEGER);

        $result = $stmt->execute();
        $sales = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $sales[] = $row;
        }
        
        return $sales;
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
