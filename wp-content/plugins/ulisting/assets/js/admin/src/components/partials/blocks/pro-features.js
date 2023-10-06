import { mapGetters} from 'vuex'
export default {
	computed: {
		...mapGetters([
			'getProImage',
			'getGlobalTexts',
		]),

		text_domains() {
			return this.getGlobalTexts.pro_features || {}
		},
	},
	template: `
		<div class="modal-window-container uListing-features">
			<div class="image-wrapper">
				<img :src="getProImage" alt="Header Logo">
			</div>
			<div class="modal-header-wrap">
				<div class="pro-feature-wrapper">
					<span>PRO</span> 	
				</div>		
				<h1 class="uListing-header-1">
					{{ text_domains.required }}
				</h1>
			</div>
			<p class="uListing-secondary-text">{{ text_domains.description }}</p>
			<div class="modal-window-options">		
				<ul>
					<li>
						<i class="icon-check-mark"></i>
						User Roles
					</li>
					<li>
						<i class="icon-check-mark"></i>
						Listing Compare
					</li>
					<li>
						<i class="icon-check-mark"></i>
						Wishlist
					</li>
					<li>
						<i class="icon-check-mark"></i>
						Socail Login
					</li>
					<li>
						<i class="icon-check-mark"></i>
						Subscription
					</li>
				</ul>
			</div>
			<div class="modal-window-option stm-center">
				<a class="uListing-button uListing-button-text uListing-normalize icon" href="https://stylemixthemes.com/wordpress-classified-plugin/" target="_blank">{{ text_domains.unlock }}</a>
			</div>
		</div>
	
	`
}