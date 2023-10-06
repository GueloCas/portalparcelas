/**
 * Require Tab components
 */
import main 	   from '@settings-tab/main/main'
import pages 	   from '@settings-tab/pages/pages'
import userRoles   from '@settings-tab/user-roles/user-main'
import payment 	   from '@settings-tab/payment/payment'
import cron 	   from '@settings-tab/cron/cron'
import email 	   from '@settings-tab/email/email'
import socialLogin from '@settings-tab/socials/social-login'

import { mapGetters, mapMutations } from 'vuex'
import { setCookie, getCookie } from "@plugins/index";

export default {
	components: {

		/**
		 *  Register Tabs content
		 */

		'main-tab'		  : main,
		'pages-tab'	 	  : pages,
		'user-roles-tab'  : userRoles,
		'payment-tab'	  : payment,
		'email-tab'		  : email,
		'cron-tab'		  : cron,
		'social-login-tab': socialLogin,
	},

	data() {
		return {
			archive: '',
			tabs: [
				'cron-tab',
				'social-login-tab',
				'email-tab',
				'payment-tab',
				'user-roles-tab',
				'pages-tab',
				'main-tab',
			]
		}
	},

	mounted() {

		/**
		 *  check is set cookie
		 */
		this.archive = getCookie('uListing_settings_active_tab') || ''

		/**
		 * Save all components data in saveData state
		 */
		this.tabs.forEach(element => setTimeout(() => this.activeTab = element))

		if ( this.archive )
			setTimeout(() => this.activeTab = this.archive)

	},

	computed: {
		...mapGetters([
			'getActiveTab',
		]),

		activeTab: {
			get() {
				return this.getActiveTab
			},

			set(tab) {
				setCookie('uListing_settings_active_tab', tab)
				this.setActiveTab(tab)
			}
		}
	},

	methods: {
		...mapMutations([
			'setActiveTab',
		]),
	},

	template: `
			<keep-alive>
				<component :key="activeTab" :is="activeTab"></component>
			</keep-alive>
	`
}