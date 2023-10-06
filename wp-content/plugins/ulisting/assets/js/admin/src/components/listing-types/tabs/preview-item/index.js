import editLayout  	   from '../builder/edit-layout'
import postRequest     from '@plugins/postRequest'
import { renderToast } from "@plugins/index"
import {mapActions, mapGetters, mapMutations} from 'vuex'
import fetchRequest    from '@plugins/fetchRequest'
import fileRequest 	   from "@plugins/fileRequest";

export default {
	props: ['content'],

	components: {
		'edit-layout': editLayout,
	},

	data() {
		return {
			contentLoad: false,
			loader: false,
			listing_type_id: 0,
			export_url: '',
			sections: [],
			config: [],
			elements: [],
			donor: [],
			prev_section: [],
			active_layout: { id: 0 },
			title_hover: '',
			card_config :[],
			card_layout :[
				{
					id: "grid",
					icon: "fa fa-th-large",
					name: "Grid",
					field_group: null,
					config_show: false,
					config: {
						template: "none",
						column: {
							large: 3,
							medium: 2,
							small: 1,
							extra_large: 4,
							extra_small: 1,
						}
					}
				},
				{
					id:"list",
					icon:"fa fa-th-list",
					name:"List",
					field_group:null,
					config_show:false,
					config:{
						template:"none",
					}
				},
				{
					id:"map",
					icon:"fa fa-map-marker",
					name:"Map",
					field_group:null,
					config_show:false,
					config: {
						template:"none",
					}
				}
			],
		}
	},

	created(){
		this.listing_type_id = settings_data?.type_id

		if ( typeof this.content !== "undefined" ) {
			const { sections, export_url, layout } = this.content
			if ( sections ) {
				this.donor       = sections.donor
				this.config   	 = sections.config
				this.elements 	 = sections.elements
				this.export_url  = export_url
				this.card_config = this.config?.card_config || {}
				this.set_layout(this.card_layout[0], layout)
			}
		}

		this.contentLoad = true
	},

	methods: {
		...mapActions([
			'saveListingType',
		]),

		...mapMutations([
			'setTypeLoader',
		]),

		set_layout(layout, current){
			this.active_layout = layout
			this.active_layout.field_group = this.card_config[this.active_layout.id]
			if ( typeof current !== "undefined" ) {
				this.sections			  		= current.sections
				this.active_layout.field_group 	= this.card_config[this.active_layout.id]
				this.active_layout.config		= current.config
				return
			}
			this.get_layout()
		},

		get_layout() {
			this.loader = true
			const layout_id = this.active_layout.id
			const data = {
				layout_id,
				nonce: this.getNonce,
				action: 'uListing_preview_item_data',
				listing_type_id: this.listing_type_id
			}

			postRequest(this.getAjaxUrl, data, response => {
				if ( response.success ) {
					this.active_layout = {...this.active_layout, ...response.data}
					this.sections	   = this.active_layout.sections
				}
				this.loader = false
			})
		},

		set_config(config) {
			if ( typeof config !== "undefined" )
				this.active_layout.config = config
		},

		update(sections){
			this.loader = true

			if ( !this.sections )
				this.sections = sections

			const data = {
				nonce: this.getNonce,
				sections: this.sections,
				layout: this.active_layout,
				listing_type_id: this.listing_type_id,
			}

			const type_id	= settings_data?.type_id || null
			const is_create	= settings_data?.is_create || false

			fetchRequest(this.getAjaxUrl + `?action=uListing_preview_item_data_save`, 'POST', data)
				.then(response => response.json())
				.then(response => {
					renderToast(response.message, response.status)
					this.loader = false

					if ( is_create ) {
						this.loader = true
						this.saveListingType({url: this.getAjaxUrl, id: type_id, title: this.getTitle, is_create: true})
					}
				})
				.catch(err => console.log(err));

		},
		importLayout() {
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
				const formData = new FormData();

				formData.append('file', this.files);
				formData.append('nonce', this.getNonce);
				formData.append('type', 'preview');
				formData.append('listing_type_id', this.listing_type_id);
				formData.append('action', 'uListing_import_preview_item');
				formData.append('id', `stm_listing_item_card_${this.active_layout.id}`);

				fileRequest(this.getAjaxUrl, formData, response => {
					this.files   = null;
					this.active_layout.field_group = this.card_config[this.active_layout.id]
					this.active_layout = {...this.active_layout, ...response.layout}
					this.sections	   = this.active_layout.sections
					this.loader = false
					this.contentLoad = true
					this.set_layout(this.active_layout)
					renderToast(response.title, response.status)
				})
			}
		},
	},

	computed: {
		...mapGetters([
			'getNonce',
			'getTitle',
			'getAjaxUrl',
			'getPreloaderUrl',
		]),
		generateUrl(){
			return `${this.export_url}&download=${this.active_layout.id}&listing_type=${this.listing_type_id}&stm_nonce=${this.getNonce}`;
		},
	},

	template: `
				<div id="ulisting-builder" class="ulisting-main ulisting-builder preview-item" v-if="getAjaxUrl && contentLoad">
					<template v-if="loader">
						<div class="uListing-page-loader">
							<div class="uListing-preloader-wrapper">
								<div class="uListing-loader"></div>
								<img :src="getPreloaderUrl" alt="Preloader Url">
							</div>
						</div>						
					</template>
					
					<template v-else>						
						<div class="panel-custom">
							<edit-layout
								:card_layout="card_layout"
								@set-config="set_config"
								:is_preview="true"
								@update-card="set_layout"
								:key="'ulisting_preview_card_layout'"
								:show_back="false"
								:active_layout="active_layout"
								:sections="sections"
								:config="config"
								@save-layout="update"
								:elements="elements"
								:donor="donor">
							</edit-layout>
						</div>
					</template>

					<template>
						<div style="margin: 0 35px 35px;">							
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
					</template>
				</div>
	`
}