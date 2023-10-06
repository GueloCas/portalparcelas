import { mapGetters, mapMutations } from 'vuex'
import fetchRequest    from '@plugins/fetchRequest'
import postRequest     from '@plugins/postRequest'
import fileRequest 	   from "@plugins/fileRequest";
import { renderToast } from "@plugins/index"
import editLayout 	   from '../builder/edit-layout'

export default {
	props: ['content'],

	components: {
		'edit-layout': editLayout,
	},

	data() {
		return {
			load: false,
			default_icon: '',
			export_url: '',
			active_layout: '',
			title_hover: null,
			layouts: [],
			layout_id: null,
			layout_selected: null,
			donor: [],
			config: [],
			elements: [],
			sections: [],
			builder_data: [],

			image: {
				file: null
			},
			files: null,
			file: null,

			prev_layout: null,
			listing_type_id: null,
			layouts_load: false,
		}
	},

	created() {
		this.listing_type_id = settings_data?.type_id

		if ( typeof this.content !== "undefined" ) {
			const { sections, layouts, active, export_url, default_icon } = this.content
			this.layouts	   	= layouts || []
			this.active_layout 	= active
			this.export_url		= export_url
			this.default_icon	= default_icon

			if ( sections ) {
				this.donor    = sections.donor;
				this.config   = sections.config;
				this.sections = sections.sections;
				this.elements = sections.elements;
			}
		}
		this.load = true
	},

	methods: {

		...mapMutations([
			'setBuilderLoader',
		]),

		createNewLayout() {
			const uniqId = this.generateId()
			this.sections = [];
			this.layout_selected = {
				id			: `ulisting_single_page_layout_${uniqId}`,
				name 		: `New Layout`,
			};

			this.layout_id = this.layout_selected.id
		},

		applyImporter() {
			let vm = this;
			vm.file = document.querySelector('#uListing-file');
			vm.file.click();
		},

		update(sections) {

			if ( typeof sections !== "undefined" )
				this.sections = sections

			this.setBuilderLoader(true)
			this.active_layout = this.layout_id
			const data = {
				nonce			: this.getNonce,
				sections    	: this.sections,
				layout_id		: this.layout_id,
				layout_name 	: this.layout_selected?.name,
				listing_type_id : this.listing_type_id,
			}

			console.log('data: ', data)

			fetchRequest(this.getAjaxUrl + `?action=uListing_save_single_layout`, 'POST', data)
				.then(response => response.json())
				.then(response => {
					const { layouts } 	= response.data
					this.layouts		= layouts
					renderToast(response.message, response.status)
					this.setBuilderLoader(false)
				})
				.catch(err => console.log(err))
		},

		generateId() {
			if ( ! this.layouts )
				return 0;

			let count  = 0,
				result = null,
				keys   = [],
				access = true;

			this.layouts.forEach(value => keys.push(parseInt(value.id.slice(value.id.lastIndexOf('_') + 1, value.id.length))))
			keys = keys.sort( (a, b) => a - b)
			keys.forEach( _ => {
				if ( keys.indexOf(count) === -1 && access ) {
					access = false
					result = count
				}
				count++
			})

			if (result || result === 0) return result;
			return this.layouts.length;
		},

		edit(id) {
			this.setBuilderLoader(true)
			this.layout_id = id
			const data = {
				nonce			: this.getNonce,
				action			: 'uListing_edit_single_layout',
				layout_id		: id,
				listing_type_id : this.listing_type_id
			}

			postRequest(this.getAjaxUrl, data, response => {
				if ( response.success ) {
					this.layout_selected = response.data
					this.sections 		 = this.layout_selected?.section
				}

				this.setBuilderLoader(false)
			})
		},

		activate(id) {

			if ( this.active_layout === id )
				return false;

			this.active_layout = id

			const data = {
				nonce			: this.getNonce,
				action			: 'uListing_active_single_template',
				layout_id		: id,
				listing_type_id : this.listing_type_id,
			}

			postRequest(this.getAjaxUrl, data, response => renderToast(response.message, response.status))
		},

		reset_layout() {
			this.layout_id       = null
			this.layout_selected = null
		},

		importLayout() {
			this.file = document.querySelector('#uListing-file');
			this.file.click();
		},

		importNewLayout(){
			if ( this.files ){
				const formData = new FormData();

				formData.append('file', this.files);
				formData.append('nonce', this.getNonce);
				formData.append('type', 'single');
				formData.append('listing_type_id', this.listing_type_id);
				formData.append('action', 'uListing_import_single');
				formData.append('id', `ulisting_single_page_layout_${this.generateId()}`);

				fileRequest(this.getAjaxUrl, formData, response => {
					this.files   = null;
					this.layouts = response.layouts || []
					renderToast(response.title, response.status)
				})
			}
		},

		loadImage() {
			if ( this.file.value )
				this.noFile = this.file.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];

			if ( this.$refs['image-file'].files[0] )
				this.files = this.$refs['image-file'].files[0];

			setTimeout(() => this.importNewLayout())
		},



		remove(id) {
			const url 	 = this.getAjaxUrl
			const action = 'uListing_delete_single_layout'

			if ( confirm( 'Are you sure to delete this layout?' ) )
				postRequest(url, {action, id: this.listing_type_id, layout_id: id, nonce: this.getNonce}, response => {
					renderToast(response.message, response.status)
					if ( response.success )
						this.layouts = this.layouts.filter(l => l.id !== id)
				})
		},

		update_name(name) {
			this.layout_selected.name = name
			this.layouts			  = this.layouts.map(layout => {
				if ( layout.id === this.layout_id )
					layout.name = name
				return layout
			})
		}
	},

	computed: {
		...mapGetters([
			'getBuilderLoader',
			'getNonce',
			'getAjaxUrl',
			'getPreloaderUrl',
		]),

		generateUrl(){
			return `${this.export_url}&download=${this.active_layout}&listing_type=${this.listing_type_id}&stm_nonce=${this.getNonce}`;
		},
	},

	template: `
		<div class="custom-fields layout-list ulisting-main ulisting-builder" id="ulisting-builder" v-if="load">
			<template v-if="getBuilderLoader">
				<div class="uListing-page-loader">
					<div class="uListing-preloader-wrapper">
						<div class="uListing-loader"></div>
						<img :src="getPreloaderUrl" alt="Preloader Url">
					</div>
				</div>
			</template>
			<template v-else-if="!getBuilderLoader">
				<div v-if="!layout_selected">
					<div class="layout-container" style="padding: 35px">
						<div class="layout-card-wrapper" v-for="layout in layouts" :key="layout.id" :class="{'selected-layout': active_layout === layout.id}">
							<div class="layout-card">
								<div class="layout-card-effect">
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
								<div class="layout-actions" :class="{'title-hover': title_hover === layout.id}">
									<button @click.prevent="edit(layout.id)" class="uListing-button uListing-button-text uListing-normalize icon">
										<i class="icon-pen"></i>
										Edit Template
									</button>
									<button @click.prevent="activate(layout.id)" class="uListing-button uListing-button-text uListing-normalize green icon" :class="{'title-hover': title_hover === layout.id, activated: active_layout === layout.id}">
										<i class="icon-shield"></i>
										{{ active_layout === layout.id ? 'Activated' : 'Activate' }}
									</button>
								</div>
							</div>
							<div class="layout-card-title">
								<span class="uListing-admin-field-title" @mouseover="title_hover = layout.id" @mouseleave="title_hover =  null">{{ layout.name }}</span>
							</div>
						</div>
					</div>
					<div style="margin: 0 35px 35px;">
						<button @click.prevent="createNewLayout" class="actions uListing-button uListing-button-text uListing-normalize blue">
							<i class="icon-992651"></i>
							Create new
						</button>
						<button @click.prevent="importLayout" class="actions uListing-button uListing-button-text uListing-normalize green">
							<i class="icon-cloud-computing-1"></i>	
							Import
						</button>
						
						<a :href="generateUrl" class="actions uListing-button uListing-button-text uListing-normalize black">
							<i class="icon-cloud-computing-1"></i>	
							Export
						</a>
						<input type="file" id="uListing-file" hidden="hidden" accept=".txt" @change="loadImage()" ref="image-file"/>
					</div>
				</div>
				<div class="panel-custom" v-else>
					<edit-layout
						:key="'ulisting_single_page'"
						@reset-layout="reset_layout"
						@save-layout="update"
						@update-name="update_name"
						:name="layout_selected.name"
						:sections="sections"
						:config="config"
						:elements="elements"
						:donor="donor">
					</edit-layout>
				</div>
			</template>
		</div>
	`
}