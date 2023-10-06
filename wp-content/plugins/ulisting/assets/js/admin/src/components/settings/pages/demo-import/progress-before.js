export default {
	props: ['progress_data', 'progress', 'text_domains'],
	template: `
		<div>
			<h1 style="margin-bottom: 35px" class="uListing-normalize uListing-header-1">{{ progress_data }}</h1>
			<div class="progress">
				<div class="progress-bar progress-bar-animated" role="progressbar"   v-bind:aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100" v-bind:style="'width: '+progress+'%'"></div>
			</div>
			<p class="uListing-secondary-text uListing-normalize" style="margin-top: 20px; text-align: center">{{ text_domains.processing }} <strong>{{ progress }}%</strong></p>
		</div>
	`
}