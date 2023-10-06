Vue.component('stm-location-component', {
    data: function () {
        return {
            place: null,
            marker: {
                lat: 0,
                lng: 0,
            }
        }
    },
    mounted: function () {
        if (typeof this.value.latitude == 'undefined' || typeof this.value.longitude == 'undefined')
            this.value = {address: '', latitude: 0, longitude: 0};

        this.value.latitude = Number(this.value.latitude)
        this.value.longitude = Number(this.value.longitude)

        this.setPositionMrkers(this.value.latitude, this.value.longitude);
    },
    methods: {
        placeChanged: function (value) {
            this.value = value
            this.setPositionMrkers(this.value.latitude, this.value.longitude)
            this.updateValue();
        },
        updateValue: function () {
            this.$emit('input', this.value);
        },
        clickMap(e) {
            this.setPositionMrkers(e.latLng.lat(), e.latLng.lng())
            this.geocoder(e.latLng.lat(), e.latLng.lng())
        },
        setPositionMrkers(lat, lng) {
            this.marker.lat = lat;
            this.marker.lng = lng;
        },
        geocoder(lat, lng) {
            var vm = this;
            var LatLng = new google.maps.LatLng({lat: parseFloat(lat), lng: parseFloat(lng)});
            var geocoder = new google.maps.Geocoder;
            geocoder.geocode({'location': LatLng}, function (results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        var postal_code = "";
                        vm.address = [
                            (results[0].address_components[0] && results[0].address_components[0].short_name || ''),
                            (results[0].address_components[1] && results[0].address_components[1].short_name || ''),
                            (results[0].address_components[2] && results[0].address_components[2].short_name || '')
                        ].join(' ');

                        for (var i = 0; i < results.length; i++) {
                            for (var j = 0; j < results[i].address_components.length; j++) {
                                for (var k = 0; k < results[i].address_components[j].types.length; k++) {
                                    if (results[i].address_components[j].types[k] == "postal_code") {
                                        postal_code = results[i].address_components[j].short_name;
                                    }
                                }
                            }
                        }

                        vm.value = {
                            address: vm.address,
                            latitude: lat,
                            longitude: lng,
                            postal_code: postal_code
                        };
                        vm.updateValue();

                    } else {
                        window.alert('No results found');
                    }
                } else {
                    window.alert('Geocoder failed due to: ' + status);
                }
            });

        }
    },
    props: {
        value: {
            default: {
                address: '',
                latitude: 0,
                longitude: 0,
                postal_code: ''
            }
        },
        address: {
            type: String,
            default: ''
        }
    }
})





