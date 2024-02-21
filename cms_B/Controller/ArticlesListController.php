<?php
include "Model/Connection.php";
include "Model/DatabaseService.php";
include_once __DIR__ . "/../View/ArticlesView.php";

class ArticlesController {
    private $databaseService;

    public function __construct(DatabaseService $databaseService) {
        $this->databaseService = $databaseService;
    }

    public function getArticles() {
        return $this->databaseService->getArticles();
    }

    public function renderArticlesView() {
        $articles = $this->getArticles();
        $view = new ArticlesView($articles);
        $view->RenderArticles();
    }
}

$connection = new DatabaseConnection();
$databaseService = new DatabaseService($connection);
$articleService = new ArticlesController($databaseService);
$articleService->renderArticlesView();
?>
