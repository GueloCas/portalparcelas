<?php
use uListing\Lib\PricingPlan\Classes\StmUserPlanListTable;
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo get_admin_page_title() ?></h1>
	<a href="<?php echo get_admin_url(null,'/edit.php?post_type=stm_pricing_plans&page=stm_user_plans_add')?>" class="page-title-action">Add New</a>
	<form action="<?php echo admin_url('edit.php?post_type=stm_pricing_plans&page=stm_user_plans')?>" method="get">
		<input type="hidden" name="post_type" value="stm_pricing_plans">
		<input type="hidden" name="page" value="stm_user_plans">
		<?php
			$userPlanListTable = new StmUserPlanListTable();
			$userPlanListTable->display();
		?>
		<a href="<?php echo admin_url("edit.php?post_type=stm_pricing_plans&page=stm_user_plans")?>" class="button"><?php echo esc_html__('Clear filter', "ulisting")?></a>
	</form>
</div>


