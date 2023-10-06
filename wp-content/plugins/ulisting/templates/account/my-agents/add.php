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
	'agency_id' => $user->ID
);

wp_enqueue_script('stm-agent-add', ULISTING_URL . '/assets/js/frontend/stm-agent-add.js', array('vue'), ULISTING_VERSION, true);
wp_add_inline_script('stm-agent-add', "var ulisting_user_agent_add_data = json_parse('". ulisting_convert_content(json_encode($data)) ."');", 'before');
?>

<div id="stm-listing-agent-add" class="container">
	<div class="ulisting-form-gruop">
		<label> <?php echo  esc_html__('Login', "ulisting"); ?></label>
		<input type="text"
			data-v-model="login"
			class="form-control"
			placeholder="<?php esc_html_e('Enter login', "ulisting"); ?>"/>
		<span data-v-if="errors['login']" style="color: red">{{errors['login']}}</span>
	</div>

	<div class="ulisting-form-gruop">
		<label> <?php echo  esc_html__('First name', "ulisting"); ?></label>
		<input type="text"
			data-v-model="first_name"
			class="form-control"
			placeholder="<?php esc_html_e('Enter first name', "ulisting"); ?>"/>
		<span data-v-if="errors['first_name']" style="color: red">{{errors['first_name']}}</span>
	</div>

	<div class="ulisting-form-gruop">
		<label> <?php echo  esc_html__('Last name', "ulisting"); ?></label>
		<input type="text"
			data-v-model="last_name"
			class="form-control"
			placeholder="<?php esc_html_e('Enter last name', "ulisting"); ?>"/>
		<span data-v-if="errors['last_name']" style="color: red">{{errors['last_name']}}</span>
	</div>

	<div class="ulisting-form-gruop">
		<label> <?php echo  esc_html__('Email', "ulisting"); ?></label>
		<input type="email"
			data-v-model="email"
			class="form-control"
			placeholder="<?php esc_html_e('Enter email', "ulisting"); ?>"/>
		    <span data-v-if="errors['email']" style="color: red">{{errors['email']}}</span>
	</div>

	<div class="ulisting-form-gruop">
		<label> <?php echo  esc_html__('Password', "ulisting"); ?></label>
		<input type="password"
			data-v-model="password"
			class="form-control"
			placeholder="<?php esc_html_e('Enter password', "ulisting"); ?>"/>
			<span data-v-if="errors['password']" style="color: red">{{errors['password']}}</span>
	</div>

	<div class="ulisting-form-gruop">
		<label> <?php echo  esc_html__('Password repeat', "ulisting"); ?></label>
		<input type="password"
			data-v-model="password_repeat"
			class="form-control"
			placeholder="<?php esc_html_e('Enter password repeat', "ulisting"); ?>"/>
			<span data-v-if="errors['password_repeat']" style="color: red">{{errors['password_repeat']}}</span>
	</div>

	<div class="ulisting-form-gruop">
		<button data-v-on_click="add_agent" type="button" class="btn btn-primary w-full"><?php echo  esc_html__('Add Agent', "ulisting"); ?></button>
	</div>

	<div data-v-if="loading">Loading...</div>

	<div data-v-if="message"  data-v-bind_class="status" >{{message}}</div>

</div>






