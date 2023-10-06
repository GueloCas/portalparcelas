import draggable from 'vuedraggable'
import {mapActions, mapGetters, mapMutations} from 'vuex'

import formContent from './search-form-content'

export default {
	props: ['content'],

	components: {
		'draggable': draggable,
		'search-form-content': formContent,
	},

	data() {
		return {
			tabs: [],
			active_tab: 'search-advanced-form',
			child_content: {},
		}
	},

	created() {
		if ( typeof this.content !== "undefined" ) {
			this.tabs = this.content?.tabs || []

			this.child_content = JSON.parse(JSON.stringify(this.content))
			delete this.child_content.tabs

			this.open_all_tabs()
		}
	},

	computed: {
		...mapGetters([

		])
	},

	methods: {
		set_tab(tab) {
			this.active_tab = tab
		},

		open_all_tabs() {
			Object.values(this.tabs).forEach(tab => {
				const { search_data, visibility, key, field_data }  = tab

				if ( typeof search_data !== "undefined" ) {
					this.setAutoUsed(search_data.used || [])
					this.setAutoAvailable(search_data.available || [])
				}

				if ( typeof field_data !== "undefined" )
					this.setSearchFormData({type: key, data: field_data})

				if ( typeof visibility !== "undefined" )
					this.setVisibility(visibility)

				this.searchBuildFields({type: key})
			})
		},

		...mapActions([
			'searchBuildFields',
		]),

		...mapMutations([
			'setSearchFormData',
			'setAutoUsed',
			'setAutoAvailable',
			'setVisibility'
		]),
	},

	template: `
			<div class="custom-field search-form">
				<div class="search-form-tab">
					<div v-for="(tab, index) in tabs" class="search-item" @click.prevent="set_tab(index)" :class="{active: index === active_tab}">
						<div class="search-content">
							<img :src="tab.img">
						</div>
						<p class="uListing-normalize uListing-admin-field-title">{{ tab.title }}</p>
					</div>
				</div>
				<keep-alive>
					<search-form-content v-for="(tab, index) in tabs" :key="index" v-if="active_tab === index" :content="child_content" :tab="tabs[active_tab]"></search-form-content>
				</keep-alive>
			</div>
	`
}