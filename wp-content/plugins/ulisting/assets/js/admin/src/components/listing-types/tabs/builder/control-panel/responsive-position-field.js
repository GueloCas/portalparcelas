export default {
	data: function () {
		return {
			active:"extra_large",
			type: '',
			icons:{
				extra_large:"fa fa-desktop",
				large:"fa fa-tablet transform-rotate-90",
				medium:"fa fa-tablet",
				small:"fa fa-mobile transform-rotate-90",
				extra_small:"fa fa-mobile",
			},
			size:{
				extra_large:"none",
				large:"none",
				medium:"none",
				small:"none",
				extra_small:"none",
			},
			values: {
				extra_large:{
					top: "",
					right: "",
					bottom: "",
					left: "",
				},
				large:{
					top: "",
					right: "",
					bottom: "",
					left: "",
				},
				medium:{
					top: "",
					right: "",
					bottom: "",
					left: "",
				},
				small:{
					top: "",
					right: "",
					bottom: "",
					left: "",
				},
				extra_small:{
					top: "",
					right: "",
					bottom: "",
					left: "",
				},
			}
		}
	},
	mounted: function () {

		if(typeof this.data[this.field.name] != "undefined")
			this.size = this.data[this.field.name];
		if(typeof this.data.values != "undefined")
			this.values = this.data.values;
	},
	methods:{
		set_active: function(active){
			this.active = active;
		},
		change: function(){
			this.data['values'] = this.values;
			this.data[this.field.name] = JSON.parse(JSON.stringify(this.size));
			this.data.__ob__.dep.notify()
		},

		build_array_for_select2(data) {
			const items = [];
			for(let key in data){
				items.push({
					id:key,
					text:data[key]
				})
			}
			return items;
		},

	},
	props: {
		id:{
			default:0
		},
		element: { default: []},
		data: { default: []},
		field: { default: [] },
	},
	watch:{

		values:{
			handler(val){
				this.change();
			},
			deep: true
		},

		size:{
			handler(val){
				this.change()
			},
			deep: true
		},
	},
	activate: function() {

	},
	template:`
		<div class="form-group p-t-15 indention-field">
			<label style="margin-bottom: 15px !important; font-size: 16px !important;">{{ field.label }}</label>
			<div class="stm-row m-b-15">
				<div class="stm-col-12">
					<label>Device</label>
				</div>
				<div class="stm-col-12 ulisting-builder-device-width">
					<button v-for="(val, key) in size" @click="set_active(key)" class="btn " v-bind:class="{'active': active==key}" type="button"><i v-bind:class="icons[key]"></i></button>	
				</div>
			</div>

			<div v-for="(val, key) in size" v-if="key == active" class="m-b-15">
				<div class="form-group" style="margin: 0;">
					<select  v-model="size[key]">
						<option v-for="option in build_array_for_select2(field.items)" v-bind:value="option['id']">
							{{ option.text }}
						</option>
					</select>					
				</div>					
			</div>
			
			<label>{{ field.label }} size (px)</label>
			<div v-for="(val, key) in values" v-if="key == active">
				<div class="stm-row indention-sizes form-control-group">
					<div class="stm-col-3 text-center">
						<input type="number"  v-model="val.top" placeholder="Top">
					</div>
					<div class="stm-col-3 text-center">
						<input type="number"  v-model="val.right" placeholder="Right">
					</div>
					<div class="stm-col-3 text-center">
						<input type="number"  v-model="val.bottom" placeholder="Bottom">
					</div>
					<div class="stm-col-3 text-center">
						<input type="number"  v-model="val.left" placeholder="Left">
					</div>
				</div>
			</div>			
				
		</div>
	`,
}