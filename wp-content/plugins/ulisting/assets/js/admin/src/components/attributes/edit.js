import { required, minLength } from 'vuelidate/lib/validators'
import visual from '@partials/blocks/visual-icon'
import { mapGetters } from 'vuex'

export default {
	props: ['attr_data'],

	components: {
		'visual-icon': visual,
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

		affix: {

		}
	},

	data() {
		return {
			attr: null,
			field: null,
			title: '',
			name: '',
			type: '',
			affix: '',
		}
	},

	mounted() {
		this.init()
	},

	computed: {
		iconPicker() {
			return this.field?.visual.icon
		},

		...mapGetters([
			'getNonce',
		])
	},

	methods: {

		init() {
			if ( typeof this.attr_data !== "undefined" ) {
				this.attr   = JSON.parse(JSON.stringify(this.attr_data))
				this.field  = this.attr.fields
				this.initFields()
			}
		},

		initFields() {
			this.title = this.field.title.value
			this.name  = this.field.name.value
			this.type  = this.field.type.value
			this.affix = this.field.affix.value
		},

		submit(event){
			this.$v.$touch()
			if(this.$v.$invalid){
				event.preventDefault();
			}else{

			}
		},

		setName() {
			if (this.id === 0) {
				this.name = this.title;
				this.validateName();
			}
		},

		validateName() {
			let name = '';
			for ( let i = 0; i < this.name.length; i++ ) {
				if (
					( this.name.charCodeAt(i) >= 48  && this.name.charCodeAt(i) <= 57 )  ||
					( this.name.charCodeAt(i) >= 97  && this.name.charCodeAt(i) <= 122 ) ||
					( this.name.charCodeAt(i) >= 65  && this.name.charCodeAt(i) <= 90 )  ||
					this.name.charCodeAt(i) === 32 ||
					this.name.charCodeAt(i) === 45 ||
					this.name.charCodeAt(i) === 95
				) {
					name +=this.name.charAt(i)
					name = name.replace(/[- ]/g,'_');
				}
			}
			this.name = name.toLowerCase();
		}
	},

	template: `
				<form id="edittag" v-on:submit="submit" method="post" :action="attr.action" class="validate" v-if="attr && field">
					<input type="hidden" name="StmListingAttribute[id]" v-model="field.id.value"/>
					<input type="hidden" name="StmListingAttribute[name]" v-model="field.name.value"/>
					<input type="hidden" name="StmListingAttribute[type]" v-model="field.type.value"/>
					<input type="hidden" name="action" value="listing_attribute_save"/>
				
					<table class="form-table" role="presentation">
						<tbody>
						<tr class="form-field form-required term-name-wrap" :class="{ 'form-invalid': $v.title.$error }">
							<th scope="row"><label for="listing-attribute-title">{{ field.title.title }}</label></th>
							<td>
								<input name="StmListingAttribute[title]" v-model.trim="title" v-on:change="setName" v-on:keyup="setName" id="listing-attribute-title" type="text">
								<p class="description">The title is how it appears on your site.</p>
								<div class="error" v-if="!$v.title.minLength">Title must have at least {{$v.title.$params.minLength.min}} letters.</div>
							</td>
						</tr>
						<tr class="form-field form-required term-name-wrap" :class="{ 'form-invalid': $v.name.$error }">
							<th scope="row"><label for="listing-attribute-name">{{ field.affix.title }}</label></th>
							<td>
								<input name="StmListingAttribute[affix]" v-model.trim="affix" id="listing-attribute-name" type="text">
							</td>
						</tr>
						<tr class="form-field form-required term-name-wrap" :class="{ 'form-invalid': $v.type.$error }">
							<th scope="row"><label for="listing-attribute-type">{{ field.type.title }}</label></th>
							<td>
								<select style="height: 40px; border-radius: 4px" class="form-control" id="listing-attribute-type" type="text" disabled v-model="type">
									<option v-for="(option, key) in field.type.options" :value="key" :key="key">{{ option }}</option>
								</select>
							</td>
						</tr>
						<tr><th></th><td></td></tr>
						<tr class="form-field form-required term-name-wrap">
							<th scope="row"><label for="listing-attribute-types">{{ field.listing_type.title }}</label></th>
							<td>
								<ulisting-select2 :options="field.listing_type.options" multiple="multiple" v-model="field.listing_type.value">
									<option disabled value="0">Select one</option>
								</ulisting-select2>
								<input v-for="type in field.listing_type.value" type="hidden" name="StmListingAttribute[listing_type][]" :value="type">
								<p>Choose on what listing types should this term be available.</p>
							</td>
						</tr>
						<tr class="form-field form-required term-name-wrap">
							<th scope="row">
								<label for="listing-attribute-types">{{ field.visual.title }}</label>
							</th>
							<td>
								<visual-icon 
									:thumbnail="field.visual.image"
									:icon_picker="iconPicker"
									name="StmListingAttribute" 
									:is_add="true">
								</visual-icon>
							</td>
						</tr>
						</tbody>
					</table>
					<input type="hidden" name="nonce" :value="getNonce">
					<div class="edit-tag-actions">
						<input type="submit" name="submit" id="submit" class="button button-primary" value="Update">
					</div>
				</form>
	
	`
}