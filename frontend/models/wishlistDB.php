<?php

class wishlistDB
{
    private $db;

    public function __construct() 
    {
        $this->db = new SQLite3(__DIR__ . '/../db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $this->makeTable();
    }

    private function makeTable()
    {
        $query = 
        "CREATE TABLE IF NOT EXISTS wishlist (
            wishlistid INTEGER PRIMARY KEY AUTOINCREMENT,
            productid INTEGER, 
            userid INTEGER,
            FOREIGN KEY (productid) REFERENCES products(productid),
            FOREIGN KEY (userid) REFERENCES users(userid)
        )"; 
        $this->db->exec($query);
    }

    public function insertValues($productid, $userid)
    {
        $query = "
        INSERT INTO wishlist (productid, userid)
        VALUES (:productid, :userid)
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':productid', $productid, SQLITE3_INTEGER);
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);

        $stmt->execute();

        return $this->db->lastInsertRowID();
    }

    public function saveToDatabase($productid, $userid) 
    {
        $stmt = $this->db->prepare("INSERT INTO wishlist (productid, userid) VALUES (?, ?)");
        $stmt->bindValue(1, $productid, SQLITE3_INTEGER);
        $stmt->bindValue(2, $userid, SQLITE3_INTEGER);

        return $stmt->execute();
    }

    public function getWishlistByUser($userid)
    {
        $query = "SELECT * FROM wishlist WHERE userid = :userid";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":userid", $userid, SQLITE3_INTEGER);

        $result = $stmt->execute();
        $wishlist = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $wishlist[] = $row;
        }

        return $wishlist;
    }

    public function deleteWishlistItem($wishlistid)
    {
        $query = "DELETE FROM wishlist WHERE wishlistid = :wishlistid";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":wishlistid", $wishlistid, SQLITE3_INTEGER);

        return $stmt->execute();
    }
}

?>
