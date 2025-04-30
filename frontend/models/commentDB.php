<?php

class commentDB
{ 
    private $db;
    public function __construct()
    {
        $this->db = new PDO('sqlite:'. __DIR__ . '/../db.sqlite');
        $this->makeTable();
        $this->addInitialComments();
    }

    private function addInitialComments()
    {
        // Check if comments exist first
        $result = $this->db->query("SELECT COUNT(*) as count FROM Comments");
        $count = $result->fetch(PDO::FETCH_ASSOC)['count'];
        
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
        $stmt->bindValue(1, $comment->getuserID(), PDO::PARAM_INT);
        $stmt->bindValue(2, $comment->getproductID(), PDO::PARAM_INT);
        $stmt->bindValue(3, $comment->getcomment(), PDO::PARAM_STR);

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
        )";
        try
        {
            $this->db->exec($query);
        }
        catch (PDOException $e)
        {
            error_log('probalbly an error with the cpmment beign too long?' . $e->getMessage());
        }
    }

    public function getCommentsByProductID($productId)
    {
        $query = "SELECT * FROM Comments WHERE productID = :productID";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':productID', $productId, PDO::PARAM_INT);
        
        $stmt->execute();
        $comments = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $comments[] = $row; 
        }

        return $comments;  
    }
}
?>
