import toggleValidator from '@plugins/toggleValidator'
import { toNumber }    from "@plugins/index"
import { mapGetters }  from "vuex";

export default {
	props: ['field', 'is_pro'],

	mounted() {
		if ( this.field && typeof this.field.networks !== "undefined" ) {
			this.networks = this.field.networks

			Object.keys(this.networks).forEach(element => {
				const value = this.networks[element]
				if ( typeof value !== "undefined" )
					this.networks[element].enable = toggleValidator(value.enable)
			})

		}

		if (this.field && typeof this.field.is_pro_active !== "undefined")
			this.is_pro_active = toNumber(this.field.is_pro_active)

		this.update()
	},

	data() {
		return {
			networks: {},
			is_pro_active: null,
			edit_panel_id: null,
		}
	},

	computed: {
		...mapGetters([
			'getGlobalTexts',
		]),

		text_domains() {
			return this.getGlobalTexts.social_login || {}
		},
	},

	methods: {
		show_panel(index) {

			if ( this.is_pro_active !== 1 )
				return

			if (this.edit_panel_id === index){
				this.edit_panel_id = null;
				return;
			}
			this.edit_panel_id = index;
		},

		update() {
			this.$emit('update', {value: this.networks, isNetwork: true})
		},

		verifyNow(methodName, isTest = 1) {
			let location = window.location.origin;
			let openWindow = window.open(location + '/wp-login.php?social_method=' + methodName + '&verify=' + isTest, methodName + ' verification', 'width=600,height=600,left=2340,top=120,scrollbars=1');
			if (window.focus) {
				openWindow.focus();
			}
			return openWindow;
		},

		getSvg(type) {
			if ( type === 'twitter' ) {
				return `
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="19.5" viewBox="0 0 24 19.5">
					  <path id="twitter_1_" data-name="twitter (1)" d="M24,50.308a10.258,10.258,0,0,1-2.835.777,4.893,4.893,0,0,0,2.164-2.72,9.833,9.833,0,0,1-3.12,1.191A4.92,4.92,0,0,0,11.7,52.922a5.066,5.066,0,0,0,.114,1.122A13.927,13.927,0,0,1,1.671,48.9a4.922,4.922,0,0,0,1.512,6.576A4.859,4.859,0,0,1,.96,54.867v.054A4.943,4.943,0,0,0,4.9,59.755a4.911,4.911,0,0,1-1.29.162,4.35,4.35,0,0,1-.931-.084,4.967,4.967,0,0,0,4.6,3.427,9.886,9.886,0,0,1-6.1,2.1A9.216,9.216,0,0,1,0,65.292,13.852,13.852,0,0,0,7.548,67.5a13.907,13.907,0,0,0,14-14c0-.217-.008-.427-.018-.636A9.815,9.815,0,0,0,24,50.308Z" transform="translate(0 -48)" fill="#03a9f4"/>
					</svg>
				`
			}

			if ( type === 'facebook' ) {
				return `
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					  <path id="facebook" d="M24,12A12,12,0,1,0,12,24c.07,0,.141,0,.211,0V14.658H9.633v-3h2.578V9.441A3.61,3.61,0,0,1,16.064,5.48a20.943,20.943,0,0,1,2.311.117V8.278H16.8c-1.242,0-1.486.591-1.486,1.458v1.913h2.977l-.389,3H15.314v8.883A12,12,0,0,0,24,12Z" fill="#1373f6"/>
					</svg>
				`
			}

			if ( type === 'google' ) {
				return `
					<svg id="search" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					  <path d="M5.319,146.29l-.835,3.119-3.053.065a12.021,12.021,0,0,1-.088-11.205h0l2.718.5,1.191,2.7a7.162,7.162,0,0,0,.067,4.822Z" transform="translate(0 -131.787)" fill="#fbbb00"/>
					  <path d="M273.154,208.176a12,12,0,0,1-4.278,11.6h0l-3.424-.175-.485-3.025a7.152,7.152,0,0,0,3.077-3.652h-6.417v-4.747h11.526Z" transform="translate(-249.364 -198.418)" fill="#518ef8"/>
					  <path d="M48.591,316.263h0a12,12,0,0,1-18.082-3.671l3.889-3.183a7.137,7.137,0,0,0,10.284,3.654Z" transform="translate(-29.079 -294.905)" fill="#28b446"/>
					  <path d="M46.942,2.763,43.054,5.945a7.136,7.136,0,0,0-10.52,3.737l-3.909-3.2h0A12,12,0,0,1,46.942,2.763Z" transform="translate(-27.282)" fill="#f14336"/>
					</svg>


				`
			}

			if ( type === 'vkontakte' ) {
				return `
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="14" viewBox="0 0 24 14">
					  <path id="vk" d="M19.916,13.028c-.388-.49-.277-.708,0-1.146,0-.005,3.208-4.431,3.538-5.932h0c.164-.547,0-.949-.793-.949H20.039a1.127,1.127,0,0,0-1.141.731A21.464,21.464,0,0,1,15.672,11c-.61.6-.892.791-1.225.791-.164,0-.419-.192-.419-.739V5.949c0-.656-.187-.949-.74-.949H9.161a.628.628,0,0,0-.668.591c0,.622.945.765,1.043,2.515v3.8c0,.832-.151.985-.486.985-.892,0-3.057-3.211-4.34-6.886C4.451,5.289,4.2,5,3.525,5H.9c-.749,0-.9.345-.9.731,0,.682.892,4.073,4.148,8.553C6.318,17.343,9.374,19,12.154,19c1.671,0,1.875-.368,1.875-1,0-2.922-.151-3.2.686-3.2.388,0,1.056.192,2.616,1.667C19.115,18.217,19.408,19,20.406,19H23.03c.748,0,1.127-.368.909-1.094C23.44,16.379,20.068,13.238,19.916,13.028Z" transform="translate(0 -5)" fill="#4680c4"/>
					</svg>

				`
			}
		}
	},

	template: `
		<div class="accordion-container social-login">
			<div class="accordion-item" :class="{'uListing-open': edit_panel_id == index, 'uListing-close': edit_panel_id != index}" v-for="(network, index) in networks" v-if="network">
				
				<div class="accordion-row title">
					<div class="accordion-col">
						<h3 class="uListing-normalize uListing-header-3" style="display: flex; align-items: center">
							<p v-html="getSvg(index)" style="margin-right: 15px"></p>
							{{ network.title }}
						</h3>
					</div>
					<div class="accordion-col">
						<div class="accordion-actions">
							<div class="uListing-switch-field" style="margin-right: 20px">
								<label class="uListing-switch">
									<input type="checkbox" @change="update" v-model="network.enable">
									<span class="uListing-slider uListing-round"></span>
								</label>
								<span class="uListing-switch-text uListing-normalize">{{ network.enable ? text_domains.enabled : text_domains.disabled }}</span>
							</div>
							 <span class="uListing-accordion-toggle" @click="show_panel(index)">
								<i v-if="edit_panel_id == index" class="icon--360"></i>
								<i v-if="edit_panel_id != index" class="icon--54"></i>
							</span>
						</div>
					</div>
				</div>
				
				<template v-if="edit_panel_id == index" class="accordion-wrapper">
					
					<div class="accordion-row">
						<div class="accordion-col">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">{{ network.client_id.title }}</span>
								<input type="text" @input="update"  v-model="network.client_id.value" :placeholder="'Enter your ' + network.client_id.title" class="uListing-input uListing-input-text uListing-normalize input-field">
							</div>
						</div>
						<div class="accordion-col">
							<div class="uListing-input-field">
								<span class="uListing-admin-field-title">{{ network.client_secret.title }}</span>
								<input type="text" @input="update" v-model="network.client_secret.value"  :placeholder="'Enter your ' + network.client_secret.title" class="uListing-input uListing-input-text uListing-normalize input-field">
							</div>
						</div>
					</div>
					
					<div class="accordion-row">
						<div class="accordion-col">
							<div class="accordion-description">
								<div>
									<p class="uListing-note-text" v-html="network.description"></p>
									<p class="uListing-note-text"><span>{{ network.social_links.redirect_url }}</span></p>
								</div>
								<p class="uListing-note-text"><button class="uListing-button uListing-button-text uListing-normalize blue small" @click.prevent="verifyNow(network.title)">{{ text_domains.verify_btn }}</button> {{ text_domains.verify_text }}</p>
							</div>
						</div>
					</div>
					
				</template>
			</div>
		</div>
	`
}