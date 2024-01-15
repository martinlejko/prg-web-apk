<?php
include 'database/db_controller.php';

class ArticleEditor{
    private $pageId;
    private $article;

    public function __construct($id) {
        $this->pageId = $id;
        $DBController = new DBController();
        $this->article = $DBController->getArticleById($this->pageId);
        
        if (isset($_POST["save"])) {
            $DBController->connect();
            $DBController->updateArticle($this->pageId, $_POST["name"], $_POST["content"]);
            header("Location: ../articles");
            return;
        }
    }

    public function renderEditPage() {
        $this->renderForm();
        $this->renderScripts();
    }


    public function renderForm() {
        ?>
        <div id="article-container">
            <form id="article-form" method="post">
                <label for="name">Name:</label><br>
                <input type="text" id="name" name="name" required maxlength="32" value="<?php echo $this->article['name']; ?>"><br>
                <label for="text">Text:</label><br>
                <textarea id="text" name="content" maxlength="1024" rows="10" cols="50"><?php echo $this->article['content']; ?></textarea><br>
                <div class="button-wrapper">
                    <input type="submit" value="Save" name="save" >
                    <button id="back-button">Back to articles</button>
                </div>
            </form>
        </div>
        <?php
    }

    private function renderScripts() {
        ?>
        <script>
            var pageId = <?php echo $this->pageId; ?>;
        </script>
        <script type="text/javascript" src="../controllers/editController.js"></script>
        <?php
    }
}

$articleEditor = new ArticleEditor($id);
$articleEditor->renderEditPage();