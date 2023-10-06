import { mapGetters, mapActions }  from "vuex";
import VueEasyTinyMCE  from 'vue-easy-tinymce'
import postRequest     from '@plugins/postRequest'
import { toNumber, renderToast }  from "@plugins/index";

export default {
	components: {
		'tinymce': VueEasyTinyMCE
	},
	
	data() {
		return {
			tinymcePlugins: [
				'advlist autolink lists link textcolor',
				'searchreplace visualblocks code',
				'insertdatetime media table contextmenu paste code directionality template colorpicker textpattern'
			],
			tinymceToolbar1: 'undo redo | bold italic strikethrough | forecolor backcolor | template link | bullist numlist | ltr rtl | removeformat',
			tinymceToolbar2: '',
			tinymceOtherOptions: {
				height: 150,
			},
			slug: '',
			field: null,
		}
	},

	mounted() {
		if ( this.getModel ) {
			this.field = JSON.parse(JSON.stringify(this.getModel))
			this.convertVal()
		}
	},

	methods: {
		saveSettings() {
			this.convertVal()
			this.$emit('update', this.field)
			this.getCurrent?.update(this.field)

			setTimeout(() => {
				postRequest(this.getAjaxUrl, {action: 'stm_save_template', slug: this.field.slug, data: this.field, nonce: this.getNonce }, res => {
					if (res.success)
						renderToast(res.message, res.status)
				}, 100)
			})
		},

		convertVal() {
			this.field.banner 	 = toNumber(this.field.banner)
			this.field.is_active = toNumber(this.field.is_active)
			this.field.footer 	 = toNumber(this.field.footer)
			this.field.header 	 = toNumber(this.field.header)
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
			'getModalText',
			'getCurrent',
			'getNonce',
		]),

		text_domains() {
			return this.getModalText
		},

		toggleText() {
			return this.text_domains ? this.text_domains.toggle : {}
		},

		logoText() {
			return this.text_domains ? this.text_domains.logo : {}
		},

		socialText() {
			return this.text_domains ? this.text_domains.social : {}
		},

		bannerText() {
			return this.text_domains ? this.text_domains.banner : {}
		},
	},

	template: `
		<div class="modal-window-container" v-if="field">
			<h1 class="uListing-header-1">{{ field.title }}</h1>
			<div class="modal-window-options">
				<div class="modal-window-option">
					<div class="uListing-switch-field">
						<label class="uListing-switch">
							<input type="checkbox" v-model="field.is_active">
							<span class="uListing-slider uListing-round"></span>
						</label>
						<span style="display: flex; flex-direction: column; margin-left: 20px">
							<span class="uListing-switch-text uListing-normalize" style="margin: 0 0 5px 0">{{ toggleText.title }}</span>
							<span class="uListing-descriptions">{{ toggleText.desc }}</span>	
						</span>
					</div>
				</div>
				<div class="modal-window-option">
					<div class="uListing-switch-field">
						<label class="uListing-switch">
							<input type="checkbox" v-model="field.header">
							<span class="uListing-slider uListing-round"></span>
						</label>
						<span style="display: flex; flex-direction: column; margin-left: 20px">
							<span class="uListing-switch-text uListing-normalize" style="margin: 0 0 5px 0">{{ logoText.title }}</span>
							<span class="uListing-descriptions">{{ logoText.desc }}</span>	
						</span>
					</div>
				</div>
				<div class="modal-window-option">
					<div class="uListing-switch-field">
						<label class="uListing-switch">
							<input type="checkbox" v-model="field.footer">
							<span class="uListing-slider uListing-round"></span>
						</label>
						<span style="display: flex; flex-direction: column; margin-left: 20px">
							<span class="uListing-switch-text uListing-normalize" style="margin: 0 0 5px 0">{{ socialText.title }}</span>
							<span class="uListing-descriptions">{{ socialText.desc }}</span>	
						</span>
					</div>
				</div>

				<div class="modal-window-option">
					<div class="uListing-switch-field">
						<label class="uListing-switch">
							<input type="checkbox" v-model="field.banner">
							<span class="uListing-slider uListing-round"></span>
						</label>
						<span style="display: flex; flex-direction: column; margin-left: 20px">
							<span class="uListing-switch-text uListing-normalize" style="margin: 0 0 5px 0">{{ bannerText.title }}</span>
							<span class="uListing-descriptions">{{ bannerText.desc }}</span>	
						</span>
					</div>
				</div>
				
				<div class="modal-window-option">
					<p class="uListing-admin-field-title">{{ text_domains.subject }}</p>
					<input type="text" v-model="field.subject" class="uListing-input-field uListing-input uListing-input-text">
				</div>
				
				<div class="modal-window-option">
					<p class="uListing-admin-field-title">{{ text_domains.content }}</p>
					<tinymce v-model="field.content"
					  :plugins="tinymcePlugins"
					  :toolbar1="tinymceToolbar1"
					  :toolbar2="tinymceToolbar2"
					  :other="tinymceOtherOptions"
					  >
					</tinymce>
				</div>
				<div class="modal-window-option">
					<button class="uListing-button uListing-button-text uListing-normalize icon"  style="font-size: 14px !important;" @click.prevent="saveSettings">{{ getButtonsText.save_changes }}</button>
					<button class="uListing-button uListing-button-text uListing-normalize blue"  style="font-size: 14px !important;" @click.prevent="saveAndClose">{{ getButtonsText.save_and_close }}</button>
					<button class="uListing-button uListing-button-text uListing-normalize trash" style="font-size: 14px !important;" @click.prevent="close">{{ getButtonsText.close_btn }}</button>
				</div>
			</div>
		</div>
	`
}