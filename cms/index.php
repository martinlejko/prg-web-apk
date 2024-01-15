<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



class pageHandler {
    private $page = "";
    private $pageId;
    private $isError = false;

    private function sendError($errorCode) {
        $this->isError = true;
        echo "Error: " . $errorCode;
        http_response_code($errorCode);
        exit;
    }

    public function handleRequest() {
        if (isset($_GET["page"])) {
            $this->router($_GET["page"]);
        } else {
            $this->page = "./render/articles.php";
        }
        if (!$this->isError) {
            $this->renderPage();
        }
    }

    private function router($page) {
        $matched = false;
        $patterns = [
            '/^article\/[0-9]+$/' => './render/article.php',
            '/^articles$/' => './render/articles.php',
            '/^article-edit\/[0-9]+$/' => './render/article-edit.php',
        ];

        foreach ($patterns as $pattern => $path) {
            if (preg_match($pattern, $page)) {
                $this->page = $path;
                if ($pattern == '/^article\/[0-9]+$/' || $pattern == '/^article-edit\/[0-9]+$/') {
                    $this->pageId = preg_replace('/^.*\/([0-9]+)$/', '$1', $page);
                }
                
                $matched = true;
                break;
            }
        }
        if (!$matched) {
            $this->sendError(404);
        }
    }

    private function renderPage() {
        include 'templates/start.php';
        $id = $this->pageId;
        include $this->page;
        include 'templates/end.php';
    }

}

$pageHandler = new pageHandler();
$pageHandler->handleRequest();
?>
