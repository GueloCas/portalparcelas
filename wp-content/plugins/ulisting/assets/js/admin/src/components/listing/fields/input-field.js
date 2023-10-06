export default {
	props: ['field'],

	data() {
		return {
			input_value: '',
			type: 'text',
		}
	},

	created() {
		if ( this.field ) {
			this.input_value	= this.field.value
			this.type			= this.field.sub_type
			this.update()
		}
	},

	methods: {
		update() {
			this.$emit('update', this.field?.name, {[this.field?.attr_key]: this.input_value})
		}
	},

	template: `
		<div class="col-4" style="margin-bottom: 20px">
			<div class="uListing-input-field">
				<span class="uListing-admin-field-title">{{ field && field.title }}</span>
				<input @input="update" v-model="input_value" :type="type" :placeholder="'Enter ' + field.title" class="uListing-input uListing-input-text uListing-normalize input-field medium">
			</div>
		</div>
	`
}