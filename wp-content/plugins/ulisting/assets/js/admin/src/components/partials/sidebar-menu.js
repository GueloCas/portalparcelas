import { mapGetters, mapMutations } from 'vuex'
import { setCookie } from "@plugins/index";
import search from '@partials/blocks/search'

export default {
	data() {
		return {

		}
	},

	components: {
		'search-field': search,
	},

	computed: {
		...mapGetters([
			'getSidebarPages',
			'searchText',
			'getActiveTab',
			'openContent'
		])
	},

	methods: {
		...mapMutations([
			'setActiveTab'
		]),

		setTab(tab) {
			setCookie('uListing_settings_active_tab', tab)
			this.setActiveTab(tab)
		}
	},

	template: `
			<div class="uListing-sidebar">
				<div class="uListing-sticky-sidebar">
					<search-field v-if="openContent" :field="searchText"></search-field>
					<div class="us-menu">
						<ul>
							<li class="uListing-sidebar-menu-text" :class="{active: page.component === getActiveTab}" v-for="page in getSidebarPages" :key="page.icon" @click="setTab(page.component)">
								<i :class="page.icon"></i>
								{{ page.title }}
							</li>
						</ul>
					</div>
				</div>
			</div>
	`
}