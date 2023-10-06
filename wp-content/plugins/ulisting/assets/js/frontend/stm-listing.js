var googleApiLoad = false;

function googleApiLoadToggle() {
	googleApiLoad = true
}

/**
 *
 * @param data
 * @returns {any}
 */
function  json_parse(data) {
	return JSON.parse(data)
}

/**
 *
 * @param url
 * @param key
 * @param value
 * @returns {string}
 */
function updateQueryStringParameter(url, key, value) {
	var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	var separator = url.indexOf('?') !== -1 ? "&" : "?";
	if (url.match(re)) {
		return url.replace(re, '$1' + key + "=" + value + '$2');
	} else {
		return url + separator + key + "=" + value;
	}
}

/**
 *
 * @param key
 * @param url
 */
function removeParamUrl(key, url) {
	var rtn = url.split("?")[0],
		param,
		params_arr = [],
		queryString = (url.indexOf("?") !== -1) ? url.split("?")[1] : "";
	if (queryString !== "") {
		params_arr = queryString.split("&");
		for (var i = params_arr.length - 1; i >= 0; i -= 1) {
			param = params_arr[i].split("=")[0];
			if (param === key) {
				params_arr.splice(i, 1);
			}
		}
		rtn = rtn + "?" + params_arr.join("&");
	}
	return rtn;
}

/**
 *
 * @returns {string}
 */
function generateRandomId() {
    return parseFloat(Math.round( Math.random() * 100) / 100 ).toFixed(4) * 1000+'_'+Date.now();
}