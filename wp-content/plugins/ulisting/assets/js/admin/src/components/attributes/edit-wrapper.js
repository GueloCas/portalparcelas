import { mapGetters, mapMutations } from 'vuex'
import getRequest from '@plugins/getRequest'
import attrEdit   from './edit'

export default {
	components: {
		'attr-edit': attrEdit,
	},

	data() {
		return {
			attr_data: null
		}
	},

	mounted() {
		this.init()
	},

	methods: {
		...mapMutations([
			'setLoader',
			'initGlobalTexts',
			'updateOpenContent'
		]),

		init() {
			const data = {
				action: 'stm_attr_edit',
				attr_id: settings_data?.attr_id
			}

			getRequest(this.getAjaxUrl, data, response => {
				const { data } = response
				this.attr_data = data
				this.initGlobalTexts(data.text_domains)
				this.updateOpenContent(true)
				this.setLoader(false)
			})
		}
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
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
				<attr-edit v-if="openContent" :attr_data="attr_data"></attr-edit>
			</div>
		
	`
}