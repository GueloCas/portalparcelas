import toggleValidator from '@plugins/toggleValidator'

export default {
	props: {
		field: {
			default: {},
			type: Object,
		},

		type: {
			default: '',
			type: String,
		},

		index: {
			default: null,
			type: String,
		},
	},

	data() {
		return {
			toggle: ''
		}
	},

	mounted() {
		this.toggle = this.field.value || this.field.default
		this.toggle = toggleValidator(this.toggle)
		this.toggleHandler()
	},

	methods: {
		toggleHandler() {
			if ( this.index ) {
				this.$emit('update', {type: this.type, index: this.index, value: this.toggle})
			} else {
				this.$emit('update', {type: this.type, index: 'no-data', value: this.toggle,})
			}
		}
	},

	template: `
			<div class="uListing-switch-field">
				<label class="uListing-switch">
					<input type="checkbox" v-model="toggle" @change="toggleHandler">
					<span class="uListing-slider uListing-round"></span>
				</label>
				<span class="uListing-switch-text uListing-normalize">{{ field.title }}</span>
			</div>
	`
}