import { mapGetters } from 'vuex'
import section from "../sections";

export default {
	components: {
		'section-row': section,
	},

	data() {
		return {

		}
	},

	computed: {
		...mapGetters([
			'getPaymentsData',
			'openContent',
			'getPaymentsData',
			'getModalStatus',
		]),
	},

	template: `
		<div class="uListing-content uListing-payments" v-if="this.openContent"  :class="{'modal-open': getModalStatus}">
			<div class="uListing-row" v-for="(main, index) in getPaymentsData" :key="index" :class="{'first-widget': index === 0, [main.key]: true}">
				<section-row v-for="(row, row_index) in main.rows" :main="main" :row="row" :row_index="row_index" :key="row_index"></section-row>			
			</div>
		</div>
	`
}