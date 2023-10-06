import toggleValidator from '@plugins/toggleValidator'
import { randomId } from "@plugins/index";

export default {
	props: ['field', 'type', 'index'],
	data() {
		return {
			radio: '',
			id: '',
		}
	},

	mounted() {
		this.radio = this.field.value || this.field.default
		this.radio = toggleValidator(this.radio)
		this.id	   = `checkbox_${randomId()}`
		this.radioHandler()
	},

	methods: {
		radioHandler() {
			this.$emit('update', {type: this.type, index: this.index, value: this.checkbox})
		}
	},

	template: `
		<div class="uListing-radio-field">
			<input type="radio" :name="id" :id="id" @change="radioHandler" v-model="radio">
			<label :for="id" class="uListing-normalize uListing-radio-text"> {{ field.title }} </label>
		</div>
	`
}