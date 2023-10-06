<?php
/*
   * https://fellowtuts.com/wordpress/change-password-with-ajax-in-wordpress-login-and-register/
   * https://www.tutspointer.com/custom-user-change-password-using-ajax-in-wordpress/
*/
class Tolips_Addons_Change_Pwd_Ajax{
   
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

   public function ajax_auth_init(){ 
      add_action( 'wp_ajax_nopriv_tolips_change_password', array($this, 'ajax_change_password') );
      add_action( 'wp_ajax_tolips_change_password', array($this, 'ajax_change_password') );
   }
 
   public function ajax_change_password(){
   
      // First check the nonce, if it fails the function will break
      check_ajax_referer( 'tolips-ajax-security-nonce', 'security' );
      
      if ( !is_user_logged_in() ) {
         return;
      }

      $old_pwd = sanitize_text_field( $_POST['old_password'] );
      $new_pwd = sanitize_text_field( $_POST['new_password'] );
      $re_pwd = sanitize_text_field( $_POST['re_password'] );

      if ( empty( $old_pwd ) || empty( $new_pwd ) || empty( $re_pwd ) ) {
         echo json_encode(array(
            'message' => '<div class="alert alert-warning">' . esc_html__('All fields are required.', 'tolips-themer') . '</div>'
         ));
         exit;
      }

      if ( $new_password != $retype_password ) {
         echo json_encode(array(
            'message' => '<div class="alert alert-warning">' . esc_html__('New and retyped password are not same.', 'tolips-themer') . '</div>'
         ));
         exit;
      }

      $user = wp_get_current_user();

      if ( ! wp_check_password( $old_pwd, $user->data->user_pass, $user->ID ) ) {
         echo json_encode(array(
            'message' => '<div class="alert alert-warning">' . esc_html__('Your old password is not correct.', 'tolips-themer') . '</div>'
         ));
         exit;
      }

      wp_set_password( $new_pwd, $user->ID );
      
      $info['user_login'] = $user->nickname;
      $info['user_password'] = $new_pwd;
      $info['remember'] = 1;
      wp_signon( $info, false );

      echo json_encode(array(
            'message' =>  '<div class="alert alert-success">' . esc_html__('Your password has been successfully changed.', 'tolips-themer') . '</div>'
         ));
         exit;
   }

   public static function html_form(){ 
   ?>
      <form id="change_password" class="ajax-form-content" method="post">    
         <div class="form-status"></div>
         <?php wp_nonce_field('tolips_change_pwd_nonce', 'security_change_pwd'); ?>  
         <h3 class="title"><?php echo esc_html__('Change Password', 'tolips-themer') ?></h3>
         <div class="form-group">
            <input id="old_password" type="text" class="required" placeholder="<?php echo esc_html__('Old Password', 'tolips-themer') ?>" name="old_password">
         </div>
         <div class="form-group">
            <input id="new_password" type="text" class="required" placeholder="<?php echo esc_html__('New Password', 'tolips-themer') ?>" name="new_password">
         </div>
         <div class="form-group">
            <input id="re_password" type="text" class="required" placeholder="<?php echo esc_html__('Re-password', 'tolips-themer') ?>" name="re_password">
         </div>
         <div class="form-group">
            <input class="submit_button" type="submit" value="<?php echo esc_html__('Submit', 'tolips-themer') ?>">
         </div>
      </form> 
   <?php   

   }
}

new Tolips_Addons_Change_Pwd_Ajax();