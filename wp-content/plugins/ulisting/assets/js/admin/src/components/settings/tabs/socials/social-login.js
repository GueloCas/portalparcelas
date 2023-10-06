import section 	   	  from '../sections'
import proWrapper 	  from '@partials/blocks/pro-wrapper'
import { toNumber }   from "@plugins/index"
import { mapGetters, mapMutations, mapActions } from 'vuex'

export default {
	components: {
		'section-row' : section,
		'pro-wrapper' : proWrapper,
	},

	data() {
		return {
			networks: {},
			preferences: {
				tab: 'yes',
				icons: 'square',
				redirect_url: '',
				title: 'Login with social ID',
			},
		}
	},

	methods: {
		change(data) {
			const { index, value, isNetwork } = data
			if ( isNetwork ) {
				this.networks = value
				this.updateSocials({networks: this.networks, preferences: this.preferences})
				return
			}

			if (this.preferences && typeof this.preferences[index] !== "undefined") {
				this.preferences[index] = value
				this.updateSocials({networks: this.networks, preferences: this.preferences})
			}
		},

		checkProStatus(num) {
			return toNumber(num)
		},

		...mapMutations([
			'updateSocials'
		]),

		...mapActions([
			'saveSettings',
		])
	},

	computed: {
		...mapGetters([
			'getSocialLoginData',
			'openContent',
			'getButtonsText',
			'getAjaxUrl',
			'getModalStatus',
		]),

		getSocialLoginStatus() {
			const data = this.getSocialLoginData[0] ?  this.getSocialLoginData[0] : {}
			return toNumber(data.is_pro)
		}
	},

	template: `
		<div class="uListing-content" v-if="this.openContent" :class="{'modal-open': getModalStatus}">
			<div class="uListing-row social-login-with-pro" v-if="getSocialLoginStatus !== 1">
				<div class="uListing-row-inner"  style="margin: 7px 20px; display: flex; justify-content: flex-start;">
					<div class="ur-content" style="display: block !important; margin-top: 25px">
						<pro-wrapper text="Social Login Add-on."></pro-wrapper>
					</div>
				</div>
			</div>
			<div class="uListing-row" v-for="(main, index) in getSocialLoginData" :key="index" :class="{'first-widget': index === 0, lock: checkProStatus(main.is_pro) !== 1, [main.key]: true}">
				<section-row v-for="(row, row_index) in main.rows" :main="main" :row="row" :row_index="row_index" :key="row_index" @change="change"></section-row>			
			</div>
		</div>
	`
}