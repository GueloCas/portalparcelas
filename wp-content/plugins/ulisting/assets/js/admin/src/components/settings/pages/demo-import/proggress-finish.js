export default {
	props: ['text_domains', 'links'],
	computed: {
		getListingTypeUrl() {
			return this.links.listing_type
		},

		getListingUrl() {
			return this.links.listing
		},
	},
	template: `
		<div>
			<h1 class="uListing-normalize uListing-header-1 complete">{{ text_domains.complete }}</h1>
			<div>
				<a class="uListing-button-text uListing-normalize uListing-button white" :href="getListingTypeUrl">{{ text_domains.listing_type && text_domains.listing_type.text }}</a>
				<a class="uListing-button-text uListing-normalize uListing-button secondary" :href="getListingUrl">{{ text_domains.listings && text_domains.listings.text }}</a>
			</div>
		</div>
	`
}