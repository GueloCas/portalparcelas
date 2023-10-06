import postRequest 	    from '@plugins/postRequest'
import { renderToast }  from "@plugins/index";
import { mapGetters, mapActions }   from 'vuex'

export default {
	props: ['field', 'text'],

	data() {
		return {
			payment: null,
			showModal: false,
			hasAccess: true,
			payments: [],
		}
	},

	computed: {
		...mapGetters([
			'getAjaxUrl',
			'getGlobalTexts',
			'getNonce'
		]),

		text_domains() {
			return this.getGlobalTexts.payments || {}
		},
	},

	methods: {
		...mapActions([
			'openModal',
		]),

		toggle(type, payment_method) {
			postRequest(this.getAjaxUrl, { action: 'stm_payment_method', type, payment_method, nonce: this.getNonce }, res => {
				if ( res.status === 'success' ) {
					this.payments = res.payments
					if ( res.success )
						renderToast(res.message, res.status)
				}
			})
		},

		open(model) {
			this.openModal({current: this, component: 'payment-settings', model, size: 'modal-sm payment', text: this.text_domains})
		},

		update(field) {
			Object.keys(this.payments).forEach( key => {
				if ( this.payments[key] && this.payments[key].id === field.id )
					this.payments[key] = field
			})
		},
	},

	mounted() {
		if ( this.field && typeof this.field.payments !== "undefined" )
			this.payments = this.field.payments

		if ( this.field && typeof this.field.text !== "undefined" )
			this.text_domains = this.field.text
	},

	template: `
		<div class="payment-card">
			<div class="payment-card-item" v-for="payment in payments" :key="payment.title">
				<div class="payment-card-logo">
					<img :src="payment.image" alt="No image">
				</div>
				<div class="payment-card-content">
					<div class="payment-card-content__header">
						<h3 class="uListing-header-3">{{ payment.title }}</h3>
						<p class="uListing-note-text">{{ payment.description }}</p>
					</div>
					<div class="payment-card-footer">
						<button v-if="payment.enabled === 'no'" @click.prevent="toggle('install', payment.id)" class="uListing-button uListing-button-text small icon green payment">
							<i class="icon-check-mark"></i>
							{{ text_domains.install }}
						</button>
						
						<template v-else-if="payment.enabled === 'yes'">
							<button class="uListing-button uListing-button-text blue payment" @click.prevent="open(payment)">
								<i class="icon-adjust"></i>
								{{ text_domains.settings }}
							</button>
							
							<button class="uListing-button uListing-button-text simple payment" @click.prevent="toggle('uninstall', payment.id)">
								<i class="icon-alert"></i>
								{{ text_domains.uninstall }}
							</button>
						</template>
					</div>
				</div>
			</div>
		</div>
		 
	`
}