export default {
	props: ['attribute', 'class_name'],

	data() {
		return {
			selector: '',
			timeValue: '',
		}
	},

	created() {
		if ( this.attribute ) {
			this.timeValue = this.attribute?.value
			this.selector  = `uListing_time_${this.attribute.id}`
			setTimeout(() => {
				jQuery(`#${this.selector}`).val(this.timeValue)
				jQuery(`#${this.selector}`)?.clockTimePicker()
			})
			this.update()
		}
	},

	methods: {
		update() {
			this.$emit('update', this.attribute?.name, {[this.attribute?.attr_key]: this.timeValue})
		},

		changed(event) {
			this.timeValue = event?.target.value || ''
			this.update()
		}
	},

	template: `
		<div class="row" :class="class_name">
			<div class="col-12">
				<h1 class="uListing-header-1">
					{{ attribute && attribute.title }}
				</h1>
			</div>
			<div class="col-12">
				<div class="uListing-input-field">
					<input type="text" @change="changed" :id="selector" placeholder="Select Time" class="uListing-input uListing-input-text uListing-normalize input-field medium">
				</div>
			</div>
		</div>	
	`
}