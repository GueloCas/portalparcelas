export default {
	props: ['attribute', 'class_name'],

	data() {
		return {
			radio_list: [],
			radioValue: '',
			radioName: '',
		}
	},

	created() {
		if ( this.attribute ) {
			const { items } = this.attribute
			this.radio_list	= items || []
			this.radioValue = this.attribute.value || ''
			this.changed()
		}
		this.radioName = `uListing_radio_${this.generateRandomId}`
	},

	computed: {
		generateRandomId() {
			return parseFloat(Math.round( Math.random() * 100) / 100 ).toFixed(4) * 1000+'_'+Date.now();
		},
	},

	methods: {
		changed() {
			this.$emit('update', this.attribute?.name, {[this.attribute?.attr_key]: this.radioValue})
		},
	},

	template: `
		<div class="row" :class="class_name">
			<div class="col-12">
				<h1 class="uListing-header-1">{{ attribute && attribute.title }}</h1>
			</div>
			
			<div class="col-12">
				<div class="uListing-radio-wrapper">
					<div class="uListing-radio-field uListing-radio-listing-field" v-for="(title, key) in radio_list" :key="key">
						<input type="radio" @change="changed" :name="radioName" :id="radioName + '_' + key" v-model="radioValue" :value="key">
						<label :for="radioName + '_' + key" class="uListing-normalize uListing-radio-text">{{ title }}</label>
					</div>
				</div>
			</div>
		</div>
	`
}