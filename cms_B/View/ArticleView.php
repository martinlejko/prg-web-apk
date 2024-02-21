<?php

class ArticleView {
    private $id;
    private $article;

    public function __construct($id, $article) {
        $this->id = $id;
        $this->article = $article;
    }

    public function renderArticle() {
        $article_name = htmlspecialchars($this->article["name"]);
        $article_content = htmlspecialchars($this->article["content"]);
        $tags = isset($this->article["tags"]) ? $this->article["tags"] : [];

        ?>
        <h1 id="article-name-header"><?php echo $article_name?></h1>
        <?php
        if ($this->article["content"] !== null) {
            $paragraphs = explode("\n", $article_content);
            foreach ($paragraphs as $paragraph) {
                echo "<p id='content-paragraph'>" . $paragraph . "</p>";
            }
        }
        ?>
        <h2 id="article-tags"><?php echo "Tags: " ?></h2>
        <?php
        
        if (is_string($tags)) {
            
            $tagsArray = explode(",", $tags);

            
            foreach ($tagsArray as $tag) {
                echo "<p class='tag-paragraph'>" . htmlspecialchars($tag) . "</p>";
            }
            if($tags === null){echo "empty";}
        } elseif (is_array($tags)) {
            
            foreach ($tags as $tag) {
                echo "<p class='tag-paragraph'>" . htmlspecialchars($tag) . "</p>";
            }
            if($tags === []){echo "empty";}
        } else {
            echo "<p>No tags available</p>";
        }
        ?>

        
        <table id="view-buttons" class="buttons">
            <tr>
                <td><button id="edit-button">Edit</button></td>
                <td><button id="back-button">Back to articles</button></td>
            </tr>
        </table>
        <div id="display-text"></div>
        <script> var id = <?php echo $this->id; ?>; </script>
        <script src="../JScontrollers/DisplayArticleController.js"></script>
        <?php
    }
}
?>
