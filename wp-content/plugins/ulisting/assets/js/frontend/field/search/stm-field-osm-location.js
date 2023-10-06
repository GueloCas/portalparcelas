var $ = jQuery;

export default {
    props: {
        id: {
            default: 'autocomplete'
        },
        callback_change: {
            default: null
        },
        map: {
            default: null,
        },
        value: {
            type: Object,
            default: {
                address: "",
                lat: 0,
                lng: 0
            }
        },
        icon_url: {
            default: '',
        },
        name: {
            default: null
        },
        placeholder: {
            type: String,
            default: 'Enter your address'
        },
        country: {
            type: [String, Array],
            default: null
        },
        enableGeolocation: {
            type: Boolean,
            default: false
        },
        geolocationOptions: {
            type: Object,
            default: null
        },
        attribute_name: {
            default: null
        },
        access_token: {
            default: '',
        }
    },
    data: function () {
        return {
            query: '',
            setIntervalInitAutocomplete: null,
            autocomplete: null,
            autocompleteText: '',
            current_position: null,
            geolocation: {
                geocoder: null,
                loc: null,
                position: null
            },
            ajax_url: 'https://nominatim.openstreetmap.org/search',
        }
    },

    mounted() {
        let vm = this;
        setTimeout(function () {
            vm.renderAutoComplete();
        }, 1000);
    },

    methods: {
        renderAutoComplete: function () {
            let vm = this;
            vm.query = '#' + vm.id;
            $(vm.query).autocomplete({
                source: function (req, res) {
                    $.get(vm.ajax_url, {format: 'json', q: req.term,}, function (result) {
                        if (!result.length) {
                            res([{value: '', label: 'No results found'}]);
                            return;
                        }
                        res(result.map(function (p) {
                            return {
                                latitude: p.lat,
                                longitude: p.lon,
                                label: p.display_name,
                                value: p.display_name,

                            };
                        }));
                        $('.ui-autocomplete').width($(vm.query).parent().width());
                        this.autocompleteText = $(vm.query).val();
                    }, 'json');
                },

                select: function (e, placeFormat) {
                    vm.value = {
                        address: placeFormat.item.value,
                        lat: placeFormat.item.latitude,
                        lng: placeFormat.item.longitude,
                    };

                    vm.onChange();
                    vm.$emit('input', vm.value);
                    vm.callback_change(vm.attribute_name);
                }
            });
        },

        onChange() {
            this.$emit('change', this.autocompleteText);
        },

        findMyLocation: function () {
            let vm = this;

            if (location.protocol === 'https:') {
                vm.map.on('locationfound', vm.onLocationFound);
                vm.map.on('locationerror', vm.onLocationError);
                setTimeout(function () {
                    vm.map.locate({setView: true, maxZoom: 16});
                }, 1000);
            } else {
                jQuery.getJSON("http://ipinfo.io", function(ipinfo){
                    vm.renderLocation(ipinfo);
                });
            }
        },

        renderLocation: function(info){

            let vm = this;
            let latLng = info.loc.split(",");

            vm.$http.get(vm.ajax_url + '?format=json&q=' + latLng[0] + '+' + latLng[1]).then(function (result) {
                if (result.body.length === 0)
                    return false;

                let body = result.body[0];
                if (result.status === 200 && body) {

                    $(vm.query).val(body.display_name);
                    let value = {
                        lat: body.lat,
                        lng: body.lon,
                        address: body.display_name,
                    };

                    vm.onChange();
                    vm.$emit('input', value);
                    vm.callback_change('location');

                    if (vm.current_position)
                        vm.map.removeLayer(vm.current_position);

                    let customIcon = L.icon({iconUrl: vm.icon_url});
                    vm.current_position = L.marker({lat: body.lat, lng: body.lon}, {icon: customIcon}).addTo(vm.map);

                    vm.map.panTo(new L.LatLng(body.lat, body.lon));
                    vm.map.setZoom(vm.map.getZoom());

                }
            });
        },

        onLocationFound: function (e) {
            let vm = this;

            vm.$http.get(vm.ajax_url + '?format=json&q=' + e.latlng.lat + '+' + e.latlng.lng).then(function (result) {

                if (result.body.length === 0)
                    return false;

                let body = result.body[0];

                if (result.status === 200 && body) {

                    $(vm.query).val(body.display_name);
                    let value = {
                        lat: body.lat,
                        lng: body.lon,
                        address: body.display_name,
                    };
                    vm.onChange();
                    vm.$emit('input', value);
                    vm.callback_change('location');

                    if (vm.current_position)
                        vm.map.removeLayer(vm.current_position);


                    let customIcon = L.icon({iconUrl: vm.icon_url});

                    vm.current_position = L.marker(e.latlng, {icon: customIcon}).addTo(vm.map);
                }
            });


        },

        onLocationError: function (e) {
            alert(e.message);
        },
    }
}