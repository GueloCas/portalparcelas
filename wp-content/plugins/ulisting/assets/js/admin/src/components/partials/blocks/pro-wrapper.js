import modal from '@partials/blocks/modal'
import { mapGetters, mapActions, mapMutations } from 'vuex'

export default {
	props: {
		btn: {
			type: String,
			default: 'Pro Features',
		},
		text: {
			type: String,
			default: ''
		},

		type: {
			type: String,
			default: '',
		},

		icon: {
			type: String,
			default: '',
		},
	},
	data() {
		return {
			showModal: false,
			hasAccess: true,
		}
	},

	methods: {
		...mapMutations([
			'setShowModal',
			'setHasAccess',
		]),

		...mapActions([
			'closeModal',
			'openModal',
		]),

		open() {
			this.openModal({current: this, component: 'pro-features', size: 'modal-sm pro-features'})
		},

		closeModal() {
			this.showModal = false
		},
	},

	components: {
		'modal-window' : modal,
	},

	template: `
		<div class="pro-feature social-login">
			<button @click="open" class="uListing-button uListing-button-text icon" :class="type" style="margin-right: 10px; padding-top: 13px; padding-bottom: 13px">
				<i v-if="icon" :class="icon"></i>
			  	{{ btn }}
			</button>
		    <i class="icon-padlock icon-btn"></i>
		    <p>
		    	Available in uListing - <a href="https://stylemixthemes.com/wordpress-classified-plugin/" target="_blank">{{ text }}</a>
			</p>
		</div>
	`
}