export default {
	props: ['attribute', 'class_name'],

	data() {
		return {
			checkbox_list: [],
			value_store: [],
			values: {},
			id: '',
		}
	},

	created() {
		if ( this.attribute ) {
			const { checkbox_list } = this.attribute
			this.checkbox_list	= checkbox_list || []
			this.value_store	= this.checkbox_list
				.filter(checkbox => checkbox.checked)
				.map(checkbox => checkbox.value) || []
			this.changed()

			this.id = `checkbox_${this.generateRandomId()}_`
		}
	},

	methods: {

		generateRandomId() {
			return parseFloat(Math.round( Math.random() * 100) / 100 ).toFixed(4) * 1000+'_'+Date.now();
		},

		changed() {
			this.update_value()
			this.$emit('update', this.attribute?.name, this.values)
		},

		update_value() {
			this.values = {}
			this.checkbox_list
				.filter(checkbox => this.value_store?.includes(checkbox.value))
				.forEach(checkbox => {
					let key 	= 'new'
					let value 	= checkbox.value;

					if ( !checkbox.key ) {
						if ( typeof this.values[key] === "undefined" )
							this.values[key] = [value]
						else {
							this.values[key].push(value)
						}
					} else {
						this.values[checkbox.key] = value
					}

				})
		}
	},

	template: `
		<div class="row" :class="class_name">
			<div class="col-12">
				<h1 class="uListing-header-1">{{ attribute && attribute.title }}</h1>
			</div>
			<div class="col-12">
				<div class="uListing-checkbox-wrapper">
					<div class="uListing-checkbox-field" v-for="(checkbox, key) in checkbox_list" :key="key">
						<input type="checkbox" :id="id + key" :value="checkbox.value" @change="changed(checkbox.value)" v-model="value_store">
						<label :for="id + key">
							<span class="uListing-checkbox-text uListing-normalize">
								{{ checkbox.title }}
							</span>
						</label>
					</div>
				</div>
			</div>
		</div>
	`
}