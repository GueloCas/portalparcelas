export default {
	data: function () {
		return {
		}
	},
	mounted: function () {

	},
	methods:{
		setСontrolData: function(items, index) {

			EventBus.$emit("ulisting_builder_test"+this.id);
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
		<div class="ulisting-builder-draggabble-items element" :data-type="element.type">
			<div class="ulisting-builder-draggabble-items-btn-panel">
				<span  class="btn btn-default btn-xs" @click="setСontrolData(items, index)"><i class="fa fa-pencil"></i></span>
				<span class="btn btn-info btn-xs handle"><i class="fa fa-arrows"></i></span>
				<span class="btn btn-danger btn-xs" @click="remove(items, index)"><i class="fa fa-trash"></i></span>
			</div>
			<div class="content">
				<span class="title"> 
					{{element.title}} <span class="m-l-5" v-if="element.params.type == 'html' && element.params.title"> {{element.params.title}}</span>
				</span>
			</div>
		</div>
	`,
}