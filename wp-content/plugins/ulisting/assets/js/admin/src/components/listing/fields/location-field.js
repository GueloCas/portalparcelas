import google from './maps/google-map'
import osm    from './maps/osm-map'

export default {
	props: ['attribute', 'class_name'],

	components: {
		'google-map': google,
		'osm-map'	: osm,
	},

	data() {
		return {
			map_data: null,
			type: '',
			place: null,
		}
	},

	created() {
		if ( typeof this.attribute ) {
			this.map_data 	= this.attribute?.map_data || null
			this.type		= this.map_data?.type
		}
	},

	methods: {
		update(key, value) {
			this.$emit('update', key, value)
		}
	},

	template: `
		<div class="row" :class="class_name">
			<div class="col-12">
				<google-map @update="update" v-if="type === 'google'" :content="map_data"></google-map> 
				<osm-map @update="update" v-else :content="map_data"></osm-map>
			</div>
		</div>
	`
}