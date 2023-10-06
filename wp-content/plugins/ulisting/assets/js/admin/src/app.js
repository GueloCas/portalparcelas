/**
 * Import packages
 */
import Vue   from 'vue'
import state from '@store/index'
import Vuex, { mapMutations, mapGetters } from 'vuex'

/**
 * Inline Component's Source
 */
import {
	uListingSetting, attributeAdd, attributeEdit, attrOptionsAdd, attrOptionsEdit, inventoryList,
	listCategoryAdd, listCategoryEdit, listing, listingTypes, uListingPricing, uListingUserPlan
} from './components'


/**
 * Import Builder Components
 */
import {
	elementAccordion, elementAttrBox, elementCols,
	elementModule, elementTabs, elementThumbnail, similarListing
} from './components/listing-types/tabs/builder'

/**
 * Register Builder Components Globally
 */
Vue.component('ulisting-builder-module-element', elementModule)
Vue.component('ulisting-builder-module-attribute-box', elementAttrBox)
Vue.component('ulisting-builder-module-accordion', elementAccordion)
Vue.component('ulisting-builder-module-columns', elementCols)
Vue.component('ulisting-builder-module-thumbnail-box', elementThumbnail)
Vue.component('ulisting-builder-module-tabs', elementTabs)
Vue.component('ulisting-builder-module-similar-listings', similarListing)

import vueDraggable from '../../vue/vuedraggable.min'
Vue.component('draggable', vueDraggable)

/**
 * Register Validator if vue-validate is defined
 */
import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)

/**
 * Register global components
 */
import vueSelect2 from '@js/vue/vue-select2'
import vSelect    from "vue-select";
import DatePicker from 'vue2-datepicker';
Vue.component('date-picker', DatePicker)
Vue.component("v-select", vSelect);
Vue.component('ulisting-select2', vueSelect2)


/**
 * Register Vuex and State
 */
Vue.use(Vuex)
const store = new Vuex.Store(state)

/**
 * Create Vue instance
 */
new Vue({
	store,
	el: '#uListing-main',

	mounted() {
		if ( typeof settings_data !== "undefined" ) {
			this.setApiUrl(settings_data.apiUrl)
			this.setPreloaderUrl(settings_data.uListingPreloader)
			this.setProImageUrl(settings_data.uListingProImage)
			this.setNonce(settings_data.uListingAjaxNonce)
			this.setAjaxUrl(settings_data.currentAjaxUrl)
			this.setActivePage(settings_data.activePage)
		}
	},

	computed: {
		...mapGetters([
			'getApiUrl'
		])
	},

	methods: {
		...mapMutations([
			'setNonce',
			'setApiUrl',
			'setAjaxUrl',
			'setActivePage',
			'setProImageUrl',
			'setPreloaderUrl',
		]),
	},

	components: {
		/**
		 * Main Settings
		 */
		'settings-page': uListingSetting,

		/**
		 * Pricing plans
		 */
		'pricing-plan' : uListingPricing,
		'user-plan'    : uListingUserPlan,

		/**
		 * Listing attributes
		 */
		'listing-attribute-edit': attributeEdit,
		'listing-attribute-add' : attributeAdd,

		/**
		 * Listing attribute options
		 */
		'listing-attr-options-add' : attrOptionsAdd,
		'listing-attr-options-edit': attrOptionsEdit,

		/**
		 * Listing Category
		 */
		'listing-category-add'  : listCategoryAdd,
		'listing-category-edit' : listCategoryEdit,

		/**
		 * Listing Types
		 */
		'listing-type-settings': listingTypes,

		/**
		 * Inventory list
		 */
		'inventory-list': inventoryList,

		'listing-list'  : listing,
	},
})