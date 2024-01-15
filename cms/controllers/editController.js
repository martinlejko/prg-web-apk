class EditController {
    constructor(pageId) {
        this.backButton = document.getElementById('back-button');

        this.backButton.addEventListener('click', (event) => this.goBack(event));

    }

    goBack(event) {
        event.preventDefault(); 
        window.location.href = 'https://webik.ms.mff.cuni.cz/~56704379/cms/articles';
    }

    

}

const editController = new EditController(pageId);
