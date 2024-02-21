<?php
include "Model/Connection.php";
include_once __DIR__ . "/../View/ArticleEditView.php";
include "Model/DatabaseService.php";

class ArticleEditController {
    private $id;
    private $article;
    private $databaseService;

    public function __construct(DatabaseService $databaseService, $id) {
        $this->id = $_GET["id"] ?? $id;
        $this->databaseService = $databaseService;
        $this->article = $this->databaseService->getArticleById($this->id);

        if ($this->article == false) {
            $this->handleError("Non exist Article");
        }

        if (isset($_POST["save"])) {
            $this->Save();
        }
    }

    private function handleError($message) {
        echo $message;
        exit;
    }

    private function Save() {
        $articleName = $_POST["article-name"];
        $articleContent = $_POST["article-content"];
        $articleTags = $_POST["article-tags"];

        if ($this->validateInput($articleName, $articleContent)) {
            $this->updateArticle($articleName, $articleContent, $articleTags);
            header("Location: ../articles");
        }
    }

    private function validateInput($articleName, $articleContent) {
        if (strlen($articleName) === 0 || !(strlen($articleName) < 32) ||
            !(strlen($articleContent) < 1024)) {
            $this->throwError("400");
            return false;
        }

        return true;
    }

    private function updateArticle($articleName, $articleContent, $articleTags) {
        echo $articleName;
        $this->databaseService->updateArticle($this->id, $articleName, $articleContent, $articleTags);
    }

    private function throwError($code) {
        echo "Error: " . $code;
        exit;
    }

    public function Process() {
        $view = new ArticleEditView($this->id, $this->article);
        $view->render();
    }
}

$connection = new DatabaseConnection();
$databaseService = new DatabaseService($connection);

$articleEditHandler = new ArticleEditController($databaseService, $id);
$articleEditHandler->Process();
?>
