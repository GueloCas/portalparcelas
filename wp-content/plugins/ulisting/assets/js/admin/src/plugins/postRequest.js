const $ = require('jquery')
export default (url, data, callback, onError) => {
	$.ajax({
		url,
		data,
		type: "post",
		dataType: "json",
		success: callback,
		error: onError,
	})
}