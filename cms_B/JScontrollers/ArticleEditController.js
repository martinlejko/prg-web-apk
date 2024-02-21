class ArticleEditController {
    constructor() {
        document.addEventListener("DOMContentLoaded", () => {
            this.setupEventListeners();
        });
    }

    setupEventListeners() {
        document.body.addEventListener("click", (event) => {
            if (event.target.id === "back-button") {
                event.preventDefault();
                this.navigateToArticlesPage();
            }
        });
    }
    
    
    navigateToArticlesPage() {
        window.location.href = "https://webik.ms.mff.cuni.cz/~23080152/cms/articles";
    }


}

const pageController = new ArticleEditController();
