import { mapGetters, mapMutations, mapActions } from "vuex";
import { required, minLength } from 'vuelidate/lib/validators'
import postRequest     	from '@plugins/postRequest'
import { renderToast } 	from "@plugins/index"
import iconPicker		from './icon-picker/index'

export default {
	data() {
		return {
			name  : '',
			title : '',
			type  : '',
			icon  : '',
			image : '',
			image_url: null,
			options: [],
			key_count: 0,
		}
	},

	components: {
		'icon-picker': iconPicker,
	},

	validations: {
		title: {
			required: required,
			minLength: minLength(3)
		},
		name: {
			required: required,
			minLength: minLength(3)
		},
		type: {
			required: required
		},
	},

	computed: {
		getThumbnail() {
			return {
				id:  this.image,
				url: this.image_url
			}
		},
		getOptions() {
			const { title, name, type, icon, image, image_url } = this.getFormData

			this.title 		= title
			this.name 		= name
			this.type 		= type
			this.icon 		= icon
			this.image 		= image
			this.image_url 	= image_url

			this.key_count++
			return 	this.getAttrListOptions
		},

		...mapGetters([
			'getAttrListOptions',
			'getFormData',
			'getNonce',
			'getAjaxUrl',
			'getBtnType',
		]),
	},

	methods: {
		...mapMutations([
			'setFormData',
			'setBtnType',
		]),

		...mapActions([
			'newField',
			'updateField',
		]),

		removeIcon() {
			this.icon = ''
		},

		updateIcon(icon) {
			this.icon = icon
		},

		updateImage(image, image_url) {
			this.image 		= image
			this.image_url 	= image_url
		},

		removeImage() {
			this.image 		= null
			this.image_url 	= null
		},

		addNewField() {
			if ( !this.submit() )
				return false

			const data = {
				name:    this.name,
				type:    this.type,
				title:   this.title,
				icon: 	 this.icon,
				wpnonce: this.getNonce,
				thumbnail_id: this.image,
				action:  'stm_attribute_listing_type_create',
			}

			postRequest( this.getAjaxUrl, data, response => {
				if ( response.success ) {
					const field = {
						id			: response.id,
						title		: response.title,
						name		: response.name,
						type		: this.type,
						icon		: response.icon,
						image_url 	: this.image_url
					}

					this.newField(field)
					this.reset();
				}

				renderToast(response.message, response.status)
			})
		},

		editField() {
			if ( !this.submit() )
				return false

			const data = {
				name:    this.name,
				type:    this.type,
				title:   this.title,
				icon:    this.icon,
				image:   this.image,
				wpnonce: this.getNonce,
				id:		 this.getFormData?.id,
				action:  'stm_attribute_listing_type_update',
			}

			postRequest( this.getAjaxUrl, data, response => {
				if ( response.success ) {

					const field = {
						id			: this.getFormData?.id,
						title		: response.title,
						name		: response.name,
						icon		: response.icon,
						type		: this.type,
						image		: response.image,
						image_url	: this.image_url
					}

					this.updateField(field)
					this.setBtnType('add')
					this.reset()
				}

				renderToast(response.message, response.status)
			})
		},

		setName() {
			this.name = this.title
			this.validateName()
		},

		validateName() {
			let name = '';
			for (let i = 0; i < this.name.length; i++) {
				if(
					(this.name.charCodeAt(i) >= 48  && this.name.charCodeAt(i) <= 57 ) ||
					(this.name.charCodeAt(i) >= 97  && this.name.charCodeAt(i) <= 122 ) ||
					(this.name.charCodeAt(i) >= 65  && this.name.charCodeAt(i) <= 90 ) ||
					this.name.charCodeAt(i) === 32 ||
					this.name.charCodeAt(i) === 45 ||
					this.name.charCodeAt(i) === 95
				) {
					name += this.name.charAt(i)
					name = name.replace(/[- ]/g,'_');
				}
			}
			this.name = name.toLowerCase();
		},

		cancelUpdate() {
			this.setBtnType('add')
			this.reset()
		},

		reset() {
			this.title = ''
			this.name  = ''
			this.type  = ''
			this.icon = ''
			this.image = ''
			this.image_url = null
			this.setFormData({ title: '', name: '', type: '', image: '', icon: '', image_url: null })
			this.$v.$reset()
			this.key_count++
		},

		submit() {
			this.$v.$touch()
			return !this.$v.$invalid
		},
	},

	template: `
		<div class="add-new-container">
			<div class="add-new-container-sticky">
				<h3 class="uListing-normalize uListing-header-3">{{ getBtnType === 'add' ? 'New Custom Field' : 'Edit Custom Field' }}</h3>
	
				<div class="uListing-input-field" :class="{ 'has-error': $v.title.$error }">
					<input type="text" class="uListing-input uListing-input-text uListing-normalize input-field" @change="setName" @keyup="setName" v-model.trim="title" placeholder="Title">
				</div>
				
				<div class="uListing-input-field" :class="{ 'has-error': $v.name.$error }">
					<input type="text" class="uListing-input uListing-input-text uListing-normalize input-field" v-model.trim="name" placeholder="Slug" @change="validateName" @keyup="validateName">
				</div>
				
				<div class="uListing-admin-select" :class="{ 'has-error': $v.type.$error, disabled: getBtnType === 'edit' }">
					<select class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="type">
						<option value="" selected>Type</option>
						<option v-for="(text, key) in getOptions" :key="key" :value="key">{{ text }}</option>
					</select>
				</div>

				<icon-picker
					:is_add="true"
					:key="key_count"
					:thumbnail="getThumbnail"
					:icon_picker="icon"
					@update-image="updateImage"
					@remove-image="removeImage"
					@remove-icon="removeIcon"
					@update-icon="updateIcon"
				 />
				 <div class="form-actions-container">
					<button class="uListing-button uListing-button-text icon green with-icon" @click.prevent="addNewField" v-if="getBtnType === 'add'">Add Field</button>
					<button class="uListing-button uListing-button-text icon green with-icon" @click.prevent="editField" v-if="getBtnType === 'edit'">Update Field</button>
					<button class="uListing-button uListing-button-text icon white with-icon" @click.prevent="cancelUpdate" v-if="getBtnType === 'edit'">Cancel</button>
				 </div>
			</div>
		</div>	
	`
}