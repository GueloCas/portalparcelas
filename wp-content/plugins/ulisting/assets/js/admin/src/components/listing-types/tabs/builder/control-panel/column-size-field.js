export default {
	data: function () {
		return {
			active	: "extra_large",
			icons	: {
				extra_large	: "fa fa-desktop",
				large		: "fa fa-tablet transform-rotate-90",
				medium		: "fa fa-tablet",
				small		: "fa fa-mobile transform-rotate-90",
				extra_small	: "fa fa-mobile",
			},
			size	: {
				large		: 0,
				medium		: 0,
				small		: 0,
				extra_small	: 0,
				extra_large	: 0,
			}
		}
	},
	mounted: function () {
		if(typeof this.data[this.field.name] != "undefined")
			this.size = this.data[this.field.name];
	},
	methods:{
		set_active: function(active){
			this.active = active;
		},
		change: function(){
			this.data[this.field.name] =  JSON.parse(JSON.stringify(this.size));
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
		field: { default: [] }
	},

	watch:{
		size:{
			handler(val){
				this.change()
			},
			deep: true
		}
	},

	activate: function() {

	},

	template:`
		<div class="form-group p-t-15">
			<div class="stm-row">
				<div class="stm-col-12">
					<label>{{field.label}}</label>
				</div>
				<div class="stm-col-12 ulisting-builder-device-width">
					<button v-for="(val, key) in size" @click="set_active(key)" class="btn" v-bind:class="{'active': active==key}" type="button"><i v-bind:class="icons[key]"></i></button>	
				</div>
			</div>
						
			<div v-for="(val, key) in size" v-if="key == active">
				<ulisting-select2 :options='build_array_for_select2(field.items)'
						 :key="size[key]+'_'+key"
						 v-model="size[key]"
						 :placeholder="'Select size'"
						 class="ulisting-select">
				</ulisting-select2>
			</div>		
		</div>
	`,
}