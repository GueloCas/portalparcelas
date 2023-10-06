<div class="stm_ulisting_pro_popup hidden" id="stm_ulisting_pro_popup">
	<div class="ulisting_overlay"></div>
	<div class="ulisting_popup">
		<div class="ulisting_close"></div>
		<div class="owl-carousel owl-theme">
			<?php if (!empty($features)) {
				foreach ($features as $feature) { ?>
					<div class="item">
						<div class="popup_title">
							<h2><?php echo apply_filters('stm_no_echo_variable', $feature['title']); ?></h2>
						</div>
						<div class="popup_subtitle">
							<?php echo apply_filters('stm_no_echo_variable', $feature['subtitle']); ?>
						</div>
						<div class="popup_image">
							<img src="<?php echo apply_filters('stm_no_echo_variable', $feature['image']); ?>" />
						</div>
					</div>
				<?php }
			} ?>
		</div>
		<div class="popup_footer">
			<div class="text">Extend uListing functionality with <a href="https://stylemixthemes.com/wordpress-classified-plugin/?utm_source=admin&utm_medium=promo&utm_campaign=2020" target="_blank">uListing PRO</a> Addons!</div>
			<a href="https://stylemixthemes.com/wordpress-classified-plugin/?utm_source=admin&utm_medium=promo&utm_campaign=2020" class="pro_button" target="_blank">
				More Details
			</a>
		</div>
	</div>
</div>
<script>
    (function ($) {
        'use strict';
        $(document).ready(function () {
            $('.owl-carousel').owlCarousel({
                loop: true,
                lazyLoad: true,
                items: 1,
            });
            $('.stm_ulisting_pro_popup .ulisting_overlay, .stm_ulisting_pro_popup .ulisting_close').on('click', function (e) {
                e.preventDefault();
                $(this).closest('.stm_ulisting_pro_popup').fadeOut();
            });
        })
    })(jQuery);
</script>