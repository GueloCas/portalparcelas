export function randomId() {
	return parseFloat(Math.round(Math.random() * 100) / 100).toFixed(4) * 1000 + '_' + Date.now();
}

export function toNumber(val) {
	if (['', '0', 'false'].indexOf(val) !== -1)
		val = 0

	if (['true'].indexOf(val) !== -1)
		val = 1

	return +val
}

export function renderToast(title, type, {duration, position} = {duration: 2000, position: 'top-right'}) {
	if ( typeof vt !== "undefined" && typeof vt[type] === "function" )
		vt[type](title, { position, duration, closable: false })
}

export function setCookie(name,value,days) {
	let expires = "";
	if (days) {
		const date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		expires = "; expires=" + date.toUTCString();
	}
	document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

export function getCookie(name) {
	const nameEQ = name + "=";
	const ca = document.cookie.split(';');
	for ( let i = 0; i < ca.length; i++ ) {
		let c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}

export function eraseCookie(name) {
	document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}