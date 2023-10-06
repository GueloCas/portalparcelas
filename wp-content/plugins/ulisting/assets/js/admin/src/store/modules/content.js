import postRequest	   from '@plugins/postRequest'
import { renderToast } from "@plugins/index";

export default {
	state: {
		main: {},
		cron: {},
		pages: {},
		emails: {},
		payments: {},
		userRoles: {},
		socialLogin: {},

		saveData: {},
	},

	getters: {
		getCronData   		 : s => s.cron,
		getMainData   		 : s => s.main,
		getPagesData   		 : s => s.pages,
		getEmailData   		 : s => s.emails,
		getPaymentsData   	 : s => s.payments,
		getUserRolesData   	 : s => s.userRoles,
		getSocialLoginData   : s => s.socialLogin,

		getAllData : s => {
			return {
				main		: s.main,
				cron		: s.cron,
				pages		: s.pages,
				emails		: s.emails,
				payments	: s.payments,
				userRoles	: s.userRoles,
				socialLogin : s.socialLogin
			}
		},
	},

	mutations: {
		initializeContent(state, content) {
			state.cron 		  = content.cron || {}
			state.main 		  = content.main || {}
			state.pages 	  = content.pages || {}
			state.emails 	  = content.emails || {}
			state.payments 	  = content.payments || {}
			state.userRoles   = content.user_roles || {}
			state.socialLogin = content.socials || {}
		},

		updateCron(state, cron) {
			state.saveData.cron = cron
		},

		updateMain(state, main) {
			state.saveData.main = main
		},

		updatePages(state, main) {
			state.saveData.pages = main
		},

		updateSocials(state, socialLogin) {
			state.saveData.socialLogin = socialLogin
		},

		generatePages(state, pages) {
			state.pages = pages
		}
	},

	actions: {
		async saveSettings({state, getters}, data) {
			await postRequest(data.url, { action: 'stm_settings_save', data: state.saveData, nonce: data.nonce }, res => {
				if ( res.success )
					renderToast(res.message, res.status)
			})
		},

		async generatePages ({ commit, state }, data) {
			await postRequest(data.url, { action: 'stm_generate_pages', pages: state.saveData.pages, nonce: data.nonce }, res => {
				if (res.success) {
					renderToast(res.message, res.status)
					const { pages } 	  = res
					commit('generatePages', pages)
				}
			})
		},

		async savePayments({state, getters}, data) {
			await postRequest(data.url, { action: 'stm_settings_save', data: state.saveData }, res => {
				if (res.success) {
					renderToast(res.message, res.status)
				}
			})
		},
	}
}