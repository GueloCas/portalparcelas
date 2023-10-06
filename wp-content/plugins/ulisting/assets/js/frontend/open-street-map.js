Vue.component('open-street-map', {
    template: `<div v-bind:style='{"z-index" : index, width: width, height: height}' v-bind:id='id' ></div>`,

    props: {
        id: {
            default: 'stm-open-street-map'
        },
        width: {
            default: '100%'
        },
        height: {
            default: '400px'
        },
        zoom: {
            default: 10
        },
        callback_change: {
            default: null,
        },
        center: {
            default: {lat: 0, lng: 0}
        },
        set_center: {
            default: false
        },
        markers: {
            default: null
        },
        marker: {
            default: null
        },
        open_marker: {
            default: "click"
        },
        close_marker: {
            default: null
        },
        bounds: {
            default: false
        },
        cluster: {
            default: false
        },
        access_token: {
            default: '',
        },
        zoom_control: {
            default: true,
        },

        index: {
            default: 1,
        },
        map: {
            default: null,
        },
        open_map_by_hover: {
            default: 'yes',
        },
    },

    data: function () {
        return {
            index: 0,
            location: {
                address: '',
                lng: '',
                lat: '',
            },
            innerWidth: 0,
            osmMarkers: [],
            osm_bounds: null,
            fullscreen: false,
            hasAccess: false,
            array_markers: [],
            marker_cluster: null,
            setIntervalInitMap: null,

        }
    },

    mounted() {
        let vm = this;
        setTimeout(function () {
            vm.renderMap();
            vm.handleResize();
            vm.disableOSMScroll();
        }, 100)
    },

    methods: {
        renderMap: function () {
            let vm = this;
            vm.map = L.map(vm.id, {
                zoom: 10,
                zoomControl: vm.zoom_control,
                center: L.latLng(vm.center.lat, vm.center.lng),
            });
            vm.map.addLayer(vm.getLayer());

            if (vm.markers != null && vm.markers.length) {
                vm.setMarkers();
            }
            vm.$emit('change', vm.map);
            vm.hasAccess = true;
        },

        setMarkers: function () {
            let vm = this;
            vm.osmMarkers = [];
            vm.handleHover();
            setTimeout(function () {
                vm.map.invalidateSize();
            }, 1000);

            if (!vm.markers.length)
                return
            if (vm.bounds) {
                let bounds = null;
                vm.marker_cluster = L.markerClusterGroup({
                    iconCreateFunction: function (osm_cluster) {
                        return L.divIcon({html: '<div class="uListing-leaflet-cluster">' + osm_cluster.getChildCount() + '</div>'});
                    },
                });

                for (let i = 0; i < vm.markers.length; i++) {
                    let propertyMarker = '';
                    let osmMarker = vm.markers[i];
                    let markerCenter = L.latLng(osmMarker.lat, osmMarker.lng);

                    if (osmMarker.icon.url !== false) {
                        let markerImage = {
                            iconUrl: osmMarker.icon.url,
                            iconSize: [40, 40],
                            iconAnchor: [20, 50],
                            popupAnchor: [1, -50]
                        };

                        let markerOptions = {
                            riseOnHover: true
                        };

                        markerOptions.icon = L.icon(markerImage);
                        propertyMarker = L.marker(markerCenter, markerOptions);
                    } else {
                        propertyMarker = L.marker(markerCenter);
                    }

                    let position = L.latLng(osmMarker.lat, osmMarker.lng);
                    bounds = L.latLngBounds(position);

                    vm.marker_cluster.addLayer(propertyMarker);

                    vm.array_markers.push(propertyMarker);
                    if (vm.osm_bounds !== null) {
                        vm.osm_bounds.extend(propertyMarker.getLatLng());
                    } else {
                        vm.osm_bounds = L.latLngBounds(propertyMarker.getLatLng(), propertyMarker.getLatLng());
                    }

                    propertyMarker.bindPopup(osmMarker.html).on(vm.open_marker, function (e) {
                        let px = vm.map.project(e.target._popup._latlng);
                        px.y -= e.target._popup._container.clientHeight / 2;
                        vm.map.panTo(vm.map.unproject(px), {animate: true});
                        vm.map.invalidateSize();
                    });

                    vm.osmMarkers.push(osmMarker);
                }

                vm.map.addLayer(vm.marker_cluster);

                if (vm.osm_bounds.isValid()) {
                    if (vm.array_markers.length === 1) {
                        let markerCenter = vm.array_markers[0].getLatLng();
                        vm.map.setView(new L.LatLng(markerCenter.lat, markerCenter.lng), 10);
                    } else {
                        vm.map.fitBounds(vm.osm_bounds);
                    }
                }
            } else {
                let osmMarker = vm.markers[0];
                let markerCenter = L.latLng(osmMarker.lat, osmMarker.lng);
                L.marker(markerCenter).addTo(vm.map);
            }

        },

        clearMarker: function () {
            if (this.cluster && this.array_markers.length)
                this.map.removeLayer(this.marker_cluster);

            for (let i = 0; i < this.array_markers.length; i++) {
                this.map.removeLayer(this.array_markers[i]);
            }

        },

        getLayer: function () {
            let vm = this;
            let tLayer = null;

            if (vm.access_token !== '') {
                tLayer = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + vm.access_token, {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 18,
                        id: 'mapbox/streets-v11',
                        accessToken: 'your.mapbox.access.token'
                    }
                );
            } else {
                tLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                });
            }
            return tLayer;
        },

        disableOSMScroll: function () {
            let vm = this;
            vm.innerWidth = window.innerWidth;
            if (1025 > vm.innerWidth) {
                vm.map.scrollWheelZoom.disable();
            }
        },

        handleResize: function () {
            let vm = this;
            if (window.addEventListener) {
                window.addEventListener('resize', function () {
                    vm.disableOSMScroll();
                }, true);
            }
        },

        handleHover: function () {
            let vm = this;

            if(vm.open_map_by_hover === 'no') return;

            let timeOut = 300;
            let removeTime = 500;
            let timeOutZoom = 200;
            let inventories = document.querySelectorAll('.inventory-thumbnail-box');

            if (inventories.length === 0) {
                timeOut = 500;
                removeTime = 800;
                timeOutZoom = 300;
                inventories = document.querySelectorAll('.ulisting-thumbnail-box');
            }

            for (let i = 0; i < inventories.length; i++) {

                inventories[i].addEventListener('mouseover', function () {
                    let id = inventories[i].getAttribute('data-id');

                    for (let j = 0; j < vm.osmMarkers.length; j++) {
                        if (parseInt(vm.osmMarkers[j].id) === parseInt(id)) {

                            let px = vm.map.project({lat: vm.osmMarkers[j].lat, lng: vm.osmMarkers[j].lng});
                            px.y -= 50;
                            vm.map.panTo(vm.map.unproject(px), {animate: true});

                            setTimeout(function () {
                                vm.map.setZoom(13);
                                setTimeout(function () {
                                    vm.array_markers[j].openPopup();
                                }, timeOut);
                                vm.map.invalidateSize();
                            }, timeOutZoom);

                        } else {
                            vm.array_markers[j].closePopup();
                        }
                    }
                });

                inventories[i].addEventListener('mouseleave', function () {
                    setTimeout(function () {
                        for (let j = 0; j < vm.osmMarkers.length; j++) {
                            vm.array_markers[j].closePopup();
                        }
                    }, removeTime)
                });
            }
        },

        mapPagination: function (value) {

            let vm = this;
            let timeOut = 400;
            let zoomTimeout = 200;
            let max = vm.osmMarkers.length;
            let inventories = document.querySelectorAll('.inventory-thumbnail-box');

            vm.index += parseInt(value);

            if(inventories.length === 0){
                timeOut = 500;
                zoomTimeout = 300;
            }

            if(vm.index < 0) vm.index = max - 1;
            if(vm.index >= max) vm.index = 0;

            for(let i = 0 ; i < max; i++){
                if(vm.index === i) {

                    let px = vm.map.project({lat: vm.osmMarkers[i].lat, lng: vm.osmMarkers[i].lng});
                    px.y -= 50;
                    vm.map.panTo(vm.map.unproject(px), {animate: true});

                    setTimeout(function () {
                        vm.map.setZoom(13);
                        setTimeout(function () {
                            vm.array_markers[i].openPopup();
                            vm.map.invalidateSize();
                        }, timeOut)
                    }, zoomTimeout);

                }else{
                    vm.array_markers[i].closePopup();
                }
            }
        },

        changeZoom: function (value, type) {
            let vm = this;
            value = parseInt(value);
            let zoom = vm.map.getZoom();
            zoom = parseInt(zoom);

            if (!(zoom === 0 || zoom === 20)) {
                zoom += value;
                vm.map.setZoom(zoom);
                return true;
            }

            if ((zoom === 20 && type === 'minus') || (zoom === 0 && type === 'plus')) {
                zoom += value;
                vm.map.setZoom(zoom);
                return true;
            }
        },

        openFullScreen: function () {
            let vm = this;
            vm.fullscreen = !vm.fullscreen;

            vm.map.invalidateSize();
            setTimeout(function () {
                vm.map.invalidateSize();
            }, 500);
        },
    },

    watch: {
        markers: {
            handler(val) {
                this.clearMarker();
                this.array_markers = [];
                this.setMarkers();
            },
            deep: true
        },
    }

});