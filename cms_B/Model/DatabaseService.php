<?php
class DatabaseService
{
    private $connection;

    public function __construct(DatabaseConnection $connection)
    {
        $this->connection = $connection;
    }
   
    
    public function getArticles() {
        $query = "SELECT id, name, tags FROM articles";
        $result = $this->executeQuery($query);
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : null;
    }

    public function getArticleById($articleId) {
        $query = "SELECT id, name, content, tags FROM articles WHERE id = ?";
        $stmt = $this->connection->getMysqli()->prepare($query);

        if ($stmt) {
            $stmt->bind_param("i", $articleId);
            $result = $stmt->execute();

            if ($result) {
                $result = $stmt->get_result();
                $article = $result->fetch_assoc();
            }

            $stmt->close();
            return isset($article) ? $article : null;
        }

        return null;
    }

    public function updateArticle($articleId, $newName, $newContent, $tags) {
        $query = "UPDATE articles SET name = ?, content = ?, tags = ? WHERE id = ?";
        $stmt = $this->connection->getMysqli()->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param("sssi", $newName, $newContent, $tags, $articleId);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    
        return false;
    }
    

    public function deleteArticle($articleId) {
        $query = "DELETE FROM articles WHERE id = ?";
        $stmt = $this->connection->getMysqli()->prepare($query);

        if ($stmt) {
            $stmt->bind_param("i", $articleId);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }

        return false;
    }

    public function createArticle($name) {
        $query = "INSERT INTO articles (name) VALUES (?)";
        $stmt = $this->connection->getMysqli()->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $name);
            $result = $stmt->execute();
            $newArticleId = $stmt->insert_id;
            $stmt->close();
            return $result ? $newArticleId : null;
        }

        return null;
    }

    private function executeQuery($query) {
        $result = $this->connection->getMysqli()->query($query);
        return $result;
    }

    public function createTag($name)
    {
        $query = "INSERT INTO tags (name) VALUES (?)";
        $stmt = $this->connection->getMysqli()->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $name);
            $result = $stmt->execute();
            $newTagId = $stmt->insert_id;
            $stmt->close();
            return $result ? $newTagId : null;
        }


    }
}

    
