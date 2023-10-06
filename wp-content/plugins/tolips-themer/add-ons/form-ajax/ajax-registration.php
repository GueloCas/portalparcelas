<?php

/*
* https://gist.github.com/vishalbasnet23/1937b45be0ea73784cc5
*/

class Tolips_Addons_Registration_Ajax{
	
	private static $instance = null;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct(){
		add_action('wp_ajax_register_user_frontend', array($this, 'register_user'));
		add_action('wp_ajax_nopriv_register_user_frontend', array($this, 'register_user'));
	}

	function register_user() {
		
		check_ajax_referer( 'tolips-ajax-security-nonce', 'security' );

		$user_name = stripcslashes($_POST['user_name']);
		$user_email = stripcslashes($_POST['user_email']);
		$user_password = $_POST['user_password'];
		$re_user_password = $_POST['re_user_password'];
		$first_name = strtolower($_POST['first_name']);
		$last_name = strtolower($_POST['last_name']);

		if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
			$message = esc_html__( 'Invalid email format', 'tolips-themer');
			echo json_encode(array('message' => '<div class="alert alert-warning">' . $message . '</div>'));
			die();
		}

		if( strlen($user_password) < 5 ){
			$message = esc_html__( 'Password length must be greater than 5', 'tolips-themer');
			echo json_encode(array('message' => '<div class="alert alert-warning">' . $message . '</div>'));
			die();
		}

		if( $user_password != $re_user_password ){
			$message = esc_html__( 'Password must be equal Confirm Password', 'tolips-themer');
			echo json_encode(array('message' => '<div class="alert alert-warning">' . $message . '</div>'));
			die();
		}

		$user_data = array(
			'user_login' => $user_name,
			'user_email' => $user_email,
			'user_pass' => $user_password,
			'user_nicename' => $user_name,
			'display_name' => $user_name,
			'role' => 'subscriber'
		);
		$user_id = wp_insert_user($user_data);
		if (!is_wp_error($user_id)) {
			$message = esc_html__('We have created an account for you.', 'tolips-themer');
			echo json_encode(array('message' => '<div class="alert alert-success">' . $message . '</div>'));
			die();
		}else{

			$message = '';
	 		if(isset($user_id->errors)){

				foreach ($user_id->errors as $errors) {
					foreach ($errors as $error) {
						if( empty($message) ){
							$message = $error;
						}else{
							$message .= ' ,' . $error;
						}
					}
				}
		  		echo json_encode(array('message' => '<div class="alert alert-warning">' . $message . '</div>'));
		  		die();
			}else{

				$message = esc_html__('Register unsuccessful, plese try again!', 'tolips-themer');
				echo json_encode(array('message' => '<div class="alert alert-warning">' . $message . '</div>'));
				die();
			}
		}
		die;
	}

	public static function html_form(){ 
	?>
		<form id="ajax-register-user" method="post" class="ajax-form-content register-form">
			<div class="form-status"></div>
			<div class="form-group">
				<label for="username"><?php echo esc_html__('Username', 'tolips-themer') ?></label>
				<input type="text" name="user_name" class="form-control" placeholder="<?php echo esc_html__('Username', 'tolips-themer') ?>" id="register-username" required>
			</div>
			<div class="form-group">
				<label for="username"><?php echo esc_html__('Email Address', 'tolips-themer') ?></label>
				<input type="email" name="user_email" class="form-control" placeholder="<?php echo esc_html__('Email Address', 'tolips-themer') ?>" id="register-useremail" required>
			</div>
			<div class="form-group">
				<label for="username"><?php echo esc_html__('Password', 'tolips-themer') ?></label>
				<input type="password" name="user_password" class="form-control" placeholder="******" id="register-userpassword" required>
			</div>
			<div class="form-group">
				<label for="username"><?php echo esc_html__('Re-enter Password', 'tolips-themer') ?></label>
				<input type="password" name="re-pwd" class="form-control" placeholder="******" id="register-re-pwd">
			</div>
			<div class="form-group form-action">
				<input type="submit" name="submit" class="btn-theme btn-fw" value="<?php echo esc_html__('Register Now', 'tolips-themer') ?>">
			</div>
	 </form> 
	<?php
	}

}

new Tolips_Addons_Registration_Ajax();