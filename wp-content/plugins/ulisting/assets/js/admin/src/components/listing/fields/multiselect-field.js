export default {
	props: ['field'],

	data() {
		return {
			items: [],
			select_value: [],
		}
	},

	created() {
		if ( this.field ) {
			this.select_value 	= Object.values(this.field.value)
			this.items			= this.field.items
			this.update()
		}
	},

	methods: {
		update() {
			const result		= {new: []}
			const result_store 	= this.clone_data(this.field.value)
			let select_value 	= this.clone_data(this.select_value)

			if ( select_value ) {
				for ( let key in result_store ) {
					if ( select_value.indexOf(result_store[key]) !== -1 ) {
						result[key] = result_store[key]
						select_value = select_value.filter(e => e !== result_store[key])
					}
				}
				select_value?.forEach(e => result.new.push(e))
			}
			this.$emit('update', this.field?.name, result)
		},

		clone_data(value) {
			return JSON.parse(JSON.stringify(value))
		}
	},

	computed: {
		options() {
			if ( this.items && !Array.isArray(this.items) )
				return Object.keys(this.items).map(key => ({text: this.items[key], id: key}))

			return []
		}
	},

	watch: {
		select_value() {
			this.update()
		}
	},

	template: `
		<div class="col-4" style="margin-bottom: 20px">
			<span class="uListing-admin-field-title">{{ field && field.title }}</span>
			<ulisting-select2 :options="options" multiple="multiple" v-model="select_value">
				<option disabled value="">Not Selected</option>
			</ulisting-select2>
		</div>
	`
}