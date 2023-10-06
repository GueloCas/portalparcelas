import postRequest     from '@plugins/postRequest'
import { renderToast } from "@plugins/index"
import clickCopy 	   from '@partials/blocks/click-copy'

import { mapGetters, mapActions }  from "vuex"

export default {
	components: {
		'click-copy': clickCopy,
	},

	data() {
		return {
			type: '',
			field: null,
			modal_data: null,
			text_domains: null
		}
	},

	mounted() {
		if ( this.getModel ) {
			this.field = JSON.parse(JSON.stringify(this.getModel))
			if ( this.field.modal && typeof this.field.modal.data) {
				this.modal_data   = this.field.modal.data
				this.text_domains = this.field.modal.text
			}
		}
	},

	methods: {
		saveSettings() {
			const data = {}
			this.getCurrent?.update(this.field)
			this.$emit('update', this.field)
			setTimeout(() => {
				const id = this.field.id

				if ( id === 'paypal_standard' ) {
					data.email = this.modal_data.email
					data.mode  = this.modal_data.mode_selected
				} else if ( id === 'stripe' ) {
					data.publishable_key = this.modal_data.publishable_key
					data.secret_key		 = this.modal_data.secret_key
					data.whsec			 = this.modal_data.whsec
				} else if ( id === 'paypal' ) {
					data.client_id 		= this.modal_data.client_id
					data.client_secret 	= this.modal_data.client_secret
					data.webhook_id 	= this.modal_data.web_hook_id
					data.mode 			= this.modal_data.mode_selected
				}

				postRequest(this.getAjaxUrl, {action: 'stm_save_payment', id, data, nonce: this.getNonce }, res => {
					if (res.success)
						renderToast(res.message, res.status)
				}, 100)
			})
		},

		close() {
			setTimeout(() => this.closeModal(), 100)
		},

		saveAndClose(){
			this.saveSettings()
			this.close()
		},

		...mapActions([
			'closeModal'
		])
	},

	computed: {
		...mapGetters([
			'getButtonsText',
			'getAjaxUrl',
			'getModel',
			'getNonce',
			'getModalText',
			'getCurrent'
		]),
	},

	template: `
		<div class="modal-window-container" v-if="field && modal_data">
			<h1 class="uListing-header-1">{{ field.title }} Settings</h1>
			<div class="modal-window-options">		
				
				<template v-if="field.id === 'paypal_standard'">
									
					<div class="modal-window-option">
						<p class="uListing-admin-field-title">{{ text_domains.email  }}</p>
						<input type="email" v-model="modal_data.email" class="uListing-input-field uListing-input uListing-input-text">
					</div>
					
					<div class="modal-window-option">
						<p class="uListing-admin-field-title">{{ text_domains.ipn }}</p>
						<click-copy :copy_text="modal_data.ipn"></click-copy>	
					</div>
					
					<div class="modal-window-option uListing-admin-select">
						<p class="uListing-admin-field-title">{{ text_domains.mode  }}</p>
						<select class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="modal_data.mode_selected">
							<option v-for="(option, key) in modal_data.modes" :key="key" :value="key">{{ option }}</option>
						</select>
					</div>

					<div class="modal-window-option" style="display: flex; justify-content: space-between">
						<button class="uListing-button uListing-button-text uListing-normalize icon"  style="font-size: 14px" @click.prevent="saveAndClose">{{ getButtonsText.save_changes }}</button>
					</div>
					
				</template>	
				
				<template v-if="field.id === 'stripe'">
									
					<div class="modal-window-option">
						<p class="uListing-admin-field-title">{{ text_domains.web_hook_url }}</p>
						<click-copy :copy_text="modal_data.web_hook_url"></click-copy>	
					</div>			
					
					<div class="modal-window-option">
						<p class="uListing-admin-field-title">{{ text_domains.publishable_key  }}</p>
						<input type="text" v-model="modal_data.publishable_key" class="uListing-input-field uListing-input uListing-input-text">
					</div>
					
					<div class="modal-window-option">
						<p class="uListing-admin-field-title">{{ text_domains.secret_key  }}</p>
						<input type="text" v-model="modal_data.secret_key" class="uListing-input-field uListing-input uListing-input-text">
					</div>
					
					<div class="modal-window-option">
						<p class="uListing-admin-field-title">{{ text_domains.whsec  }}</p>
						<input type="text" v-model="modal_data.whsec" class="uListing-input-field uListing-input uListing-input-text">
					</div>

					<div class="modal-window-option" style="display: flex; justify-content: space-between">
						<button class="uListing-button uListing-button-text uListing-normalize icon"  style="font-size: 14px" @click.prevent="saveAndClose">{{ getButtonsText.save_changes }}</button>
					</div>
					
				</template>	
				
				<template v-if="field.id === 'paypal'">
									
					<div class="modal-window-option">
						<p class="uListing-admin-field-title">{{ text_domains.web_hook_url }}</p>
						<click-copy :copy_text="modal_data.web_hook_url"></click-copy>	
					</div>			
					
					<div class="modal-window-option" :class="{disabled: modal_data.access_update_account !== 0}">
						<p class="uListing-admin-field-title">{{ text_domains.client_id  }}</p>
						<input type="text" v-model="modal_data.client_id" class="uListing-input-field uListing-input uListing-input-text">
					</div>
					
					<div class="modal-window-option" :class="{disabled: modal_data.access_update_account !== 0}">
						<p class="uListing-admin-field-title">{{ text_domains.client_secret  }}</p>
						<input type="text" v-model="modal_data.client_secret" class="uListing-input-field uListing-input uListing-input-text">
					</div>
					
					<div class="modal-window-option" :class="{disabled: modal_data.access_update_account !== 0}">
						<p class="uListing-admin-field-title">{{ text_domains.web_hook_id  }}</p>
						<input type="text" v-model="modal_data.web_hook_id" class="uListing-input-field uListing-input uListing-input-text">
					</div>
					
					<div class="modal-window-option uListing-admin-select" :class="{disabled: modal_data.access_update_account !== 0}">
						<p class="uListing-admin-field-title">{{ text_domains.mode  }}</p>
						<select  class="uListing-select-box uListing-select-box-text uListing-normalize" v-model="modal_data.mode_selected">
							<option v-for="(option, key) in modal_data.modes" :key="key" :value="key">{{ option }}</option>
						</select>
					</div>

					<div class="modal-window-option" style="display: flex; justify-content: space-between">
						<button class="uListing-button uListing-button-text uListing-normalize icon" style="font-size: 14px" @click.prevent="saveAndClose">{{ getButtonsText.save_changes }}</button>
					</div>
					
				</template>		
				
			</div>
		</div>
	`
}