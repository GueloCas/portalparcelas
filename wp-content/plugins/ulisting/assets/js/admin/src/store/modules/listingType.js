import postRequest     from '@plugins/postRequest'
import { renderToast } from "@plugins/index"
import Vue from 'vue'

export default {
	state: {
		typeLoader: true,
		builderLoader: false,
		attrListOptions: [],
		btnType: 'add',

		available: [],
		used: [],
		listingTypeTitle: '',
		available_clone: [],

		id: null,
		title: '',
		name: '',
		type: '',
		arrType: null,
		icon: null,
		image: null,
		image_url: null,

		listingTypeActiveTab: '',

		/**
		 * Listing order
		 */
		orderByDefault: null,
		viewType: '',
		orderUsed: [],

		/**
		 * Search Forms
		 */
		search_forms: {
			stm_search_form_advanced: [],
			stm_search_form_type: 	  [],
			stm_search_form_category: [],
		},

		autoUsedList: [],
		autoAvailableList: [],
		visibility: 0,

		/**
		 * Listing Compare
		 */
		compareUsedList: [],
		compareAvailableList: [],
		compareAvailableListClone: [],

		/**
		 * Listing Quick View
		 */
		quickViewUsedList: [],
		quickViewAvailableList: [],
		quickViewAvailableListClone: [],
		quickViewTemplate: '',

		/**
		 * Similar Listing
		 */
		similar: {
			matching: {
				same_type: true,
				same_category: false,
				same_region: false,
				same_tag: false,
			},
			count: 3,
			order_by: 'title',
			view_type: 'grid',
		},

		similarUsedList: [],
		similarAvailableList: [],

		/**
		 * Submit Form Data
		 */
		submitFormUsed: [],
		submitFormAvailable: [],
	},

	getters: {
		getAttrListOptions:   s => s.attrListOptions,
		getAvailableList: 	  s => s.available,
		getUsedList: 		  s => s.used,
		getBtnType:		      s => s.btnType,
		listingTypeActiveTab: s => s.listingTypeActiveTab,
		getFormData: 		  s => ({ title: s.title, name: s.name, type: s.type, id: s.id, icon: s.icon, image: s.image, image_url: s.image_url }),

		/**
		 * Listing order
		 * @param s
		 * @returns {null|*|orderByDefault|{set, get}|default.computed.orderByDefault}
		 */
		getOrderByDefault: s => s.orderByDefault,
		getViewType: 	   s => s.viewType,
		getOrderUsed:	   s => s.orderUsed,
		getVisibility:     s => s.visibility,

		/**
		 * Search Forms
		 * @param s
		 * @returns {function(*): *}
		 */
		getSearchFormData: s => type => {
			return s.search_forms[type]
		},
		getAutoUsedList: 	   s => s.autoUsedList,
		getAutoAvailableList:  s => s.autoAvailableList,

		getAvailableClone: s => s.available_clone,

		/**
         * Listing Compare
		 * @param s
		 * @returns {[]|*}
		 */
		getCompareUsedList: 	 		s => s.compareUsedList,
		getCompareAvailableList: 		s => s.compareAvailableList,
		getCompareAvailableListClone: 	s => s.compareAvailableListClone,

		getQuickViewUsedList: 	   		s => s.quickViewUsedList,
		getQuickViewAvailableList: 		s => s.quickViewAvailableList,
		getQuickViewAvailableListClone: s => s.quickViewAvailableListClone,
		getQuickViewTemplate: 	   		s => s.quickViewTemplate,

		/**
		 * Similar Listing
		 * @param s
		 * @returns {default.state.similar|{enable, count, order_by, matching}}
		 */

		getSimilar: s => s.similar,
		getSimilarUsedList: s => s.similarUsedList,
		getSimilarAvailableList: s => s.similarAvailableList,

		/**
		 * Submit Form Data
		 * @param s
		 * @returns {[]}
		 */
		getSubmitFormUsed: s => s.submitFormUsed,
		getSubmitFormAvailable: s => s.submitFormAvailable,

		getTitle: s => s.listingTypeTitle,

		getTypeLoader: s => s.typeLoader,

		getBuilderLoader: s => s.builderLoader
	},

	mutations: {
		setAttrListOptions(state, value) {
			state.attrListOptions = value
		},

		setFormData(state, data) {
			const { title, name, type, arrType, id, icon, image, image_url } = data
			state.id   	  	= id
			state.title   	= title
			state.name    	= name
			state.icon    	= icon
			state.image   	= image
			state.type    	= type
			state.image_url	= image_url
			state.arrType 	= arrType
		},

		setAvailable(state, value) {
			state.available = value
		},

		setUsed(state, value) {
			state.used = value
		},

		setBtnType(state, value) {
			state.btnType = value
		},

		setAvailableClone(state, value) {
			state.available_clone = value
		},

		setListingTypeActiveTab(state, value) {
			state.listingTypeActiveTab = value
		},

		/**
		 * Listing order
		 */
		setOrderByDefault(state, value) {
			state.orderByDefault = value
		},

		setViewType(state, value) {
			state.viewType = value
		},

		setOrderUsed(state, value) {
			state.orderUsed = value
		},

		/**
		 * Listing Type Search form
		 */

		setSearchFormData(state, {type, data}) {
			Vue.set(state.search_forms, type, data)
		},

		setAutoUsed(state, value) {
			state.autoUsedList = value
		},

		setAutoAvailable(state, value) {
			state.autoAvailableList = value
		},

		setVisibility(state, value) {
			state.visibility = value
		},

		/**
		 * Listing Compare
		 */
		setCompareUsed(state, value) {
			state.compareUsedList = value
		},

		setCompareAvailable(state, value) {
			state.compareAvailableList = value
		},

		setCompareAvailableClone(state, value) {
			state.compareAvailableListClone = value
		},

		/**
		 * Listing Quick View
		 */
		setQuickViewUsed(state, value) {
			state.quickViewUsedList = value
		},

		setQuickViewAvailable(state, value) {
			state.quickViewAvailableList = value
		},

		setQuickViewAvailableClone(state, value) {
			state.quickViewAvailableListClone = value
		},

		setQuickViewTemplate(state, value) {
			state.quickViewTemplate = value
		},

		setSimilar(state, value) {
			state.similar = value
		},

		setTypeLoader(state, value) {
			state.typeLoader = value
		},

		setBuilderLoader(state, value) {
			state.builderLoader = value
		},

		/**
		 * Submit Form
		 */
		setSubmitFormUsed(state, value) {
			state.submitFormUsed = value
		},

		setSubmitFormAvailable(state, value) {
			state.submitFormAvailable = value
		},

		setTitle(state, value) {
			state.listingTypeTitle = value
		},

		setSimilarUsedList(state, value) {
			state.similarUsedList = value
		},

		setSimilarAvailableList(state, value) {
			state.similarAvailableList = value
		},
	},

	actions: {
		newField({state, commit}, data) {
			const available = JSON.parse(JSON.stringify(state.available))
			available.unshift(data)

			state.available 		= available
			state.available_clone 	= available
		},

		updateField({state}, data) {
			state.available = state.available.map(a => {
				if ( a.id === data.id ) {
					a.name  	= data.name
					a.type  	= data.type
					a.title 	= data.title
					a.icon  	= data.icon
					a.image		= data.image
					a.image_url	= data.image_url
				}
				return a
			})

			state.used = state.used.map(u => {
				if ( u.id === data.id ) {
					u.name  	= data.name
					u.type  	= data.type
					u.title 	= data.title
					u.icon  	= data.icon
					u.image 	= data.image
					u.image_url	= data.image_url
				}
				return u
			})
		},

		saveListingType({state}, request_data) {
			const required_attributes 		= state.used.filter(a => a.required).map(r => r.id)
			const attributes 		  		= state.used.map(u => u.id)
			const search_autocomplete_used 	= state.autoUsedList.map(s => s.id)

			const items	= state.orderUsed.map(item => {
				delete item.id
				delete item.is_open
				return item
			})

			const search_data = {}
			const search_clone = JSON.parse(JSON.stringify(state.search_forms ))
			for( let key in search_clone ) {
				let newValue = []
				let result = search_clone[key] || []

				result.forEach(element => {
					delete element.placeholder_input
					delete element.use_field_input
					delete element.order_by_input
					delete element.order_type_input
					delete element.hide_empty_input
					delete element.is_open
					delete element.range_type_input
					delete element.prefix_input
					delete element.suffix_input
					delete element.max_input
					delete element.min_input
					delete element.default_input
					delete element.format_data_input
					delete element.column_input

					newValue.push({[element.type]: element})
				})

				search_data[key] = newValue
			}

			const submitCols = {}
			const submitUsed = state.submitFormUsed.map(el => {
				submitCols[el.id] = el.col
				return el.id
			})  || [];

			const data = {
				id				: request_data.id,
				title			: request_data?.title,
				nonce			: request_data.nonce,
				action			: 'stm_listing_type_save',
				attribute		: {
					attributes,
					required_attributes
				},

				listing_order	: {
					items,
					view_type: state.viewType,
					order_by_default: state.orderByDefault,
				},

				search_forms	: {
					search_data,
					search_autocomplete_used,
					visibility: state.visibility,
				},

				listing_compare	: state.compareUsedList?.map(c => c.id),

				similar_listing	: {
					settings: state.similar,
					used: state.similarUsedList?.map(c => c.id)
				},

				quick_view		: {
					used	 : state.quickViewUsedList?.map(c => c.id),
					template : state.quickViewTemplate
				},

				submit_form		: {
					cols	: submitCols,
					used	: submitUsed,
				}
			}

			if ( request_data.is_create )
				data.is_create = true

			postRequest(request_data.url, data, response => {
				state.typeLoader = false
				renderToast(response.message, response.status)
				if ( response.redirect_url && request_data.is_create )
					window.location.replace(response.redirect_url)
			})
		},

		searchBuildFields({state}, {type}) {
			let fields = state.search_forms[type] || []
			fields = fields.map(item => {
					if ( item.type === 'search' ) {
						item.placeholder_input = true;
						item.use_field_input = true;
					}

					else if ( item.type === 'location' ) {
						item.placeholder_input = true;
					}

					else if ( item.type === 'proximity' ) {
						item.units_input = true;
						item.min_input = true;
						item.max_input = true;
						item.default_input = true;
					}

					else if ( item.type === 'date' ) {
						item.use_field_input = true;
						item.date_type_input = true;
					}

					else if ( item.type === 'range' ) {
						item.use_field_input = true;
						item.range_type_input = true;
						item.prefix_input = true;
						item.suffix_input = true;
						item.format_data_input = true;
					}

					else if ( item.type === 'dropdown' ) {
						item.placeholder_input = true;
						item.use_field_input = true;
						item.order_by_input = true;
						item.order_type_input = true;
						item.hide_empty_input = true;
					}

					else if ( item.type === 'checkbox' ) {
						item.use_field_input = true;
						item.order_by_input = true;
						item.order_type_input = true;
						item.hide_empty_input = true;
						item.column_input = true;
						item.column = '1';
					}

				item.is_open = false
				return item
			})
			Vue.set(state.search_forms, type, fields)
		},

		addSearchField({state, dispatch}, {type, key, label}) {
			switch(type) {
				case 'search':
					state.search_forms[key].push({
					type: type,
					label: label,
					placeholder: '',
					use_field: '',
					is_open: false
				})
					break;
				case 'location':
					state.search_forms[key].push({
					type: type,
					label: label,
					placeholder: '',
					is_open: false
				})
					break;
				case 'proximity':
					state.search_forms[key].push({
					type: type,
					label: label,
					placeholder: '',
					units:'km',
					min: '100',
					max: '1000',
					default: '10',
					is_open: false
				})
					break;
				case 'date':
					state.search_forms[key].push({
					type: type,
					label: label,
					use_field: null,
					is_open: false
				})
					break;
				case 'range':
					state.search_forms[key].push({
					type: type,
					label: label,
					use_field: null,
					range_type: null,
					prefix: '$',
					suffix: null,
					format_data: null,
					is_open: false
				})
					break;
				case 'dropdown':
					state.search_forms[key].push({
					type: type,
					label: label,
					placeholder: '',
					use_field: null,
					order_by: null,
					order_type: null,
					hide_empty: false,
					is_open: false
				})
					break;
				case 'checkbox':
					state.search_forms[key].push({
					type: type,
					label: label,
					use_field: null,
					order_by: null,
					order_type: null,
					hide_empty: false,
					is_open: false
				})
					break;
				default:
					break;
			}

			dispatch('searchBuildFields', {type: key, data: state.search_forms[key]})
		}
	}
}