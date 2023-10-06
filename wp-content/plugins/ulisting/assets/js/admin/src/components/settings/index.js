/**
 * General Component
 */
import headerMenu   from '../partials/header-menu'
import modal 	    from '@partials/blocks/modal'

/**
 * Import Modal Components
 */
import proFeatures  from '@partials/blocks/pro-features'
import paymentModal from './tabs/payment/payment-modal'
import emailModal	from './tabs/email/email-modal'
/**
 * Pages Component
 */
import settings  	 from './pages/settings/index'
import extensions  	 from '../settings/pages/extensions/index'
import savedSearches from '../settings/pages/saved-searches/index'
import demoImport  	 from '../settings/pages/demo-import/index'
import status  		 from '../settings/pages/status/index'


import { mapGetters, mapActions, mapMutations } from 'vuex'

export default {
	components: {
		'header-menu'  		  : headerMenu,
		'modal-window'        : modal,

		/**
		 *  Register Pages
		 */

		'status-page'		  : status,
		'settings-page'		  : settings,
		'extensions-page'	  : extensions,
		'demo-import-page'	  : demoImport,
		'saved-searches-page' : savedSearches,

		/**
		 * Register Modal Components
		 */
		'pro-features'     : proFeatures,
		'email-manager'	   : emailModal,
		'payment-settings' : paymentModal,
	},

	computed: {
		...mapGetters([
			'getLoader',
			'getAjaxUrl',
			'openContent',
			'getPreloaderUrl',
			'getActivePage',
			'getShowModal',
			'getHasAccess',
			'getModalComponent',
			'getSize'
		])
	},

	mounted() {
		setTimeout(() => this.getSettingsData())
	},

	methods: {
		...mapActions([
			'getSettingsData',
			'closeModal',
			'openModal',
			'setLoader',
		]),

		...mapMutations([
			'setShowModal',
			'setHasAccess',
		]),

		close() {
			this.dispatch('closeModal')
		}
	},

	data() {
		return {}
	},

	template: `
		<div class="uListing-container">
			<div class="uListing-page-loader" v-if="getLoader">
				<div class="uListing-preloader-wrapper">
					<div class="uListing-loader"></div>
					<img :src="getPreloaderUrl" alt="Preloader Url">
				</div>
			</div>
			<div v-if="openContent" :class="{unlock: !getLoader}" class="uListing-container-inner">
				<header-menu></header-menu>
				<components :is="getActivePage"></components>

				<modal-window v-if="getShowModal" v-on:close="closeModal" :size="getSize"> 
					<div slot="content">
						<div class="modal-body">
							<component :is="getModalComponent"  v-on:close="close"  :key="getShowModal"></component>
						</div>
					</div>
				</modal-window>
			</div>
		</div>
	`
}