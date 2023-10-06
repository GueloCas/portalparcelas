Vue.component('stm-listing-map', {
	data: function(){
		return {}
	},
	created() {
		this.$emit('exists-map', true);
	},
	methods:{
	},
	props:{
		center:{
			default:{lat:0, lng:0}
		},
		markers:{
			default:[]
		},
		polygon:{
			default:{
				is_update: false,
				paths: [],
				draggable: false,
				editable: false,
				strokeColor: '#0078ff',
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillColor: '#0078ff',
				fillOpacity: 0.35
			}
		}
	},
	watch:{
		polygon:function(val) {

		}
	}
});