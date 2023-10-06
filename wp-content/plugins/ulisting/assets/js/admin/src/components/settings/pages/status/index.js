import { mapGetters } from 'vuex'
import getRequest from '@plugins/getRequest'
import statusDanger   from './danger'
import statusComplete from './complete'

export default {

	data() {
		return {
			count: 0,
			loader: true,
			conflicts: [],
		}
	},

	components: {
		'status-danger'  : statusDanger,
		'status-complete': statusComplete,
	},

	mounted() {
		getRequest(this.getAjaxUrl, {action: 'stm_template_status'}, res => {
			if ( res.success ) {
				this.conflicts 	  = res.conflicts
				this.count 	      = res.count
				this.loader    	  = false
			}
		})
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
			'getGlobalTexts',
		]),

		text_domains() {
			return this.getGlobalTexts.status || {}
		},
	},

	template: `
		<div class="uListing-content-wrapper uListing-status">
			<h1 class="uListing-normalize uListing-header-1">{{ text_domains.title }}</h1>
			<div class="uListing-status-container">
				<status-danger :text_domains="text_domains" :conflicts="conflicts"></status-danger>
  			    <status-complete :text_domains="text_domains" :count="count"></status-complete>
			</div>
		</div>
	`
}