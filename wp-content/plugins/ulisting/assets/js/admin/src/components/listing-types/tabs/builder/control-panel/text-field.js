export default {
	data: function () {
		return {
		}
	},
	mounted: function () {
		if(typeof this.data !== 'undefined' && (this.data.hasOwnProperty('short_code')) ){
			String.prototype.replaceAll = function (search, replace) {
				return this.split(search).join(replace);
			};

			this.data.short_code = this.data.short_code.replaceAll('u0022', '"');
		}
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
			<input type="text"  v-model="data[field.name]">
	    </div>
	`,
}