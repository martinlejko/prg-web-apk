class DialogController {
    constructor() {
        this.initElements();
        this.setupEventListeners();
    }

    initElements() {
        this.createButton = this.getElement("create-button");
        this.dialog = this.getElement("dialog");
        this.cancelButton = this.getElement("cancel");
        this.submitButton = this.getElement("submit");
        this.articleName = this.getElement("article-name");
        this.form = this.getElement("create-article-form");

        this.tagCreationDialog = this.getElement("tag-creation-dialog");
        this.createTagButton = this.getElement("create-tag-button");
        this.tagName = this.getElement("tag-name");
        this.tagForm = this.getElement("create-tag-form");
        this.cancelTagButton = this.getElement("cancel-tag");
        this.submitTagButton = this.getElement("submit-tag");
    }

    setupEventListeners() {
        this.createButton.addEventListener("click", () => this.openDialog());
        this.cancelButton.addEventListener("click", () => this.closeDialog());
        this.articleName.addEventListener("input", () => this.allowSubmit());
        this.form.addEventListener("submit", (event) => this.submit(event));
        
        this.createTagButton.addEventListener("click", () => this.openTagCreationDialog());
        this.cancelTagButton.addEventListener("click", () => this.closeTagCreationDialog());
        this.submitTagButton.addEventListener("click", (event) => this.submitTag(event));

    }

    openTagCreationDialog() {
        this.tagCreationDialog.style.display = "block";
    }
    
    closeTagCreationDialog() {
        this.tagCreationDialog.style.display = "none";
    }

    openDialog() {
        this.dialog.style.display = "block";
    }

    closeDialog() {
        this.dialog.style.display = "none";
    }

    allowSubmit() {
        this.submitButton.disabled = this.articleName.value === '';
    }

   async submitTag() {
        event.preventDefault();
        const tagName = this.tagName.value.trim();
    
        try {
            if (tagName.length < 1) {
                throw new Error("Invalid name");
            }

            const response = await this.postData("https://webik.ms.mff.cuni.cz/~23080152/cms/TagCreate.php", { tagName });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            this.closeTagCreationDialog();
        } catch (error) {
            this.handleError(error);
        }
    }

    async submit(event) {
        event.preventDefault();
        const name = this.articleName.value;

        try {
            if (name.length < 1) {
                throw new Error("Invalid name");
            }

            const response = await this.postData("https://webik.ms.mff.cuni.cz/~23080152/cms/Create.php", { name });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            

            const data = await response.json();
            this.handleSuccess(data);
        } catch (error) {
            this.handleError(error);
        }
    }

    handleSuccess(data) {
        this.closeDialog();
        window.location.href = `https://webik.ms.mff.cuni.cz/~23080152/cms/article-edit/${data.id}`;
    }

    handleError(error) {
        console.error('Error:', error);
    }

    getElement(id) {
        return document.getElementById(id);
    }

    async postData(url, body) {
        const response = await fetch(url, {
            method: "POST",
            body: JSON.stringify(body)
        });
        return response;
    }
}

const articleCreationDialog = new DialogController();
