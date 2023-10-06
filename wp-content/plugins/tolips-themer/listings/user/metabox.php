<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class Tolips_User_Metabox{
	public function __construct(){ 
		add_action( 'show_user_profile', array($this, 'profile_socials') );
		add_action( 'edit_user_profile', array($this, 'profile_socials') );

		add_action( 'personal_options_update', array($this, 'save'));
  		add_action( 'edit_user_profile_update', array($this, 'save'));
	}

	public function profile_socials( $user ){
	  	$data = get_the_author_meta( 'user_socials', $user->ID, false );
	  	$user_address = get_the_author_meta( 'user_address', $user->ID );
	  	$keys = array(
	  		'facebook' 		=> 'Facebook', 
	  		'twitter' 		=> 'Twitter', 
	  		'google' 		=> 'Google', 
	  		'pinterest'		=> 'Pinterest', 
	  		'linkedin' 		=> 'Linkedin', 
	  		'instagram'		=> 'Instagram'
	  	);
	?>
		<h3><?php echo esc_html__('User Information', 'tolips-themer') ?></h3>
		<table class="form-table">
			<input class="regular-text" name="user_address" placeholder="Address" value="<?php echo $user_address ?>" />
			<?php foreach ($keys as $key => $title) { ?>
				<tr>
					<th><?php echo esc_html($title) ?></th>
					<td>
						<input class="regular-text" name="user_socials[<?php echo $key ?>]" placeholder="<?php echo esc_html($title) ?>" value="<?php echo ( isset($data[$key]) ? $data[$key] : '' ) ?>" />
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php
	}

	function save( $user_id ) {
    	if ( ! current_user_can( 'edit_user', $user_id ) ) {
     		return false;
    	}

    	if ( !empty( $_POST['user_socials'] ) ) {
     		update_usermeta( $user_id, 'user_socials', $_POST['user_socials'] );
    	}

    	if ( !empty( $_POST['user_address'] ) ) {
     		update_usermeta( $user_id, 'user_address', $_POST['user_address'] );
    	}
  	}

}

new Tolips_User_Metabox();
