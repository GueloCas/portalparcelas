<?php

/*
* https://gist.github.com/vishalbasnet23/1937b45be0ea73784cc5
*/

class Tolips_Addons_Wishlist_Ajax{
	
	private static $instance = null;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct(){
		add_action('wp_ajax_tolips_wishlist', array($this, 'wishlist_add'));
		add_action('wp_ajax_nopriv_tolips_wishlist', array($this, 'wishlist_add'));
	}

	function wishlist_add() {

		check_ajax_referer( 'tolips-ajax-security-nonce', 'security' );

		$mode = $_POST['mode'];
		$post_id = $_POST['post_id'];
		$user_id = get_current_user_id();
		// Show Popup login of !user_logged
		if (!is_user_logged_in()) {
			echo json_encode(array('logged_in' => false, 'add_wishlist' => ''));
			die();
		}

		if($mode == 'add'){
			$wishlist = get_user_meta($user_id, 'lt_wishlist', true);
			if ( is_array($wishlist) ) {
				if ( !in_array($post_id, $wishlist) ) {
					$wishlist[] = $post_id;
					update_user_meta( $user_id, 'lt_wishlist', $wishlist );
				}
			} else {
				$wishlist = array($post_id);
				update_user_meta( $user_id, 'lt_wishlist', $wishlist );
			}

			echo json_encode( array('logged_in' => true, 'add_wishlist' => 'added', 'mode' => 'add') );
			die();
		}

		if($mode == 'remove'){
			$wishlist = get_user_meta($user_id, 'lt_wishlist', true);
		
			if ( is_array($wishlist) ){
				foreach ($wishlist as $key => $value) {
					if ( $post_id == $value ) {
						unset($wishlist[$key]);
					}
				}
			} 
			update_user_meta( $user_id, 'lt_wishlist', $wishlist );
			echo json_encode( array('logged_in' => true, 'remove_wishlist' => 'removed', 'mode' => 'remove') );
			die();
		}

	}

	public static function html_icon($post_id, $text = ''){ 
		$user_id = get_current_user_id();
		$data = get_user_meta($user_id, 'lt_wishlist', true);
		$check_wishlist = false;
		if ( is_array($data) ) {
			if ( in_array($post_id, $data) ) {
				$check_wishlist = true;
			}
		}else{
			if($post_id == $data){
				$check_wishlist = true;
			}
		}
	?>
		<div class="wishlist-icon-content" style="display: none">
			<a href="#" data-post_id="<?php echo esc_attr($post_id) ?>" class="ajax-wishlist-link <?php echo (!$check_wishlist ? 'wishlist-add' : 'wishlist-remove wishlist-added') ?>" title="<?php echo esc_attr__('Wishlist', 'tolips-themer') ?>">
				<i class="icon far fa-heart"></i>
				<?php if($text){ 
					echo '<span>' . $text . '</span>';
				} ?>
			</a>
	 	</div> 
	<?php
	}

}

new Tolips_Addons_Wishlist_Ajax();