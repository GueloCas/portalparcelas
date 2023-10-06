import VueEasyTinyMCE  from 'vue-easy-tinymce'
export default {
	props: ['attribute', 'class_name'],

	components: {
		'tinymce': VueEasyTinyMCE,
	},

	data() {
		return {
			content: '',
			tinymcePlugins: [
				'advlist autolink lists link textcolor',
				'searchreplace visualblocks code',
				'insertdatetime media table contextmenu paste code directionality template colorpicker textpattern'
			],
			tinymceToolbar1: 'undo redo | bold italic strikethrough | forecolor backcolor | template link | bullist numlist | ltr rtl | removeformat',
			tinymceToolbar2: '',
			tinymceOtherOptions: {
				height: 300,
			},
		}
	},

	created() {
		if ( this.attribute ) {
			this.content = this.attribute.value || ''
			this.change()
		}
	},

	methods: {
		change() {
			this.$emit('update', this.attribute.name, {[this.attribute?.editor_key]: this.content})
		}
	},

	template: `
		<div class="row" v-if="attribute" :class="class_name">
			<div class="col-12">
				<h1 class="uListing-header-1">{{ attribute && attribute.title }}</h1>
			</div>
			<div class="col-12">
				<tinymce v-model="content"
					  @input="change"	
					  :plugins="tinymcePlugins"
					  :toolbar1="tinymceToolbar1"
					  :toolbar2="tinymceToolbar2"
					  :other="tinymceOtherOptions"
					  >
				</tinymce>
			</div>
		</div>
	`
}