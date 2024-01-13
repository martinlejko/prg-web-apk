/**
 * Example of a local function which is not exported. You may use it internally in processFormData().
 * This function verifies the base URL (i.e., the URL prefix) and returns true if it is valid.
 * @param {*} url 
 */
function verifyBaseUrl(url)
{
	return Boolean(url.match(/^https:\/\/[-a-z0-9._]+([:][0-9]+)?(\/[-a-z0-9._/]*)?$/i));
}

/**
 * Example of a local function which is not exported. You may use it internally in processFormData().
 * This function verifies the relative URL (i.e., the URL suffix) and returns true if it is valid.
 * @param {*} url 
 */
function verifyRelativeUrl(url)
{
	return Boolean(url.match(/^[-a-z0-9_/]*([?]([-a-z0-9_\]\[]+=[^&=]*&)*([-a-z0-9_\]\[]+=[^&=?#]*)?)?$/i));
}

function dateToTime({ day, month, year }) {
	const dateString = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
	return new Date(dateString + 'T00:00:00Z').getTime() / 1000;
}

function dateChecker(date) {
	const patterns = [
		/^\d{4}-\d{2}-\d{2}$/,         // yyyy-mm-dd
		/^\d{1,2}\.\d{1,2}\.\d{4}$/,  // d.m.yyyy
		/^\d{1,2}\/\d{1,2}\/\d{4}$/    // m/d/yyyy
	];

	const formats = ["yyyy-mm-dd", "d.m.yyyy", "m/d/yyyy"];

	for (let i = 0; i < patterns.length; i++) {
		if (patterns[i].test(date)) {
			let day, month, year;
			const parts = date.split(formats[i] === "yyyy-mm-dd" ? "-" : formats[i] === "d.m.yyyy" ? "." : "/");

			if (parts.length !== 3) return { isValid: false, error: 'Invalid date format.' };

			switch (formats[i]) {
				case "yyyy-mm-dd":
					[year, month, day] = parts;
					break;
				case "d.m.yyyy":
					[day, month, year] = parts;
					break;
				case "m/d/yyyy":
					[month, day, year] = parts;
					break;
			}

			if (!isNaN(year) && !isNaN(month) && !isNaN(day) && month >= 1 && month <= 12 && day >= 1 && day <= 31) {
				return { isValid: true, value: dateToTime({ day, month, year }) };
			}
		}
	}

	return { isValid: false, error: 'Invalid date format.' };
}


function timeHelperFunction(time) {
	const timeRegex = /^(\d{1,2}):(\d{2})(?::(\d{2}))?$/;
	const match = time.match(timeRegex);
	
	if (!match) return { isValid: false, error: 'Invalid time format.' };
	
	const [hours, minutes, seconds] = match.slice(1).map(Number);
	
	if (hours >= 24 || minutes >= 60 || seconds >= 60) {
		return { isValid: false, error: 'Invalid time format.' };
	}
	
	const timeInSeconds = hours * 3600 + minutes * 60 + (seconds || 0);
	return { isValid: true, value: timeInSeconds };
}	

function timeChecker(time, repeat) {
	const timeParts = time.split('-').map(part => part.trim());
    
    if (timeParts.length === 1) {
        const result = timeHelperFunction(timeParts[0]);
        return result.isValid ? { isValid: true, value: result.value } : { isValid: false, error: result.error };
    } else if (timeParts.length === 2 && repeat >= 2) {
        const [fromTime, toTime] = timeParts.map(timeHelperFunction);
        
        if (fromTime.isValid && toTime.isValid && fromTime.value < toTime.value) {
            return { isValid: true, value: { from: fromTime.value, to: toTime.value } };
        }
        
        return { isValid: false, error: 'Invalid time interval format.' };
    }
    
    return { isValid: false, error: 'Invalid time format' };
}

function methodChecker(method) {
	const validMethods = ['GET', 'POST', 'PUT', 'DELETE'];

	if (validMethods.includes(method)) {
		return { isValid: true, value: method };
	}
	return { isValid: false, error: 'Invalid method.' };
}

function bodyChecker(body) {
	if (body === "") return { isValid: true, value: {} };

	try {
		const jsonBody = JSON.parse(body);
		return { isValid: true, value: jsonBody };
	} catch (error) {
		return { isValid: false, error: 'Invalid JSON format.' };
	}
}

function urlChecker(url, formData) {
	let urlBase = formData.get('url_base');
	if (!verifyRelativeUrl(url)) {
		return { isValid: false, error: 'Invalid format.' };
	}
	return { isValid: true, value: urlBase + url };
}

function urlValidate(answer, errors, formData) {
	const urlBase = formData.get('url_base');
  
	if (!verifyBaseUrl(urlBase)) {
	  errors['url_base'] = "Invalid URL format.";
	} else {
	  answer['url_base'] = { 'url_base': urlBase };
	}
}

function repeatChecker(repeat) {
	const val = parseInt(repeat, 10);
	if (!isNaN(val) && val <= 100 && val >= 1 && val.toString() === repeat) {
		return { isValid: true, value: val };
	}
	return { isValid: false, error: 'Invalid repeat value.' };
}

function checker(variable, value, operation, index, errors, formData) {
	const res = getRes(variable, value, formData, index);

	if (!res.isValid) {
		errors[variable] = errors[variable] || {};
		errors[variable][index] = res.error;
	} else if (res.value !== undefined) {
		operation[variable] = res.value;
	}
}

function getRes(variable, value, formData, index) {
	switch (variable) {
		case 'date':
			return dateChecker(value);
		case 'time':
			return timeChecker(value, formData.getAll('repeat')[index]);
		case 'repeat':
			return repeatChecker(value);
		case 'url':
			return urlChecker(value, formData, index);
		case 'method':
			return methodChecker(value);
		case 'body':
			return bodyChecker(value);
		default:
			return { isValid: false, error: 'Invalid field name.' };
	}
}
/**
 * Main exported function that process the form and yields the sanitized data (or errors).
 * @param {*} formData Input data as FormData instance.
 * @param {*} errors Object which collects errors (if any).
 * @return Serialized JSON containing sanitized form data.
 */
function processFormData(formData, errors)
{
	const variables = ['date', 'time', 'repeat', 'url', 'method', 'body'];
	const answer = [];
	const opData = {};

	urlValidate(answer, errors, formData);


	const numOfOp = formData.getAll(variables[0]).length;

	for (let i = 0; i < numOfOp; i++) {
		let operation = {};
		variables.forEach(variable => {
			opData[variable] = formData.getAll(variable);
			checker(variable, opData[variable][i], operation, i, errors, formData);
		});
		answer.push(operation);
	}

	if (Object.keys(errors).length === 0) {

		const jsonString = JSON.stringify(answer, null, 2);
		return jsonString;
	} else {
		return null;
	}
}


// In nodejs, this is the way how export is performed.
// In browser, module has to be a global varibale object.
module.exports = { processFormData };
