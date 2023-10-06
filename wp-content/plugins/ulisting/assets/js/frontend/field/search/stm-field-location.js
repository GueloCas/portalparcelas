const ADDRESS_COMPONENTS = {
    subpremise: 'short_name',
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    administrative_area_level_2: 'long_name',
    country: 'long_name',
    postal_code: 'short_name'
};
const CITIES_TYPE = ['locality', 'administrative_area_level_3'];
const REGIONS_TYPE = ['locality', 'sublocality', 'postal_code', 'country',
    'administrative_area_level_1', 'administrative_area_level_2'];

export default {
    data() {
        return {
            checkTimes: 0,
            noFound: true,
            hasAccess: false,
            checkTimer: null,
            autocomplete: null,
            autocompleteText: '',
            setIntervalInitMap: null,
            geolocation: {
                geocoder: null,
                loc: null,
                position: null
            },
            ajax_url: 'https://nominatim.openstreetmap.org/search',
        }
    },
    created(){

    },
    mounted: function () {
        var vm = this;
        this.setIntervalInitAutocomplete = setInterval(function () {
            if (googleApiLoad) {
                vm.initAutocomplete();;
            }
        }, 1000);
    },

    methods: {
        initAutocomplete: function () {
            var vm = this;
            const options = {};
            if (vm.types) {
                options.types = [vm.types];
            }
            if (vm.country) {
                options.componentRestrictions = {
                    country: vm.country
                };
            }

            vm.autocomplete = new google.maps.places.Autocomplete(
                document.getElementById(vm.id),
                options
            );

            vm.autocomplete.addListener('place_changed', vm.onPlaceChanged);
            window.clearInterval(vm.setIntervalInitAutocomplete);
        },

        onPlaceChanged: function () {
            var place = this.autocomplete.getPlace();
            if (!place.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                this.$emit('no-results-found', place, this.id);
                return;
            }

            if (place.address_components !== undefined) {
                // return returnData object and PlaceResult object
                this.$emit('placechanged', this.formatResult(place, document.getElementById(this.id).value), place, this.id);
                // update autocompleteText then emit change event
                this.autocompleteText = document.getElementById(this.id).value;
                this.onChange();
            }

            var placeFormat = this.formatResult(place, document.getElementById(this.id).value);
            this.value = {
                address: (placeFormat.route) ? placeFormat.route : place.formatted_address,
                lat: placeFormat.latitude,
                lng: placeFormat.longitude,
            };

            this.$emit('input', this.value);
            this.callback_change(this.attribute_name);
            this.watchForChange();
        },

        startCheck: function () {
            let vm = this;
            this.removeClass('uListing-empty-location');
            let inp = document.getElementById(vm.id),
                nodes = document.querySelectorAll('.pac-container'),
                value = inp.value;
            setTimeout(function () {
                if (value && vm.autocompleteText !== value) {
                    let len = null;
                    if (nodes.length > 0)
                        len = nodes[nodes.length - 1].querySelectorAll('.pac-item').length;
                    else
                        len = document.querySelectorAll('.pac-item').length;
                    vm.noFound = len > 0;
                } else if (value === '') {
                    vm.noFound = true;
                }
            }, 100);

        },

        watchForInput: function() {
            let vm = this;
            setTimeout(function () {
                vm.startCheck();
            }, 200)
        },

        watchForChange: function(){
            this.removeClass('uListing-empty-location');
            this.removeClass('uListing-no-results-location');
        },

        removeClass: function(className){
            setTimeout(function () {
                let name = '.' + className;
                let element = document.querySelector(name);
                if( element )
                    element.classList.remove(className);

            }, 1)
        },

        onFocus() {
            this.biasAutocompleteLocation();
            this.$emit('focus');
        },

        onBlur() {
            this.$emit('blur');
        },

        onChange() {
            this.$emit('change', this.autocompleteText);
        },

        onKeyPress(event) {
            this.$emit('keypress', event);
        },

        onKeyUp(event) {
            this.$emit('keyup', event);
        },

        clear() {
            this.autocompleteText = ''
        },

        focus() {
            this.$refs.autocomplete.focus()
        },

        blur() {
            this.$refs.autocomplete.blur()
        },

        update(value, callback = null) {
            this.autocompleteText = value;

            if(typeof callback === "function") callback();
        },

        updateCoordinates(value, callback = null) {
            if (!value && !(value.lat || value.lng)) return;
            if (!this.geolocation.geocoder) this.geolocation.geocoder = new google.maps.Geocoder();

            this.geolocation.geocoder.geocode({'location': value}, (results, status) => {
                if (status === 'OK') {
                    results = this.filterGeocodeResultTypes(results);
                    if (results[0]) {
                        this.$emit('placechanged', this.formatResult(results[0]), results[0], this.id);
                        this.update(results[0].formatted_address, callback);
                    } else {
                        this.$emit('error', 'no result for provided coordinates');
                    }
                } else {
                    this.$emit('error', 'error getting address from coords');
                }
            })
        },

        geolocate() {
            this.updateGeolocation((geolocation, position) => {
                this.updateCoordinates(geolocation)
            })
        },

        updateGeolocation(callback = null) {
            if (navigator.geolocation) {
                var options = {};
                if (this.geolocationOptions) Object.assign(options, this.geolocationOptions);
                navigator.geolocation.getCurrentPosition(position => {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    this.geolocation.loc = geolocation;
                    this.geolocation.position = position;
                    if (callback) callback(geolocation, position);
                }, err => {
                    this.$emit('error', 'Cannot get Coordinates from navigator', err);
                }, options);
            }
        },

        biasAutocompleteLocation() {
            if (this.enableGeolocation) {
                this.updateGeolocation((geolocation, position) => {
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    this.autocomplete.setBounds(circle.getBounds());
                })
            }
        },

        formatResult(place, value) {
            var returnData = {};
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (ADDRESS_COMPONENTS[addressType]) {
                    var val = place.address_components[i][ADDRESS_COMPONENTS[addressType]];
                    returnData[addressType] = val;
                }
            }
            returnData['route'] = value;
            returnData['latitude'] = place.geometry.location.lat();
            returnData['longitude'] = place.geometry.location.lng();
            return returnData
        },

        filterGeocodeResultTypes(results) {
            if (!results || !this.types) return results;
            var output = [];
            var types = [this.types];
            if (types.includes('(cities)')) types = types.concat(CITIES_TYPE);
            if (types.includes('(regions)')) types = types.concat(REGIONS_TYPE);
            for (var r of results) {
                for (var t of r.types) {
                    if (types.includes(t)) {
                        output.push(r);
                        break;
                    }
                }
            }
            return output;
        },

        findMyLocation: function(){
            let vm = this;
            if(navigator.geolocation){
                if (location.protocol !== 'https:') {
                    jQuery.getJSON("http://ipinfo.io", function(ipinfo){
                        let latLng = ipinfo.loc.split(",");
                        vm.renderMyLocationHttp(latLng);
                    });
                }else{
                    navigator.geolocation.getCurrentPosition(vm.renderMyLocation,vm.myLocationFail,{timeout:10000});
                }
            }
        },

        myLocationFail: function(){
            alert("The browser couldn't detect your location");
        },

        renderMyLocation: function(latLng){
            let vm = this;
            latLng = [latLng.coords.latitude, latLng.coords.longitude];
            vm.updateCoordinates({lat: latLng[0], lng: latLng[1]}, function () {
                if(document.getElementById(vm.id)){
                    document.getElementById(vm.id).value = vm.autocompleteText;
                    vm.onChange();

                    let value = {
                        address: vm.autocompleteText,
                        lat: latLng[0],
                        lng: latLng[1],
                    };

                    vm.$emit('input', value);
                    vm.callback_change('location');
                }
            });


            if(vm.map){
                vm.map.setZoom(vm.map.getZoom());
                vm.map.setCenter(vm.getPoint(latLng));
                vm.getMarker(latLng);
            }
        },

        renderMyLocationHttp: function(latLng){
            let vm = this;
            vm.$http.get(vm.ajax_url + '?format=json&q=' + latLng[0] + '+' + latLng[1]).then(function (result) {
                if (result.body.length === 0)
                    return false;

                let body = result.body[0];
                if (result.status === 200 && body) {

                    if(document.getElementById(vm.id)) {
                        document.getElementById(vm.id).value = body.display_name;
                    }

                    let value = {
                        lat: body.lat,
                        lng: body.lon,
                        address: body.display_name,
                    };

                    vm.onChange();
                    vm.$emit('input', value);
                    vm.callback_change('location');

                    if(vm.map){
                        vm.map.setZoom(vm.map.getZoom());
                        vm.map.setCenter(vm.getPoint(latLng));
                        vm.getMarker(latLng);
                    }
                }
            });
        },

        getPoint: function (latLng) {
            return new google.maps.LatLng( latLng[0], latLng[1]);
        },

        getMarker: function (latLng) {
            let vm = this;
            return new google.maps.Marker({
                position: vm.getPoint(latLng),
                map: vm.map,
                icon: vm.icon_url,
                shape: vm.getShape(),
                title: '',
                zIndex: 999999,
                image:'',
                price:'',
                category:'',
                action:'',
                link:'' ,
                infoWindowIndex : 99,
            });
        },

        getShape: function () {
            return {
                coord: [1, 1, 1, 38, 38, 59, 59 , 1],
                type: 'poly'
            };
        },
    },

    props: {
        icon_url: {
            default: '',
        },
        map: {
            default: null,
        },
        id: {
            default: 'autocomplete'
        },
        callback_change: {
            default: null
        },
        value: {
            type: Object,
            default: {
                address: "",
                lat: 0,
                lng: 0
            }
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
        }
    },
    watch: {
        autocompleteText: function (newVal, oldVal) {
            this.$emit('inputChange', {newVal, oldVal}, this.id);
        },
        country: function (newVal, oldVal) {
            this.autocomplete.setComponentRestrictions({
                country: this.country === null ? [] : this.country
            });
        }
    }
}