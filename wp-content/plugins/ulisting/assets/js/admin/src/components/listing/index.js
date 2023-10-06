import getRequest 		from "../../plugins/getRequest";
import { mapGetters } 	from "vuex";
import fetchRequest 	from '@plugins/fetchRequest'
import toggleValidator 	from '@plugins/toggleValidator'
import { renderToast } 	from "../../plugins";

import {
	accordion, checkbox, date, file, labels, gallery, location,
	parameters, price, radio, textarea, time, video, wp_editor
} from './fields'

export default {
	components: {
		'radio_button-field': radio,
		'text_area-field'  	: textarea,
		'time-field'	  	: time,
		'date-field'	  	: date,
		'price-field'     	: price,
		'wp_editor-field' 	: wp_editor,
		'gallery-field'   	: gallery,
		'location-field'  	: location,
		'custom-labels'	  	: labels,
		'accordion-field' 	: accordion,
		'checkbox-field'  	: checkbox,
		'parameters-field'	: parameters,
		'video-field'		: video,
		'file-field'		: file
	},

	data() {
		return {
			link			: '',
			is_create		: false,
			base_url		: '',
			edit_link		: false,
			regions_values	: [],
			category_values : [],
			change_type	    : false,
			user_list		: [],
			type_list		: [],
			labels_list		: [],
			custom_labels	: [],
			selected_type	: '',
			status_options	: [],
			post_status		: 'publish',
			status_helper   : 'publish',
			regions_list	: [],
			category_list	: [],
			first_update    : true,

			title		: '',
			options		: [],

			feature		: '',
			thumbnail	: null,
			created_by	: '',
			listing_link: '',
			activeTab	: 'main',
			listing_id	: null,
			load		: true,
			tabs		: [
				{ id: 'main', title: 'Main' },
				{ id: 'labels', title: 'Labels' },
			],
			accordion_data: {},
			listing_data: {

			},
		}
	},

	created() {
		if ( typeof settings_data !== "undefined" ) {
			this.listing_id	= settings_data?.listing_id
			this.is_create	= settings_data?.is_create || false

			getRequest(this.getAjaxUrl, {action: 'uListing_listing_data', listing_id: this.listing_id, nonce: this.getNonce}, response => {
				if ( response.success ) {
					const { type_list, regions_list, category_list,  labels_list, status_options, user_list, selected_type, ...other_data } = response.data
					this.title				= other_data?.title
					this.base_url			= other_data.base_url
					this.regions_list		= regions_list
					this.category_list		= category_list
					this.regions_values		= other_data?.regions
					this.category_values	= other_data?.categories
					this.labels_list		= labels_list || null
					this.status_options		= status_options || []
					this.post_status		= other_data?.post_status || 'publish'
					this.status_helper		= other_data?.post_status || 'publish'
					this.type_list     		= type_list || []
					this.user_list 	   		= user_list || []
					this.selected_type 		= selected_type || ''
					this.created_by	   		= other_data?.created_by || ''
					this.options	   		= other_data?.options || []
					this.custom_labels		= other_data?.custom_labels || []
					this.feature			= other_data?.feature || false
					this.listing_link 		= decodeURIComponent(other_data.post_link)
					this.change_type		= selected_type
					this.thumbnail			= other_data?.thumbnail

					if ( !Array.isArray(this.custom_labels) ) {
						const labels = JSON.parse(JSON.stringify(this.custom_labels))
						this.custom_labels = []
						for ( let key in labels )
							this.custom_labels.push(labels[key])
					}
					this.reRenderTabs()
				}

				this.load = false
				setTimeout(() => this.preventForms())
			})
		}
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
			'getNonce',
			'getPreloaderUrl',
		]),

		getLink() {
			return this.base_url + decodeURIComponent(this.listing_link)
		},

		disabled() {
			return (!(this.title && this.title.trim().length > 0) || !this.selected_type)
		},

		getOptions() {
			return this.options || []
		},
	},

	methods: {
		preventForms() {
			const forms = document.querySelectorAll('form') || []
			Array.from(forms).forEach(form => {
				if ( form.name === 'post' )
					form.addEventListener('submit', e => e.preventDefault())
			})
		},

		reRenderTabs() {
			this.tabs = [
				{ id: 'main', title: 'Main' },
				{ id: 'labels', title: 'Labels' },
			]

			if ( this.regions_list?.length > 0 )
				this.tabs.push({ id: 'regions', title: 'Regions' })

			if ( this.category_list?.length > 0 )
				this.tabs.push({ id: 'category', title: 'Categories' })

			Object.values(this.options).forEach(element => this.tabs.push({id: `${element.type}_${element.id}`, title: element.title}))
		},

		scrollTo(id) {
			this.activeTab	= id
			const className = `.uListing-scroll-to-${id}`
			const $row		= document.querySelector(className) || null

			if ( $row )
				window.scroll(0, ($row.offsetTop || 0) + 7)
		},

		update(key, value, scroll = false) {
			if ( key === 'thumbnail_id' ) {
				this.thumbnail = value
			} else if ( typeof key !== "undefined" )
				this.listing_data[key] = value

			if ( scroll ) {
				if ( !this.first_update ) {
					setTimeout(() => this.scrollTo(scroll))
				}
				setTimeout(() => this.first_update = false, 500)
			}
		},

		save() {
			this.load = true
			this.scrollTo('main')

			if ( this.status_helper !== 'publish' )
				this.post_status = 'publish'

			const permalink = this.valid_link(JSON.parse(JSON.stringify(this.title)))

			const data 	= {
				post_status		: this.post_status,
				regions			: this.regions_values,
				categories      : this.category_values,
				nonce			: this.getNonce,
				post_id 		: this.listing_id,
				type_id 		: this.selected_type,
				title			: this.title,
				thumbnail_id	: this.thumbnail,
				permalink		: permalink,
				created_by		: this.created_by,
				accordion_data	: this.accordion_data,
				options			: this.listing_data
			}

			if (toggleValidator(this.feature))
				data.options.feature = 1

			if ( this.is_create ) {
				data.is_create = true
			}

			fetchRequest(this.getAjaxUrl + `?action=uListing_listing_save`, 'POST', data)
				.then(response => response.json())
				.then(response => {
					renderToast(response.message, response.status)
					this.change_type = true
					this.load = false
					this.status_helper = this.post_status

					if ( response.redirect_url && this.is_create ) {
						window.location.replace(response.redirect_url)
						return true
					}
					window.location.reload()
				})
				.catch(err => console.log(err))
		},

		accordion_update(key, data) {
			this.accordion_data[key] = JSON.stringify(data)
		},

		valid_link(title) {
			let name = '';
			let listing_link = typeof title !== "undefined" ? title : decodeURIComponent(this.listing_link)

			for (let i = 0; i < listing_link.length; i++) {
				if ( listing_link.charCodeAt(i) === 45 || !(listing_link.charCodeAt(i) >= 33 && listing_link.charCodeAt(i) <= 47) ) {
					name += listing_link.charAt(i)
					name = name.replace(/[ ]/g,'-');
				}
			}

			name = name.toLowerCase();
			while(name.indexOf('--') !== -1 ) {
				name = name.split('--').join('-')
			}

			this.listing_link = name
			return name
		},

		type_selected() {
			this.load 			= true
			if ( !this.selected_type ) {
				this.options = []
				this.reRenderTabs()
				setTimeout(() => this.load = false, 1000)
				return
			}

			this.first_update 	= true
			const data = {
				action		: 'uListing_get_selected_type_options',
				type_id		: this.selected_type,
				listing_id	: this.listing_id,
				nonce		: this.getNonce,
			}

			getRequest(this.getAjaxUrl, data, response => {
				if ( response.success ) {
					const { data } = response

					if ( typeof data !== "undefined" ) {
						this.title				= data?.title
						this.created_by 		= data.created_by
						this.regions_values		= data?.regions
						this.category_values	= data?.categories
						this.listing_link 		= decodeURIComponent(data.post_link)
						this.options			= data.options
						this.thumbnail			= data.thumbnail
						this.post_status		= data?.post_status || 'publish'
						this.status_helper		= data?.post_status || 'publish'
						this.feature			= toggleValidator(data.feature)
						this.custom_labels		= data?.custom_labels || []
					}

					this.reRenderTabs()
					this.load = false
				}
			})
		},

		stopPropagation(e) {
			if ( this.edit_link )
				e.preventDefault()
		},

		saveLink() {
			this.edit_link = false
			const listing_link = this.valid_link()
			const data 	= {
				nonce			: this.getNonce,
				post_id 		: this.listing_id,
				base_url		: this.base_url + decodeURIComponent(listing_link),
				listing_link	: listing_link,
			}

			fetchRequest(this.getAjaxUrl + `?action=uListing_listing_link_save`, 'POST', data)
				.then(response => response.json())
				.then(response => {
					renderToast(response.message, response.status)
					this.listing_link = response.post_name
					this.load = false
				})
				.catch(err => console.log(err))
		}
	},

	template: `
		<div class="uListing-container" :class="{'unlock': !load}">
			<div class="uListing-page-loader" :class="{'unlock': !load}">
				<div class="uListing-preloader-wrapper">
					<div class="uListing-loader"></div>
					<img :src="getPreloaderUrl" alt="Preloader Url">
				</div>
			</div>
			<div class="uListing-listing-type-container" :class="{'unlock': !load}">
				<div class="uListing-header">
					<span class="uListing-editable-title header">
						<span class="editable-label">Listings <span class="header-icon icon--361"></span>{{ !change_type ? 'New' : 'Edit' }} Listing</span>
					</span>
					
					<div style="display: flex">
						<div class="uListing-admin-select" style="margin-right: 25px; min-width: 150px" v-if="status_helper === 'publish'">
							<select v-model="post_status" class="uListing-select-box uListing-select-box-text uListing-normalize">
								<option value="publish" selected>Publish</option>
								<option v-for="(value, key) in status_options" :value="key">{{ value }}</option>
							</select>
						</div>
						<button class="uListing-button uListing-button-text uListing-normalize icon save-listing" :class="{'disabled': disabled}" @click.prevent="save">
							 {{ status_helper === 'publish' ? 'Save Changes' : 'Publish' }}
						</button>
					</div>
				</div>
				<div class="uListing-main-container">
					<div class="uListing-sidebar" id="listing-scroll">
						<ul style=" overflow-y: scroll; height: 100vh;">
							<li v-for="tab in tabs" :key="tab.id" :class="{active: activeTab === tab.id}" @click.prevent="scrollTo(tab.id)">
								{{ tab.title }}
							</li>
						</ul>
					</div>
					<div class="uListing-content similar">
						<div class="container">
							<div class="row uListing-scroll-to-main">	
													
								<div class="col-12" style="margin-bottom: 10px">
									<h1 class="uListing-header-1">Listing</h1>
								</div>

								<div class="col-8" >
									<div class="uListing-admin-select" :class="{disabled: change_type}">
										<span class="uListing-admin-field-title">Listing Type</span>
										<select class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="selected_type" @change="type_selected">
											<option value="" selected>Not Selected</option>
											<option v-for="(title, key) in type_list" :key="key" :value="key">{{ title }}</option>
										</select>
									</div>
								</div>

								<div class="col-4" style="display: flex; align-items: flex-end;">
									<div class="uListing-switch-field">
										<label class="uListing-switch">
											<input type="checkbox" :value="true" v-model="feature">
											<span class="uListing-slider uListing-round"></span>
										</label>
										<span class="uListing-switch-text uListing-normalize">Mark as featured</span>
									</div>
								</div>
								
								<div class="col-8">
									<div class="uListing-input-field">
										<span class="uListing-admin-field-title">Title</span>
										<input v-model="title" type="text" class="uListing-input uListing-input-text uListing-normalize input-field medium">
									</div>
								</div>
								
								<div class="col-4" style="margin-bottom: 15px;">
									<div class="uListing-admin-select">
										<span class="uListing-admin-field-title">Listing Owner</span>
										<select class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="created_by">
											<option value="" selected>Not Selected</option>
											<option v-for="user in user_list" :key="user.id" :value="user.id">{{ user.name }}</option>
										</select>
									</div>
								</div>
								
								<div class="col-12" style="margin-bottom: 30px;" v-if="!is_create && listing_link.indexOf('http://') === -1">
									<div class="listing-link">
										<div class="link-text">
											<a draggable="false" @click="stopPropagation" :href="getLink" target="_blank" class="listing-link-text listing-view" :class="{'no-decoration': edit_link}">{{ base_url }}<span v-if="!edit_link">{{ listing_link }}</span><input style="padding: 3px 6px !important;" v-else type="text" v-model="listing_link">/</a>		
										</div>
										<div class="link-action">
											<button v-if="!edit_link" class="uListing-button uListing-button-text uListing-normalize blue small" @click.prevent="edit_link = true">Edit</button>
											<button v-else class="uListing-button uListing-button-text uListing-normalize blue small" @click.prevent="saveLink">Save</button>
										</div>
									</div>
								</div>
								
								
							</div>
							
							<div class="row uListing-scroll-to-regions" v-if="regions_list && regions_list.length > 0">
								<div class="col-12">
									<h1 class="uListing-header-1">Regions</h1>
								</div>
								<div class="col-12">
									<div class="uListing-checkbox-wrapper">
										<div class="uListing-checkbox-field" style="width: 20% !important" v-for="region in regions_list" :key="region.id">
											<input type="checkbox" :id="region.id + '_region'" :value="region.id" v-model="regions_values">
											<label style="line-height: 2.5" :for="region.id + '_region'">
												<span class="uListing-checkbox-text uListing-normalize">
													{{ region.name }}
												</span>
											</label>
										</div>
									</div>
								</div>
							</div>
							
							<div class="row uListing-scroll-to-category" v-if="category_list && category_list.length > 0">
								<div class="col-12">
									<h1 class="uListing-header-1">Categories</h1>
								</div>
								<div class="col-12">
									<div class="uListing-checkbox-wrapper">
										<div class="uListing-checkbox-field" style="width: 20% !important" v-for="category in category_list" :key="category.id">
											<input type="checkbox" :id="category.id + '_category'" :value="category.id" v-model="category_values">
											<label style="line-height: 2.5" :for="category.id + '_category'">
												<span class="uListing-checkbox-text uListing-normalize">
													{{ category.name }}
												</span>
											</label>
										</div>
									</div>
								</div>
							</div>
							
							<div class="row uListing-scroll-to-labels">
								<div class="col-12">
									<h1 class="uListing-header-1">Labels</h1>
								</div>
								<custom-labels @update="update" :labels_list="labels_list" :custom_labels="custom_labels"></custom-labels>
							</div>
							
							<component @update="update" @accordion-data="accordion_update" :is="option.type + '-field'" :selected_id="thumbnail" v-for="(option, key) in getOptions" :type="option.type + '_' + option.id" :class_name="'uListing-scroll-to-' + option.type + '_' + option.id" :attribute="option" :key="key"></component>
						</div>
					</div>
				</div>
			</div>
		</div>
	`
}