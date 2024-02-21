class DataTable {
    constructor(tableId, data, previousButtonId, nextButtonId, pageCounterId, tagFilterInputId, applyTagFilterButtonId) {
        this.table = document.getElementById(tableId);
        this.pageCounter = document.getElementById(pageCounterId);
        this.data = data;
        this.pageNumber = 0;
        this.articlesPerPage = 10;
        this.totalPages = Math.ceil(data.length / this.articlesPerPage);
        this.noArticle = false;

        this.previousButton = document.getElementById(previousButtonId);
        this.nextButton = document.getElementById(nextButtonId);

        this.tagFilterInput = document.getElementById(tagFilterInputId);
        this.applyTagFilterButton = document.getElementById(applyTagFilterButtonId);

        this.previousButton.addEventListener("click", () => this.handlePreviousButtonClick());
        this.nextButton.addEventListener("click", () => this.handleNextButtonClick());

        
        this.applyTagFilterButton.addEventListener("click", () => this.filterByTag());

        this.updatePage();
    }


    filterByTag() {
        const tags = this.tagFilterInput.value.split(',').map(tag => tag.trim());
    
        if (tags.length > 0) {
            this.filteredArticles = this.data.filter(article => {
                const articleTags = (article.tags || '').split(',').map(tag => tag.trim());
                return tags.every(tag => articleTags.includes(tag));
            });
            this.data = this.filteredArticles;
        } else {
            this.filteredArticles = this.data;
        }

        if(this.filteredArticles.length == 0)
        {
            this.noArticle = true;
        }
        else{
            this.noArticle = false;
        }

        this.totalPages = Math.ceil(this.filteredArticles.length / this.articlesPerPage);
        this.pageNumber = 0;
        this.displayTableContent(); 
        this.updatePage();
    }
    
    

    renderArticles() {
        this.clearTable();
        this.updateButtonsVisibility();
        this.displayTableContent();
        this.updateCounter();
    }
    handlePreviousButtonClick() {
        const newPageNumber = this.pageNumber - 1;
        if (newPageNumber >= 0) {
            this.pageNumber = newPageNumber;
            this.updatePage();
        }
    }

    handleNextButtonClick() {
        const newPageNumber = this.pageNumber + 1;
        if (newPageNumber < this.totalPages) {
            this.pageNumber = newPageNumber;
            this.updatePage();
        }
    }

    updateCounter() {
        this.pageCounter.innerText = `${this.pageNumber + 1}/${this.totalPages}`;
    }

    updateButtonsVisibility() {
        if (this.pageNumber <= 0) {
            this.previousButton.style.visibility = "hidden";
        } else {
            this.previousButton.style.visibility = "visible";
        }
    
        if (this.pageNumber >= this.totalPages - 1) {
            this.nextButton.style.visibility = "hidden";
        } else {
            this.nextButton.style.visibility = "visible";
        }
    }
    

    updatePage() {
        this.clearTable();
        this.updateButtonsVisibility();
        this.displayTableContent();
        this.updateCounter();
    }

    clearTable() {
        this.table.innerHTML = '';
    }

    displayTableContent() {
        const start = this.pageNumber * this.articlesPerPage;
        const end = Math.min(start + this.articlesPerPage, this.data.length);

        if(this.noArticle)
        {
            const tr = document.createElement("tr");
            const td = document.createElement("td");
            td.colSpan = 4; 
            td.textContent = "There are no articles";
            tr.appendChild(td);
            this.table.appendChild(tr);
        }

        for (let i = start; i < end; i++) {
            const tr = this.createTableRow(this.data[i]);
            this.table.appendChild(tr);
        }
    }

    createTableRow(item) {
        const tr = document.createElement("tr");
        tr.appendChild(this.createTextCell(item.name));
        tr.appendChild(this.createObjectCell(this.createLink("Show", `./article/${item.id}`, "show")));
        tr.appendChild(this.createObjectCell(this.createLink("Edit", `./article-edit/${item.id}`, "edit")));
        tr.appendChild(this.createObjectCell(this.createDeleteButton(item.id)));
        return tr;
    }

createTextCell(content) {
    return this.createTableCell(content);
}

createObjectCell(object) {
    return this.createTableCell(object);
}

createLink(textContent, href, elementId) {
    return Object.assign(document.createElement('a'), {
        textContent,
        href,
        id: elementId
    });
}


createDeleteButton(id) {
    const button = this.createLink("Delete", "#", "delete");
    button.addEventListener("click", (event) => { event.preventDefault(); this.handleDeleteClick(id) });
    return button;
}

createTableCell(content) {
    const td = document.createElement("td");
    td.appendChild(content instanceof Node ? content : document.createTextNode(content));
    return td;
}

    async handleDeleteClick(id) {
        try {
            const response = await fetch(`https://webik.ms.mff.cuni.cz/~23080152/cms/Delete.php?id=${id}`, { method: 'DELETE' });

            if (response.ok) {
                this.data = this.data.filter(article => article.id !== id);
                this.totalPages = Math.ceil(this.data.length / this.articlesPerPage);

                if (this.totalPages === this.pageNumber) {
                    this.pageNumber--;
                }
                this.updatePage();
            }
        } catch (error) {
            console.error(error);
        }
    }
}

const articleListTable = new DataTable(
    "article-list-table",
    articlesObject,
    "previous-button",
    "next-button",
    "page-counter",
    "tag-filter-input",
    "apply-tag-filter"
);