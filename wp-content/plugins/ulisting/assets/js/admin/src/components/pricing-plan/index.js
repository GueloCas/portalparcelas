import { mapGetters, mapMutations } from "vuex";
import getRequest from "../../plugins/getRequest";
import AddEdit from './pages/edit'

export default {
	data() {
		return {
			loading: false,
			return_url: null,
			pricing_plan_id: null,
			pricing_plan_data: null,
		}
	},

	components: {
		'add-edit': AddEdit,
	},

	mounted() {
		if ( typeof settings_data !== "undefined" )
			this.pricing_plan_id = settings_data.pricing_plan_id

		if ( typeof settings_data !== "undefined" )
			this.return_url = settings_data.return_url

		const pricingUrl = `${this.getApiUrl}pricing-plan/form-data`

		getRequest(pricingUrl, { pricing_plan_id: this.pricing_plan_id }, res => {
			if ( res.success ) {

				const { data } = res
				this.pricing_plan_data = data

				this.initGlobalTexts(data.text_domains)
				this.updateOpenContent(true)
				this.setLoader(false)
			}
		})

		setTimeout(() => this.preventForms())
	},

	methods: {
		...mapMutations([
			'setLoader',
			'initGlobalTexts',
			'updateOpenContent'
		]),

		preventForms() {
			const forms = document.querySelectorAll('form') || []
			Array.from(forms).forEach(form => {
				if ( form.name === 'post' )
					form.addEventListener('submit', e => e.preventDefault())
			})
		},
	},

	computed: {
		...mapGetters([
			'getApiUrl',
			'getLoader',
			'openContent',
			'getPreloaderUrl',
		])
	},

	template: `
		<div class="uListing-container" style="margin: 0">
			<div class="uListing-page-loader pricing-plan" v-if="getLoader">
				<div class="uListing-preloader-wrapper">
					<div class="uListing-loader"></div>
					<img :src="getPreloaderUrl" alt="Preloader Url">
				</div>
			</div>
			<add-edit :plans="pricing_plan_data" :pricing_plan_id="pricing_plan_id" v-if="openContent"></add-edit>
		</div>
	`
}