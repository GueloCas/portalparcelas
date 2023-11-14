<?php
/**
 * Account login
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/login.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.5.7
 */
wp_enqueue_script('stm-login', ULISTING_URL . '/assets/js/frontend/stm-login.js', array('vue'), ULISTING_VERSION, true);
$background_image = get_template_directory_uri() . '/images/bg-account.jpg';
$background_media_id = tolips_get_option('lt_login_background', array('id'=> 0));
if( isset($background_media_id['id']) && $background_media_id['id'] ){
	$image = wp_get_attachment_image_src( $background_media_id['id'], 'full');
	if(isset($image[0]) && $image[0]){
		$background_image = esc_url($image[0]);
	}
}

?>

<div class="stm-listing-login">
	<div class="row no-margin">
		<div class="col-12 col-md-4 login-content-left">
		  <span class="bg-login" style="background-image: url('<?php echo esc_url($background_image) ?>')" /></span>
		  <div class="content-inner">
			 <h3 class="title"><?php echo tolips_get_option('lt_login_title', esc_html__('Welcome Back', 'tolips') ) ?></h3>
			 <div class="desc"><?php echo tolips_get_option('lt_login_desc', esc_html__('Sign in to continue access', 'tolips') ) ?></div>
		  </div>  
		</div>  

		<div class="login-form-content col-12 col-md-8">
		  <div class="ulisting-form-gruop" :class="{error: errors['login']}">
				<label> <?php echo esc_html__('Login', "tolips"); ?></label>
				<input type="text"
						 @keyup.enter="logIn"
						 v-model="login"
						 class="form-control"
						 placeholder="<?php echo esc_attr__('Enter login', "tolips"); ?>"/>
				<span  v-if="errors['login']" style="color: red">{{errors['login']}}</span>
		  </div>

		  <div class="ulisting-form-gruop" :class="{error: errors['password']}">
				<label> <?php echo esc_html__('Password', "tolips"); ?></label>
				<input type="password"
						 @keyup.enter="logIn"
						 v-model="password"
						 class="form-control"
						 placeholder="<?php echo esc_attr__('Enter password', "tolips"); ?>"/>
				<span  v-if="errors['password']" style="color: red">{{errors['password']}}</span>
		  </div>

		  <div class="ulisting-form-gruop">
				<div class="stm-row">
					 <div class="stm-col">
						  <label>
								<input type="checkbox" value="1" :true-value="1" :false-value="0"
										 v-model="remember"> <?php esc_html_e('Remember me', "tolips") ?>
						  </label>
					 </div>
					 <div class="stm-col forgot-password">
					 	<a class="lost-popup" data-toggle="modal" data-target="#form-ajax-lost-password-popup"><?php esc_html_e('Forgot Password?', 'tolips') ?></a>
					 </div>
				</div>
		  </div>
		  <div class="ulisting-form-gruop">
				<button @click="logIn" type="button"
						  class="btn btn-primary w-full"><?php echo esc_html__('Login', "tolips"); ?></button>
		  </div>
		  <div v-if="loading">Loading...</div>
		  <div v-if="message" :class="status">{{message}}</div>
		</div> 
	 </div> 
</div>

<?php
	 echo apply_filters('usl_social_login_view', '');
?>

