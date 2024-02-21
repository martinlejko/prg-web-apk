<?php

class ArticlesView {
    private $articles;

    public function __construct($articles) {
        $this->articles = $articles;
    }

    public function RenderArticles() {
        ?>
        <h1>Article List</h1>
        <table id="article-list-table">
        </table>
        <div id="tag-filter">
            <label for="tag-filter-input">Filter by Tag: </label>
            <input type="text" id="tag-filter-input" placeholder="Enter tags (comma-separated)" />
            <button id="apply-tag-filter">Apply</button>
        </div>

        <table class="list-buttons">
            <tr>
                <td><button id="previous-button">Previous</button></td>
                <td><button id="next-button">Next</button></td>
                <td><p id="page-counter"></p></td>
                <td><button id="create-button">Create Article</button></td>
                <td><button id="create-tag-button">Create Tag</button></td>
            </tr>
        </table>
        <dialog id="dialog" style="display: none;">
            <form id="create-article-form">
                <table id="create-article-table">
                    <tr>
                        <td><label for="article-name">Name: </label></td>
                        <td><input type="text" id="article-name" maxlength="32" placeholder="Article name" required/></td>
                    </tr>
                    <tr>
                        <td><button type="button" id="cancel">Cancel</button></td>
                        <td><button type="submit" id="submit">Create</button></td>
                    </tr>
                </table>
            </form>
        </dialog>
        <dialog id="tag-creation-dialog" style="display: none;">
            <form id="create-tag-form">
                <table id="create-tag-table">
                    <tr>
                        <td><label for="tag-name">Tag: </label></td>
                        <td><input type="text" id="tag-name" maxlength="32" placeholder="Tag name" required/></td>
                    </tr>
                    <tr>
                        <td><button type="button" id="cancel-tag">Cancel</button></td>
                        <td><button type="submit" id="submit-tag">Create</button></td>
                    </tr>
                </table>
            </form>
        </dialog>
        <script>
            var articlesObject = JSON.parse('<?php echo json_encode($this->articles); ?>');
        </script>
        <script src="./JScontrollers/ListController.js"></script>
        <script src="./JScontrollers/DialogController.js"></script>
        <?php
    }
}

?>
