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
  }

  onSearch(value) {
    // TODO 4)
  }

}

class View {

  onMount(presenter) {
    console.log("Component mounted.");
    // TODO 4)
  }

  onStartLoading() {
    console.log("Loading started.");
    // TODO 3) Show loading message in 'div#message'.
  }

  onNewItem(identifier) {
    console.log("New item: " + identifier);
    // TODO 3) Add new element with loading text to 'div#list'.
  }

  onItemData(identifier, payload) {
    console.log("Data for: " + identifier, payload);
    // TODO 3) Show item (first_name, last_name) in the respective element.
  }

  onItemFailed(identifier) {
    console.log("Failed to fetch: " + identifier);
    // TODO 3) Show error message in the respective element.
  }

  onCompleted() {
    console.log("Loading completed.");
    // TODO 3) Hide loading message in 'div#message'.
  }

}

(function() {
  // Application entry point.
  (new Presenter(new Model(), new View())).mount();
})();

  </script>
</body>
</html>
