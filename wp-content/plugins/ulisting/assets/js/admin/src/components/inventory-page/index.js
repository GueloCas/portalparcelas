import { mapGetters, mapMutations } from 'vuex'
import fetchRequest    from '@plugins/fetchRequest'
import fileRequest 	   from "@plugins/fileRequest";
import postRequest     from '@plugins/postRequest'
import { renderToast } from "@plugins/index"
import editLayout 	   from '../listing-types/tabs/builder/edit-layout'


export default {
	props: ['content'],

	components: {
		'edit-layout': editLayout,
	},

	data() {
		return {
			export_url: '',
			default_icon: '',
			loader: true,
			title_hover: null,
			layout_id: null,
			active_layout_id: null,
			layouts: [],
			builder_data: [],
			elements: [],
			sections: [],
			config: [],
			donor: [],
			listing_type_id: 0,
			layout_selected: {
				id: 0,
				name: "",
			},


			image	: {
				file: null
			},
			files	: null,
			file	: null,
		}	
	},

	created() {
		this.listing_type_id = settings_data?.type_id
		this.layout_id		 = settings_data?.selected_layout || null

		this.init()
	},

	computed: {
		...mapGetters([
			'getNonce',
			'getAjaxUrl',
			'getPreloaderUrl'
		]),

		getSections() {
			return this.sections || {}
		},


		generateUrl(){
			return `${this.export_url}&download=${this.layout_id}&stm_nonce=${this.getNonce}`;
		}
	},

	methods: {
		init() {
			postRequest(this.getAjaxUrl, {action: 'uListing_inventory_page_data', layout_id: this.layout_id, nonce: this.getNonce}, response => {
				if ( response.success ) {
					const { sections, layouts, layout, default_icon, export_url } = response.data

					this.export_url 		= export_url
					this.layout_selected 	= layout
					this.layouts 			= layouts || []
					this.default_icon		= default_icon

					if ( sections )
						this.initSection(sections)
				}

				if ( this.layout_id ) {
					this.edit(this.layout_id)
				} else {
					setTimeout(() => this.loader = false)
				}
			})
		},

		initSection(sections) {
			this.donor    = sections.donor
			this.config   = sections.config
			this.sections = sections.sections
			this.elements = sections.elements
		},

		applyImporter() {
			this.file = document.querySelector('#uListing-file');
			this.file.click();
		},

		loadImage() {
			if ( this.file.value )
				this.noFile = this.file.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];

			if ( this.$refs['image-file'].files[0] )
				this.files = this.$refs['image-file'].files[0];

			setTimeout(() => this.importNewLayout())
		},

		importNewLayout(){
			if ( this.files ){
				const formData = new FormData()
				formData.append('file', this.files)
				formData.append('nonce', this.getNonce)
				formData.append('type', 'inventory')
				formData.append('action', 'uListing_import_single')
				formData.append('id', `ulisting_type_page_layout_${this.generateId()}`)

				fileRequest(this.getAjaxUrl, formData, response => {
					this.files   = null
					this.layouts = response.layouts || []
					renderToast(response.title, response.status)
				})
			}
		},

		edit(id) {
			this.loader = true
			this.layout_id = id

			const data = {
				layout_id	: id,
				nonce		: this.getNonce,
				action		: 'uListing_edit_inventory_layout',
			}

			postRequest(this.getAjaxUrl, data, response => {
				if ( response.success ) {
					this.layout_selected = response.data
					this.sections 		 = this.layout_selected?.section
				}
				this.loader = false
			})
		},

		reset_layout() {
			this.layout_id       = null
			this.layout_selected = null
		},

		createNewLayout () {
			const uniqId = this.generateId()
			this.sections = [];
			this.layout_selected = {
				id:` ulisting_type_page_layout_${uniqId}`,
				name: `New Layout`,
			}
			this.layout_id = this.layout_selected.id
		},

		update (sections) {

			if ( typeof sections !== "undefined" )
				this.sections = sections

			let params = this.getSearchParameters();

			this.loader = true
			const data = {
				layout_id: this.layout_id,
				nonce: this.getNonce,
				layout_name: this.layout_selected?.name,
				sections: this.sections,
				listing_type_id : params['listing_type_id']
			}

			fetchRequest(this.getAjaxUrl + `?action=uListing_save_inventory_layout`, 'POST', data)
				.then(response => response.json())
				.then(response => {
					const { layouts } 	= response.data
					this.layouts		= layouts
					renderToast(response.message, response.status)
					this.loader = false
				})
				.catch(err => console.log(err));
		},

		remove (id) {
			const url 	 = this.getAjaxUrl
			const action = 'uListing_delete_inventory'
			if ( confirm( 'Are you sure to delete this layout?' ) )
				postRequest(url, {action, id, nonce: this.getNonce}, response => {
					renderToast(response.message, response.status)
					if ( response.success ) {
						this.layouts = this.layouts.filter(l => l.id !== id)
					}
				})
		},
		
		generateId () {
			if (!this.layouts) return 0;

			let count = 0, result = null, keys = [], access = true;
			this.layouts.forEach(value => keys.push(parseInt(value.id.slice(value.id.lastIndexOf('_') + 1, value.id.length))));

			keys = keys.sort((a, b) => a - b)
			keys.forEach( value => {
				if (keys.indexOf(count) === -1 && access) {
					access = false
					result = count
				} count++
			})

			if (result || result === 0)
				return result;
			return this.layouts.length;
		},

		update_name(name) {
			this.layout_selected.name = name
			this.layouts			  = this.layouts.map(layout => {
				if ( layout.id === this.layout_id )
					layout.name = name
				return layout
			})
		},
		getSearchParameters() {
			var prmstr = window.location.search.substr(1);
			return prmstr != null && prmstr != "" ? this.transformToAssocArray(prmstr) : {};
		},

		transformToAssocArray(prmstr) {
			var params = {};
			var prmarr = prmstr.split("&");
			for (var i = 0; i < prmarr.length; i++) {
				var tmparr = prmarr[i].split("=");
				params[tmparr[0]] = tmparr[1];
			}
			return params;
		}
	},
	
	template: `
		<div class="wrap ulisting-main inventory-list">			
			<div class="uListing-main">
				<div class="inventory-header">
					<h1 class="wp-heading-inline">
						Inventory Layout
					</h1>
				</div>
				<div class="uListing-container custom-fields layout-list ulisting-builder" id="ulisting-builder">
					<template v-if="loader">
						<div class="uListing-page-loader inventory">
							<div class="uListing-preloader-wrapper">
								<div class="uListing-loader"></div>
								<img :src="getPreloaderUrl" alt="Preloader Url">
							</div>
						</div>
					</template>
					<template v-else>
						<div v-if="!layout_selected">
							<div class="layout-container">
								<div class="layout-card-wrapper" v-for="layout in layouts" :key="layout.id" v-if="layouts.length">
									<div class="layout-card">
										<div class="layout-card-effect" style="    background-color: #fff;">
											<div class="default-image" v-if="!layout.image" style="height: 100%">
												<img :src="default_icon" alt="default icon">
											</div>
											<div class="default-image" v-else  :class="{'title-hover': title_hover === layout.id}">
												<img :src="layout.image" alt="default icon">
											</div>
										</div>
										<div class="remove" :class="{'title-hover': title_hover === layout.id}" @click.prevent="remove(layout.id)">
											<i class="icon-close"></i>
										</div>
										<div class="layout-actions">
											<button @click.prevent="edit(layout.id)" class="uListing-button uListing-button-text uListing-normalize icon" :class="{'title-hover': title_hover === layout.id}">
												<i class="icon-pen"></i>
												Edit Template
											</button>
										</div>
									</div>
									<div class="layout-card-title">
										<span class="uListing-admin-field-title" @mouseover="title_hover = layout.id" @mouseleave="title_hover =  null">{{ layout.name }}</span>
									</div>
								</div>
							</div>
							<div style="padding: 0 35px 35px;">
								<button @click.prevent="createNewLayout" class="actions uListing-button uListing-button-text uListing-normalize blue">
									<i class="icon-992651"></i>
									Create new
								</button>
								<button @click.prevent="applyImporter" class="actions uListing-button uListing-button-text uListing-normalize green">
									<i class="icon-cloud-computing-1"></i>	
									Import
								</button>
								<input type="file" id="uListing-file" hidden="hidden" accept=".txt" @change="loadImage()" ref="image-file"/>
							</div>
						</div>
						
						<div class="panel-custom" style="float: unset;" v-else>
							<edit-layout
								:key="'ulisting_single_page'"
								@reset-layout="reset_layout"
								@save-layout="update"
								@update-name="update_name"
								:is_inventory="generateUrl"
								:name="layout_selected.name"
								:sections="getSections"
								:config="config"
								:elements="elements"
								:donor="donor">
							</edit-layout>
						</div>
					</template>
			
				</div>
			</div>
		</div>
	`
}