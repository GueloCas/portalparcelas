import section 	 from '../sections'
import { mapGetters, mapMutations, mapActions } from 'vuex'

export default {
	components: {
		'section-row'    : section,
	},

	data() {
		return {
			count: 0,
			settings: {
				account_page: {
					account: '',
				},
				account_endpoint: {
					'edit-profile': '',
					'my-plans': '',
					'payment-history': '',
					'my-listing': '',
					'saved-searches': '',
					'my-card': '',
					'edit-account': '',
				},
				add_listing: {
					listing: '',
				},
				pricing_plan: {
					pricing: '',
				},
				listing_type_page: {},
				wishlist: {
					wishlist_page: ''
				},
				compare: {
					compare_page: ''
				},
			}
		}
	},

	methods: {
		change({type, index, value}) {
			if (this.settings && typeof this.settings[type] !== "undefined") {
				this.settings[type][index] = value
				this.updatePages(this.settings)
			}
		},

		...mapMutations([
			'updatePages'
		]),

		...mapActions([
			'saveSettings',
			'generatePages'
		])
	},

	computed: {
		...mapGetters([
			'getPagesData',
			'openContent',
			'getButtonsText',
			'getAjaxUrl',
			'getNonce',
			'getActiveTab'
		]),

		getData() {
			this.count++;
			return this.getPagesData
		}
	},

	template: `
		<div class="uListing-content" v-if="this.openContent">
			<div class="uListing-row">
				<div class="uListing-row-inner">
					<div class="ur-header">
						<h1 class="uListing-header-1 uListing-normalize">Page Generator</h1>
					</div>
					<div class="container" style="max-width: 100%">
						<div class="row">
							<div class="col-lg-6">
								<button  @click.prevent="generatePages({url: getAjaxUrl, nonce: getNonce})" class="uListing-button uListing-button-text uListing-normalize generate-pages secondary"> {{ getButtonsText.generate_page }} </button>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="uListing-row" v-for="(main, index) in getData" :key="index">
				<section-row v-for="(row, row_index) in main.rows" type="page" :main="main" :row="row" :row_index="row_index" :key="row_index + count" @change="change"></section-row>			
			</div>
		</div>
	`
}