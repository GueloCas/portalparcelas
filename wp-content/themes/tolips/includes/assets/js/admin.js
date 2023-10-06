(function($) {
"use strict";
    $(window).load(function() {
        function gavias_setup_metatabs() {
            //Breacrumb js
            $('.breadcrumb_setting').hide();
            if($('#tolips_breadcrumb_layout').val() == 'page_options'){
                $('.breadcrumb_setting').show();
            }
            $('#tolips_breadcrumb_layout').on('change', function(e){
                if($(this).val() == 'page_options'){
                    $('.breadcrumb_setting').show();
                }else{
                    $('.breadcrumb_setting').hide();
                }
            })
        }
        gavias_setup_metatabs(); 
        
    }); 
})(jQuery);