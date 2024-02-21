class DisplayArticleController {
    constructor() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        const backButton = document.getElementById("back-button");
        const toEditButton = document.getElementById("edit-button");

        backButton.addEventListener("click", () => this.navigateToArticlesList());
        toEditButton.addEventListener("click", () => this.navigateToEditArticle());
    }

    navigateToArticlesList() {
        window.location.href = "https://webik.ms.mff.cuni.cz/~23080152/cms/articles";
    }

    navigateToEditArticle() {
        window.location.href = `https://webik.ms.mff.cuni.cz/~23080152/cms/article-edit/${id}`;
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const articlePage = new DisplayArticleController();
});