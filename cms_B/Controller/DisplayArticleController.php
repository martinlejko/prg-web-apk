<?php

include "Model/Connection.php";
include_once __DIR__ . "/../View/ArticleView.php";
include "Model/DatabaseService.php";

class DisplayArticleController
{
    private $id;
    private $article;

    public function __construct($id)
    {
        $this->id = $id;
        $this->initializeArticle();
    }

    private function initializeArticle()
    {
        $connector = new DatabaseConnection();
        $service = new DatabaseService($connector);

        $this->article = $service->getArticleById($this->id);

        if ($this->article === null) {
            $this->handleArticleNotFound();
        }
    }

    private function handleArticleNotFound()
    {
        echo "Article not found";
        exit;
    }

    public function process()
    {
        $view = new ArticleView($this->id, $this->article);
        $view->renderArticle();
    }

    public static function createControllerFromRequest($defaultId = 1)
    {
        $id = isset($_GET["id"]) ? $_GET["id"] : $defaultId;
        return new self($id);
    }
}

$displayArticleController = DisplayArticleController::createControllerFromRequest();
$displayArticleController->process();
