import thumbNail from '@partials/fields/thumbnail'
import stmIcon   from '@partials/fields/stm-icon-picker'
import { mapActions, mapGetters } from "vuex";

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
		...mapActions([
			'openIconPicker',
			'closeIconPicker',
		]),

		update(val) {
			this.icon = val
			this.$emit('update-icon', val)
		},

		updateImage(image) {
			this.image = image?.id
			const url  = image?.url
			this.$emit('update-image', this.image, url)
		},

		openIconModel() {
			this.openIconPicker({current: this, model: this.icon})
		},

		removeIcon() {
			this.icon = null
			this.$emit('remove-icon')
		},

		removeImage() {
			this.image = null
			this.$emit('remove-image')
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
							<div class="icon-side" v-if="icon">
								<div @click="removeIcon"  :class="{'uListing-remove-icon': icon }"></div>
								<i v-bind:class="icon"></i>
							</div>
							<button style="padding: 10px 20px" @click.prevent="openIconModel" class="uListing-button uListing-button-text uListing-normalize generate-pages secondary">Select Icon</button>
						</template>
						<template v-else>
							<image-picker
								@update="updateImage"
								@remove="removeIcon"
   							    :field="imagePicker"
							>
							</image-picker>
						</template>
					</div>
				</div>
	`
}