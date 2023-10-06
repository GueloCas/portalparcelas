export default {
	props: ['attribute', 'class_name'],

	data() {
		return {
			genuine: '',
			sale: '',
			suffix: '',
		}
	},

	created() {
		if ( this.attribute ) {
			const { meta } = this.attribute
			this.genuine 	= meta?.genuine || ''
			this.sale		= meta?.sale
			this.suffix		= meta?.suffix
			this.change()
		}
	},

	methods: {
		change() {
			const meta = { genuine: this.genuine, sale: this.sale, suffix: this.suffix }
			this.$emit('update', this.attribute.name, {meta, value: {[this.attribute?.price_key]: this.genuine}})
		}
	},

	template: `
		<div class="row" :class="class_name">

			<div class="col-12" style="margin-bottom: 10px">
				<h1 class="uListing-header-1">{{ attribute && attribute.title }}</h1>
			</div>
			
			<div class="col-4">
				<div class="uListing-input-field">
					<span class="uListing-admin-field-title">Price</span>
					<input placeholder="Enter Price" v-model="genuine" @input="change" type="number" class="uListing-input uListing-input-text uListing-normalize input-field medium">
				</div>
			</div>
			
			<div class="col-4">
				<div class="uListing-input-field">
					<span class="uListing-admin-field-title">Sale Price</span>
					<input placeholder="Enter Sale Price" v-model="sale" @input="change" type="number" class="uListing-input uListing-input-text uListing-normalize input-field medium">
				</div>
			</div>
			
			<div class="col-4">
				<div class="uListing-input-field">
					<span class="uListing-admin-field-title">Suffix Price</span>
					<input placeholder="Enter Suffix Price" v-model="suffix" @input="change" type="text" class="uListing-input uListing-input-text uListing-normalize input-field medium">
				</div>
			</div>
		</div>
	`
}