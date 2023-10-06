export default function (url, method, data) {
	return fetch(url, {
		method,
		credentials: 'same-origin',
		headers: {
			'Accept': 'application/json, text/plain',
			'Content-Type': 'application/json; charset=UTF-8 ',
			'Cache-Control': 'no-cache',
		},
		body: JSON.stringify(data)
	})
}