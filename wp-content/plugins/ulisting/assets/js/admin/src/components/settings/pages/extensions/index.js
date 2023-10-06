import getRequest from '@plugins/getRequest'
import extensionCard from './extension-card'
import { mapGetters } from 'vuex'

export default {

	data() {
		return {
			loader: true,
			extensions: [],
			plugins: {},
			plugin_url: '',
		}
	},

	components: {
		'extension-card': extensionCard,
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
			'getGlobalTexts',
		]),

		text_domains() {
			return this.getGlobalTexts.extensions || {}
		}
	},

	async mounted() {
		const url = 'https://stylemixthemes.com/api/extensions/ulisting.json'
		await getRequest(url, {}, res => {
			if ( res && res.extensions ) {
				this.extensions   = res.extensions
			}

			getRequest(this.getAjaxUrl, {action: 'stm_extensions'}, response => {
				if ( response.success ) {
					this.plugins      = response.plugins
					this.plugin_url   = response.plugin_url
				}
				this.loader 	= false
			})
		})
	},

	template: `
			<div class="uListing-content-wrapper uListing-extensions">
				<h1 class="uListing-normalize uListing-header-1">uListing {{ text_domains && text_domains.title }}</h1>
				<div class="uListing-extensions-container">
					<extension-card 
						v-for="(extension, index) in extensions" 
						:key="index"
						:plugins="plugins" 
						:extension="extension"
						:plugin_url="plugin_url"
						:text_domains="text_domains">
					</extension-card>
				</div>
			</div>
	`
}