<?php
/**
 * Account navigation
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/navigation.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.3.1
 */
use uListing\Classes\StmUser;
$active = ulisting_page_endpoint();

if(empty($active))
	$active = "dashboard";
?>
<ul class="nav nav-tabs">
	<li class="nav-item">
		<a class="nav-link <?php echo ($active == 'dashboard')?'active':null?>" href="<?php echo StmUser::getProfileUrl()?>"><?php esc_html_e('Dashboard', "ulisting")?></a>
	</li>
	<?php foreach (StmUser::get_account_link('account-navigation') as $item):?>
		<li class="nav-item">
			<a class="nav-link <?php echo ($active == $item['var'])?'active':null?>" href="<?php echo StmUser::getUrl($item['var'])?>"><?php  esc_html_e($item['title'], "ulisting")?></a>
		</li>
	<?php endforeach;?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo wp_logout_url( home_url() ); ?>"><?php  esc_html_e('Logout', "ulisting")?></a>
    </li>
</ul>


