<?php

$DATABASE = [
    ["id" => "001" ,"first_name" => "Michal","last_name" => "Stonman","email" => "mstonman0@hubpages.com","ip_address" => "111.97.50.155"],
    ["id" => "002" ,"first_name" => "Derek","last_name" => "Pleass","email" => "dpleass1@state.gov","ip_address" => "99.206.214.159"],
    ["id" => "003" ,"first_name" => "Jyoti","last_name" => "MacKnockiter","email" => "jmacknockiter2@bizjournals.com","ip_address" => "217.179.14.0"],
    ["id" => "004" ,"first_name" => "Merna","last_name" => "Preble","email" => "mpreble3@example.com","ip_address" => "116.243.200.111"],
    ["id" => "005", "first_name" => "Marmaduke","last_name" => "Hofton","email" => "mhofton4@hostgator.com","ip_address" => "80.44.204.5"],
    ["id" => "006" ,"first_name" => "Ewell","last_name" => "Burchatt","email" => "eburchatt5@macromedia.com","ip_address" => "191.36.143.7"],
    ["id" => "007" ,"first_name" => "Aubrette","last_name" => "Bricham","email" => "abricham6@youku.com","ip_address" => "243.246.89.92"],
    ["id" => "008" ,"first_name" => "Bengt","last_name" => "Pont","email" => "bpont7@chicagotribune.com","ip_address" => "128.232.213.52"],
    ["id" => "009" ,"first_name" => "Ewell","last_name" => "Gabbetis","email" => "egabbetis8@spiegel.de","ip_address" => "202.238.250.201"],
    ["id" => "010" ,"first_name" => "Madeline","last_name" => "Drewet","email" => "mdrewet9@networkadvertising.org","ip_address" => "207.251.30.207"],
    ["id" => "011" ,"first_name" => "Kissie","last_name" => "Tonner","email" => "ktonnera@bloglovin.com","ip_address" => "152.23.222.232"],
    ["id" => "012" ,"first_name" => "Dalila","last_name" => "Hiers","email" => "dhiersb@buzzfeed.com","ip_address" => "172.231.36.241"],
    ["id" => "013" ,"first_name" => "Jeanette","last_name" => "Crackett","email" => "jcrackettc@europa.eu","ip_address" => "136.220.160.60"],
    ["id" => "014" ,"first_name" => "Bryant","last_name" => "McKenna","email" => "bmckennad@aol.com","ip_address" => "199.81.246.167"],
    ["id" => "015" ,"first_name" => "Desiri","last_name" => "Bertolin","email" => "dbertoline@amazon.co.jp","ip_address" => "167.89.137.172"],
    ["id" => "016" ,"first_name" => "Mikael","last_name" => "Tailby","email" => "mtailbyf@ftc.gov","ip_address" => "172.141.115.96"],
    ["id" => "017" ,"first_name" => "Clevey","last_name" => "Waterstone","email" => "cwaterstoneg@issuu.com","ip_address" => "211.129.159.153"],
    ["id" => "018" ,"first_name" => "Vaughan","last_name" => "Matthiae","email" => "vmatthiaeh@seesaa.net","ip_address" => "38.98.247.211"],
    ["id" => "019" ,"first_name" => "Leta","last_name" => "Virr","email" => "lvirri@toplist.cz","ip_address" => "192.250.182.27"],
    ["id" => "020" ,"first_name" => "Anabal","last_name" => "Olliver","email" => "aolliverj@wsj.com","ip_address" => "199.61.86.152"],
  ];


?>

<!doctype html>
<html lang="en">
<head>
  <title>JavaScript model</title>
</head>
<body>
  <!-- Use following element to show loading message. -->
  <div id="message" style="height: 2em;"></div>

  <div>
    <label>
      Search: <input type="text" id="search">
    </label>
  </div>

  <!-- Insert data items into following element. -->
  <ul id="list"></ul>

  <script type="text/javascript">

    /**
     * Responsible for loading data and performing API calls.
     */
    class Model {
      constructor() {
        this.data = <?php print(json_encode($DATABASE)); ?> ;
      }

      async fetchList() {    
        return this.data.map(item => ({ "id": item.id }));
      }

      async fetchItem(identifier) {
        // TODO Iterate this.data and find the right item.
        const item = this.data.find(item => item.id === identifier);
        return item || null;
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

        try {
          this.view.onStartLoading();

          const itemList = await this.model.fetchList();

          for (const item of itemList) {
            this.view.onNewItem(item.id);

            try {
              const data = await this.model.fetchItem(item.id);
              this.view.onItemData(item.id, data);
            } catch (error) {
              this.view.onItemFailed(item.id);
            }
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
          const itemName = item.innerText.toLowerCase().split(' ')[0];
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
        messageDiv.innerText = 'Cakaaaaj robiii...';
      }

      onNewItem(identifier) {
        console.log("New item: " + identifier);
        // TODO 3) Add new element with loading text to 'div#list'.
        const list = document.getElementById('list');
        const newItem = document.createElement('li');
        newItem.id = 'item-' + identifier;
        newItem.innerText = 'Loading item ' + identifier + '...';
        list.appendChild(newItem);
      }

      onItemData(identifier, payload) {
        console.log("Data for: " + identifier, payload);
        // TODO 3) Show item (first_name, last_name) in the respective element.
        const listItem = document.getElementById('item-' + identifier);
        listItem.innerText = payload ? payload.first_name + ' ' + payload.last_name : 'Item not found';
      }

      onItemFailed(identifier) {
        console.log("Failed to fetch: " + identifier);
        // TODO 3) Show error message in the respective element.
        const listItem = document.getElementById('item-' + identifier);
        listItem.innerText = 'Failed to fetch item ' + identifier;
      }

      onCompleted() {
        console.log("Loading completed.");
        // TODO 3) Hide loading message in 'div#message'.
        const messageDiv = document.getElementById('message');
        messageDiv.innerText = 'skonceno done';
      }
    }

    (function() {
      // Application entry point.
      (new Presenter(new Model(), new View())).mount();
    })();

  </script>
</body>
</html>
