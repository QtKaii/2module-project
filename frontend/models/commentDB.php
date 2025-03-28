<?php

class commentDB
{ 
    private $db;
    public function __construct()
    {
        $this->db = new SQLite3(__DIR__ . '/../db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $this->makeTable();
        $this->addInitialComments();
    }

    private function addInitialComments()
    {
        // Check if comments exist first
        $result = $this->db->query("SELECT COUNT(*) as count FROM Comments");
        $count = $result->fetchArray()['count'];
        
        if ($count === 0) {
            $initialComments = [
                ['userID' => 1, 'productID' => 1, 'comment' => 'Beautiful coin, exactly as described. The toning is gorgeous!'],
                ['userID' => 2, 'productID' => 1, 'comment' => 'Great addition to my collection. Fast shipping too.'],
                ['userID' => 2, 'productID' => 2, 'comment' => 'The pocket watch keeps perfect time. Love the patina!'],
                ['userID' => 3, 'productID' => 3, 'comment' => 'This diner clock looks amazing in my retro-themed kitchen!'],
                ['userID' => 1, 'productID' => 4, 'comment' => 'All three cameras work perfectly. A great vintage set.']
            ];

            foreach ($initialComments as $commentData) {
                $comment = new comment($commentData['userID'], $commentData['productID'], $commentData['comment']);
                $this->makeRecord($comment);
            }
        }
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
