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
			<label>{{field.label}}</label>
			<select   v-model="data[field.name]">
				<option v-for="(val, key) in field.items" v-bind:value="key">{{val}}</option>
			</select>
			
	    </div>
	`,
}
