import attribute  	  from "./tabs/custom-fields/"
import order  	 	  from "./tabs/listing-order/"
import searchForms    from "./tabs/search-form/"
import compare        from "./tabs/listing-compare/"
import similar        from "./tabs/similar-listing/"
import quickView	  from './tabs/quick-view'
import inventory	  from './tabs/inventory'
import submitForm	  from './tabs/submit-form'
import singlePage	  from './tabs/single-page'
import previewItem	  from './tabs/preview-item'
import previewConfig  from './tabs/preview-item/preview-config'

import getRequest 	  from "../../plugins/getRequest"
import { mapActions, mapGetters, mapMutations } from "vuex"
import { setCookie, getCookie } from "@plugins/index"
import modal 	      from '@partials/blocks/modal'
/**
 * Right bars
 */
import attrRight 	from './tabs/custom-fields/add-new'
import iconPicker	from '@partials/fields/stm-icon-picker'

export default {
	components: {
		attribute,
		'modal-window'        : modal,
		'search-forms' 		  : searchForms,
		'listing-order'		  : order,
		'attribute-right-bar' : attrRight,
		'listing-compare'     : compare,
		'similar-listings'    : similar,
		'quick-view'		  : quickView,
		'inventory-page'	  : inventory,
		'submit-form'		  : submitForm,
		'single-page'		  : singlePage,
		'preview-item'		  : previewItem,
		'preview-config'	  : previewConfig,
		'stm-icon-picker'	  : iconPicker,
	},

	data() {
		return {
			tabs: null,
			type_id: null,
			editable: false,
			tabs_data: null,
			is_create: false,
			random_id: null,
			active_tab: null,
			rightBarType: null,
			archive: '',
			tabsList: [
				'attribute',
				'search-forms',
				'listing-order',
				'listing-compare',
				'similar-listings',
				'quick-view',
			]
		}
	},

	created() {
		if ( typeof settings_data !== "undefined" ) {
			this.tabs       = settings_data?.tabs || []
			this.activeTab  = settings_data?.activeTab || 'attribute'
			this.type_id	= settings_data?.type_id || null
			this.is_create	= settings_data?.is_create || false

			getRequest(this.getAjaxUrl, {action: 'stm_listing_type_data', id: this.type_id}, response => {
				if ( response.success ) {
					this.tabs_data 	= response.data
					this.title		= this.tabs_data?.title
				}

				/**
				 *  check is set cookie
				 */
				this.archive = getCookie('ulisting_listing_type_active_tab') || ''

				/**
				 * Save all components data in state
				 */
				this.tabsList.forEach(element => setTimeout(() => this.activeTab = element))

				if ( this.archive )
					setTimeout(() => this.activeTab = this.archive)

				setTimeout(() => this.loader = false)
			})
		}
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
			'getTitle',
			'getNonce',
			'getTypeLoader',
			'getIconModal',
			'getShowModal',
			'getModalComponent',
			'getPreloaderUrl',
			'listingTypeActiveTab'
		]),

		hideActive() {
			const hideTabs = ['single-page', 'preview-item']
			return hideTabs.indexOf(this.activeTab) === -1
		},

		loader: {
			get(){
				return this.getTypeLoader
			},

			set(value) {
				this.setTypeLoader(value)
			}
		},

		activeTab: {
			get() {
				return this.listingTypeActiveTab
			},

			set(tab) {
				setCookie('ulisting_listing_type_active_tab', tab)
				this.setListingTypeActiveTab(tab)
			}
		},

		title: {
			get() {
				return this.getTitle
			},

			set(value) {
				this.setTitle(value)
			}
		}
	},

	methods: {
		save() {
			this.loader = true
			this.saveListingType({url: this.getAjaxUrl, id: this.type_id, title: this.title, is_create: this.is_create, nonce: this.getNonce})
		},

		...mapMutations([
			'setTitle',
			'setTypeLoader',
			'setListingTypeActiveTab',
			'openIconPicker',
		]),

		...mapActions([
			'saveListingType',
			'closeModal',
			'closeIconPicker',
		]),

		close() {
			this.dispatch('closeModal')
		}
	},

	template: `
		<div class="uListing-container " :class="{unlock: !loader}">
			<div class="uListing-page-loader" :class="{'unlock': !loader}">
				<div class="uListing-preloader-wrapper">
					<div class="uListing-loader"></div>
					<img :src="getPreloaderUrl" alt="Preloader Url">
				</div>
			</div>
			<modal-window v-if="getShowModal" v-on:close="closeModal" size="modal-sm preview-config"> 
				<div slot="content">
					<div class="modal-body">
						<component :is="getModalComponent" :key="getShowModal" v-on:close="close"></component>
					</div>
				</div>
			</modal-window>
			<div class="icon-picker-wrapper" v-if="getIconModal">
				<stm-icon-picker @close="closeIconPicker" :icon_picker_open="true" :key="getIconModal"></stm-icon-picker>
			</div>
			<div :class="{unlock: !loader}" class="uListing-listing-type-container">
				<div class="uListing-header">
					<span class="uListing-editable-title header">
						<span class="editable-label">Edit Listing Type:</span>
						<span class="editable-title" v-if="!editable">
							{{ title ? title : 'Empty' }} 
						</span>
						<i class="icon-pen" @click.prevent="editable = true" v-if="!editable"></i>
						<span v-if="editable" class="editable-field">
							<input type="text" v-model="title">
							<button type="button" class="uListing-button" @click.prevent="editable = false">Save</button>
						</span>
					</span>
					<button v-if="hideActive" @click.prevent="save" class="uListing-button uListing-button-text uListing-normalize icon">
						Save Changes
					</button>
				</div>
				<div class="uListing-main-container">
					<div class="uListing-sidebar" v-if="tabs">
						<ul>
							<li v-for="(tab, key) in tabs" :key="key" :class="{active: activeTab === key}" @click.prevent="activeTab = key">
								<i :class="tab.icon"></i>
								{{ tab.title }}
							</li>
						</ul>
					</div>
					<div class="uListing-content" v-if="tabs_data" :class="{'similar': activeTab === 'attribute' ||  activeTab === 'similar-listings' || activeTab === 'submit-form' || activeTab === 'single-page' || activeTab === 'preview-item'}">
						<keep-alive>
							<component :is="activeTab" :content="tabs_data[activeTab]"></component>
						</keep-alive>
					</div>
				</div>
			</div>
		</div>
	`
}