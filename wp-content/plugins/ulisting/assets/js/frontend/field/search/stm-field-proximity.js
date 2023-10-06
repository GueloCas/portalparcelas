export default {
    data() {
        return {
            setTimeoutEvent: null
        }
    },
    methods: {
        updateValue: function (value) {
            var vm = this;
            let element = document.querySelector('.uListing-no-results-location');
            if( element )
                element.classList.remove('uListing-no-results-location');
            if (this.setTimeoutEvent)
                clearTimeout(this.setTimeoutEvent)
            this.setTimeoutEvent = setTimeout(function () {
                vm.$emit('input', value);
                vm.callback_change();
            }, 100);

            setTimeout(function () {
                vm.checkLocation();
            }, 1000);
        },

        checkLocation: function () {
            let fields = document.querySelectorAll('.inventory-location-filter');
            for(let i = 0; i < fields.length; i++){
                let currentElem =	fields[i].querySelector('input');
                if(fields[i].className.indexOf('uListing-empty-location') === -1 && !currentElem.value){
                    fields[i].classList.add('uListing-empty-location');
                }else if(currentElem.value && fields[i].className.indexOf('uListing-empty-location') !== -1){
                    fields[i].classList.remove('uListing-empty-location');
                }
            }
        }
    },
    props: [
        'value',
        'min',
        'max',
        'units',
        'callback_change'
    ],
}