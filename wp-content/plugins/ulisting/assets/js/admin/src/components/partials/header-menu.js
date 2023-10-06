import { mapGetters, mapMutations, mapActions } from 'vuex'

export default {
	data() {
		return {
			sticky: false,
		}
	},

	computed: {
		...mapGetters([
			'getHeaderPages',
			'getHeaderLogo',
			'getButtonsText',
			'getActivePage',
			'getAjaxUrl',
			'getActiveTab',
			'getNonce',

		]),

		showHeader() {
			return this.getActiveTab !== 'user-roles-tab' && this.getActiveTab !== 'payment-tab' && this.getActiveTab !== 'email-tab'
		}
	},

	mounted() {
		window.addEventListener('scroll', () => {
			const header = this.$refs.headerMenu
			const sticky = header.parentNode?.offsetTop || 0
			this.sticky = window.pageYOffset >= (sticky - 1)
		})
	},

	methods: {
		...mapMutations([
			'setActivePage'
		]),

		...mapActions([
			'saveSettings',
			'generatePages'
		]),
	},

	template: `
				<div class="uListing-header-menu" ref="headerMenu" :class="{sticky: sticky}">
					<div class="uhm-navigator">
						<div class="uhm-logo">
							<img :src="getHeaderLogo" alt="uListing logo">
						</div>
						<div class="uhm-menu">
							<ul>
								<li v-for="page in getHeaderPages" :key="page.icon">
									<a :href="page.link" :class="{active: page.component === getActivePage}" class="uListing-top-menu-text uListing-normalize">
										<i :class="page.icon"></i>
										{{ page.title }}
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="uhm-actions">
						<button style="padding: 11px 17px;" v-if="showHeader" class="uListing-button uListing-button-text uListing-normalize icon" @click.prevent="saveSettings({url: getAjaxUrl, nonce: getNonce})">{{ getButtonsText.save_changes }}</button>
					</div>
				</div>
	`
}