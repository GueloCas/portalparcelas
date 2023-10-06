Vue.component('stm-thumbnail-field', {
	template: `
	 	<div style="width: 100%;">
			<div class="uListing-thumbnail-field-panel" style="display: flex; justify-content: center" :class="getClass">
				<div v-if="!thumbnail_url" class="uListing-thumbnail-field-media" :class="getClass" :style="getStyles">
					 <p class="uListing-normalize uListing-image-placeholder-text uListing-normalize" style="margin-bottom: 5px">Image not selected</p>
					 <button @click.prevent="openMedia" class="uListing-button uListing-button-text  icon" >Select image</button>
                </div>
                <div v-if="thumbnail_url" class="uListing-thumbnail-field-media" :class="getClass">
					<img v-bind:src="thumbnail_url" style="max-width: 280px; height: auto;">
				</div>
			</div>
			
			<div class="uListing-thumbnail-bottom" style="margin-top: 10px; text-align: center">
				<button @click.prevent="openMedia" class="uListing-button uListing-button-text uListing-normalize blue small">Replace</button>
				<button @click.prevent="clear" class="uListing-button uListing-button-text uListing-normalize trash small">Delete</button>
			</div>
			<input type="hidden" v-bind:name="name" v-model="value">
		</div>
    `,

	data() {
		return {

		};
	},

	methods: {
		openMedia: function() {
			const vm = this;
			if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
				wp.media.editor.open();
				wp.media.editor.send.attachment = function(props, attachment) {

					switch (vm.value_type) {
						case 'id':
							vm.value = attachment.id;
							break;
						case 'url':
							vm.value = attachment.url;
							break;
					}

					vm.thumbnail_url = attachment.url;
					vm.$emit('update', {option_name: vm.image_type, id: vm.value})
				};
			}
		},
		clear: function() {
			this.thumbnail_url = null
			this.value = null;
			this.$emit('update', {option_name: this.image_type, id: this.value})
		}
	},

	computed: {
		getClass() {
			return  this.thumbnail_url ? 'has-image' : 'no-image'
		},

		getStyles() {
			if ( !this.thumbnail_url ) {
				return {
					display: 'flex',
					flexDirection: 'column',
					alignItems: 'center'
				}
			}

			return  {}
		}
	},

	props: {
		name: {default: "thumbnail"},
		value_type: {default: "id"},
		thumbnail_url: { default: null},
		image_type: { default: ""},
		btn_value: { default: "Choose image"},
		value: { default: null}
	}
});
