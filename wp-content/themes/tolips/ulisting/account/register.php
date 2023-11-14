<?php
/**
 * Account register
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/register.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0.2
 */
use uListing\Classes\UlistingUserRole;

$register_data = [];
$user_role_list = [];
$userRole = new UlistingUserRole();
foreach ($userRole->roles as $key => $role){
	$user_role_list[] = [
		"id" => $key,
		"text" => $role['name'],
	];
}
$register_data['user_role_list'] = $user_role_list;
wp_enqueue_script('stm-register', ULISTING_URL . '/assets/js/frontend/stm-register.js', array('vue'), ULISTING_VERSION, true);
wp_add_inline_script('stm-register', "var ulisting_user_register_data = json_parse('". ulisting_convert_content(json_encode($register_data)) ."');", 'before');

$background_image = get_template_directory_uri() . '/images/bg-account.jpg';
$background_media_id = tolips_get_option('lt_register_background', array('id'=> 0));
if( isset($background_media_id['id']) && $background_media_id['id'] ){
	$image = wp_get_attachment_image_src( $background_media_id['id'], 'full');
	if(isset($image[0]) && $image[0]){
		$background_image = esc_url($image[0]);
	}
}

?>

<div id="stm-listing-register">

	<div class="row no-margin">
		
		<div class="col-12 col-md-4 register-content-left">
		  	<span class="bg-register" style="background-image: url('<?php echo esc_url($background_image) ?>')" /></span>
		  	<div class="content-inner">
		  		<h3 class="title"><?php echo tolips_get_option('lt_register_title', esc_html__('Bienvenido', 'tolips') ) ?></h3>
			 	<div class="desc"><?php echo tolips_get_option('lt_register_desc', esc_html__('Regístrate para continuar con el acceso.', 'tolips') ) ?></div>
		  	</div>  
		</div>  

		<div class="register-form-content col-12 col-md-8">

			<div class="row">
				<div class="col-12 col-md-12">
					<div class="ulisting-form-gruop">
						<label> <?php echo  esc_html__('Nombre de usuario', "tolips"); ?></label>
						<input type="text"
							data-v-model="login"
							class="form-control"
							placeholder="<?php echo esc_attr__('Ingrese nombre de usuario', "tolips"); ?>"/>
						<span data-v-if="errors['login']" style="color: red">{{errors['login']}}</span>
					</div>
				</div>
			</div>			

			<div class="row">
				<div class="col-12 col-md-6">
					<div class="ulisting-form-gruop">
						<label> <?php echo esc_html__('Nombre', "tolips"); ?></label>
						<input type="text"
							data-v-model="first_name"
							class="form-control"
							placeholder="<?php echo esc_attr__('Ingrese su nombre', "tolips"); ?>"/>
						<span data-v-if="errors['first_name']" style="color: red">{{errors['first_name']}}</span>
					</div>
				</div>	
				<div class="col-12 col-md-6">
					<div class="ulisting-form-gruop">
						<label> <?php echo  esc_html__('Apellido', "tolips"); ?></label>
						<input type="text"
							data-v-model="last_name"
							class="form-control"
							placeholder="<?php echo esc_attr__('Ingrese su apellido', "tolips"); ?>"/>
						<span data-v-if="errors['last_name']" style="color: red">{{errors['last_name']}}</span>
					</div>
				</div>
			</div>		

			<div class="row">
				<div class="col-12 col-md-6">
					<div class="ulisting-form-gruop">
						<label> <?php echo  esc_html__('Email', "tolips"); ?></label>
						<input type="email"
							data-v-model="email"
							class="form-control"
							placeholder="<?php echo esc_attr__('Ingrese su email', "tolips"); ?>"/>
						    <span data-v-if="errors['email']" style="color: red">{{errors['email']}}</span>
					</div>
				</div>	
				<div class="col-12 col-md-6">
					<div class="ulisting-form-gruop">
						<label> <?php echo  esc_html__('Tipo de usuario', "tolips"); ?></label>
						<ulisting-select2 :options='user_role_list' data-v-model='role'></ulisting-select2>
						<span data-v-if="errors['role']" style="color: red">{{errors['role']}}</span>
					</div>
				</div>	
			</div>	

			<div class="row">
				<div class="col-12 col-md-6">
					<div class="ulisting-form-gruop">
						<label> <?php echo  esc_html__('Contraseña', "tolips"); ?></label>
						<input type="password"
							data-v-model="password"
							class="form-control"
							placeholder="<?php echo esc_attr__('Ingrese su contraseña', "tolips"); ?>"/>
							<span data-v-if="errors['password']" style="color: red">{{errors['password']}}</span>
					</div>
				</div>	
				<div class="col-12 col-md-6">
					<div class="ulisting-form-gruop">
						<label> <?php echo  esc_html__('Confirmar contraseña', "tolips"); ?></label>
						<input type="password"
							data-v-model="password_repeat"
							class="form-control"
							placeholder="<?php echo esc_attr__('Confirme su contraseña', "tolips"); ?>"/>
							<span data-v-if="errors['password_repeat']" style="color: red">{{errors['password_repeat']}}</span>
					</div>
				</div>
			</div>		

			<div class="ulisting-form-gruop">
				<button data-v-on_click="register" type="button" class="btn btn-theme"><?php echo esc_html__('Registrarse', "tolips"); ?></button>
			</div>

			<div data-v-if="loading"><?php echo esc_html__('Cargando...', "tolips"); ?></div>

			<div data-v-if="message"  data-v-bind_class="status" >{{message}}</div>
		</div>
	</div>		

</div>






