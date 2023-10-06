export default {
	data: function () {
		return {
		}
	},
	mounted: function () {

	},
	methods:{
		setСontrolData: function(items, index) {
			EventBus.$emit("ulisting_builder_set_control_data_"+ this.id, items, index);
		},
		remove: function(items, index){
			EventBus.$emit("ulisting_builder_remove_"+ this.id, items , index);
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
			<draggable class="ulisting-builder-draggabble ulisting-builder-draggabble-row inner" :list="element.columns" :options="{group:{ name: 'element-columns'}, handle:'.handle'}" >
				
					<div v-for="(col, colIndex) in element.columns" 
					    class="ulisting-builder-draggabble-items stm-col"
					    v-bind:class="'stm-col-xl-' + col.params.size.extra_large + 
									   ' stm-col-gl-' + col.params.size.large + 
									   ' stm-col-md-' + col.params.size.medium + 
									   ' stm-col-sm-' + col.params.size.small + 
									   ' stm-col-' + col.params.size.extra_small">
						<div class="ulisting-builder-draggabble-items-btn-panel">
							<span  class="btn btn-default btn-xs" @click="setСontrolData(element.columns, colIndex)"><i class="fa fa-pencil"></i></span>
							<span class="btn btn-info btn-xs handle"><i class="fa fa-arrows"></i></span>
						</div>

						<!-- element -->
						<draggable :list="col.elements" 
								   :options="{group:{
													name: 'elements',
													put: function (to, from, dragEl, evt) {
													  var type = dragEl.getAttribute('data-type')
													  var module = dragEl.getAttribute('data-module')
													  if( module != 'columns' && (type == 'attribute' || type == 'element' || type == 'inventory_element' || type == 'basic') )
														return true;
													  return false;
													}
												},
												handle:'.handle'}"
								   class="ulisting-builder-draggabble uListing-col-name">
							<component v-for="(element, elementIndex) in col.elements" :key="elementIndex" 
									   v-bind:is="'ulisting-builder-module-'+element.module"
									   :id="id"
									   :data-type="element.type"
									   :element="element"
									   :items="col.elements"
									   :index="elementIndex" >
							</component>
						</draggable>
					</div>
				</draggable>
		</div>
	`,
}