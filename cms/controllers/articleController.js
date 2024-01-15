class ArticleController {
    constructor() {
        this.backButton = document.getElementById('back-button');
        this.editButton = document.getElementById('edit-button');

        this.backButton.addEventListener('click', () => this.goBack());
        this.editButton.addEventListener('click', () => this.editArticle());

        this.utmSourceInput = document.getElementById('utm-source');
        this.utmCampaignInput = document.getElementById('utm-campaign');

        this.utmSourceInput.addEventListener('input', () => this.updateReferralLink());
    }

    goBack() {
        window.location.href = 'https://webik.ms.mff.cuni.cz/~56704379/cms/articles';
    }

    editArticle() {
        var articleId = pageId;
        window.location.href = `https://webik.ms.mff.cuni.cz/~56704379/cms/article-edit/${articleId}`;
    }

    updateReferralLink() {
        var articleId = pageId;
        var name = articleName;
        const utmSource = this.utmSourceInput.value;

        var isValid = /^[a-z0-9]{1,64}$/.test(utmSource);
        if (!isValid) {
            alert('Utm source must be alphanumeric and shorter than 64 characters');
            const utmSource = this.utmSourceInput.value = '';
            return;
        }
        const linkToPage = `<a href=https://webik.ms.mff.cuni.cz/~56704379/cms/article/${articleId}?utm_source=${encodeURIComponent(utmSource)}> ${name} </a>`;
        this.utmCampaignInput.value = linkToPage;
    }
}

var articleController = new ArticleController();
