export default {
	data: function () {
		return {
			active:"extra_large",
			icons:{
				extra_large:"fa fa-desktop",
				large:"fa fa-tablet transform-rotate-90",
				medium:"fa fa-tablet",
				small:"fa fa-mobile transform-rotate-90",
				extra_small:"fa fa-mobile",
			},
			padding:{
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
			this.padding = this.data[this.field.name];
	},
	methods:{
		set_active: function(active){
			this.active = active;
		},
		change: function(){
			this.data[this.field.name] = this.padding;
			this.data.__ob__.dep.notify()
		}
	},
	props: {
		id:{
			default:0
		},
		data: { default: "" },
		field: { default: ""}
	},
	watch:{
		padding:{
			handler(val){
				this.change()
			},
			deep: true
		}
	},
	template:`
		<div class="form-group p-t-15 indention-field">
			<label for="#" style="margin-bottom: 15px !important; font-size: 16px !important;">{{ field.label }}</label>
			<div class="stm-row  m-b-15">
				<div class="stm-col-12">
					<label>Device</label>
				</div>
				<div class="stm-col-12 ulisting-builder-device-width">
					<button v-for="(val, key) in padding" @click="set_active(key)" class="btn" v-bind:class="{'active': active==key}" type="button"><i v-bind:class="icons[key]"></i></button>	
				</div>
			</div>
						
			<label>{{ field.label }} size (px)</label>	
			<div v-for="(val, key) in padding" v-if="key == active">
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