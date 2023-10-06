/**
 * Fields components
 */
import select 	 from '@partials/fields/select'
import input	 from '@partials/fields/input'
import toggle 	 from '@partials/fields/switch'
import text 	 from '@partials/fields/text'
import wishList  from '@partials/fields/wishlist'
import thumbNail from '@partials/fields/thumbnail'
import clickCopy from '../../../partials/blocks/click-copy'

/**
 * Section block components
 */
import userRoles from '@settings-tab/user-roles/user-roles'
import payment   from '@settings-tab/payment/payment-card'
import socials   from '@settings-tab/socials/social-network'

export default {
	props: ['row', 'main', 'row_index', 'type'],
	components: {
		'text-field' 	  : text,
		'input-field' 	  : input,
		'select-field'	  : select,
		'switch-field'	  : toggle,
		'wishlist-field'  : wishList,
		'image-picker'	  : thumbNail,
		'user-roles' 	  : userRoles,
		'payment-card' 	  : payment,
		'social-networks' : socials,
		'click-copy'	  : clickCopy,
	},

	methods: {
		update(data) {
			this.$emit('change', data)
		}
	},

	computed: {
		getEmptyStatus() {
			return typeof this.type !== "undefined" && this.type === 'page'
		}
	},

	template: `
				<div class="uListing-row-inner" v-if="row">
					<div class="ur-header" v-if="row_index === 0 && (main && main.title)">
						<h1 class="uListing-header-1 uListing-normalize">{{ main.title }}</h1>
					</div>
					<div class="container" style="max-width: 100%">
						<div class="row">
							<template  v-for="(field, field_index) in row">
								<div :class="field.classes">
									<component 
										v-if="field"
										:field="field" 
										:is="field.name"
										:name="field.name"
										:type="main.key" 
										:empty="getEmptyStatus"
										:index="field_index" 
										:key="main.key" 
										@update="update">
									</component>
								</div>
								<div class="col" style="margin-bottom: 11px" v-if="main.key === 'short_codes' && field.description">
									<div class="ur-content-item" style="margin-top: 3px; display: flex;align-items: center;" >
										 <span class="info-title">Shortcode :</span>
										 <click-copy :name="field.name" :copy_text="field.description"></click-copy>
									</div>
								</div>
								<div class="col" v-if="(main.key === 'pricing_plans' || main.key === 'extra') && field.description">
									<div class="ur-content-item" style="margin-top: 3px; display: flex;align-items: center;" >
										<p class="uListing-normalize uListing-info m-t-5">{{ field.description }}</p>
									</div>
								</div>
							</template>
						</div>
					</div>	
				</div>
	`
}