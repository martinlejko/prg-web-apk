/**
 * Data model for loading the work hour categories and fileld hours.
 * The model implements internal cache, so the data does not have to be
 * loaded every time from the REST API.
 */
class DataModel {
	/**
	 * Initialize the data model with given URL pointing to REST API.
	 * @param {string} apiUrl Api URL prefix (without the query part).
	 */
	constructor(apiUrl)
	{
		this.apiUrl = apiUrl;
		this.cache = null;
	}


	async fetchCategories() {
        const response = await fetch(`${this.apiUrl}`);
        const categoriesData = await response.json();
        if (!categoriesData.ok) {
            throw new Error(categoriesData.error);
        }
        return categoriesData.payload;
    }

    async fetchCategoryHours(categoryId) {
        const response = await fetch(`${this.apiUrl}?action=hours&id=${categoryId}`);
        const hoursData = await response.json();
        if (!hoursData.ok) {
            throw new Error(hoursData.error);
        }
        return hoursData.payload.hours;
    }

    async fetchData() {
        try {
            const categories = await this.fetchCategories();
            const combinedData = await Promise.all(categories.map(async category => {
                const hours = await this.fetchCategoryHours(category.id);
                return { ...category, hours };
            }));
            return combinedData;
        } catch (error) {
            throw error;
        }
    }


	/**
	 * Retrieve the data and pass them to given callback function.
	 * If the data are available in cache, the callback is invoked immediately (synchronously).
	 * Otherwise the data are loaded from the REST API and cached internally.
	 * @param {Function} callback Function which is called back once the data become available.
	 *                     The callback receives the data (as array of objects, where each object
	 *                     holds `id`, `caption`, and `hours` properties).
	 *                     If the fetch failed, the callback is invoked with two arguments,
	 *                     first one (data) is null, the second one is error message
	 */
	getData(callback) {
        if (this.cache) {
            callback(this.cache);
            return;
        }

        this.fetchData()
            .then(data => {
                this.cache = data;
                callback(data);
            })
            .catch(error => {
                callback(null, error.message);
            });
    }


	/**
	 * Invalidate internal cache. Next invocation of getData() will be forced to load data from the server.
	 */
	invalidate()
	{
		this.cache = null;
	}

	
	/**
	 * Modify hours for one record.
	 * @param {number} id ID of the record in question.
	 * @param {number} hours New value of the hours (m)
	 * @param {Function} callback Invoked when the operation is completed.
	 *                            On failutre, one argument with error message is passed to the callback.
	 */
	setHours(id, hours, callback = null) {
		fetch(`${this.apiUrl}?action=hours&id=${id}&hours=${hours}`, {
			method: 'POST',
			headers: {
				'Content-type': 'application/x-www-form-urlencoded',
			},
		})
		.then(response => response.json())
		.then(result => {
			if (!result.ok) {
				throw new Error(result.error);
			}
	
			if (this.cache) {
				const index = this.cache.findIndex(item => item.id === id);
				if (index !== -1) {
					this.cache[index].hours = hours;
				}
			}
			if (callback) {
				callback();
			}
		})
		.catch(error => {
			if (callback) {
				callback(error.message);
			}
		});
	}
}


// In nodejs, this is the way how export is performed.
// In browser, module has to be a global varibale object.
module.exports = { DataModel };
