<?php
include 'database/db_controller.php';

class ArticleRenderer {
    private $pageId;
    private $article;

    public function __construct($id) {
        $this->pageId = $id;
        $DBController = new DBController();
        $this->article = $DBController->getArticleById($this->pageId);
    }

    public function renderArticlePage() {
        $this->renderArticle();
        $this->renderButtons();
        $this->renderScripts();
    }



    private function renderArticle() {
        $content = $this->article['content'];
        $paragraphs = explode("\n", $content); 
    
        foreach ($paragraphs as $paragraph) {
            ?>
            <div id="article-container">
                <p id="article-paragraph"><?php echo $paragraph; ?></p>
            </div>
            <?php
        }
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
        </script>
        <script type="text/javascript" src="../controllers/articleController.js"></script>
        <?php
    }
    
}


$articleRenderer = new ArticleRenderer($id);
$articleRenderer->renderArticlePage();

?>
