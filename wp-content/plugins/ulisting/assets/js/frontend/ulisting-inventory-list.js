new VueW3CValid({el: '#ulisting-inventory-list'});
new Vue({
    el: "#ulisting-inventory-list",
    data: {
        page: null,
        url: "",
        count: 0,
        map: null,
        matches: 0,
        user_id: 0,
        markers: [],
        icon_url: '',
        layout: false,
        query_data: [],
        field_show: [],
        preloader: false,
        url_update: true,
        listing_type_id: 0,
        search_form_type: "",
        listing_order_data: {},
        center_map: {lat: 0, lng: 0},
        polygon: { paths: [] },
        ulisting_inventory_map: {
            is_exists: false
        },
        listing_order: {
            order_by: null,
            order: null
        },
        paginate: {
            is_exists: false,
            page: 1,
            pageCount: 0,
        },
        filter: {
            show: false,
            is_exists: false,
            field_data: [],
            field_type: [],
            search_fields: []
        },
    },
    created() {
        if (typeof ulisting_inventory_list_data == "undefined")
            return;
        if (typeof ulisting_inventory_list_data.layout != "undefined")
            this.layout = ulisting_inventory_list_data.layout;

        if (typeof ulisting_inventory_list_data.listing_type_id != "undefined")
            this.listing_type_id = ulisting_inventory_list_data.listing_type_id;

        if (typeof ulisting_inventory_list_data.search_form_type != "undefined")
            this.search_form_type = ulisting_inventory_list_data.search_form_type;

        if (typeof ulisting_inventory_list_data.listing_type_id != "undefined")
            this.listing_type_id = ulisting_inventory_list_data.listing_type_id;

        if (typeof ulisting_inventory_list_data.listing_order_data != "undefined")
            this.listing_order_data = ulisting_inventory_list_data.listing_order_data;

        if (typeof ulisting_inventory_list_data.total_pages != "undefined")
            this.paginate.pageCount = ulisting_inventory_list_data.total_pages;

        if (typeof ulisting_inventory_list_data.query_data != "undefined")
            this.query_data = ulisting_inventory_list_data.query_data;

        if (typeof ulisting_inventory_list_data.markers != "undefined")
            this.markers = ulisting_inventory_list_data.markers;

        if (typeof ulisting_inventory_list_data.polygon != "undefined")
            this.polygon = ulisting_inventory_list_data.polygon;

        if (typeof ulisting_inventory_list_data.user_id != "undefined")
            this.user_id = ulisting_inventory_list_data.user_id;

        if (typeof ulisting_inventory_list_data.count != "undefined")
            this.count = ulisting_inventory_list_data.count;

        if (typeof ulisting_inventory_list_data.icon_url != "undefined")
            this.icon_url = ulisting_inventory_list_data.icon_url;

        if (typeof ulisting_inventory_list_data.matches != "undefined")
            this.matches = ulisting_inventory_list_data.matches;
    },
    mounted: function () {
        if (window.location.search && typeof check_saved_search === "function") {
            var url = removeParamUrl("current_page", window.location.search);
            url = removeParamUrl("layout", window.location.search);
            check_saved_search(this.user_id, url, this.listing_type_id)
        }
    },
    methods: {
        location_update: function (location) {
            this.center_map.lat = location.lat;
            this.center_map.lng = location.lng;
        },
        get_active_options: function (model, attribute) {
            var vm = this;
            var active_options = [];
            if (vm.filter.field_data.attribute_items[attribute]) {
                vm.filter.field_data.attribute_items[attribute].forEach(function (option) {
                    if (model.indexOf(option.value) > -1) {
                        active_options.push(option.name);
                    }
                });
                active_options = active_options.join(", ");
            }
            return active_options;
        },

        scroll_to_list: function () {
            const listing_list_panel = document.getElementsByClassName('scroll-panel-list');
            if (typeof listing_list_panel[0] !== "undefined") {
                let top = listing_list_panel[0].scrollTop;
                const scroll_interval = setInterval(function () {
                    if (top > 100) {
                        top -= 100;
                        listing_list_panel[0].scrollTop = top;
                    } else
                        scroll_interval_clear();
                }, 25);

                function scroll_interval_clear() {
                    clearInterval(scroll_interval);
                }
            } else {
                animateScrollTo(document.querySelector('body'));
            }
        },

        set_url: function (url) {
            this.url = url;
            this.paginate.page = 1;
            this.url = updateQueryStringParameter(this.url, 'current_page', this.paginate.page);
            if (this.listing_order.order_by && this.listing_order.order) {
                this.url = updateQueryStringParameter(this.url, 'order_by', this.listing_order.order_by);
                this.url = updateQueryStringParameter(this.url, 'order_type', this.listing_order.order);
            }
        },
        exists_filter(filter) {
            this.filter.is_exists = filter;
            this.get_filter_data();
        },
        exists_map(filter) {
            this.ulisting_inventory_map.is_exists = true;
        },

        set_filter_data(filter_data) {
            var vm = this;
            if (!vm.filter.search_fields.length)
                return;
            filter_data.forEach(function (item) {
                var key = Object.keys(item);
                var field = item[key[0]];
                if (field.items)
                    vm.filter.field_data.attribute_items[field.attribute_name] = field.items
            });
        },
        pagination_update(page) {
            this.page = page;
            this.paginate.page = page;
            this.url = updateQueryStringParameter(this.url, 'current_page', this.paginate.page);

            this.send_request();
        },
        set_order: function (order) {
            var vm = this;
            vm.listing_order.order_by = order.order_by;
            vm.listing_order.order = order.order;

            vm.url = updateQueryStringParameter(vm.url, 'order_by', vm.listing_order.order_by);
            vm.url = updateQueryStringParameter(vm.url, 'order_type', vm.listing_order.order);
        },
        get_filter_data: function () {
            var vm = this;
            this.$http.post("search-form/get-form-data",
                {
                    listing_type_id: vm.listing_type_id,
                    search_form_type: vm.search_form_type,
                    value: vm.query_data,
                    query_data: vm.query_data,
                    nonce: ulistingAjaxNonce
                }).then(function (response) {
                if (response.body.success) {
                    vm.filter.field_data = response.body.field_data;
                    vm.filter.field_type = response.body.field_type;
                    vm.filter.search_fields = response.body.search_fields;
                    vm.field_show = response.body.field_show;
                    vm.filter.show = true;
                }

            }).catch(error => {
                setTimeout(function () {
                    vm.get_filter_data();
                }, 3000)
            });
        },
        send_request() {
            const vm = this;

            if (typeof vm.$refs.filter !== "undefined") {
                vm.$refs.filter.change(null, true);
            }

            vm.preloader = true;
            let params = {map: false};
            let api_url = vm.url;
            this.paginate.page = this.page ? this.page : this.paginate.page;

            api_url = updateQueryStringParameter(api_url, 'listing_type', vm.listing_type_id);
            api_url = updateQueryStringParameter(api_url, 'search_form_type', vm.search_form_type);
            api_url = updateQueryStringParameter(api_url, 'current_page', vm.paginate.page);
            if (vm.layout)
                api_url = updateQueryStringParameter(api_url, 'layout', vm.layout);

            if (typeof ulisting_inventory_list_params !== "undefined")
                params.element_list = ulisting_inventory_list_params;

            if (this.ulisting_inventory_map.is_exists)
                params.map = true;
            this.$http.get("listing-type/list" + api_url, {params: params}).then(response => {

                vm.preloader = false;
                if (!response.body.success)
                    return;

                if (response.body.markers)
                    vm.markers = response.body.markers;

                if(response.body.hasOwnProperty('matches'))
                    vm.matches = response.body.matches;

                if(response.body.hasOwnProperty('count')){
                    vm.count = response.body.count;
                    setTimeout(function () {
                        let no_lists = document.querySelectorAll('.uListing-no-results');
                        if (no_lists.length > 0) {
                            for (let i = 0; i < no_lists.length; i++) {
                                if (vm.count > 0) {
                                    no_lists[i].style ? no_lists[i].style.display = 'none' : null;
                                } else {
                                    no_lists[i].style ? no_lists[i].style.display = 'block' : null;
                                }
                            }
                        }
                    }, 50);
                }


                let new_href = vm.url;
                if (vm.layout)
                    new_href = updateQueryStringParameter(new_href, 'layout', vm.layout);

                let url =  window.location.protocol + "//" + window.location.host + window.location.pathname + new_href;
                if ( history.pushState ) {
                    const changedUrl = this.replaceUrlParam(url, 'current_page', this.paginate.page);
                    history.pushState(null,null, changedUrl);
                }

                // history.pushState(null, null, new_href);
                var stm_listing_list_panel = document.getElementById('stm-listing-list-panel');

                if (stm_listing_list_panel) {
                    stm_listing_list_panel.innerHTML = response.body.html;
                    vm.scroll_to_list();
                }

                this.paginate.pageCount = response.body.total_pages;

                if (response.body.polygon)
                    vm.polygon = response.body.polygon;
                else
                    vm.polygon = {paths: []};

                if (typeof check_saved_search === "function") {
                    var saved_searchs_url = removeParamUrl('layout', vm.url);
                    check_saved_search(vm.user_id, saved_searchs_url, vm.listing_type_id)
                }
                vm.url_update = true;
            });
        },

        setMap: function (map) {
            this.map = map;
        },

        replaceUrlParam(url, paramName, paramValue)
        {
            if (paramValue == null) {
                paramValue = '';
            }
            var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
            if (url.search(pattern)>=0) {
                return url.replace(pattern,'$1' + paramValue + '$2');
            }
            url = url.replace(/[?#]$/,'');
            return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
        }
    },

    watch: {
        url: function (url) {
            if (this.url_update)
                this.send_request();
        }
    }
});