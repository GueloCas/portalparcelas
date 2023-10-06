import section 	 from '../sections'
import Vue from 'vue'
import { mapGetters, mapMutations, mapActions } from 'vuex'

export default {
	components: {
		'section-row'    : section,
	},

	data() {
		return {
			settings: {
				currency: {
					currency			: '',
					position			: '',
					characters_after	: '',
					decimal_separator	: '',
					thousands_separator : '',
				},
				map: {
					api_key		 : '',
					map_type	 : '',
					hover_option : '',
				},
				pricing_plans: {
					back_slots		: '',
					delete_listings : '',
				},
				short_codes: {
					categories		  : '',
					featured_listings : '',
				},
				extra: {
					remove_db : '',
				},
				default_placeholder: '',
			}

		}
	},

	methods: {
		slotControl() {
			const vm = this
			Vue.nextTick(function () {
				const $wrapper 			= document.querySelector('.uListing-row.pricing_plans')
				const $deleteListing 	= $wrapper.querySelector('input')
				const $backSlotRow 		= $wrapper.querySelectorAll('.row')
				const [, $row] 			= $backSlotRow

				if ( vm.settings?.pricing_plans && !$deleteListing.checked && $row ) {
					vm.settings.pricing_plans.back_slots = false
					vm.mainData = vm.settings
					const $checkbox = $row.querySelector('input')
					$row.classList.add('disabled')

					if ( $checkbox )
						$checkbox.checked = false
				} else if ( $deleteListing.checked && $row ) {
					$row.classList.remove('disabled')
				}
			})
		},

		change(data) {
			if (data.image) {
				this.updateImagePlaceholder(data)
				return
			}

			const { type, index, value } = data
			if ( this.settings && typeof this.settings[type] !== "undefined" ) {
				this.settings[type][index] = value
				this.mainData = this.settings
			}

			if ( index === 'delete_listings' )
				this.slotControl()
		},

		updateImagePlaceholder(data) {
			this.imagePlaceholderField = data
		},

		...mapMutations([
			'updateMain'
		]),

		...mapActions([
			'saveSettings',
		])
	},

	computed: {
		...mapGetters([
			'getMainData',
			'openContent',
		]),

		mainData: {
			get() {
				return this.getMainData
			},

			set(settings) {
				this.updateMain(settings)
			}
		},


		imagePlaceholderField: {
			get() {
				return this.mainData.default_image
			},

			set(data) {
				this.settings.default_placeholder = data.id
				this.mainData = this.settings
			}
		},

		imagePlaceholder() {
			let result = {}
			if ( this.mainData.default_image )
				result = this.mainData.default_image.placeholder
			return result
		},
 	},

	template: `
		<div class="uListing-content uListing-main" v-if="openContent">
			<div class="uListing-row" v-for="(main, index) in mainData" :key="index" :class="{'first-widget': index === 0, [main.key]: true}">
				<section-row v-for="(row, row_index) in main.rows" :main="main" :row="row" :row_index="row_index" :key="row_index" @change="change"></section-row>			
			</div>
		</div>
	`
}