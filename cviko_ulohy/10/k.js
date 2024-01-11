<!doctype html>
<html lang="en">
<head>
  <title>JavaScript model</title>
</head>
<body>
  <!-- Use following element to show loading message. -->
  <div id="message" style="height: 2em;"></div>

  <div>
    <Label>
      Search: <input type="text" id="search">
    </Label>
  </div>

  <!-- Insert data items into following element. -->
  <ul id="list"></ul>

  <script type="text/javascript">

function fetchWrap(url) {
  const delay = Math.random() * 3000;
  return new Promise((resolve, reject) => {
    setTimeout(() => fetch(url).then(resolve).catch(reject), delay);
  });  
}

/**
 * Responsible for loading data and performing API calls.
 */
class Model {

  async fetchList() {
    // TODO 1) fetch and return list of items.
    // Do NOT use fetch use fetchWrap instead, it adds artificial delay.
    console.log("Model.fetchList");
    try {
      const response = await fetchWrap('https://webik.ms.mff.cuni.cz/nswi142/practicals/script/11-api.php');
      const data = await response.json();
      return data["data"];
    } catch (error) {
      console.error("Error fetching list:", error);
      throw error;
    }
  }

  async fetchItem(identifier) {
    // TODO 1) Fetch and return given item.
    // Do NOT use fetch use fetchWrap instead, it adds artificial delay.
    console.log("Model.fetchItem", identifier);
    try {
      const response = await fetchWrap(`https://webik.ms.mff.cuni.cz/nswi142/practicals/script/11-api.php?identifier=${identifier}`);
      const data = await response.json();
      return data["data"];
    } catch (error) {
      console.error(`Error fetching item ${identifier}:`, error);
      throw error;
    }
  }
}


/**
 * Handles operations.
 */ 
class Presenter {

  constructor(model, view) {
    this.model = model;
    this.view = view;
  }

  async mount() {
    this.view.onMount(this);

    // TODO 2) Notify View that we started loading by calling 'onStartLoading'.

    // TODO 2) Fetch items using model.

    // TODO 2) Report each new item using 'onNewItem'.

    // TODO 2) Use model to fetch item detail.

    // TODO 2) Call 'onItemData' or 'onItemFailed' once fetch for item detail is resolved.

    // TODO 2) Once all is finished call 'onCompleted'.

    try {
        this.view.onStartLoading();

        const itemList = await this.model.fetchList();
        for (const item of itemList) {
            this.view.onNewItem(item.id);

            this.model.fetchItem(item.id)
              .then(data => this.view.onItemData(item.id, data))
              .catch(() => this.view.onItemFailed(item.id));
        }

        this.view.onCompleted();
    } catch (error) {
        console.error("Error mounting presenter:", error);
    }
  }

  onSearch(value) {
    // TODO 4)
    const listItems = document.querySelectorAll('#list li');
    listItems.forEach((item) => {
    const itemName = item.innerText.toLowerCase();
    if (itemName.includes(value.toLowerCase())) {
      item.style.display = 'block';
    } else {
      item.style.display = 'none';
    }
  });
  }

}

class View {

  onMount(presenter) {
    console.log("Component mounted.");
    // TODO 4)
    const searchInput = document.getElementById('search');
    searchInput.addEventListener('input', (event) => {
      presenter.onSearch(event.target.value);
    });
  }

  onStartLoading() {
    console.log("Loading started.");
    // TODO 3) Show loading message in 'div#message'.
    const messageDiv = document.getElementById('message');
    messageDiv.innerText = 'Loading...';
  }

  onNewItem(identifier) {
    console.log("New item: " + identifier);
    // TODO 3) Add new element with loading text to 'div#list'.
    const listDiv = document.getElementById('list');
    const newItem = document.createElement('li');
    newItem.innerText = `Loading item ${identifier}...`;
    listDiv.appendChild(newItem);
  }

  onItemData(identifier, payload) {
    console.log("Data for: " + identifier, payload);
    // TODO 3) Show item (first_name, last_name) in the respective element.
    const listDiv = document.getElementById('list');
    const itemElement = Array.from(listDiv.children).find(
      (item) => item.innerText.includes(`Loading item ${identifier}`)
    );

    if (itemElement) {
      itemElement.innerText = `${payload.first_name} ${payload.last_name}`;
    }

  }

  onItemFailed(identifier) {
    console.log("Failed to fetch: " + identifier);
    // TODO 3) Show error message in the respective element.
    const listDiv = document.getElementById('list');
    const itemElement = Array.from(listDiv.children).find(
      (item) => item.innerText.includes(`Loading item ${identifier}`)
    );

    if (itemElement) {
      itemElement.innerText = `Failed to fetch item ${identifier}`;
    }
  }

  onCompleted() {
    console.log("Loading completed.");
    // TODO 3) Hide loading message in 'div#message'.
    const messageDiv = document.getElementById('message');
    messageDiv.innerText = '';
  }

}

(function() {
  // Application entry point.
  (new Presenter(new Model(), new View())).mount();
})();

  </script>
</body>
</html>
