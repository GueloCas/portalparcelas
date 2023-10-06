export default {
	data: function () {
		return {
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
		<div class="ulisting-builder-draggabble-items">
			<div class="ulisting-builder-draggabble-items-btn-panel">
				<span  class="btn btn-default btn-xs" @click="setСontrolData(items, index)"><i class="fa fa-pencil"></i></span>
				<span class="btn btn-info btn-xs handle"><i class="fa fa-arrows"></i></span>
				<span class="btn btn-danger btn-xs" @click.prevent="remove(items, index)"><i class="fa fa-trash"></i></span>
			</div>
			<div class="ulisting-thumbnail-box">
				<draggable :list="element.elements_top" 
						   :options="{group:{
											name: 'elements',
											put: function (to, from, dragEl, evt) {
											  var type = dragEl.getAttribute('data-type')
											  if(type == 'inventory_element' || type == 'basic' | type == 'element' || type == 'attribute')
												return true;
											  return false;
											}
										},
										handle:'.handle'}"
						   class="ulisting-builder-draggabble">
						  
						<component v-for="(innerElement, elementIndex) in element.elements_top" 
								   :key="elementIndex"
								   v-bind:is="'ulisting-builder-module-'+innerElement.module"
								   :id="id"
								   :element="innerElement"
								   :items="element.elements_top"
								   :index="elementIndex" >
						</component>
				</draggable>
				<div class="ulisting-thumbnail-box-feature-image"><span>featured image</span></div>	
				<draggable :list="element.elements_bottom" 
					   :options="{group:{
										name: 'elements',
										put: function (to, from, dragEl, evt) {
										  var type = dragEl.getAttribute('data-type')
										  if(type == 'inventory_element' || type == 'basic' | type == 'element' || type == 'attribute')
											return true;
										  return false;
										}
									},
									handle:'.handle'}"
					   class="ulisting-builder-draggabble">
					  
					<component v-for="(innerElement, elementIndex) in element.elements_bottom" 
							   v-bind:is="'ulisting-builder-module-'+innerElement.module"
							   :id="id"
							   :key="elementIndex"
							   :element="innerElement"
							   :items="element.elements_bottom"
							   :index="elementIndex" >
					</component>
			</draggable>
			</div>	
		</div>
	`,
}