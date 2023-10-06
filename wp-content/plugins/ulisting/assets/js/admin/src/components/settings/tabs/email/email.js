import section 	 	  from '../sections'
import postRequest    from '@plugins/postRequest'
import proWrapper 	  from '@partials/blocks/pro-wrapper'

import { mapGetters, mapActions } from 'vuex'
import { toNumber, renderToast }  from "@plugins/index";

export default {
	components: {
		'section-row'   : section,
		'pro-wrapper'   : proWrapper,
	},

	data() {
		return {
			count: 0,
			email: null,
			showModal: false,
			hasAccess: true,
			models: [],
			settings: {
				logo: '',
				banner: '',
				facebook: {
					label: 'Facebook',
					link: '',
				},

				twitter: {
					label: 'Twitter',
					link: '',
				},

				instagram: {
					label: 'Instagram',
					link: '',
				},

				youtube: {
					label: 'Youtube',
					link: '',
				},
			}
		}
	},

	methods: {
		...mapActions([
			'openModal',
		]),

		change(data) {
			if ( data.image ) {
				this.updateImagePlaceholder(data)
				return
			}

			const {index, value} = data
			if ( this.settings && typeof this.settings[index] !== "undefined" ) {
				this.settings[index].link = value
			}
		},

		updateImagePlaceholder(data) {
			this.settings[data.index] = data.id
		},

		saveSettings() {
			const { instagram, facebook, youtube, twitter, banner, logo } = this.settings
			const data = {
				banner, logo,
				socials: {
					instagram, facebook, youtube, twitter
				}
			}

			postRequest(this.getAjaxUrl, { action: 'stm_update_email_data', data, nonce: this.getNonce}, res => {
				if (res.success)
					renderToast(res.message, res.status)
			})
		},

		open(model) {
			this.openModal({current: this, component: 'email-manager', model: model, size: 'modal-lg email-modal', text: this.text_domains})
		},

		update(field) {
			this.getModels = field
		},

		parseVal(val) {
			return toNumber(val)
		},
	},

	computed: {
		...mapGetters([
			'getEmailData',
			'openContent',
			'getButtonsText',
			'getAjaxUrl',
			'getGlobalTexts',
			'getModalStatus',
			'getNonce',
		]),

		getData() {
			return this.getEmailData ? this.getEmailData.rows : {}
		},

		getWishlistStatus() {
			return toNumber(this.getEmailData.wishlist_active) !== 1
		},

		getModels: {
			get() {
				return this.getEmailData ? this.getEmailData.models : {}
			},

			set(field) {
				const models = this.getEmailData.models
				Object.keys(models).forEach( key => {
					if ( key === field.slug )
						this.getEmailData.models[key] = field
				})
			}
		},

		text_domains() {
			return this.getGlobalTexts.email || {}
		},
	},

	template: `
		<div class="uListing-content emails-tab" v-if="openContent"  :class="{'modal-open': getModalStatus}">
			<div class="uListing-row social-login" v-for="(main, index) in getData" :key="index" :class="{'first-widget': index === 0, [main.key]: true}">
				<section-row v-for="(row, row_index) in main.rows" :main="main" :row="row" :row_index="row_index" :key="row_index" @change="change"></section-row>			
				<div class="uListing-row-inner"  style="display: flex; margin: 30px 50px 50px;">
					<div class="ur-content" style="display: block !important;">
						<button class="uListing-button uListing-button-text uListing-normalize icon" @click.prevent="saveSettings"> {{ getButtonsText.save_email }} </button>
					</div>
				</div>
			</div>
			<div class="uListing-row email-templates">
				<div class="uListing-row-inner">
					<div class="ur-content" style="flex-direction: column; align-items: baseline">
						<pro-wrapper v-if="getWishlistStatus" text="Wishlist Add-on."></pro-wrapper>
						<div class="container" style="max-width: 100%; margin-top: 50px;">
							<div class="row">
								<div class="col-3" v-for="model in getModels" :key="model.slug" :class="{lock: model.slug === 'saved-search' && getWishlistStatus}">
									<div class="uListing-email-item">
										<div class="uListing-email-content">
											<h3 class="uListing-header-3">
												{{ model.title }}
												<span v-if="model.slug === 'saved-search' && getWishlistStatus" class="uListing-info-status">Inactive</span>
												<span v-else class="uListing-info-status" :class="{'active': parseVal(model.is_active) == '1'}">{{ parseVal(model.is_active) == '1' ? 'Active' : 'Inactive' }}</span>
											</h3>
											<p class="uListing-note-text">{{ model.description }}</p>
										</div>
										<div class="uListing-email-footer">
											<button class="uListing-button uListing-button-text uListing-normalize blue" @click.prevent="open(model)">
												<i class="icon-adjust"></i>
												{{ text_domains.manage }}
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	`
}
