import thumbNail from '@partials/fields/thumbnail'
import stmIcon   from '@partials/fields/stm-icon-picker'

export default {
	data() {
		return {
			icon: '',
			icon_type: 0,
		}
	},

	props: ['name', 'is_add', 'icon_picker', 'thumbnail'],

	components: {
		'image-picker'	  : thumbNail,
		'stm-icon-picker' : stmIcon,
	},

	mounted() {
		this.icon = this.icon_picker
		if(this.thumbnail) {
			this.icon_type = 1
		}

		this.reset()
	},

	computed: {
		imagePicker() {

			let data = {
				id: null,
				url: null,
				value: null,

				btn: 'Select Image',
				delete_btn: 'Delete',
				replace_btn: 'Replace',
				not_selected: 'Image not selected',
			}

			if ( this.thumbnail )
				data = { ...data, ...this.thumbnail }
			return data
		},
	},

	methods: {

		reset() {
			const $reset = document.querySelector('.reset-icons')
			const $submit_btn = document.querySelector('#addtag input[type=submit]')
			if ( $reset && $submit_btn ) {
				$submit_btn.addEventListener('click', () => {
					setTimeout(() => {
						this.icon = ''
					}, 1000)
				})
			}
		},

		update(val) {
			this.icon = val
		}
	},

	template: `
				<div class="uListing-visual" :class="{add: is_add}">
					<div class="visual-header">
						<div class="uListing-radio-field">
							<input type="radio" name="icon_type" id="icon_type_icon" v-model="icon_type" value="0">
							<label for="icon_type_icon" class="uListing-normalize uListing-radio-text">Icon</label>
						</div>
						
						<div class="uListing-radio-field">
							<input type="radio" name="icon_type" id="icon_type_image" v-model="icon_type" value="1">
							<label for="icon_type_image" class="uListing-normalize uListing-radio-text">Image</label>
						</div>
					</div>
					<div class="visual-wrapper">
						<template v-if="icon_type == 0">
							<input type="hidden" v-model="icon" :name="name + '[icon]'">
							<stm-icon-picker @icon-event="update" :icon="icon" ></stm-icon-picker>
						</template>
						<template v-else>
							<image-picker
   							    :field="imagePicker"
								:name="name + '[thumbnail_id]'">
							</image-picker>
						</template>
					</div>
				</div>
	`
}