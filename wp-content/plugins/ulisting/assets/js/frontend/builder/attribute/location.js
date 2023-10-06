new Vue({
	el:"#ulisting_attribute_location_"+ulisting_attribute_location_data.id,
	data:{
		zoom:10,
		center: {
			address: "",
			lat: 0,
			lng: 0,
		},
		markers : [{
			id: "",
			html: "",
			lat: 0,
			lng: 0,
			icon:{
				url: "",
				scaledSize: { height: 50, width: 50}
			}
		}]
	},
	created(){
		if(typeof ulisting_attribute_location_data == "undefined")
			return;

		if(typeof ulisting_attribute_location_data.address != "undefined")
			this.center.address = ulisting_attribute_location_data.address;

		if(typeof ulisting_attribute_location_data.latitude != "undefined")
			this.center.lat = Number(ulisting_attribute_location_data.latitude) ;

		if(typeof ulisting_attribute_location_data.longitude != "undefined")
			this.center.lng = Number(ulisting_attribute_location_data.longitude);

		if(typeof ulisting_attribute_location_data.zoom != "undefined")
			this.zoom = Number(ulisting_attribute_location_data.zoom);

		this.markers[0].id   = ulisting_attribute_location_data.id;
		this.markers[0].lat  = this.center.lat;
		this.markers[0].lng  = this.center.lng;

		if(typeof ulisting_attribute_location_data.marker != "undefined") {
			this.markers[0].html = ulisting_attribute_location_data.marker.html;
			this.markers[0].icon = ulisting_attribute_location_data.marker.icon;
		}
	}
});


