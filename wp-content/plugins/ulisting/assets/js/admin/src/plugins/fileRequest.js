const $ = require('jquery')
export default (url, data, callback, onError) => {
	$.ajax({
		url,
		data,
		type: "POST",

		contentType: false,
		processData: false,

		error: onError,
		success: callback,
	})
}