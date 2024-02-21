<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class RequestHandler {
    private $page;
    private $id;

    public function Process() {
        $this->parseRequest();
        $this->renderPage();
    }

    private function parseRequest() {
        if (isset($_GET["page"])) {
            $this->routePage($_GET["page"]);
        } else {
            $this->page = "ArticlesListController";
        }
    }

    private function routePage($page) {
        $segments = explode('/', $page);
        $action = strtolower($segments[0]);

        switch ($action) {
            case 'articles':
                $this->page = 'ArticlesListController';
                break;

            case 'article-edit':
               $this->page = 'ArticleEditController'; 
               $this->id = isset($segments[1]) ? $segments[1] : null;
                break;

            case 'article':
                $this->page = 'DisplayArticleController';
                $this->id = isset($segments[1]) ? $segments[1] : null;
                break;

            default:
                throwError(404);
        }
    }

    private function renderPage() {
        include "Templates/Header.php";
        $id = $this->id;
        include "Controller/{$this->page}.php";
        include "Templates/Footer.php";
    }
      
    
}

$requestHandler = new RequestHandler();
$requestHandler->Process();
?>
