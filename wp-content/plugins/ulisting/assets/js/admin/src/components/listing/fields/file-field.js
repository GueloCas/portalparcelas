export default {
	props: ['attribute', 'class_name'],

	data() {
		return {
			fileValue: '',
			post_file: null,
			fileName : '',
		}
	},

	created() {
		if ( typeof this.attribute ) {
			this.post_file	= this.attribute.post_file
			this.fileValue 	= this.attribute.value
			this.fileName	= this.post_file ? this.post_file.guid : ''
			this.update()
		}
	},

	methods: {
		update() {
			this.$emit('update', this.attribute?.name, {[this.attribute?.attr_key]: this.fileValue})
		},

		uploadFile() {
			if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
				wp.media.editor.open();
				wp.media.editor.send.attachment = (props, attachment) => {
					this.fileValue 	= attachment.id
					this.fileName	= attachment.url
					this.update()
				};
			}
			return false;
		}
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
				<span style="display: block" class="uListing-default-text uListing-normalize">{{ fileName }}</span>
				<button @click.prevent="uploadFile" class="actions uListing-button uListing-button-text uListing-normalize green">
					<i class="icon-cloud-computing-1"></i>	
					Upload
				</button>
			</div>
		</div>
	`
}