export default {
	props: ['text_domains'],
	methods: {
		import_progress() {
			this.$emit('run')
		}
	},
	template: `
		<div>
			<h1 class="uListing-normalize uListing-header-1">{{ text_domains.title }}</h1>
			<p class="uListing-secondary-text uListing-normalize">{{ text_domains.content }}</p>	
			<button class="uListing-button-text uListing-normalize uListing-button" @click.prevent="import_progress">{{ text_domains.btn }}</button>
		</div>
	`
}