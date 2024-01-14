class TableController {
    constructor() {
        this.table = document.getElementById('article-list-table');
        this.pageCounter = document.getElementById('page-counter');
        this.currentPage = 1;
        this.articlesPerPage = 10;
        this.data = obj;

        this.previousButton = document.getElementById('prev-button');
        this.nextButton = document.getElementById('next-button');

        this.createButton = document.getElementById('create-button');
        this.Dialog = document.getElementById('dialog');
        this.createCancel = document.getElementById('create-cancel');
        this.createSubmit = document.getElementById('create-submit');


        this.previousButton.addEventListener('click', () => this.changePage(-1));
        this.nextButton.addEventListener('click', () => this.changePage(1));
        this.createButton.addEventListener('click', () => this.showDialog());
        this.createCancel.addEventListener('click', () => this.hideDialog());
        this.createSubmit.addEventListener('click', () => this.createArticle());

        this.renderArticles();
    }

    changePage(change) {
        this.currentPage += change;

        if (this.currentPage < 1) {
            this.currentPage = 1;
        }

        var totalPages = Math.ceil(this.data.length / this.articlesPerPage);

        if (this.currentPage > totalPages) {
            this.currentPage = totalPages;
        }

        this.renderArticles();
    }

    showDialog() {
        this.Dialog.style.display = 'block';
    }

    hideDialog() {
        this.Dialog.style.display = 'none';
    }
    
    
    async createArticle() {
        event.preventDefault();
        var name = document.getElementById('article-name').value;
    
        if (name.length === 0) {
            alert('Name cannot be empty!');
            return;
        }

        fetch(`https://webik.ms.mff.cuni.cz/~56704379/cms/database/create_article.php?name=${name}`)
            .then(response => response.json())
            .then(data => {
                const articleId = data.id;
    
                window.location.href = `https://webik.ms.mff.cuni.cz/~56704379/cms/article-edit/${articleId}`;
            })

    }
    



    createShowLink(articleId) {
        var showLink = document.createElement('a');
        showLink.textContent = 'Show';
        showLink.href = "#";
        showLink.addEventListener('click', () => this.showArticle(articleId));
        return showLink;
    }
    
    createEditLink(articleId) {
        var editLink = document.createElement('a');
        editLink.textContent = 'Edit';
        editLink.href = "#";
        editLink.addEventListener('click', () => this.editArticle(articleId));
        return editLink;
    }

    createDeleteLink(articleId) {
        var deleteLink = document.createElement('a');
        deleteLink.setAttribute('id', 'delete-link');
        deleteLink.textContent = 'Delete';
        deleteLink.href = "#";
        deleteLink.addEventListener('click', () => this.deleteArticle(articleId));
        return deleteLink;
    }

    updatePageCounter() {
        var totalPages = Math.ceil(this.data.length / this.articlesPerPage);
        this.pageCounter.innerText = `Page ${this.currentPage} of ${totalPages}`;
    }

    renderArticles() {
        this.table.innerHTML = '';

        var startIndex = (this.currentPage - 1) * this.articlesPerPage;
        var endIndex = startIndex + this.articlesPerPage;

        for (var i = startIndex; i < endIndex && i < this.data.length; i++) {
            var article = this.data[i];

            var row = this.table.insertRow(i - startIndex);
            var nameCell = row.insertCell(0);
            nameCell.innerHTML = article.name;

            var showCell = row.insertCell(1);
            showCell.appendChild(this.createShowLink(article.id));

            var editCell = row.insertCell(2);
            editCell.appendChild(this.createEditLink(article.id));

            var deleteCell = row.insertCell(3);
            deleteCell.appendChild(this.createDeleteLink(article.id));
        }
        this.updatePageCounter();

        this.previousButton.style.display = this.currentPage === 1 ? 'none' : 'block';
        this.nextButton.style.display = this.currentPage === Math.ceil(this.data.length / this.articlesPerPage) ? 'none' : 'block';
    }


    editArticle(articleId) {
        window.location.href = `https://webik.ms.mff.cuni.cz/~56704379/cms/article-edit/${articleId}`;
    }

    showArticle(articleId) {
        window.location.href = `https://webik.ms.mff.cuni.cz/~56704379/cms/article/${articleId}`;
    }
    
    async deleteArticle(articleId) {
        try {
            const response = await fetch(`https://webik.ms.mff.cuni.cz/~56704379/cms/database/delete_article.php?id=${articleId}`, {
                method: 'DELETE',
            });

            if (response.ok) {
                this.data = this.data.filter(article => article.id !== articleId);
                this.renderArticles();
            } else {
                console.error('Error deleting article:', response.statusText);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
}

var tableController = new TableController();
