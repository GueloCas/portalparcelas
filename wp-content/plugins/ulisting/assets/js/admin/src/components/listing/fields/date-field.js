export default {
	props: ['attribute', 'class_name'],

	components: {
		'date-picker': DatePicker.default,
	},

	data() {
		return {
			date: new Date()
		}
	},

	created() {
		const value = this.attribute?.value
		this.date 	= new Date( moment(value, 'DD-MM-YYYY').format() )
	},
	
	methods: {
		set_expired_date(val){
			this.date = val;
			this.update()
		},

		update() {
			this.$emit('update', this.attribute?.name, {[this.attribute?.attr_key]: this.date})
		},
	},

	template: `
		<div class="row" :class="class_name">
			<div class="col-12">
				<h1 class="uListing-header-1">
					{{ attribute && attribute.title }}
				</h1>
			</div>
			<div class="col-12">
				<date-picker
					confirm
					lang="en"
					v-model="date"
					clearable="false"
					format="DD/MM/YYYY"
					input-class="uListing-date"
					class="uListing-listing-date-picker"
					@confirm="set_expired_date">
				</date-picker>
			</div>
		</div>	
	`
}