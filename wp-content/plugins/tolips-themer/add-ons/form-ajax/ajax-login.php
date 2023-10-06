<?php

/*
* https://gist.github.com/cristianstan/10273612
*/

class Tolips_Addons_Login_Ajax{
	
	private static $instance = null;
   public static function instance() {
      if ( is_null( self::$instance ) ) {
         self::$instance = new self();
      }
      return self::$instance;
   }

	public function __construct(){
		add_action( 'init', array($this, 'ajax_login_init') );
	}

	public function ajax_login_init(){
		if (!is_user_logged_in()) {
			// Enable the user with no privileges to run ajax_login() in AJAX
			add_action( 'wp_ajax_nopriv_ajaxlogin', array($this, 'ajax_login') );
		}
	}

	// Execute the action only if the user isn't logged in

	function ajax_login(){
	 	// First check the nonce, if it fails the function will break
	 	check_ajax_referer( 'tolips-ajax-security-nonce', 'security' );

	 	// Nonce is checked, get the POST data and sign user on
	 	$info = array();
	 	$info['user_login'] = $_POST['username'];
	 	$info['user_password'] = $_POST['password'];
	 	$info['remember'] = true;

	 	$user_signon = wp_signon( $info, false );

	 	if ( !is_wp_error($user_signon) ){
		  	
		  	wp_set_current_user($user_signon->ID);
		  	wp_set_auth_cookie($user_signon->ID);
			$message = esc_html__('Login successful, redirecting...', 'tolips-themer');
		  	echo json_encode(array('logged_in' => true, 'message' => '<div class="alert alert-success">' . $message . '</div>'));
		  	die();

	 	}else{

	 		$message = '';
	 		if(isset($user_signon->errors)){
				foreach ($user_signon->errors as $errors) {
					foreach ($errors as $error) {
						if( empty($message) ){
							$message = $error;
						}else{
							$message .= ' ,' . $error;
						}
					}
				}
		  		echo json_encode(array('logged_in' => false, 'message' => '<div class="alert alert-warning">' . $message . '</div>'));
		  		die();
			}else{
				$message = esc_html__('Login unsuccessful, plese try again!', 'tolips-themer');
				echo json_encode(array('logged_in' => false, 'message' => '<div class="alert alert-warning">' . $message . '</div>'));
				die();
			}
	 	}
	 	
	 	die();
	}

	public static function html_form(){ 
	?>
		<form id="ajax-login-form" method="post" class="ajax-form-content">
		   <div class="form-status"></div>
		   <div class="form-group">
			   <label for="username"><?php echo esc_html__('Username', 'tolips-themer') ?></label>
			   <input id="username" type="text" placeholder="<?php echo esc_html__('Username', 'tolips-themer') ?>" name="username" autocomplete='off' class="form-control">
			</div>
		   <div class="form-group">
			   <label for="password"><?php echo esc_html__('Password', 'tolips-themer') ?></label>
			   <input id="password" type="password" placeholder="******" name="password" autocomplete='off' class="form-control">
			</div>   
		   <div class="form-group form-action">
			   <input class="btn-theme btn-fw" type="submit" value="<?php echo esc_html__('Login', 'tolips-themer') ?>" name="submit">
			</div>   
			<div class="lost-password">
		   	<a class="lost-popup" data-toggle="modal" data-target="#form-ajax-lost-password-popup"><?php esc_html_e('Lost your password?', 'tolips-themer') ?></a>
		   </div>
		</form>
	<?php
	}
}

new Tolips_Addons_Login_Ajax();