const $ = require('jquery')
export default (url, data, callback, onError) => {
	$.ajax({
		url,
		data,
		type: "get",
		dataType: "json",
		success: callback,
		error: onError,
	})
}