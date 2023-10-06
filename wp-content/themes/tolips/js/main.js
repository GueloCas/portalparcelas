(function($) {
  	"use strict";
  	var GaviasTheme = {
		init: function(){
			this.initResponsive();
			this.initCarousel();
			this.menuMobile();
			this.postMasonry();
			this.scrollTop();
			this.stickyMenu();
			this.comment();
			this.listingMapLoop();
			this.listing();
			this.other();
	
			$('.team__progress-bar').each(function(){
			  	var $progressbar = $(this);
			  	$progressbar.css('width', $progressbar.data('progress-max'));
			})
			$('.review__progress-bar').each(function(){
			  	var $progressbar = $(this);
			  	$progressbar.css('width', $progressbar.data('progress-max'));
			})
		},

	 	initResponsive: function(){
		  	var _event = $.event,
		  	$special, resizeTimeout;
		  	$special = _event.special.debouncedresize = {
				setup: function () {
					$(this).on("resize", $special.handler);
				},
			 	teardown: function () {
					$(this).off("resize", $special.handler);
			 	},
			 	handler: function (event, execAsap) {
					var context = this,
				  		args = arguments,
				  		dispatch = function () {
					 		event.type = "debouncedresize";
					 		_event.dispatch.apply(context, args);
				  		};
				  	if (resizeTimeout) {
					 	clearTimeout(resizeTimeout);
				  	}
					execAsap ? dispatch() : resizeTimeout = setTimeout(dispatch, $special.threshold);
			 	},
		  		threshold: 150
			};
	 	},

	 	initCarousel: function(){
			$('.init-carousel-owl-theme').each(function(){
		  		var items = GaviasTheme.carouselOptInit('items', this);
		  		var items_lg = GaviasTheme.carouselOptInit('items_lg', this);
		  		var items_md = GaviasTheme.carouselOptInit('items_md', this);
		  		var items_sm = GaviasTheme.carouselOptInit('items_sm', this);
		  		var items_xs = GaviasTheme.carouselOptInit('items_xs', this);
		  		var loop = GaviasTheme.carouselOptInit('loop', this);
		  		var speed = GaviasTheme.carouselOptInit('speed', this);
		  		var auto_play = GaviasTheme.carouselOptInit('auto_play', this);
		  		var auto_play_speed = GaviasTheme.carouselOptInit('auto_play_speed', this);
		  		var auto_play_timeout = GaviasTheme.carouselOptInit('auto_play_timeout', this);
		  		var auto_play_hover = GaviasTheme.carouselOptInit('auto_play_hover', this);
		  		var navigation = GaviasTheme.carouselOptInit('navigation', this);
		  		var rewind_nav = GaviasTheme.carouselOptInit('rewind_nav', this);
		  		var pagination = GaviasTheme.carouselOptInit('pagination', this);
		  		var mouse_drag = GaviasTheme.carouselOptInit('mouse_drag', this);
		  		var touch_drag = GaviasTheme.carouselOptInit('touch_drag', this);
		  		$(this).owlCarousel({
			 		nav: navigation,
			 		autoplay: auto_play,
			 		autoplayTimeout: auto_play_timeout,
			 		autoplaySpeed: auto_play_speed,
			 		autoplayHoverPause: auto_play_hover,
			 		navText: [ '<span><i class="fas fa-arrow-left"></i></span>', '<span><i class="fas fa-arrow-right"></i></span>' ],
			 		autoHeight: false,
			 		loop: loop, 
			 		dots: pagination,
			 		rewind: rewind_nav,
			 		smartSpeed: speed,
			 		mouseDrag: mouse_drag,
			 		touchDrag: touch_drag,
			 		responsive : {
						0 : {
						  items: 1,
						  nav: false
						},
						580 : {
						  items : items_xs,
						  nav: false
						},
						768 : {
						  items : items_sm,
						  nav: false
						},
						992: {
						  items : items_md
						},
						1200: {
						  items: items_lg
						},
						1400: {
						  items: items
						}
				 	}
		  		}); 
			}); 
	 	},

	 	carouselOptInit: function(opt, context){
			const opts = {
			  	items: 5, 
			  	items_lg: 4,
			  	items_md: 3, 
			  	items_sm: 2, 
			  	items_xs: 1,
			  	loop: false, 
			  	speed: 200, 
			  	auto_play: false,
			  	auto_play_speed: false,
			  	auto_play_timeout: 1000,
			  	auto_play_hover: false,
			  	navigation: false,
			  	rewind_nav: false,
			  	pagination: false,
			  	mouse_drag: false,
			  	touch_drag: false
				}
			return $(context).data(opt) ? $(context).data(opt) : opts[opt];
	 	},

	 	menuMobile: function(){
			$('.gva-offcanvas-content ul.gva-mobile-menu > li:has(ul)').addClass("has-sub");
			$('.gva-offcanvas-content ul.gva-mobile-menu > li:has(ul) > a').after('<span class="caret"></span>');
			$( document ).on('click', '.gva-offcanvas-content ul.gva-mobile-menu > li > .caret', function(e){
			  	e.preventDefault();
			  	var checkElement = $(this).next();
			  	$('.gva-offcanvas-content ul.gva-mobile-menu > li').removeClass('menu-active');
			  	$(this).closest('li').addClass('menu-active'); 
			  
			  	if((checkElement.is('.submenu-inner')) && (checkElement.is(':visible'))) {
				 	$(this).closest('li').removeClass('menu-active');
				 	checkElement.slideUp('normal');
			  	}
		  
		  		if((checkElement.is('.submenu-inner')) && (!checkElement.is(':visible'))) {
			 		$('.gva-offcanvas-content ul.gva-mobile-menu .submenu-inner:visible').slideUp('normal');
			 		checkElement.slideDown('normal');
		  		}
		  
		  		if (checkElement.is('.submenu-inner')) {
			 		return false;
		  		} else {
			 		return true;  
		  		}   
			})

			$( document ).on( 'click', '.canvas-menu.gva-offcanvas > a.dropdown-toggle', function(e) {
		  		e.preventDefault();
		  		var $style = $(this).data('canvas');
			  	if($('.gva-offcanvas-content' + $style).hasClass('open')){
				 	$('.gva-offcanvas-content' + $style).removeClass('open');
				 	$('#gva-overlay').removeClass('open');
				 	$('#wp-main-content').removeClass('blur');
			  	}else{
				 	$('.gva-offcanvas-content' + $style).addClass('open');
				 	$('#gva-overlay').addClass('open');
				 	$('#wp-main-content').addClass('blur');
			  	}
			});
			$( document ).on( 'click', '#gva-overlay', function(e) {
			  	e.preventDefault();
			  	$(this).removeClass('open');
			  	$('.gva-offcanvas-content').removeClass('open');
			  	$('#wp-main-content').removeClass('blur');
			})
			$( document ).on( 'click', '.close-canvas', function(e) {
			  	e.preventDefault();
			  	$('.gva-offcanvas-content').removeClass('open');
			  	$('#gva-overlay').removeClass('open');
			  	$('#wp-main-content').removeClass('blur');
			})
	 	},

	 	postMasonry: function(){
			var $container = $('.post-masonry-style');
			$container.imagesLoaded( function(){
		  		$container.masonry({
			 		itemSelector : '.item-masory'
		  		}); 
			});
	 	},

		scrollTop: function(){
			var offset = 300;
			var duration = 500;

			jQuery(window).scroll(function() {
			  	if (jQuery(this).scrollTop() > offset) {
				 	jQuery('.return-top').fadeIn(duration);
			  	} else {
				 	jQuery('.return-top').fadeOut(duration);
			  	}
			});

			$( document ).on('click', '.return-top', function(event){
			  	event.preventDefault();
			  	jQuery('html, body').animate({scrollTop: 0}, duration);
			  	return false;
			});
		},

	 	carouselProductThumbnail: function(){
			$('ol.flex-control-nav').each(function(){
			  	$(this).owlCarousel({
				 	nav: false,
				 	navText: [ '<span><i class="fas fa-arrow-left"></i></span>', '<span><i class="fas fa-arrow-right"></i></span>' ],
				 	margin: 10,
				 	dots: false,
				 	responsive : {
						0 : {
						  items: 2,
						  nav: false
						},
						640 : {
						  items : 3,
						  nav: false
						},
						768 : {
						  items : 4,
						  nav: false
						},
						992: {
						  items : 4
						},
						1200: {
						  items: 4
						},
						1400: {
						  items: 4
						}
				 	}
			  	});
			});
	 	},

	 	stickyMenu: function(){
		
			if( $('.gv-sticky-menu').length > 0 ){

				$( ".gv-sticky-menu" ).wrap( "<div class='gv-sticky-wrapper'></div>" );

		      var headerHeight = $('.gv-sticky-menu').height();
		      var menu = $('.gv-sticky-wrapper');

		      $(window).on('scroll', function () {
		         if ($(window).scrollTop() > menu.offset().top) {
		            menu.addClass('is-fixed');
		            $('body').addClass('header-is-fixed');
		            menu.css('height', headerHeight);
		         } else {
		            menu.removeClass('is-fixed');
		            menu.css('height', 'auto');
		            $('body').removeClass('header-is-fixed');
		         }
		      });
		   }
	 	},

	 	comment: function(){
			var count = 0;
			var total = 0;
			$('#lt-comment-reviews').find('input.lt-review-val').each(function(){
			  	var val = $(this).val();
			  	if(val){
				 	val = parseInt(val);
				 	if(Number.isInteger(val)){
						total += val;
						count ++;
				 	}  
			  	}
			});
			var avg = total/count;
			$('#lt-comment-reviews').find('.avg-total-tmp .value').html(avg.toFixed(2));

			// Click to star
			$( '.select-review' ).on( 'click', '.star', function( event ) {
			  
			  	$( this ).nextAll( '.star' ).removeClass( 'active' );
			  	$( this ).prevAll( '.star' ).removeClass( 'active' );
			  	$( this ).addClass( 'active' );
			  	$( this ).parent().find( '.lt-review-val' ).attr( 'value', $( this ).attr( 'data-star' ) );
			  	var count = 0;
			  	var total = 0;
			  	$('#lt-comment-reviews').find('input.lt-review-val').each(function(){
				 	var val = $(this).val();
				 	if(val){
						val = parseInt(val);
						if(Number.isInteger(val)){
					  		total += val;
					  		count ++;
						}  
				 	}
			  	});
			  	var avg = total/count;
			  	$('#lt-comment-reviews').find('.avg-total-tmp .value').html(avg.toFixed(2));
			});

			$( '.lt-review-val' ).each( function( index ) {
			  	$( this ).parent( '.stars' ).find( 'span[data-star="' + $( this ).val() + '"]' ).trigger( 'click' );
			});

			$('.menu-my-account > a').on('click', function(){
			  	var parent = $(this).parent();
			  	if(parent.hasClass('open')){
				 	parent.removeClass('open');
			  	}else{
				 	parent.addClass('open');
			  	}
			});
	 	},

		listingMap: function(){
	 		var map_top = 0, $admin_tool = $('#wpadminbar'), $height_header = $('.header-sticky');
	      map_top += $admin_tool.length > 0 ? $admin_tool.height() : 0;
	      map_top += $height_header.length > 0 ? $height_header.outerHeight() : 0;

	     	if (window.innerWidth < 992) {
	         $('.map-sticky').trigger("sticky_kit:detach");
	     	} else {
	         $('.map-sticky').stick_in_parent({
	            offset_top: map_top
	         });
	     	}

	     	if (window.innerWidth > 992) {
	     	 	$('.map-full-height').css('height', 'calc(100vh - ' + map_top + 'px)');
	     	}else{
	     		$('.map-full-height').css('height', '');
	     	}
		},

		listingMapLoop: function(){
	     	GaviasTheme.listingMap();
	     	$(window).resize(function(){
	         setTimeout(function () {
	            GaviasTheme.listingMap();
	         }, 100)
	     	});
		},

		listing: function(){
			$('.listing-share .btn-control-share').on('click', function(e){
				e.preventDefault();
				if($(this).parent().hasClass('open-share')){
					$(this).parent().removeClass('open-share');
				}else{
					$(this).parent().addClass('open-share');
				}
			});

			$('.user-phone .btn-display-phone').on('click', function(e){
				e.preventDefault();
				$(this).parent('.user-phone').find('.hidden-phone').addClass('hidden');
				$(this).parent('.user-phone').find('.show-phone').removeClass('hidden');
				$(this).addClass('hidden');
			});
		
			$(document).delegate('.btn-advanced-search', 'click', function(e){
				e.preventDefault();
				var search_content = $(this).parents('.ulisting-search-form-wrapper').find('.advanced-search-content');
				if( !search_content.is(':visible') ) {
				 	search_content.slideDown('normal');
				 	$('html, body').animate({
		            scrollTop: $(this).offset().top - 280
		          }, 1000);
			  	}else{
				 	search_content.slideUp('normal');
			  	}
			});

			$('.lt-contact-fom-btn').magnificPopup({
			  	type:'inline',
			  	midClick: true,
			});

			$('input#file-upload-avatar').change(function(e) {
			   $(this).next().html($(this).val().replace(/C:\\fakepath\\/i, ''));
			});

			$('.control-filter-sidebar-mobile').on('click', function(){
				var filter = $('.filter-sidebar-wrapper.filter-sidebar-style');
				if( filter.hasClass('open-search') ){
					filter.removeClass('open-search');
					$(this).removeClass('open');
				}else{
					filter.addClass('open-search');
					$(this).addClass('open');
				}
			});

			var hide = true;
			$('body').on("click", function () {
		    	if (hide) $('.checkbox-filter').removeClass('active');
		    	hide = true;
			});

			$('body').delegate('.ulisting-field-checkboxs.checkbox-filter .show-results', 'click', function(e){
				var self = $(this).parents('.checkbox-filter');
				if( self.hasClass('active') ){
					self.removeClass('active');
					return false;
				}
				$('.checkbox-filter').removeClass('active');
				self.toggleClass('active');
				hide = false;
			});

			$('.dashboard-nav-control-link').on('click', function(e){
				e.preventDefault();
				var main = $(this).parents('.ulisting-dashboard-page');
				if(main.hasClass('open-nav')){
					main.removeClass('open-nav');
				}else{
					main.addClass('open-nav');
				}
			});

		},

		other: function(){
			$('.popup-video').magnificPopup({
			  	type: 'iframe',
			  	fixedContentPos: false
			});

			$('.gva-user .login-account').on('click', function(){
			  	if($(this).hasClass('open')){
				 	$(this).removeClass('open');
			  	}else{
				 	$(this).addClass('open');
			  	}
			})

			$('.mobile-user .login-account .link-open-menu').on('click', function(e){
				e.preventDefault();
				var wrapper = $(this).parents('.login-account');
			  	if( wrapper.hasClass('open-menu') ){
				 	wrapper.removeClass('open-menu');
			  	}else{
				 	wrapper.addClass('open-menu');
			  	}
			})

			$( document ).on( 'click', '.yith-wcwl-add-button.show a', function() {
			  $(this).addClass('loading');
			});

			$(document).on('click', '.gva-search > a.control-search', function(){
			  	if($(this).hasClass('search-open')){
				 	$(this).parents('.gva-search').removeClass('open');
				 	$(this).removeClass('search-open');
			  	}else{
				 	$(this).parents('.gva-search').addClass('open');
				 	$(this).addClass('search-open');
			  	}
			});

			$('.gva-offcanvas-content .sidebar, .mini-cart-header .dropdown .minicart-content').perfectScrollbar();
			$(".lightGallery").lightGallery({
			  	selector: '.image-item .zoomGallery'
			});

			$('a.tribe-events-button').each(function(e){
			  	if($(this).children().length == 0){
				 	$(this).wrapInner('<span></span>')
			  	}
			});

			$('.scroll-link[href*="#"]:not([href="#"])').click(function() {
		      if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
		        var target = $(this.hash);
		        target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
		        if (target.length) {
		          $('html, body').animate({
		            scrollTop: target.offset().top - 100
		          }, 1500);
		          return false;
		        }
		      }
		   });

			$('body').delegate('.stm-listing-pagination a', 'click', function(){
				$('html, body').animate({
		            scrollTop: 0
		         }, 1500);
			});

		}
	}

  	$(document).ready(function(){
	 	GaviasTheme.init();
  	})

  	$(window).load(function(){
	 	GaviasTheme.carouselProductThumbnail();
  	})

})(jQuery);
