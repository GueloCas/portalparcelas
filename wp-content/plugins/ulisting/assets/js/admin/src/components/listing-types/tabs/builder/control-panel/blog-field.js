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
			<div class="ulisting-blog-field-list p-l-10 p-r-10">
				<div class="stm-row">
					<div class="stm-col-6 p-3" v-for="(item, key) in field.items">
						<label>
							<input v-show="false" type="radio" v-model="data[field.name]" v-bind:value="key">
							<div class="ulisting-blog-field-item" 
									 v-bind:class="{'active': data[field.name] == key}" >
								  <img class="stm-max-width-100" v-bind:src="item.icon">
								  <span>{{item.name}}</span>
							</div>
						</label>
						
					</div>
				</div>		
			</div>
		</div>
	`,
}