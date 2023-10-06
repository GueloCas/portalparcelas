export default {
	data: function () {
		return {
			open_tab:0
		}
	},
	mounted: function () {

	},
	methods:{
		setСontrolData: function(items, index) {

			EventBus.$emit("ulisting_builder_set_control_data_"+this.id, items, index);
		},
		remove: function(items, index){
			EventBus.$emit("ulisting_builder_remove_"+this.id, items , index);
		}
	},
	props: {
		id:{
			default:0
		},
		element: {
			default: []
		},
		items: {
			default: []
		},
		index: {
			default: null
		},
	},
	watch:{
		data: {
			handler(val){

			},
			deep: true
		}
	},
	template:`
		<div class="ulisting-builder-draggabble-items" data-module="tabs">
			<div class="ulisting-builder-draggabble-items-btn-panel">
				<span  class="btn btn-default btn-xs" @click="setСontrolData(items, index)"><i class="fa fa-pencil"></i></span>
				<span class="btn btn-info btn-xs handle"><i class="fa fa-arrows"></i></span>
				<span class="btn btn-danger btn-xs" @click.prevent="remove(items, index)"><i class="fa fa-trash"></i></span>
			</div>
			
			<div class="ulisting-builder-tabs-panel">
				
				<ul class="nav nav-tabs">
				  <li v-for="(item, index) in element.params.items" class="nav-item" v-bind:class="{active:open_tab == index}">
					<a class="nav-link" @click="open_tab = index" href="javascript:void(0)">{{item.title}}</a>
				  </li>
				</ul>
				
				<div class="tab-content p-t-5 p-r-5  p-b-5 p-l-5 ">
				  <div v-for="(item, index) in element.params.items" v-if="open_tab == index" class="tab-pane show active">
				    <draggable class="ulisting-builder-draggabble" 
							   :list="item.elements"
							   :options="{ group:{ name: 'elements', put:['elements','basic']  }, handle:'.handle'}" >
						<component v-for="(tab_element, tab_elementIndex) in item.elements" 
								   v-bind:is="'ulisting-builder-module-'+tab_element.module"
								   :id="id"
								   :key="tab_elementIndex"
								   :data-type="tab_element.type"
								   :element="tab_element"
								   :items="item.elements"
								   :index="tab_elementIndex" >
						</component>
					</draggable>
				   
				  </div>
				</div>
				
			</div>
		
		</div>
	`,
}