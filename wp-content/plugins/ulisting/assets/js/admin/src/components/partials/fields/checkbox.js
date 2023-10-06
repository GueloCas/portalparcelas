import toggleValidator from '@plugins/toggleValidator'
import { randomId } from "@plugins/index";

export default {
	props: ['field', 'type', 'index'],
	data() {
		return {
			checkbox: '',
			id: '',
		}
	},

	mounted() {
		this.checkbox = this.field.value || this.field.default
		this.checkbox = toggleValidator(this.checkbox)
		this.id		  = `checkbox_${randomId()}`
		this.checkboxHandler()
	},

	methods: {
		checkboxHandler() {
			this.$emit('update', {type: this.type, index: this.index, value: this.checkbox})
		}
	},

	template: `
		<div class="uListing-checkbox-field">
			<input type="checkbox" :id="id" value="1" v-model="checkbox" @change="checkboxHandler">
			<label :for="id">
				<span class="uListing-checkbox-text uListing-normalize">
					{{ field.title }}
				</span>
			</label>
		</div>
	`
}