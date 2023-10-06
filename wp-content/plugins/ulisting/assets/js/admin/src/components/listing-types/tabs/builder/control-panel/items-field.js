export default {
	data: function () {
		return {
			open_item: null
		}
	},
	mounted: function () {

	},
	methods:{
		add_item: function(){
			var vm = this;
			vm.data[vm.field.name].push({
				title: "Tab Title",
				is_active: false,
				elements: []
			})
		},
		remove_item: function(index){
			var vm = this;
			if(vm.data[vm.field.name].length > 1)
				vm.data[vm.field.name].splice(index, 1);
		},
		open: function(index){
			if(this.open_item != index){
				this.open_item = index;
				return
			}
			this.open_item = null;
		}
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
			</div>
				
			<div class="stm-row">
				<draggable v-model="data[field.name]" class="ulisting-draggable-panel stm-col" :options="{group:'items', handle:'.handle'}">
					<div class="ulisting-draggable-panel-items" v-for="(element, index) in data[field.name]" :key="index">
						<div class="ulisting-draggable-panel-items-top min">
							<div class="title">
								<span class="handle min">
									<i class="fa fa-arrows"></i>
								</span>
								<p @click="open(index)" class="cursor-pointer"> {{element.title}} </p>
							</div>
							<div class="action">
								<span class="btn" @click="remove_item(index)">
									<i class="fa fa-trash"></i>
								</span>
							</div>
						</div>
						<div class="ulisting-draggable-panel-items-inside" v-if="open_item == index">
							<div class="panel-custom p-t-15 p-b-15 p-l-15 p-r-15">
								<div class="stm-row">
									<div class="stm-col">
										<input  type="text" v-model="element.title">
									</div>
								</div>
							</div>
							<div style="clear:both;"></div>
						</div>
					</div>
				</draggable>

			</div>	
			
			<div class="text-center p-t-15">
				<button @click="add_item" type="button" class="btn btn-success">Add item</button>
			</div>
		
		</div>
	`,
}