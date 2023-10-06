jQuery(document).ready(function () {
    jQuery(document).on("click",".qview", function(event) {
        event.preventDefault()
        let listing_id = jQuery(this).data('id')
        let spinner = '<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>'
        quick_fields(this, spinner, "html")

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                listing_id  : listing_id,
                action      : 'stm_listing_quick_view',
            },
            beforeSend: function() {
                let clean = [
                    ".stm-quickview-title",
                    ".stm-listing-desc",
                    ".listing_price",
                    ".listing-cat",
                    ".content-atribute",
                    ".stm-wishlist"
                ];
                jQuery.each(clean, (key, value) => quick_fields(value, '', "text"));
            },
            success(data) {
                if ( data === null || data === 'undefined' ) {
                    no_data()
                    return
                }

                if ( (typeof data) === "object" && Object.keys(data).length ) {
                    let main_gal = own();
                    let attr = {
                        ".stm-quickview-title"  : data.title,
                        ".stm-listing-desc"     : data.description,
                        ".listing_price"        : data.price.price,
                        ".listing-cat"          : data.category,
                        ".stm-wishlist"         : data.stm_wishlist,
                    };
                    jQuery.each(attr, function (key, value) {
                        if ( key === ".listing-cat" ){
                            cat(value);
                        } else {
                            quick_fields(key, value, "html");
                        }
                    });

                    jQuery(".listing-btn-view").attr("href", data.permalink)
                    attributes(data.attribute)
                    gallery(data.gallery, main_gal)
                    jQuery("#centralModalSm").modal('show')
                    spinner = '<i class="fa fa-eye" aria-hidden="true"></i>'
                    quick_fields(".qview", spinner, "html")
                } else {
                    no_data();
                }
            },
            error: function () {
                    no_data();
            }
        });
    });

    function no_data(){
        quick_fields(".stm-quickview-title", 'No Data', "text");
        jQuery("#centralModalSm").modal('show');
    }

    function thumble_click() {
        jQuery('.slider-two .item').click(function () {
            let b = jQuery(".item").index(this);
            jQuery(".slider .owl-dots .owl-dot").eq(b).trigger('click');
            jQuery(".slider-two .item").removeClass("active");
            jQuery(this).addClass("active");
        });
    }

    function quick_fields(field_id, data, show_tag) {
        if ( show_tag === "text" ) {
            jQuery(field_id).text(data);
        } else {
            jQuery(field_id).html(data);
        }
    }

    function attributes(attributes) {
        if ( typeof attributes !== 'undefined' ) {
            const attribute = Object.values(attributes).map(attribute => `<div class="ulisting-attribute-template stm-attr"><span class="ulisting-attribute-template-icon">${attribute.icon}</span>${attribute.atr_val} ${attribute.attrib_title}</div>`);
            quick_fields(".content-atribute", attribute, "html")
        }
    }

     function cat(categories) {
        const category =  categories?.map(category=>`<span class="inventory_category inventory_category_style_1">${category}</span>`);
        quick_fields(".listing-cat", category, "html")
    }

    function gallery(galleries, main_gal) {
        let attrib_html_one = '';
        let attrib_html_two = '';
        let i = 1;
        let active = 'active';
        main_gal.owl.trigger('remove.owl.carousel', [i])
            .trigger('refresh.owl.carousel');
        rem_car(main_gal);
        jQuery.each(galleries, function (key, value) {
            if (i > 1) active = '';
            attrib_html_two = ' <div style="background-image: url(' + value.thumbnail[0] + ')" class="item ' + active + '"></div>';
            main_gal.owl2.trigger('add.owl.carousel', [jQuery(attrib_html_two), i])
                .trigger('refresh.owl.carousel');
            attrib_html_one = ' <div style="background-image: url(' + value.full[0] + ')" class="item-box"></div>';
            main_gal.owl.trigger('add.owl.carousel', [jQuery(attrib_html_one), i])
                .trigger('refresh.owl.carousel');

            thumble_click();
            i++;
        });
    }

    function rem_car(main_gal){
        for ( let i = 1; i < 100; i++ ) {
            main_gal.owl.trigger('remove.owl.carousel', [i])
                .trigger('refresh.owl.carousel');
            main_gal.owl2.trigger('remove.owl.carousel', [i])
                .trigger('refresh.owl.carousel');
        }
    }

    function own() {
        let changeSlide = 5;
        let owlarray = {};
        let slide = changeSlide;
        if (jQuery(window).width() < 600) {
            let slide = changeSlide;
            slide--;
        } else if (jQuery(window).width() > 999) {
            let slide = changeSlide;
            slide++;
        } else {
            slide = changeSlide;
        }

        jQuery('.one').owlCarousel({
            nav: true,
            items: 1,
            navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
        })

        jQuery('.two').owlCarousel({
            nav: true,
            margin: 10,
            mouseDrag: false,
            touchDrag: false,
            responsive: {
                0: {
                    items: changeSlide - 1,
                    slideBy: changeSlide - 1
                },
                600: {
                    items: changeSlide,
                    slideBy: changeSlide
                },
                1000: {
                    items: changeSlide + 1,
                    slideBy: changeSlide + 1
                }
            }
        })
        let owl = jQuery('.one');
        owl.owlCarousel();
        owl.on('translated.owl.carousel', function (event) {
            jQuery(".right").removeClass("nonr");
            jQuery(".left").removeClass("nonl");
            if (jQuery('.one .owl-next').is(".disabled")) {
                jQuery(".slider .right").addClass("nonr");
            }
            if (jQuery('.one .owl-prev').is(".disabled")) {
                jQuery(".slider .left").addClass("nonl");
            }
            jQuery('.slider-two .item').removeClass("active");
            let c = jQuery(".slider .owl-item.active").index();
            jQuery('.slider-two .item').eq(c).addClass("active");
            let d = Math.ceil((c + 1) / (slide)) - 1;
            jQuery(".slider-two .owl-dots .owl-dot").eq(d).trigger('click');
        })
        jQuery('.right').click(function () {
            jQuery(".slider .owl-next").trigger('click');
        });
        jQuery('.left').click(function () {
            jQuery(".slider .owl-prev").trigger('click');
        });
        thumble_click();
        let owl2 = jQuery('.two');

        owl2.owlCarousel();

        owl2.on('translated.owl.carousel', function (event) {
            jQuery(".right-t").removeClass("nonr-t");
            jQuery(".left-t").removeClass("nonl-t");
            if (jQuery('.two .owl-next').is(".disabled")) {
                jQuery(".slider-two .right-t").addClass("nonr-t");
            }
            if (jQuery('.two .owl-prev').is(".disabled")) {
                jQuery(".slider-two .left-t").addClass("nonl-t");
            }
            thumble_click();
        })
        jQuery('.right-t').click(function () {
            jQuery(".slider-two .owl-next").trigger('click');
        });
        jQuery('.left-t').click(function () {
            jQuery(".slider-two .owl-prev").trigger('click');
        });
        owlarray.owl = owl;
        owlarray.owl2 = owl2;
        return owlarray;
    }
});