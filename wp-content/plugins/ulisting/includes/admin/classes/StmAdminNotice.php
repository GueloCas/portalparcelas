<?php

namespace uListing\Admin\Classes;

class StmAdminNotice
{
	public static $features = [
		[
			'title' => "uListing Subscription",
			'subtitle' => "Monetize your website by applying your own daily, weekly, or monthly pricing plans for clients.",
			'image' => ULISTING_URL . '/assets/img/notices/ulisting-subscription.jpg',
		],
		[
			'title' => "uListing User Roles",
			'subtitle' => "Distribute roles among the users of your website and assign each profile a specific user type.",
			'image' => ULISTING_URL . '/assets/img/notices/ulisting-user-roles.jpg',
		],
		[
			'title' => "uListing Compare",
			'subtitle' => "Enable the comparison of similar type listings and provide a better experience for your users.",
			'image' => ULISTING_URL . '/assets/img/notices/ulisting-compare.jpg',
		],
		[
			'title' => "uListing Social Login",
			'subtitle' => "Make it easy for customers, enable social login for them, and the ability to use their social media accounts to register.",
			'image' => ULISTING_URL . '/assets/img/notices/ulisting-social-login.jpg',
		],
		[
			'title' => "uListing Wishlist",
			'subtitle' => "Guests liked some of the listings? Let them add their favorites to the wishlist and save for later.",
			'image' => ULISTING_URL . '/assets/img/notices/ulisting-wishlist.jpg',
		],
	];

    public function __construct()
    {
		add_action('admin_notices', [$this, 'render_top_bar']);
		add_action('wp_ajax_stm_ulisting_ajax_add_feedback', [$this, 'add_feedback']);
		add_action('stm_admin_notice_rate_ulisting_single', [$this, 'stm_admin_notice_rate_ulisting_single']);
		add_action('stm_listing_type_created', [$this, 'stm_listing_type_created_set_option'], 100);
    }

    /**
     * @return StmAdminNotice
     */
    public static function init()
    {
        return new StmAdminNotice();
    }

	/**
	 * Render Pro Notice
	 */
	public function render_top_bar()
	{
		if ( $this->is_ulisting_page() ) {
			$pro_inactive   = $this->is_pro_inactive();
			$feedback_added = $this->is_feedback_added();

			if ( $pro_inactive ) {
				wp_enqueue_script('owl.carousel', ULISTING_URL .'/assets/js/owl.carousel.min.js', array(), false, true);
				wp_enqueue_style('owl.carousel', ULISTING_URL . '/assets/css/owl.carousel.min.css');

				ulisting_render_template(ULISTING_ADMIN_PATH . '/views/notices/pro_popup.php', ['features' => self::$features], true);
			}

			if ( ! $feedback_added ) {
				wp_enqueue_script('ulisting-feedback', ULISTING_URL .'/assets/js/feedback.js', array(), false, true);
				wp_enqueue_style('ulisting-feedback', ULISTING_URL . '/assets/css/admin/feedback.css');

				ulisting_render_template(ULISTING_ADMIN_PATH . '/views/notices/feedback.php', ['ticket_url' => $this->get_ticket_url()], true);
			}

			ulisting_render_template(ULISTING_ADMIN_PATH . '/views/notices/top_bar.php', ['pro_inactive' => $pro_inactive, 'feedback_added' => $feedback_added], true);
		}
	}

	/**
	 * Add Feedback
	 */
	public function add_feedback()
	{
		update_option( 'stm_ulisting_feedback_added', true );
	}

	/**
	 * @return bool
	 */
	public function is_ulisting_page() {
		return (
			( !empty($_GET['post_type']) && in_array(sanitize_text_field($_GET['post_type']), ['listing_type', 'listing', 'stm_pricing_plans']) )
			|| ( !empty($_GET['page']) && in_array(sanitize_text_field($_GET['page']), ['listing_attribute', 'inventory-list']) )
		);
	}

	/**
	 * @return bool
	 */
	public function is_pro_inactive() {
		return ( !ulisting_subscription_active() && !ulisting_user_role_active() );
	}

	/**
	 * @return bool
	 */
	public function is_feedback_added() {
		return get_option( 'stm_ulisting_feedback_added', false );
	}

	/**
	 * Get Support Request URL
	 *
	 * @return string
	 */
	public function get_ticket_url() {
		$type = $this->is_pro_inactive() ? 'pre-sale' : 'support';
		return "https://support.stylemixthemes.com/tickets/new/{$type}?item_id=23";
	}

	public function stm_listing_type_created_set_option() {
		$created = get_option( 'stm_listing_type_created', false );
		if ( ! $created ) {
			$data = [ 'show_time' => time(), 'step' => 0, 'prev_action' => '' ];
			set_transient( 'stm_ulisting_single_notice_setting', $data );
			update_option( 'stm_listing_type_created', true );
		}
	}

	public function stm_admin_notice_rate_ulisting_single( $data ) {
		if ( is_array( $data ) ) {
			$data['title']   = 'You did it!';
			$data['content'] = 'Now you are done with your first <strong>Listing Type</strong>. Please grant us a <strong>5 Stars</strong> rate and we would be much appreciated!</strong>';
		}

		return $data;
	}

}