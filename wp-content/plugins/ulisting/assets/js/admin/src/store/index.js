import sidebarMenu  from './modules/sidebar-menu'
import headerMenu   from './modules/header-menu'
import content      from './modules/content'
import modal        from './modules/modal'
import listingType  from './modules/listingType'
import listing 		from "./modules/listing";

import getRequest from '@plugins/getRequest'

export default {
	state: {
		loader	    : true,
		ajaxUrl		: '',
		apiUrl		: '',
		nonce 		: '',
		activeTab	: '',
		activePage	: '',
		preloaderUrl: '',
		proImage	: '',
		openContent : false,
		global_texts: {},
	},

	getters: {
		getLoader 	    : s  => s.loader,
		getApiUrl	    : s  => s.apiUrl,
		getNonce	    : s  => s.nonce,
		getAjaxUrl	    : s  => s.ajaxUrl,
		getProImage 	: s  => s.proImage,
		getPreloaderUrl	: s  => s.preloaderUrl,
		getActiveTab    : s  => s.activeTab,
		getActivePage   : s  => s.activePage,
		openContent     : s  => s.openContent,
		getGlobalTexts  : s  => s.global_texts,
	},

	mutations: {

		setAjaxUrl(state, url) {
			state.ajaxUrl = url
		},

		setApiUrl(state, url) {
			state.apiUrl = url
		},

		setPreloaderUrl(state, url) {
			state.preloaderUrl = url
		},

		setProImageUrl(state, url) {
			state.proImage = url
		},

		setNonce(state, url) {
			state.nonce = url
		},

		setLoader(state, value) {
			state.loader = value
		},

		updateOpenContent(state, value) {
			state.openContent = value
		},

		setActivePage(state, value) {
			state.activePage = value
		},

		setActiveTab(state, tab) {
			state.activeTab = tab
		},

		initGlobalTexts(state, texts) {
			state.global_texts = texts
		},
	},

	actions: {
		async getSettingsData ({ commit, state }) {
			await getRequest(state.ajaxUrl, { action: 'stm_plugin_settings' }, async (res) => {
				if (res.success) {
					const { data } 	  = res
					const { content } = data

					commit('initGlobalTexts', data.global_texts)

					commit('initializeHeader', {
						pages	: data.pages,
						logoUrl : data.logo,
						buttons : data.global_texts?.buttons
					})

					commit('initializeSidebar', {
						pages	  : data.sidebar,
						searchText: data.search
					})

					commit('initializeContent', content)
					commit('updateOpenContent', true)
					setTimeout(() => commit('setLoader', false), 100)
				}
			})
		},
	},

	modules: {
		modal,
		content,
		listing,
		headerMenu,
		listingType,
		sidebarMenu,
	}
}