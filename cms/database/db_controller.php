<?php
include 'database/db_config.php';

class DBController {
    private $mysqli;
    private $db_config;

    public function __construct(){
        try {
            require 'database/db_config.php';
            $this->db_config = $db_config;
        } catch (Error $e) {
            require 'db_config.php';
            $this->db_config = $db_config;
        }
        $this->mysqli = new mysqli($db_config['servername'], $db_config['username'], $db_config['password'], $db_config['dbname']);

        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    public function connect() {
        $this->mysqli = new mysqli($this->db_config['servername'], $this->db_config['username'], $this->db_config['password'], $this->db_config['dbname']);

        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    public function closeConnection() {
        $this->mysqli->close();
    }

    public function getArticleById($articleId) {
        $sql = "SELECT id, name, content FROM articles WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        $article = [];
    
        if ($stmt) {
            $stmt->bind_param("i", $articleId);
            $stmt->execute();
            $stmt->bind_result($id, $name, $content);
            $stmt->fetch();
            if ($id !== null) {
                $article = array('id' => $id, 'name' => $name, 'content' => $content);
            } else {
                http_response_code(404);
                exit;
            }
            $stmt->close();
        }
    
        $this->closeConnection();
        if ($article )
        return $article;
    }

    public function getArticles() {
        $sql = "SELECT id, name FROM articles";
        $result = $this->mysqli->query($sql);
        $articleList = [];
    
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $articleList[] = array('id' => $row['id'], 'name' => $row['name']);
            }
        }
    
        $this->closeConnection();
    
        return $articleList; 
    }

    public function deleteArticle($articleId) {
        $sql = "DELETE FROM articles WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $articleId);
            $stmt->execute();
            $stmt->close();
        }
        $this->closeConnection();

        return $stmt ? true : false;
    }

    public function createArticle($name) {
        $sql = "INSERT INTO articles (name) VALUES (?)";
        $stmt = $this->mysqli->prepare($sql);
    
        if ($stmt) {
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $articleId = $stmt->insert_id;
            $stmt->close();
            return $articleId;
        } else {
            http_response_code(500); 
            exit;
        }
        $this->closeConnection();
    }

    public function updateAccess($articleId, $articleName, $utmSource) {
        $sources = $this->getSources($articleId);
    
        $utmSource = (string) $utmSource;
    
        if (array_key_exists($utmSource, $sources)) {
            $sources[$utmSource]['count'] += 1;
        } else {
            $sources[$utmSource] = ['name' => $utmSource, 'count' => 1];
        }
    
        $this->updateSources($articleId, $sources);
    }
    
    
    
    public function updateSources($articleId, $sources) {
        $jsonSources = json_encode($sources);
        
        $sql = "UPDATE articles SET access = ? WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("si", $jsonSources, $articleId);
        $stmt->execute();
        $stmt->close();
    }
    
    
    public function getSources($articleId) {
        $sql = "SELECT access FROM articles WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $articleId);
        $stmt->execute();
        $stmt->bind_result($sources);
        $stmt->fetch();
        $stmt->close();
        
        if ($sources === null) {
            return [];
        } else {
            return json_decode($sources, true);
        }
    }

    public function updateArticle($articleId, $name, $content) {
        $sql = "UPDATE articles SET name = ?, content = ? WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssi", $name, $content, $articleId);
            $stmt->execute();
            $stmt->close();
        } else {
            http_response_code(500); 
            exit;
        }

        $this->closeConnection();
    }
}
?>
