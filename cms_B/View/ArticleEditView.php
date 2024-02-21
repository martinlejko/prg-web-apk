<?php

class ArticleEditView {
    private $id;
    private $article;

    public function __construct($id, $article) {
        $this->id = $id;
        $this->article = $article;
    }

    public function render() {
        ?>
        <form method="POST" id="edit-form">
            <table>
                <tr>
                    <td><label for="article-name">Name</label></td>
                </tr>
                <tr>
                    <td><input type="text" name="article-name" id="article-name" required maxlength="32" value="<?php echo $this->article["name"]; ?>"> </td>
                </tr>
                <tr>
                    <td><label for="article-content">Content</label></td>
                </tr>
                <tr>
                    <td><textarea name="article-content" id="article-content" maxlength="1024" rows="16" cols="64"><?php echo $this->article["content"]; ?></textarea></td>
                </tr>
                <tr>
                    <td><label for="article-tags">Tags</label></td>
                </tr>
                <tr>
                    <td><textarea name="article-tags" id="article-tags" maxlength="1024" rows="16" cols="64"><?php echo $this->article["tags"]; ?></textarea></td>
                </tr>
                <tr>
                    <td>
                        <div id="edit-buttons">
                            <input id="save-button" type="submit" name="save" value="Save">
                            <button id="back-button">Back to articles</button>
                        </div>
                        
                    </td>
                </tr>
    
            </table>
        </form>

        <script>
            var id = <?php echo $this->id; ?>;
        </script>
        <script src="../JScontrollers/ArticleEditController.js"></script>
        <?php
    }
}
