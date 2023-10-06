let $ = jQuery;
Vue.component('stm-osm-location-component', {
    data: function () {
        return {
            osm: null,
            ajax_url: 'https://nominatim.openstreetmap.org/search',
            theMarker: null,
            postal_code: '',
            invalidateTimer: null
        }
    },
    mounted() {
        let vm = this;
        vm.renderMap();
        setTimeout(function () {
            vm.renderAutoComplete();
            window.addEventListener('load', function () {
                vm.invalidateTimer = setInterval(function () {
                    if(vm.osm !== null){
                        vm.osm.invalidateSize();
                        clearInterval(vm.invalidateTimer);
                    }
                }, 1000);
            })
        }, 2000)
    },
    methods: {
        renderMap: function () {
            let vm = this;

            if (typeof vm.value.latitude == 'undefined' || typeof vm.value.longitude == 'undefined')
                vm.value = {address: '', latitude: 0, longitude: 0};

            vm.value.latitude = Number(vm.value.latitude);
            vm.value.longitude = Number(vm.value.longitude);

            vm.osm = L.map('openstreetmap').setView([vm.value.latitude, vm.value.longitude], 5);
            setTimeout(function () {
                vm.osm.invalidateSize();
            }, 2000);
            let layer = vm.getLayer();

            vm.osm.addLayer(layer);

            let customIcon = L.icon({
                iconUrl: ulistingUrl + '/assets/css/images/marker-icon.png',
                shadowUrl: ulistingUrl + '/assets/css/images/marker-shadow.png',
            });

            L.marker([vm.value.latitude, vm.value.longitude], {icon: customIcon}).addTo(vm.osm);

            vm.osm.on('click', function (e) {
                vm.value.postal_code = '';
                vm.value.latitude = e.latlng.lat;
                vm.value.longitude = e.latlng.lng;
                vm.updateValue();

                let latLon = [vm.value.latitude, vm.value.longitude];

                if (vm.theMarker !== null) {
                    vm.osm.removeLayer(vm.theMarker);
                }

                vm.osm.setView([vm.value.latitude, vm.value.longitude]);
                vm.theMarker = L.marker(latLon).addTo(vm.osm);
            });
        },

        getLayer: function () {
            let vm = this;

            let tLayer = null;

            if (vm.access_token !== '') {
                tLayer = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=' + vm.access_token, {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 18,
                        id: 'mapbox.streets',
                        accessToken: vm.access_token
                    }
                );
            } else {
                tLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                });
            }

            return tLayer;
        },

        renderAutoComplete: function () {
            let vm = this;
            let query = '#uListing_listing_search';
            $(query).autocomplete({
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
                        $('.ui-autocomplete').width($(query).parent().width());
                        this.autocompleteText = $(query).val();
                    }, 'json');
                },

                select: function (e, placeFormat) {
                    placeFormat = placeFormat.item;
                    vm.value.postal_code = '';
                    vm.value.address = placeFormat.value;
                    vm.value.latitude = placeFormat.latitude;
                    vm.value.longitude = placeFormat.longitude;
                    vm.updateValue();

                    if (vm.theMarker !== null) {
                        vm.osm.removeLayer(vm.theMarker);
                    }

                    vm.osm.setView([vm.value.latitude, vm.value.longitude]);
                    vm.theMarker = L.marker([vm.value.latitude, vm.value.longitude]).addTo(vm.osm);
                }
            });
        },
        updateValue: function () {
            this.$emit('input', this.value);
        },
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
        },
        access_token: {
            default: '',
            type: String,
        },
    }
});