export default {
	props: ['field', 'type', 'index', 'name'],

	data() {
		return {
			attachment_id: '',
			thumbnail_url: ''
		}
	},

	created() {
		if ( this.field && typeof this.field.guid !== "undefined" )
			this.thumbnail_url = this.field.guid

		if ( this.field && typeof this.field.value !== "undefined" )
			this.attachment_id = this.field.value

		if ( this.field && typeof this.field.id !== "undefined" )
			this.attachment_id = this.field.id

		this.update()
	},

	computed: {
		getClass() {
			return  this.thumbnail_url ? 'has-image' : 'no-image'
		}
	},

	methods: {
		openMedia() {
			const vm = this
			if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
				wp.media.editor.open()
				wp.media.editor.send.attachment = (props, attachment) => {
					this.thumbnail_url = attachment.url
					this.attachment_id = attachment.id
					this.update()
				};
			}
		},

		clear() {
			const vm = this
			vm.thumbnail_url = ''
			this.$emit('update', { type: vm.type, id: null, url: null, image: true })
			this.$emit('remove')
		},

		update() {
			this.$emit('update', { type: this.type, id: this.attachment_id, url: this.thumbnail_url, image: true, index: this.index })
		},
	},

	template: `
        <div class="thumbnail-container">
        	<span class="uListing-admin-field-title" style="padding-bottom: 5px; display: inline-block;" v-if="field.title">{{ field.title }}</span>
			<div class="uListing-thumbnail-field-panel" :class="getClass">
				<div v-if="!thumbnail_url" class="uListing-thumbnail-field-media" :class="getClass">
					 <p class="uListing-normalize uListing-image-placeholder-text uListing-normalize">{{ field.not_selected }}</p>
					 <button @click.prevent="openMedia" class="uListing-button uListing-button-text  icon" >{{field.btn}}</button>
                </div>
                <div v-if="thumbnail_url" class="uListing-thumbnail-field-media" :class="getClass">
					<img v-bind:src="thumbnail_url">
				</div>
			</div>
			<div class="uListing-thumbnail-bottom" v-if="thumbnail_url">
			
				<button @click.prevent="openMedia" class="uListing-button uListing-button-text uListing-normalize blue small">{{ field.replace_btn }}</button>
				<button @click.prevent="clear" class="uListing-button uListing-button-text uListing-normalize trash small">{{ field.delete_btn }}</button>
			</div>
			<input  v-if="name" type="hidden" :name="name" v-model="attachment_id">
		</div>
    `,
}