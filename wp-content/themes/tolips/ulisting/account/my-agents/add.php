<?php
/**
 * Account add a new agent
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/my-agents/add.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.4
 */
use uListing\Classes\UlistingUserRole;
$data = array(
	'agency_id' => $user->ID,
);
tolips_listing_action_add_agent();

wp_enqueue_script('stm-agent-add', ULISTING_URL . '/assets/js/frontend/stm-agent-add.js', array('vue'), ULISTING_VERSION, true);
wp_add_inline_script('stm-agent-add', "var ulisting_user_agent_add_data = json_parse('". ulisting_convert_content(json_encode($data)) ."');", 'before');
?>

<div id="stm-listing-agent-add">
	<h2><?php echo esc_html__('My Agents', 'tolips') ?></h2>
	<div class="listing-agent-add-form">
		<div class="stm-row">
			<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
				<div class="ulisting-form-gruop">
					<label> <?php echo  esc_html__('Login', "tolips"); ?></label>
					<input type="text"
						data-v-model="login"
						class="form-control"
						placeholder="<?php echo esc_attr__('Enter login', "tolips"); ?>"/>
					<span data-v-if="errors['login']" style="color: red">{{errors['login']}}</span>
				</div>
			</div>	

			<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
				<div class="ulisting-form-gruop">
					<label> <?php echo  esc_html__('Email', "tolips"); ?></label>
					<input type="email"
						data-v-model="email"
						class="form-control"
						placeholder="<?php echo esc_attr__('Enter email', "tolips"); ?>"/>
					    <span data-v-if="errors['email']" style="color: red">{{errors['email']}}</span>
				</div>
			</div>	

			<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
				<div class="ulisting-form-gruop">
					<label> <?php echo  esc_html__('First name', "tolips"); ?></label>
					<input type="text"
						data-v-model="first_name"
						class="form-control"
						placeholder="<?php echo esc_attr__('Enter first name', "tolips"); ?>"/>
					<span data-v-if="errors['first_name']" style="color: red">{{errors['first_name']}}</span>
				</div>
			</div>	

			<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
				<div class="ulisting-form-gruop">
					<label> <?php echo  esc_html__('Last name', "tolips"); ?></label>
					<input type="text"
						data-v-model="last_name"
						class="form-control"
						placeholder="<?php echo esc_attr__('Enter last name', "tolips"); ?>"/>
					<span data-v-if="errors['last_name']" style="color: red">{{errors['last_name']}}</span>
				</div>
			</div>

			

			<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
				<div class="ulisting-form-gruop">
					<label> <?php echo  esc_html__('Password', "tolips"); ?></label>
					<input type="password"
						data-v-model="password"
						class="form-control"
						placeholder="<?php echo esc_attr__('Enter password', "tolips"); ?>"/>
						<span data-v-if="errors['password']" style="color: red">{{errors['password']}}</span>
				</div>
			</div>

			<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
				<div class="ulisting-form-gruop">
					<label> <?php echo  esc_html__('Password repeat', "tolips"); ?></label>
					<input type="password"
						data-v-model="password_repeat"
						class="form-control"
						placeholder="<?php echo esc_attr__('Enter password repeat', "tolips"); ?>"/>
						<span data-v-if="errors['password_repeat']" style="color: red">{{errors['password_repeat']}}</span>
				</div>
			</div>

			<div class="stm-col-12 stm-col-xl-12 stm-col-lg-12">
				<div class="ulisting-form-gruop">
					<button data-v-on_click="add_agent" type="button" class="btn-theme btn-medium"><?php echo esc_html__('Add Agent', "tolips"); ?></button>
				</div>
				<div data-v-if="loading"><?php echo esc_html__('Loading...', 'tolips') ?></div>
				<div data-v-if="message"  data-v-bind_class="status">{{message}}</div>
			</div>

		</div>		
	</div>	

</div>