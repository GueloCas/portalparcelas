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
		id:{
			default:0
		},
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
			<label class="ulisting-checkbox">
				<input type="checkbox"  :true-value="1" :false-value="0" v-model="data[field.name]" />
				<span></span>
				{{field.label}}
			</label>
		</div>
	`,
}