<?php
/*
   * https://fellowtuts.com/wordpress/change-password-with-ajax-in-wordpress-login-and-register/
   * https://www.tutspointer.com/custom-user-change-password-using-ajax-in-wordpress/
*/
class Tolips_Addons_Change_User_Info_Ajax{
   
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
      add_action( 'wp_ajax_nopriv_tolips_change_user_info', array($this, 'ajax_change') );
      add_action( 'wp_ajax_tolips_change_user_info', array($this, 'ajax_change') );
   }
 
 
   public function ajax_change(){
   
      // First check the nonce, if it fails the function will break
      check_ajax_referer( 'tolips-ajax-security-nonce', 'security' );
      
      if ( !is_user_logged_in() ) {
         return;
      }
      $form_data = isset($_POST['form_data']) && $_POST['form_data'] ? $_POST['form_data'] : false;
      $data = array();
      if($form_data){
         $form_data = urldecode($_REQUEST['form_data']);
         parse_str($form_data, $data);
      }   
      $keys = array( 'first_name', 'last_name', 'display_name', 'phone', 'website', 'description');

      $user = wp_get_current_user();
      $userid = $user->ID;
      foreach ($keys as $key) {
         $value = isset($data[$key]) ? sanitize_text_field( $data[$key] ) : '';
         update_user_meta( $user, $key, $value );
      }

      if( isset($data['email']) && $data['email'] ){
         update_user_meta( $userid, 'user_email', $email );
         wp_update_user( array(
            'ID'            => $userid,
            'user_email'    => $data['email'],
         ) );
      }

      if( isset($data['user_address']) && $data['user_address'] ){
         update_user_meta( $userid, 'user_address', $data['user_address'] );
      }

      if( isset($data['user_socials']) && $data['user_socials'] ){
         update_user_meta( $userid, 'user_socials', $data['user_socials'] );
      }

      if( isset($data['current__user_avatar']) && $data['current__user_avatar'] ){
         $image_id = $this->create_attachment($data['current__user_avatar']);
         if($image_id){
            update_user_meta( $userid, '_user_avatar', $image_id );
         }
      }

      echo json_encode(array(
         'message'=> '<div class="alert alert-success">' . esc_html__('Your Profile has been successfully changed.', 'tolips-themer') . '</div>'
      ));
      exit;
   }

   protected function create_attachment( $attachment_url ) {
      include_once ABSPATH . 'wp-admin/includes/image.php';
      include_once ABSPATH . 'wp-admin/includes/media.php';

      $upload_dir     = wp_upload_dir();
      $attachment_url = esc_url( $attachment_url, [ 'http', 'https' ] );
      if ( empty( $attachment_url ) ) {
         return 0;
      }

      $attachment_url_parts = wp_parse_url( $attachment_url );

      // Relative paths aren't allowed.
      if ( false !== strpos( $attachment_url_parts['path'], '../' ) ) {
         return 0;
      }

      $attachment_url = sprintf( '%s://%s%s', $attachment_url_parts['scheme'], $attachment_url_parts['host'], $attachment_url_parts['path'] );

      $attachment_url = str_replace( [ $upload_dir['baseurl'], WP_CONTENT_URL, site_url( '/' ) ], [ $upload_dir['basedir'], WP_CONTENT_DIR, ABSPATH ], $attachment_url );
      if ( empty( $attachment_url ) || ! is_string( $attachment_url ) ) {
         return 0;
      }

      $attachment = [
         'post_title'   => wpjm_get_the_job_title( $this->job_id ),
         'post_content' => '',
         'post_status'  => 'inherit',
         'post_parent'  => $this->job_id,
         'guid'         => $attachment_url,
      ];

      $info = wp_check_filetype( $attachment_url );
      if ( $info ) {
         $attachment['post_mime_type'] = $info['type'];
      }

      $attachment_id = wp_insert_attachment( $attachment, $attachment_url, $this->job_id );

      if ( ! is_wp_error( $attachment_id ) ) {
         wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_url ) );
         return $attachment_id;
      }

      return 0;
   }
}

new Tolips_Addons_Change_User_Info_Ajax();