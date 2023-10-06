export default {
	data: function () {
		return {
		}
	},
	mounted: function () {

	},
	methods:{

	},
	props: {
		data: { default: "" },
		field:{default: ""}
	},
	watch:{
		data: function(val) {
			this.$emit('input', val)
		}
	},
	template:`
		<div class="form-group">
			<label>{{field.label}}</label>
			<input type="number"  v-model="data[field.name]">
	    </div>
	`,
}