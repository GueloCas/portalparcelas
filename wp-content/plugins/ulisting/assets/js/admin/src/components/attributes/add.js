import { required, minLength } from 'vuelidate/lib/validators'
import visual from '@partials/blocks/visual-icon'
import {mapGetters} from 'vuex'

export default {
	components: {
		'visual-icon': visual,
	},

	data() {
		return {
			icon: '',
			name: '',
			title: '',
			type: '',
			affix: '',
			action: '',
			icon_type: 0,
			show_name: 1,
			listing_type: [],
			attr_type_list: [],
			text_domains: null,
			listing_type_list: []
		}
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
		}
	},

	created() {
		if ( typeof settings_data === 'undefined' )
			return

		if ( typeof settings_data.listing_type_list !== 'undefined' )
			this.listing_type_list = settings_data.listing_type_list

		if ( typeof settings_data.action !== 'undefined' )
			this.action = settings_data.action

		if ( typeof settings_data.attr_type_list !== 'undefined' )
			this.attr_type_list = settings_data.attr_type_list

		if ( typeof settings_data.text_domains !== 'undefined' )
			this.text_domains = settings_data.text_domains
	},

	computed: {
		...mapGetters([
			'getNonce',
		])
	},

	methods:{
		select_type(){
			if ( this.type === 'location' ) {
				this.show_name = 0
				this.name 	   = this.type
			} else {
				this.show_name = 1
				if ( this.name === 'location' )
					this.name = '';
			}
		},

		submit(event){
			this.$v.$touch()
			if ( this.$v.$invalid )
				event.preventDefault();
		},

		setName() {
			if ( this.type === 'location' )
				return
			this.name = this.title;
			this.validateName();
		},

		validateName() {
			let name = '';
			for (let i = 0; i < this.name.length; i++) {
				if (
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
		}
	},

	template: `
				<form  v-on:submit="submit" method="post" :action="action" class="validate">
					<input type="hidden" name="action" value="listing_attribute_save"/>
					
					<div class="form-field form-required term-name-wrap" :class="{ 'form-invalid': $v.title.$error }">
						<label for="listing-attribute-title">{{ text_domains && text_domains.title }}</label>
						<input name="StmListingAttribute[title]" v-model.trim="title" v-on:change="setName" v-on:keyup="setName" id="listing-attribute-title"  type="text">
						<p>{{ text_domains && text_domains.title_desc }}</p>
						<div class="error" v-if="!$v.title.minLength">Title must have at least {{$v.title.$params.minLength.min}} letters.</div>
					</div>
					
					<div v-show="show_name" class="form-field form-required term-name-wrap" :class="{ 'form-invalid': $v.name.$error }">
						<label for="listing-attribute-name">{{ text_domains && text_domains.name }}</label>
						<input name="StmListingAttribute[name]" v-model.trim="name" v-on:change="validateName" v-on:keyup="validateName"  id="listing-attribute-name" type="text"  size="40" aria-required="true">
					</div>
					
					<div class="form-field form-required term-name-wrap" :class="{ 'form-invalid': $v.name.$error }">
						<label for="listing-attribute-affix">Affix</label>
						<input name="StmListingAttribute[affix]" v-model.trim="affix" id="listing-attribute-affix" type="text">
					</div>
					
					<div class="form-field form-required term-name-wrap"  :class="{ 'form-invalid': $v.type.$error }">
						<label for="listing-attribute-type">Type</label><br>
						<select class="form-control" name="StmListingAttribute[type]" v-model.trim="type" @change="select_type" id="listing-attribute-type" type="text">
							<option v-for="(val, key) in attr_type_list" :key="val" :value="key">{{ val }}</option>
						</select>
					</div>
					
					<div class="form-field term-slug-wrap">
						<label for="listing-attribute-type">{{ text_domains && text_domains.listing_type_txt }}</label>
						<ulisting-select2 :options="listing_type_list" multiple="multiple" v-model="listing_type">
							<option disabled value="0">Select one</option>
						</ulisting-select2>
						
						<input v-for="type in listing_type" :key="type" type="hidden" name="StmListingAttribute[listing_type][]" :value="type">
						<p>{{ text_domains && text_domains.choose_type }}</p>
					</div>
					
					<input type="hidden" name="nonce" :value="getNonce">
					
					<visual-icon name="StmListingAttribute" :is_add="true"></visual-icon>
					
					<p class="submit">
						<input v-if="text_domains" type="submit" name="submit" id="submit" class="button button-primary" :value="text_domains.submit_btn">
					</p>
				</form>
	`
}

