/**
 * Example of a local function which is not exported. You may use it internally in processFormData().
 * This function verifies the base URL (i.e., the URL prefix) and returns true if it is valid.
 * @param {*} url 
 */
function verifyBaseUrl(url) {
	return Boolean(url.match(/^https:\/\/[-a-z0-9._]+([:][0-9]+)?(\/[-a-z0-9._/]*)?$/i));
}

/**
 * Example of a local function which is not exported. You may use it internally in processFormData().
 * This function verifies the relative URL (i.e., the URL suffix) and returns true if it is valid.
 * @param {*} url 
 */
function verifyRelativeUrl(url) {
	return Boolean(url.match(/^[-a-z0-9_/]*([?]([-a-z0-9_\]\[]+=[^&=]*&)*([-a-z0-9_\]\[]+=[^&=?#]*)?)?$/i));
}


/**
 * Main exported function that process the form and yields the sanitized data (or errors).
 * @param {*} formData Input data as FormData instance.
 * @param {*} errors Object which collects errors (if any).
 * @return Serialized JSON containing sanitized form data.
 */
function processFormData(formData, errors) {
	const result = [];
	const properties = ['date', 'time', 'repeat', 'url', 'method', 'body'];
	let operationsData = {};
	const numberOfOperations = formData.getAll(properties[0]).length;

	// Base URL validation
	if (!verifyBaseUrl(formData.get('url_base'))) {
		errors['url_base'] = "Invalid URL format.";
	} else {
		result['url_base'] = { 'url_base': formData.get('url_base') };
	}

	// Operations processing
	for (let i = 0; i < numberOfOperations; i++) {
		let operation = {};
		properties.forEach(property => {
			operationsData[property] = formData.getAll(property);
			CheckField(property, operationsData[property][i], operation, i, errors, formData);
		});
		result.push(operation);
	}

	return Object.keys(errors).length === 0 ? JSON.stringify(result, null, 2) : null;
}

function CheckField(name, value, result, indexOp, errors, formData) {
	let validationResult = getCheckResult(name, value, formData, indexOp);

	if (!validationResult.isValid) {
		errors[name] = errors[name] || {};
		errors[name][indexOp] = validationResult.error;
	} else if (validationResult.value !== undefined) {
		result[name] = validationResult.value;
	}
}

function getCheckResult(name, value, formData, indexOp) {
	switch (name) {
		case 'date': return CheckDate(value);
		case 'time': return CheckTime(value, formData.getAll('repeat')[indexOp]);
		case 'repeat': return CheckRepeat(value);
		case 'url': return CheckUrl(value, formData);
		case 'method': return CheckMethod(value);
		case 'body': return CheckBody(value);
		default: return { isValid: false, error: 'Invalid field name.' };
	}
}

// Date Validation
function CheckDate(date) {
	const regexPatterns = [/^\d{4}-\d{2}-\d{2}$/, /^\d{1,2}\.\d{1,2}\.\d{4}$/, /^\d{1,2}\/\d{1,2}\/\d{4}$/];
	const formats = ["yyyy-mm-dd", "d.m.yyyy", "m/d/yyyy"];

	for (let i = 0; i < regexPatterns.length; i++) {
		if (regexPatterns[i].test(date)) {
			let parsedDate = parseDate(date, formats[i]);
			return parsedDate ? { isValid: true, value: dateToTimeStamp(parsedDate) } : { isValid: false, error: 'Invalid date format.' };
		}
	}
	return { isValid: false, error: 'Invalid date format.' };
}

function parseDate(date, format) {
	const dateParts = date.split(format === "yyyy-mm-dd" ? "-" : format === "d.m.yyyy" ? "." : "/");
	if (dateParts.length !== 3) return null;

	let day, month, year;
	if (format === "yyyy-mm-dd") {
		[year, month, day] = dateParts;
	} else if (format === "d.m.yyyy") {
		[day, month, year] = dateParts;
	} else if (format === "m/d/yyyy") {
		[month, day, year] = dateParts;
	}

	if (isValidDateParts(day, month, year)) {
		return { day, month, year };
	}
	return null;
}

function isValidDateParts(day, month, year) {
	return !isNaN(day) && !isNaN(month) && !isNaN(year) && day >= 1 && day <= 31 && month >= 1 && month <= 12;
}

function dateToTimeStamp({ day, month, year }) {
	const dateString = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
	return new Date(dateString + 'T00:00:00Z').getTime() / 1000;
}

// Time Validation
function CheckTime(time, repeat) {
	const timeParts = time.split('-').map(part => part.trim());
	if (timeParts.length === 1) {
		return CheckSingleTime(timeParts[0]);
	} else if (timeParts.length === 2 && repeat >= 2) {
		return CheckTimeInterval(timeParts);
	}
	return { isValid: false, error: 'Invalid time format' };
}

function CheckSingleTime(time) {
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

function CheckTimeInterval(timeParts) {
	const [fromTime, toTime] = timeParts.map(CheckSingleTime);
	if (fromTime.isValid && toTime.isValid && fromTime.value < toTime.value) {
		return { isValid: true, value: { from: fromTime.value, to: toTime.value } };
	}
	return { isValid: false, error: 'Invalid time interval format.' };
}


// Body Validation
function CheckBody(body) {
	if (body === "") return { isValid: true, value: {} };

	try {
		const parsedBody = JSON.parse(body);
		return { isValid: true, value: parsedBody };
	} catch (error) {
		return { isValid: false, error: 'Invalid JSON format.' };
	}
}

// Method Validation
function CheckMethod(method) {
	const validMethods = ['GET', 'POST', 'PUT', 'DELETE'];
	return validMethods.includes(method)
		? { isValid: true, value: method }
		: { isValid: false, error: 'Invalid method.' };
}

// Repeat Validation
function CheckRepeat(repeat) {
	const repeatValue = parseInt(repeat, 10);
	if (!isNaN(repeatValue) && repeatValue >= 1 && repeatValue <= 100 && repeatValue.toString() === repeat) {
		return { isValid: true, value: repeatValue };
	}
	return { isValid: false, error: 'Invalid repeat value.' };
}

// URL Validation (remains same as in original request)
function CheckUrl(url, formData) {
	if (!verifyRelativeUrl(url)) {
		return { isValid: false, error: 'Invalid format.' };
	}
	let baseURL = formData.get('url_base');
	return { isValid: true, value: baseURL + url };
}


// In nodejs, this is the way how export is performed.
// In browser, module has to be a global varibale object.
module.exports = { processFormData };
