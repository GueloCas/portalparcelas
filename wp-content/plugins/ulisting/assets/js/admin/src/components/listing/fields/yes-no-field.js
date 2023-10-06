export default {
	props: ['field'],

	data() {
		return {
			items: [],
			select_value: '',
		}
	},

	created() {
		if ( this.field ) {
			this.select_value 	= this.field.value || ''
			this.items			= this.field.items
			this.update()
		}
	},

	methods: {
		update() {
			this.$emit('update', this.field?.name, {[this.field?.attr_key]: this.select_value})
		},
	},

	template: `
		<div class="col-4" style="margin-bottom: 20px">
			<div class="uListing-admin-select">
				<span class="uListing-admin-field-title">{{ field && field.title }}</span>
				<select @change="update" :placeholder="'Select ' + field.title" class="uListing-select-box  uListing-select-box-text uListing-normalize" v-model="select_value">
					<option value="" selected>Not Selected</option>
					<option v-for="(item, key) in items" :key="key" :value="key">{{ item }}</option>
				</select>
			</div>
		</div>
	`
}