export default {
	state: {
		headerLogoUrl: '',
		headerPages: [],
		buttons: {}
	},

	getters: {
		getHeaderPages   : s => s.headerPages,
		getHeaderLogo    : s => s.headerLogoUrl,
		getButtonsText : s => s.buttons,
	},

	mutations: {
		initializeHeader(state, {pages, logoUrl, buttons}) {
			state.headerPages   = pages
			state.headerLogoUrl = logoUrl
			state.buttons 		= buttons || {}
		}
	},
}