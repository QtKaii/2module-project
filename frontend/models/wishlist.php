<?php

class wishlist
{
    private$db;

    public function __construct($db)
    {
        $this->db =$db;
        $this->makeTable();
        

    }
    private function makeTable()
    {
        $query =
        "CREATE TABLE IF NOT EXISTS wishlist (
        wishlistid INTEGER PRIMARY KEY AUTOINCREMENT,
        productid INTEGER, 
        userid INTEGER,
        FOREIGN KEY (productid) REFERENCES products(productid)
        FOREIGN KEY (userid) REFERENCES users(userid)
        
        "; 
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

    return $this->db->lastInsertRowID();
}
}

?>