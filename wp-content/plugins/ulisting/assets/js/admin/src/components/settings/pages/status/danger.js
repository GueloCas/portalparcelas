export default {
	props: ['text_domains', 'conflicts'],
	template: `
		<div class="status-diff" v-if="conflicts && conflicts.length > 0">
			<div class="status-item" v-for="conflict in conflicts" :key="conflict.file">
				<div class="status-item__icon">
					<i class="icon-alert danger"></i>
				</div>
				<div class="status-item__info">
					<h3 class="uListing-normalize uListing-header-3">{{ conflict.file }}</h3>
					<p class="uListing-normalize uListing-note-text">
						{{ text_domains.version }} 
						<span style="color: red; font-weight: 700">{{ conflict.theme_version }}</span>
						{{ text_domains.theme }}. 
						{{ text_domains.plugin }}
						<span style="font-weight: 700">{{ conflict.plugin_version }}</span>.
					</p>
				</div>
			</div>
		</div>
	`
}