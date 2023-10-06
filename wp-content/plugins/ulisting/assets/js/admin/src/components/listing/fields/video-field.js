export default {
	props: ['attribute', 'class_name'],

	data() {
		return {
			url_value: '',
		}
	},

	created() {
		if ( this.attribute )	{
			this.url_value = this.attribute?.value
			this.update()
		}
	},

	methods: {
		update() {
			this.$emit('update', this.attribute?.name, {[this.attribute?.attr_key]: this.url_value})
		}
	},

	template: `
		<div class="row" :class="class_name">
			<div class="col-12">
				<h1 class="uListing-header-1">{{ attribute && attribute.title }}</h1>
			</div>
			<div class="col-12">
				<div class="uListing-input-field">
					<input v-model="url_value" @input="update" type="text" placeholder="Enter Url" class="uListing-input uListing-input-text uListing-normalize input-field medium">
				</div>
			</div>
		</div>
	`
}