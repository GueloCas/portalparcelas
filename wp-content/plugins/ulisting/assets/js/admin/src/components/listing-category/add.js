export default {
	data: () => ({
		count: 0,
		listing_type:[],
		text_domains: null,
		listing_type_list:[]
	}),

	created() {
		if ( typeof settings_data !== "undefined" ) {
			this.listing_type 	   = settings_data?.listing_type
			this.text_domains 	   = settings_data?.text_domains
			this.listing_type_list = settings_data?.listing_type_list
		}

		this.resetFields()
	},

	methods: {
		resetFields() {
			const $form_btn = document.querySelector('#addtag input[type=submit]')
			const $parent = document.querySelector('#parent')
			$form_btn.addEventListener('click', () => {
				setTimeout(() => {
					this.listing_type = []
					$parent.value = '-1'
					this.count++
				}, 500)
			})
		},
	},
	
	template: `
				<div class="form-field term-slug-wrap">
					<label>{{ text_domains && text_domains.title }}</label>
					<ulisting-select2 :key="count" :options="listing_type_list" multiple="multiple" v-model="listing_type">
						<option disabled value="0">{{ text_domains && text_domains.placeholder }}</option>
					</ulisting-select2>
					<input v-for="type in listing_type" type="hidden" name="StmListingCategory[listing_type][]" :value="type">
					<p>
						{{ text_domains && text_domains.description }}
					</p>
				</div>
	
	`
}