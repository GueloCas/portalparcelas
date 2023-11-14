<?php
/*
	* https://fellowtuts.com/wordpress/forgot-password-with-ajax-in-wordpress-login-and-register/
	* https://www.tutspointer.com/custom-user-forgot-password-using-ajax-in-wordpress/
*/
class Tolips_Addons_Forget_Pwd_Ajax{
	
	private static $instance = null;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct(){
		add_action( 'init', array($this, 'ajax_auth_init') );
	}

	function ajax_auth_init(){ 
		add_action( 'wp_ajax_nopriv_tolips_lost_password', array($this, 'lost_pass_callback') );
		add_action( 'wp_ajax_tolips_lost_password', array($this, 'lost_pass_callback') );
	}
 
	function lost_pass_callback() {
		global $wpdb, $wp_hasher;
		
		check_ajax_referer( 'tolips-ajax-security-nonce', 'security' );
		
		$user_login = $_POST['user_login'];
	 
		if ( empty($user_login) ) {
		  	$mess = esc_html__('Error: Enter a username or e-mail address.', 'tolips-themer');
			echo json_encode(array('loggedin' => false, 'message'=>'<div class="alert alert-warning">' . $mess . '</div>'));
		  	die();
		} else if ( strpos( $user_login, '@' ) ) {
			$user = get_user_by( 'email', trim( $user_login ) );
			if ( empty( $user ) ){
				$mess = esc_html__('Error: There is no user registered with that email address.', 'tolips-themer');
				echo json_encode(array('loggedin' => false, 'message'=>'<div class="alert alert-warning">' . $mess . '</div>'));
				die();
			}
		} else {
			$login = trim( $user_login );
			$user = get_user_by('login', $login);
		}
		if ( !$user ) {
			$mess = esc_html__('Error: Invalid username or email.', 'tolips-themer');
			echo json_encode(array('loggedin' => false, 'message'=>'<div class="alert alert-warning">' . $mess . '</div>'));
			die();
		}
		
		$user_login = $user->user_login;
		$user_email = $user->user_email;
		$key = get_password_reset_key( $user );
	 
		if ( is_wp_error( $key ) ) {
			return $key;
		}
		$message = esc_html__('Hi ', 'tolips-themer') . $user_login . '!' . "\r\n";
		$message .= '<br>' . esc_html__('Someone requested that the password be reset for the following account', 'tolips-themer') . ' ' . network_home_url( '/' ). "\r\n";
		$message .= sprintf(esc_html__(' with username: %s', 'tolips-themer'), $user_login). "\r\n";
		$message .= '<br>' . esc_html__('If this was a mistake, just ignore this email and nothing will happen.', 'tolips-themer'). "\r\n";
		$message .= '<br>' . esc_html__('To reset your password, visit the following address:', 'tolips-themer'). "\r\n";
		$message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n";
		$message .= '<br>' . esc_html__('Thank you!', 'tolips-themer');
		//$message .= esc_url( get_permalink( 113 ) . "?action=rp&key=$key&login=" . rawurlencode($user_login) );
		
		$blogname = is_multisite() ?  $GLOBALS['current_site']->site_name : wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	 
		$title = sprintf( __('[%s] Password Reset'), $blogname );
		
		$from = get_option('admin_email'); 
		$to = $user->user_email; 
		$subject = wp_specialchars_decode($title);
		$sender = 'From: ' . get_option('name') . ' <' . $from . '>' . "\r\n";
		$headers[] = 'MIME-Version: 1.0' . "\r\n";
		$headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers[] = "X-Mailer: PHP \r\n";
		$headers[] = $sender;

		$mail = wp_mail( $to, $subject, $message, $headers );

		if ( $mail ){
			$mess = esc_html__('Success: Check your e-mail for the confirmation link.', 'tolips-themer');
			echo json_encode(array('message'=> '<div class="alert alert-success">' . $mess . '</div>'));
			die();
		}else{
			$mess = esc_html__('The e-mail could not be sent.', 'tolips-themer') . "<br/>" . esc_html__('Possible reason: your host may have disabled the mail() function.', 'tolips-themer');
			echo json_encode(array('message'=>'<div class="alert alert-warning">' . $mess . '</div>'));
			die();
		}
	}

	public static function html_form(){ 
	?>
		<form id="lost-password-form" class="ajax-form-content" method="post">
			<div class="form-status"></div>
			<?php 
				if ( function_exists( 'wp_nonce_field' ) ){
				  wp_nonce_field( 'tolips_user_lost_password_action', 'tolips_user_lost_password_nonce' );
				}
			?>
			<div class="form-group">
				<label for="forget_pwd_user_login"><?php echo esc_html__('Username or E-mail:', 'tolips-themer') ?></label>
				<input type="text" name="user_login" class="control-form input-fw" id="forget_pwd_user_login" placeholder="<?php echo esc_html__('Username', 'tolips-themer') ?>" value="" size="20" />
			</div>
			<div class="form-group form-action">
				<input type="submit" name="wp-submit" class="btn-theme btn-fw" value="<?php echo esc_attr__('Get New Password', 'tolips-themer'); ?>" />
			</div>
		</form>
	<?php   

	}
}

new Tolips_Addons_Forget_Pwd_Ajax();