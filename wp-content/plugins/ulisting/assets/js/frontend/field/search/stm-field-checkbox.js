export default {
    data() {
        return {
            list: []
        }
    },
    created() {
        this.list = this.build_items();
    },
    methods: {
        updateValue: function () {
            this.$emit('input', this.value);
            this.callback_change()
        },
        jsonCopy: function (src) {
            return JSON.parse(JSON.stringify(src));
        },

        parser: function(encodedStr) {
            const parser = new DOMParser;
            const dom = parser.parseFromString(
                '<!doctype html><body>' + encodedStr,
                'text/html');

            return  dom.body.textContent;
        },

        build_items: function () {
            var vm = this;
            var items = this.jsonCopy(vm.items);

            if (this.hide_empty) {
                items = items.filter(function (item) {
                    if (!vm.current_tab) {
                        if (item.count != null && item.count > 0)
                            return true;
                        return false;
                    }

                    if (item.count != null && item.count[vm.current_tab] > 0)
                        return true;
                    return false;
                });
            }

            if (this.order_by == 'name') {
                if (this.order == "ASC")
                    items.sort(function compare(a, b) {
                        if (a.name < b.name)
                            return -1;
                        if (a.name > b.name)
                            return 1;
                        return 0;
                    });
                else
                    items.sort(function compare(a, b) {
                        if (a.name > b.name)
                            return -1;
                        if (a.name < b.name)
                            return 1;
                        return 0;
                    });
            }
            if (this.order_by == 'count') {
                if (this.order == "ASC")
                    items.sort(function (a, b) {
                        if (!vm.current_tab)
                            return a.count - b.count
                        else
                            return a.count[vm.current_tab] - b.count[vm.current_tab];
                    });
                else
                    items.sort(function (a, b) {
                        if (!vm.current_tab)
                            return b.count - a.count
                        else
                            return b.count[vm.current_tab] - a.count[vm.current_tab];
                    });
            }
            return items;
        }
    },
    props: {
        'value': {
            default: null
        },
        'name': {
            default: null
        },
        'placeholder': {
            default: null
        },
        'items': {
            default: null
        },
        'hide_empty': {
            default: false
        },
        'order_by': {
            default: null
        },
        'order': {
            default: null
        },
        'callback_change': {
            default: null
        },
        'current_tab': {
            default: false
        }
    }
}