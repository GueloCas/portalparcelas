export default {
	data() {
		return {}
	},
	methods: {
		updateValue: function (value) {
			this.$emit('input', value);
		}
	},
	props: [
		'value',
		'placeholder',
		'callback_change'
	]
}