<?php
/**
 * $Desc
 *
 * @author     Gaviasthemes Team     
 * @copyright  Copyright (C) 2021 gaviasthemes. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 */
$keywords = isset($_GET['s']) ? $_GET['s'] : '';
?>
<form method="get" class="searchform gva-main-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="gva-search">
		<input name="s" maxlength="40" value="<?php echo esc_attr($keywords) ?>" class="form-control input-large input-search" type="text" size="20" placeholder="<?php echo esc_attr__('Search...', 'tolips') ?>">
		<span class="input-group-addon input-large btn-search">
			<input type="submit" class="fa" value="" />
         <i class="icon las la-search"></i>
		</span>
	</div>
</form>


