<?php
/**
 * Comment user comment
 *
 * Template can be modified by copying it to yourtheme/ulisting/comment/user-comment.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0
 */
if($user->access_write_review())
	echo do_shortcode("[ulisting-comment type=ulisting_user object_id=".$user->ID."]");
?>