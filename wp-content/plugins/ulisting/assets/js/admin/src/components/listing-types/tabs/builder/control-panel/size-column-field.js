export default {
	methods:{
		isActive:function(id) {
			return this.data[this.field.name] == id;
		},
		setActive:function(id) {
			this.data[this.field.name] = id;
			this.columnChanged()
		},
		columnChanged() {
			var vm = this;

			if(typeof vm.element.columns == "undefined")
				return;

			var column = vm.data[vm.field.name]
			if(vm.element.columns.length < column) {
				var i = vm.element.columns.length;
				while (i < column) {
					EventBus.$emit("ulisting_builder_add_column_"+vm.id, vm.element);
					i++;
				}
			}else{
				var i = vm.element.columns.length ;
				var elements = [];
				while (i > column) {
					elements = elements.concat(vm.element.columns[i - 1].elements);
					EventBus.$emit("ulisting_builder_remove_"+vm.id, vm.element.columns, i - 1);
					i--;
				}
				vm.element.columns[vm.element.columns.length - 1].elements = vm.element.columns[vm.element.columns.length - 1].elements.concat(elements)
			}
		}
	},
	props: {
		id:{
			default:0
		},
		element: { default: [] },
		data: { default: [] },
		field: { default: [] }
	},
	watch:{
		data: function(val){
			this.$emit('input', val)
		},
	},
	template:`
		<div class="panel-custom p-t-15 p-b-15">
			<label>{{field.label}}</label>
			<div class="stm-row ulisting-column-field-panel">
				<div class="stm-col-6">
					<div class="column" v-bind:class="{active: isActive(1)}" @click="setActive(1)">
						<span></span> 
					</div>
				</div>
				<div class="stm-col-6">
					<div class="column" v-bind:class="{active: isActive(2)}" @click="setActive(2)">
						<span></span> 
						<span></span> 
					</div>
				</div>
				<div class="stm-col-6">
					<div class="column" v-bind:class="{active: isActive(3)}" @click="setActive(3)">
						<span></span> 
						<span></span> 
						<span></span> 
					</div>
				</div>
				<div class="stm-col-6">
					<div class="column" v-bind:class="{active: isActive(4)}" @click="setActive(4)">
						<span></span> 
						<span></span> 
						<span></span> 
						<span></span> 
					</div>
				</div>
				<div class="stm-col-6">
					<div class="column" v-bind:class="{active: isActive(6)}" @click="setActive(6)">
						<span></span> 
						<span></span> 
						<span></span> 
						<span></span> 
						<span></span> 
						<span></span> 
					</div>
				</div>
			</div>
		</div>
	`,
}