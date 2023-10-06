<?php
/**
 * @var $pro_inactive
 * @var $feedback_added
 */
?>
<div class="ulisting_pro_notice">
	<div class="free">
		<img src="<?php echo esc_url(ULISTING_URL . '/assets/img/notices/ulisting-logo.png') ?>" width="40"/>
		<div class="ulisting_title">uListing</div>
		<div class="ulisting_subtitle"><?php echo esc_html( sprintf( __('v %s', 'ulisting'), ULISTING_VERSION ) ); ?></div>
	</div>
	<div class="pro">
		<?php if ( ! $feedback_added ) { ?>
			<a href="#" class="ulisting-feedback-button">
				<?php esc_html_e('Feedback', 'ulisting') ?>
				<img src="<?php echo esc_url(ULISTING_URL . '/assets/img/feedback/feedback.svg') ?>">
			</a>
		<?php } ?>
		<?php if ( $pro_inactive ) { ?>
			<a href="https://stylemixthemes.com/wordpress-classified-plugin/?utm_source=admin&utm_medium=promo&utm_campaign=2020" target="_blank">Pro features</a>
		<?php } ?>
	</div>
</div>
<script>
    (function ($) {
        'use strict';
        $(document).ready(function () {
            $('#stm_ulisting_pro_popup').hide();
            $('.ulisting_pro_notice .pro a:not(.ulisting-feedback-button)').on('click', function (e) {
                e.preventDefault();
                $('#stm_ulisting_pro_popup').fadeIn();
            })
        })
    })(jQuery);
</script>