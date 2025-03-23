<?php

class commentDB
{ 
    private $db;
    public function __construct()
    {
        $this->db = new SQLite3(__DIR__ . '/../db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $this->makeTable();
    }

    public function makeRecord($comment)
    {
        $query =
            "INSERT INTO Comments (userID, productID, comment) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $comment->getuserID(), SQLITE3_INTEGER);
        $stmt->bindValue(2, $comment->getproductID(), SQLITE3_INTEGER);
        $stmt->bindValue(3, $comment->getcomment(), SQLITE3_TEXT);

        $stmt->execute();
    }
    private function makeTable()
    {
        $query = 
        "CREATE TABLE IF NOT EXISTS Comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            userID INTEGER NOT NULL,
            productID INTEGER NOT NULL,
            comment TEXT NOT NULL CHECK(length(comment) <= 250), 
            FOREIGN KEY (userID) REFERENCES Users(id), 
            FOREIGN KEY (productID) REFERENCES Products(id)
        )
    ";
        try
        {
            $this->db->exec($query);
        }
        catch (Exception $e)
        {
            error_log('probalbly an error with the cpmment beign too long?');
        }
    }

    public function getCommentsByProductID($productId)
    {
        $query = "SELECT * FROM Comments WHERE productID = :productID";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':productID', $productId, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        $comments = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            $comments[] = $row; 
        }

        return $comments;  
    }
}
?>