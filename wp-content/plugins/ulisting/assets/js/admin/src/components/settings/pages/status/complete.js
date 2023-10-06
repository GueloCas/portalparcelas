export default {
	props: ['text_domains', 'count'],
	template: `
		<div class="status-other">
			<div class="status-item">
				<div class="status-item__icon">
					<i class="icon-check-mark success"></i>
				</div>
				<div class="status-item__info">
					<h3 class="uListing-normalize uListing-header-3">{{ text_domains.other }} {{ count }} {{ text_domains.temp }}</h3>
					<p class="uListing-normalize uListing-note-text">{{ text_domains.complete }}</p>
				</div>
			</div>
		</div>
	`
}