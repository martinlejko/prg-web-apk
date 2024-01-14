<?php
include 'database/db_controller.php';

class ArticleListRenderer {
    private $articles;

    public function __construct() {
        $DBController = new DBController();
        $this->articles = $DBController->getArticles();
    }

    public function renderListPage() {
        $this->renderTable();
        $this->renderButtons();
        $this->renderDialog();
        $this->renderScripts();
    }

    private function renderTable() {
        ?>
        <table id="info-table">
            <tr id="article-header">
                <td><h4 id="article-head">Article list</h4></td>
                <td><h4 id="counter"></h4></td>
            </tr>
        </table>
        
        <table id="article-list-table" class="common-table">

        </table>
        <?php
    }

    private function renderDialog() {
        ?>
        <dialog id="dialog" style="display: none;">
            <form id="create-form">
                <table id="article-table">
                    <tr>
                        <td><label for="article-name">Name: </label></td>
                        <td><input type="text" id="article-name" maxlength="32" placeholder="Article name"required/></td>
                    </tr>
                    <tr>
                        <td><button type="button" id="create-cancel" class="common-button">Cancel</button></td>
                        <td><button type="submit" id="create-submit" class="common-button">Create</button></td>
                    </tr>
                </table>
            </form>
        </dialog>
        <?php
    }

    private function renderButtons() {
        ?>
        <table class="buttons-list">
            <tr>
                <td><button id="prev-button" class="common-button">Previous</button></td>
                <td><button id="create-button" class="common-button">Create article</button></td>
                <td><button id="next-button" class="common-button">Next</button></td>
                <td><p1 id="page-counter">1</p1></td>
            </tr>
        </table>
        <?php
    }

    private function renderScripts() {
        ?>
        <script>
            var obj = JSON.parse('<?php echo json_encode($this->articles); ?>');
        </script>
        <script type="text/javascript" src="./controllers/listController.js"></script>
        <?php
    }
}

$articleListRenderer = new ArticleListRenderer();
$articleListRenderer->renderListPage();