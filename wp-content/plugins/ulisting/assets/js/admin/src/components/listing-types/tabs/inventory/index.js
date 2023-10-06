import postRequest     from '@plugins/postRequest'
import { renderToast } from "@plugins/index"
import { mapGetters  } from "vuex"

export default {
	props: ['content'],

	data() {
		return {
			default_icon: '',
			active_layout: '',
			layouts: [],
			title_hover: null,
			create_link: null,
			edit_link: null,
			listing_type_id: null,
		}
	},

	created() {
		this.listing_type_id = settings_data?.type_id

		if ( this.content ) {
			this.default_icon  = this.content?.default_icon || []
			this.layouts 	   = this.content?.layouts || []
			this.create_link   = this.content?.create_link || ''
			this.edit_link 	   = this.content?.edit_link || ''
			this.active_layout = this.content?.active || ''
		}
	},

	methods: {
		edit( id ) {
			if ( this.create_link && id )
				window.open(`${this.create_link}&layout=${id}&listing_type_id=${this.listing_type_id}`,'_blank');
		},

		redirect_to_inventory() {
			if ( this.create_link )
				window.open(this.create_link,'_blank');
		},

		activate(id) {
			this.active_layout = id
			const data = {
				action: 'uListing_active_inventory_template',
				nonce: this.getNonce,
				layout_id: id,
				listing_type_id: this.listing_type_id,
			}

			postRequest(this.getAjaxUrl, data, response => renderToast(response.message, response.status))
		},

		remove(id) {
			const url 	 = this.getAjaxUrl
			const action = 'uListing_delete_inventory'
			if ( confirm( 'Are you sure to delete this layout?' ) )
				postRequest(url, {action, id, nonce: this.getNonce}, response => {
					renderToast(response.message, response.status)
					if ( response.success ) {
						this.layouts = this.layouts.filter(l => l.id !== id)
					}
				})
		}
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
			'getNonce',
		]),
	},

	template: `
		<div class="custom-fields layout-list">
			<div class="layout-container">
				<div class="layout-card-wrapper" v-for="layout in layouts" :key="layout.id" :class="{'selected-layout': active_layout === layout.id}">
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
						<span class="uListing-admin-field-title" @mouseover="title_hover = layout.id" @mouseleave="title_hover = null">{{ layout.name }}</span>
					</div>
				</div>
			</div>
			<div style="margin: 40px 0; padding: 0 35px">
				<button @click.prevent="redirect_to_inventory" class="actions uListing-button uListing-button-text uListing-normalize blue">
					<i class="icon-992651"></i>
					Create new
				</button>
				<button @click.prevent="redirect_to_inventory" class="actions uListing-button uListing-button-text uListing-normalize green">
					<i class="icon-cloud-computing-1"></i>	
					Import
				</button>
			</div>
		</div>
	 `
}