Vue.component('stm-search-form-category', {
    data() {
        return {
            type: null,
            result: null,
            attribute: [],
            active_tab: null,
            categories: [],
            map: null,
            icon_url: '',
            listung_type: [],
            category_selected: [],
            switch: [],
            listings: [],
            currentFocus: 0,
            hasAccess: true,
            currentElem: null,
            currentListingType: null,
            autocomplete_search: false,
        }
    },

    created() {
        let vm = this;
        this._id = 'uListing-autocomplete-list_' + (parseFloat(Math.round(Math.random() * 100) / 100).toFixed(4) * 1000 + '_' + Date.now());
        if (typeof this.stm_search_form_category_data.categories)
            this.categories = this.stm_search_form_category_data.categories;

        if (typeof this.stm_search_form_category_data.listung_type)
            this.listung_type = this.stm_search_form_category_data.listung_type;

        if (typeof this.stm_search_form_category_data.data != "undefined")
            this.attribute = this.stm_search_form_category_data.data;

        if (typeof this.stm_search_form_category_data.active_tab)
            this.set_active_tab(this.stm_search_form_category_data.active_tab);

        window.addEventListener('click', function (e) {
            if( document.querySelector('.advanced-search-item-wrap') != null){

                let clickedElem = e.target, breakLoop = false;
                let  statement = vm.isNeedle(clickedElem.className);

                while (!breakLoop && (statement)){

                    clickedElem = clickedElem.parentNode;

                    if(clickedElem.tagName === 'BODY'){
                        breakLoop = true;
                        vm.categories[vm.category_selected.id].advanced_panel_show = !vm.categories[vm.category_selected.id].advanced_panel_show;
                    }

                    statement = vm.isNeedle(clickedElem.className);
                }
            }
        });
    },


    methods: {

        isNeedle: function(_cE){
            return _cE.indexOf('advanced-search-item-wrap') === -1 && _cE.indexOf('advanced-search-button') === -1 && _cE.indexOf('ulisting-search_box') === -1
        },

        set_active_tab: function (tab) {
            let vm = this;
            vm.active_tab = null;
            vm.set_category(vm.categories[tab], tab);
            vm.active_tab = tab;
            vm.select_type();
            vm.addCategory();
        },
        set_category: function (category, id) {
            this.category_selected = category;
            if(typeof id !== "undefined")
                this.category_selected.id = id;
            this.get_category_type();
        },
        change_listing_type: function () {
            this.change();
            if (this.currentElem && this.currentElem.target.value) {
                this.searchAutoComplete(this.currentElem);
            }
        },
        get_category_type() {
            let vm = this;
            if (vm.category_selected.types.length > 0)
                return;
            for (let key in vm.category_selected.listing_types) {
                let listing_type_id = vm.category_selected.listing_types[key];
                vm.category_selected.types.push({
                    id: vm.listung_type[listing_type_id].id,
                    text: vm.listung_type[listing_type_id].name,
                })
            }
            if (vm.category_selected.types[0])
                vm.category_selected.type_selected = vm.category_selected.types[0].id;
        },
        select_type: function (val) {
            this.currentListingType = val;
            this.change()
        },
        addCategory: function(){
            let vm = this;
            let categoryClone = [];
            for(let o in vm.categories){
                categoryClone.push({id: o, text: vm.categories[o].name });
            }

            vm.category_selected.categories = categoryClone;
        },

        change: function () {
            let vm = this;
            for (key in this.listung_type) {
                let listung_type = vm.listung_type[key];
                let url = listung_type.url;

                if (listung_type.id != vm.category_selected.type_selected || typeof listung_type.fields_types == "undefined")
                    continue;

                for (attribute in vm.attribute['listing_type_' + key]) {
                    let field = listung_type.fields_types[attribute]
                    let value = vm.attribute['listing_type_' + key][attribute]
                    if (typeof field == "undefined")
                        continue;

                    if (field.field_type == 'search' && value) {
                        url = updateQueryStringParameter(url, attribute, value.trim());
                    }

                    if (field.field_type == 'location' && value.address) {
                        url = updateQueryStringParameter(url, "address", value.address);
                        url = updateQueryStringParameter(url, "lat", value.lat);
                        url = updateQueryStringParameter(url, "lng", value.lng);
                    }

                    if (field.field_type == 'proximity' && value) {
                        url = updateQueryStringParameter(url, "proximity[" + field.units + "]", value);
                    }

                    if (field.field_type == 'range' && value) {
                        url = updateQueryStringParameter(url, "range[" + attribute + "]", value[0] + ";" + value[1]);
                    }

                    if (field.field_type == 'dropdown' && value) {
                        url = updateQueryStringParameter(url, attribute + "[]", value);
                    }

                    if (field.field_type == 'checkbox' && value.length) {
                        value.forEach(function (item) {
                            url = updateQueryStringParameter(url, attribute + "[]", item);
                        })
                    }

                    if (field.field_type == 'date' && value) {
                        if (field.date_type == "range") {
                            url = updateQueryStringParameter(url, "date_range[" + attribute + "][]", moment(value[0], "DD/MM/YYYY").format('YYYY-MM-DD'));
                            url = updateQueryStringParameter(url, "date_range[" + attribute + "][]", moment(value[1], "DD/MM/YYYY").format('YYYY-MM-DD'));
                        } else {
                            url = updateQueryStringParameter(url, attribute, moment(value, "DD/MM/YYYY").format('YYYY-MM-DD'));
                        }
                    }
                }
                url = updateQueryStringParameter(url, "category[]", vm.category_selected.id);
                vm.category_selected.url = url;
            }
        },

        hasAccessAutoComplete: function () {
            let vm = this, result = false;
            let type = vm.listung_type[vm.currentListingType ? vm.currentListingType : Object.keys(vm.listung_type)[0]];
            type = type ? type.fields_types : null;
            for (let o in type) {
                if (type.hasOwnProperty(o) && type[o].hasOwnProperty('type') && type[o].type === 'search' && !result) {
                    result = true;
                }
            }

            return result;
        },

        searchAutoComplete(event) {
            let vm = this;
            setTimeout(function () {
                if (vm.hasAccessAutoComplete()) {
                    vm.closeAllLists();
                    vm.currentElem = event;
                    let a, b, parent, val = vm.currentElem.target.value ? vm.currentElem.target.value.trim() : '';

                    if (!val) {
                        return false;
                    }

                    vm.currentFocus = -1;
                    document.addEventListener("click", function (e) {
                        vm.closeAllLists(e.target);
                    });

                    if(!vm.isAutocompleted(vm.category_selected.type_selected, vm.listung_type)) {
                        return false;
                    }

                    parent = vm.createElement('DIV', [['class', 'uListing-autocomplete-items'], ['id', vm._id]],);
                    a = vm.createElement('DIV', [['class', 'uListing-autocomplete-wrapper']]);

                    parent.appendChild(a);
                    vm.currentElem.target.parentNode.appendChild(parent);



                    let url = vm.category_selected.url,
                        apiParams = url.substring(url.indexOf('?'), url.length);

                    apiParams += '&listing_type=' + vm.category_selected.type_selected;

                    vm.$http.get('listing/listing-basic-form' + apiParams).then(function (response) {
                        let result = response.body;
                        if (result.hasOwnProperty('success') && result.success) {
                            vm.hasAccess = result.success;
                            vm.result = result.data;

                            if (vm.hasAccess && vm.result && vm.result.length > 0) {
                                vm.result.forEach(function (elem, index) {
                                    b = vm.createElement("a", [['href', elem.guid]]);

                                    let attrsInnerHTML = '';
                                    let attrs = vm.createElement('span', [['class', 'uListing-search-attrs']]);
                                    let titleTag = vm.createElement('span', [['class', 'uListing-search-post-title']]);
                                    let wrapper = vm.createElement('span', [['class', 'uListing-search-post-title-wrapper']]);
                                    elem.post_title = vm.markTitle(elem.post_title);
                                    titleTag.innerHTML = elem.post_title;

                                    Object.values(elem.attribute_elements).forEach(current => {
                                        if (vm.validCurrentIcon(current)) {
                                            attrsInnerHTML += `<span><i class="${current.attribute_icon}"></i>${current.value}</span>`
                                        } else if (vm.validCurrentImage(current)) {
                                            attrsInnerHTML += `<span class="thumbnail-image"><span style='background-image: url("${current.attribute_image}"); background-repeat: no-repeat; background-position: center center; background-size: cover;'></span>${current.value}</span>`;
                                        }
                                    })

                                    attrs.innerHTML = attrsInnerHTML;
                                    let span = vm.createElement('span', [['class', 'uListing-search-background-wrapper']]);
                                    span.appendChild(vm.createElement('img', [['src', result.images[index]]]));

                                    wrapper.appendChild(titleTag);
                                    wrapper.appendChild(attrs);

                                    b.appendChild(wrapper);
                                    b.appendChild(span);
                                    a.appendChild(b);
                                });

                                vm.currentElem.target.addEventListener("keydown", function (e) {
                                    let x = document.getElementById(vm._id);
                                    if (x) x = x.getElementsByTagName("div");
                                    if (e.keyCode === 40) {
                                        vm.currentFocus++;
                                        vm.addActive(x);
                                    } else if (e.keyCode === 38) {
                                        vm.currentFocus--;
                                        vm.addActive(x);
                                    } else if (e.keyCode === 13) {
                                        e.preventDefault();
                                        if (vm.currentFocus > -1) {
                                            if (x) x[vm.currentFocus].click();
                                        }
                                    }
                                });

                                let founded = vm.createElement('DIV', [['class', 'uListing-search-founded']]);
                                let foundedText = this.stm_search_form_category_texts && this.stm_search_form_category_texts.founded ? this.stm_search_form_category_texts.founded : 'Founded';
                                let listingsText = this.stm_search_form_category_texts && this.stm_search_form_category_texts.listings ? this.stm_search_form_category_texts.listings : 'listings';

                                founded.innerHTML = `<span>${foundedText}: <a href="${vm.category_selected.url}">${vm.result.length} ${listingsText}</a></span>`;
                                parent.appendChild(founded);

                            } else {
                                vm.noResults(a);
                            }
                        }
                    }).catch(function () {
                        vm.noResults(a);
                    });
                }
            }, 500);
        },

        isAutocompleted(category, listing_type) {

           if(listing_type[category].autocomplete_search_attributes.length == 0)
                return false;

           return true;
        },

        validCurrentIcon(current) {
            return  (current.hasOwnProperty('attribute_icon') && (current.attribute_icon || current.attribute_name === 'price') ) && (current.hasOwnProperty('value') && typeof current.value !== "undefined")
        },

        validCurrentImage(current) {
            return (current.hasOwnProperty('attribute_image') && current.attribute_image) && (current.hasOwnProperty('value') && typeof current.value !== "undefined")
        },

        noResults: function (a) {
            let b = this.createElement("DIV", [['class', 'uListing-search-no-result']]);
            let resultText = this.stm_search_form_category_texts && this.stm_search_form_category_texts.no_result ? this.stm_search_form_category_texts.no_result : 'No result was found';
            b.innerHTML = "<strong>" + resultText + "</strong>";
            a.appendChild(b);
        },

        addActive: function (x) {
            let vm = this;
            if (!x) return false;

            vm.removeActive(x);
            if (vm.currentFocus >= x.length) vm.currentFocus = 0;
            if (vm.currentFocus < 0) vm.currentFocus = (x.length - 1);
            x[vm.currentFocus].classList.add("uListing-autocomplete-active");
        },

        removeActive: function (x) {
            for (let i = 0; i < x.length; i++) {
                x[i].classList.remove("uListing-autocomplete-active");
            }
        },

        closeAllLists: function (elem) {
            let vm = this;
            let x = document.getElementsByClassName("uListing-autocomplete-items");
            for (let i = 0; i < x.length; i++) {
                if (elem !== x[i] && elem !== vm.currentElem.target) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        },
        markTitle: function (title) {
            let vm = this;

            let elemVal = vm.currentElem.target.value;
            let lowerElemVal = elemVal.toLowerCase();
            let lowerTitle = title.toLowerCase();
            let arr = [];


            while (lowerElemVal.length > 0 && lowerTitle.indexOf(lowerElemVal) !== -1) {
                let value = title.substr(lowerTitle.indexOf(lowerElemVal), elemVal.length);
                let index = parseFloat(Math.round(Math.random() * 100) / 100).toFixed(4) * 1000 + '_' + Date.now();
                title = title.replace(value, index);
                lowerTitle = lowerTitle.replace(lowerElemVal, index);
                arr.push({value: value, id: index});
            }

            arr.forEach(function (elem) {
                let replacement = '<span class="uListing-title-text-shadow">' + elem.value + '</span>';
                title = title.split(elem.id).join(replacement);
            });

            return title;
        },

        createElement: function (tagName, attrs) {
            let tag = document.createElement(tagName);
            attrs.forEach(function (value) {
                tag.setAttribute(value[0], value[1]);
            });

            return tag;
        }
    },

    watch: {
        active_tab: function () {
            if (this.currentElem && this.currentElem.target.value) {
                this.searchAutoComplete(this.currentElem);
            }
        },
    },

    props: {
        stm_search_form_category_data: {
            default: []
        },
        stm_search_form_category_texts: {
            default: []
        },
        is_style_4: {
            default: false
        }
    }
});