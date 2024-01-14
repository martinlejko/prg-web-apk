class ArticleController {
    constructor() {
        this.backButton = document.getElementById('back-button');
        this.editButton = document.getElementById('edit-button');

        this.backButton.addEventListener('click', () => this.goBack());
        this.editButton.addEventListener('click', () => this.editArticle());
    }

    goBack() {
        window.location.href = 'https://webik.ms.mff.cuni.cz/~56704379/cms/articles';
    }

    editArticle() {
        var articleId = pageId;
        window.location.href = `https://webik.ms.mff.cuni.cz/~56704379/cms/article-edit/${articleId}`;
    }
}

const articleController = new ArticleController();
