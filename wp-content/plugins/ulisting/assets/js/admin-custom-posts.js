"use strict";

jQuery(function() {
    jQuery('.tablenav .actions select').on('change', function() {
        jQuery(this).siblings('input[type="submit"]').trigger('click');
    });
});