import { mapGetters, mapMutations } from "vuex";
import getRequest from "../../plugins/getRequest";
import AddEdit from './pages/edit'

export default {
	data() {
		return {
			user_data: null,
			user_id: null,
		}
	},

	components: {
		'add-edit': AddEdit,
	},

	mounted() {
		if ( settings_data )
			this.user_id = settings_data.user_id

		getRequest(this.getAjaxUrl, { action: 'user_plan_data', id: this.user_id}, res => {
			if ( res.success ) {
				const { data } = res
				this.user_data = data

				this.initGlobalTexts(data.text_domains)
				this.setLoader(false)
			}
		})
	},

	methods: {
		...mapMutations([
			'setLoader',
			'initGlobalTexts',
		]),
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
			'getLoader',
			'getPreloaderUrl',
		])
	},

	template: `
		<div class="uListing-container">
			<div class="uListing-page-loader user-plan" v-if="getLoader">
				<div class="uListing-preloader-wrapper">
					<div class="uListing-loader"></div>
					<img :src="getPreloaderUrl" alt="Preloader Url">
				</div>
			</div>
			<add-edit v-else="getLoader" :user_data="user_data" :user_id="user_id"></add-edit>
		</div>
	`
}