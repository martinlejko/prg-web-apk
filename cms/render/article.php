<?php
include 'database/db_controller.php';

class ArticleRenderer {
    private $pageId;
    private $article;
    private $utmSource;

    public function __construct($id) {
        $this->pageId = $id;
        $DBController = new DBController();
        $this->article = $DBController->getArticleById($this->pageId);
    }


    public function renderArticlePage() {
        $this->renderSource();
        $this->renderArticle();
        $this->renderUtmForm();
        $this->renderButtons();
        $this->renderScripts();
    }

    private function renderSource() {
        $this->utmSource = isset($_GET['utm_source']) ? htmlspecialchars($_GET['utm_source']) : null;
        
        $DBController = new DBController();
        if ($this->utmSource !== null){
            $DBController->updateAccess($this->pageId, $this->article['name'], $this->utmSource);
        }
        $DBController->connect();
        $sources = $DBController->getSources($this->pageId);

            ?>
        <div id="sources-container">
            <h2 id="sources-header">Sources:</h2>
            <?php if (!empty($sources)) { ?>
                <ul>
                    <?php foreach ($sources as $source) { ?>
                        <li id="source-item">Name: <?php echo $source['name']; ?>, Count: <?php echo $source['count']; ?></li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <p id="no-sources-message">No sources available.</p>
            <?php } ?>
        </div>
        <?php

    }
        
    

    private function renderArticle() {
        $articleName = $this->article['name'];
        $content = $this->article['content'];
    
        if (isset($this->article['content'])) {
            $paragraphs = explode("\n", $content); 
        
            ?>
            <div id="article-container">
                <h2 id="article-header"><?php echo $articleName; ?></h2>
                <?php foreach ($paragraphs as $paragraph) { ?>
                    <p id="article-paragraph"><?php echo $paragraph; ?></p>
                <?php } ?>
            </div>
            <?php
        } else {
            ?>
            <div id="article-container">
                <h2 id="article-header"><?php echo $articleName; ?></h2>
            </div>
            <?php
        }
    }

    private function renderUtmForm() {
        ?>
        <div id="utm-form-container">
            <form id="utm-form">
                <label for="utm-source">Enter Source:</label>
                <input type="text" id="utm-source" name="utm_source" maxlength="64" pattern="[a-z0-9]+">

                <label for="utm-campaign">Your link:</label>
                <input type="text" id="utm-campaign" name="utm_campaign" >
            </form>
        </div>
        <?php
    }
    
    

    private function renderButtons() {
        ?>
        <table class="buttons">
            <tr>
                <td><button id="edit-button">Edit article</button></td>
                <td><button id="back-button">Back to articles</button></td>
            </tr>
        </table>
        <?php
    }

    private function renderScripts() {
        ?>
        <script>
            var pageId = <?php echo $this->pageId; ?>;
            var articleName = <?php echo json_encode($this->article['name']); ?>;
        </script>
        <script type="text/javascript" src="../controllers/articleController.js"></script>
        <?php
    }
}



$articleRenderer = new ArticleRenderer($id);
$articleRenderer->renderArticlePage();

?>
