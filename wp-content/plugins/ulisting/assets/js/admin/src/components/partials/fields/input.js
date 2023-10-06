export default {
	props: ['field', 'type', 'index'],
	data() {
		return {
			input: ''
		}
	},

	mounted() {
		this.input = this.field.value || this.field.default
		this.inputHandler()
	},

	methods: {
		inputHandler() {
			if ( this.index ) {
				this.$emit('update', {type: this.type, index: this.index, value: this.input})
			} else {
				this.$emit('update', {type: this.type, value: this.input, index: 'no-data'})
			}
		}
	},

	template: `
		<div class="uListing-input-field">
			<span class="uListing-admin-field-title">{{ field.title }}</span>
			<input :type="field.type" class="uListing-input uListing-input-text uListing-normalize input-field" v-model="input" @input="inputHandler"  :placeholder="field.placeholder">
		</div>
	`
}