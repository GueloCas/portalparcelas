export default {
	props: ['extension', 'plugins', 'plugin_url', 'text_domains'],
	template: `
		<div class="extension-item">
			<img :src="extension.image" class="extension-item__img" alt="No image">
			<div class="extension-item__content">
				<div class="extension-item__text-block">
					<h3 class="uListing-normalize uListing-header-3" style="font-weight: 700">{{ extension.title }}</h3>
					<p class="uListing-normalize uListing-descriptions">{{ extension.description }}</p>
				</div>
				<div>
					<a v-if="plugins && plugins[extension.slug] === ''" class="uListing-button uListing-button-text uListing-normalize icon" :href="extension.link" target="_blank">{{ text_domains && text_domains.get_ext }}</a>
					<a v-if="plugins && plugins[extension.slug] === 'active'" class="uListing-button-text uListing-normalize enabled"> <i class="icon-check-mark"></i> <span>{{ text_domains && text_domains.enabled }}</span></a>
					<a v-if="plugins && plugins[extension.slug] === 'inactive'" class="uListing-button uListing-normalize uListing-button-text secondary" :href="plugin_url" target="_blank">{{ text_domains && text_domains.enable }}</a>
				</div>
			</div>
		</div>
	`
}