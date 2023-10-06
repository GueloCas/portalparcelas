export default {
	props: ['attribute', 'class_name'],

	data() {
		return {
			textareaValue: '',
		}
	},

	created() {
		if ( typeof this.attribute ) {
			this.textareaValue = this.attribute.value
			this.update()
		}
	},

	methods: {
		update() {
			this.$emit('update', this.attribute?.name, {[this.attribute?.attr_key]: this.textareaValue})
		},
	},

	watch: {
		textareaValue() {
			this.update()
		}
	},

	template: `
		<div class="row" :class="class_name">
			<div class="col-12">
				<h1 class="uListing-header-1">{{ attribute && attribute.title }}</h1>
			</div>
			<div class="col-12">
				<textarea class="uListing-textarea-field" placeholder="Enter Text" v-model="textareaValue"></textarea>
			</div>
		</div>
	`
}