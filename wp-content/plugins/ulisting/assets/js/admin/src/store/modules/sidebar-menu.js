export default {
	state: {
		sidebarPages: [],
		searchText	: {}
	},

	getters: {
		getSidebarPages : s => s.sidebarPages,
		searchText  	: s => s.searchText,
	},

	mutations: {
		initializeSidebar(state, {pages, searchText}) {
			state.sidebarPages = pages
			state.searchText   = searchText
		}
	},
}