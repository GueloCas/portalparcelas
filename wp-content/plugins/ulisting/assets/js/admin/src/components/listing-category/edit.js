export default {
	data: () => ({
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
	},

	template: `
				<td>
					<ulisting-select2 :options="listing_type_list" multiple="multiple" v-model="listing_type">
						<option disabled value="0">{{ text_domains && text_domains.placeholder }}</option>
					</ulisting-select2>
					<input v-for="type in listing_type" type="hidden" name="StmListingCategory[listing_type][]" :value="type">
					<p>
						{{ text_domains && text_domains.description }}
					</p>
				</td>
	`
}