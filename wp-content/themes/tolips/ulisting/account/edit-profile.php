<?php
/**
 * Account edit profile
 *
 * Template can be modified by copying it to yourtheme/ulisting/account/edit-profile.php.
 **
 * @see     #
 * @package uListing/Templates
 * @version 1.0.2
 */
use uListing\Classes\StmListingTemplate;

$user_avatar = get_user_meta($user->ID, 'stm_listing_avatar');
$data = array(
	'user_id' => $user->ID,
	'first_name' => $user->first_name,
	'last_name' => $user->last_name,
	'email' => $user->user_email,
	'avatar_image'	=> isset($user_avatar) && !empty($user_avatar) ? current($user_avatar)['url'] : (get_template_directory_uri() . '/images/placehoder-user.jpg')
);

$user_meta = apply_filters('ulisting_user_meta_data', ['user' => $user, 'data' => []]);
$edit_data = apply_filters('ulisting_profile_edit_data', ['user' => $user, 'data' => []]);
$data = array_merge($data, $edit_data['data']);
$data['user_meta'] = $user_meta['data'];
wp_enqueue_script('stm-profile-edit', ULISTING_URL . '/assets/js/frontend/stm-profile-edit.js', array('vue'), ULISTING_VERSION, true);
wp_add_inline_script('stm-profile-edit', "var stm_user_data = json_parse('".ulisting_convert_content(json_encode($data))."');", 'before');
?>


<div id="stm-listing-profile-edit" class="panel-custom p-t-30 p-b-30">
	<h2><?php echo esc_html__( 'Edit Profile', 'tolips' ) ?></h2>
	<div class="listing-profile-edit-content">
		<div class="profile-avata">
			<div class="stm-row">
				<div class="stm-col-12 stm-col-xl-3 stm-col-lg-4">
					<div class="profile-title">

						<h3><?php echo esc_html__('Photo', 'tolips') ?></h3>
						<div class="des"><?php echo esc_html__('Upload your profile photo.', 'tolips') ?></div>
					</div>
				</div>
				<div class="stm-col-12 stm-col-xl-9 stm-col-lg-8">
					<div class="ulisting-form-gruop">
						<img class="stm-listing-profile-avatar" src="<?php echo esc_url($data['avatar_image']); ?>" alt="<?php echo esc_attr($user->user_login); ?>"/>
						<div class="input-file">
							<input type="file" id="file-upload-avatar" ref="avatar" value="Browse" v-on:change="handleFileUpload()" class="form-control"/>
							<label for="file-upload-avatar" class="input-title"><?php echo esc_html__( 'Select Image', 'tolips' ) ?></label>
						</div>	
						<span v-if="errors['avatar']" style="color: red">{{errors['avatar']}}</span>
					</div>
				</div>
			</div>	
		</div>

		<hr>

		<div class="profile-information">
			<div class="stm-row">
				<div class="stm-col-12 stm-col-xl-12 stm-col-lg-12">
					<div class="profile-title">
						<h3><?php echo esc_html__('Profile Information', 'tolips') ?></h3>
						<div class="des"><?php echo esc_html__('Add your information.', 'tolips') ?></div>
					</div>
				</div>

				<div class="stm-col-12 stm-col-xl-12 stm-col-lg-12">
					<div class="stm-row">
						
						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
								<label> <?php echo  esc_html__('First name', "tolips"); ?></label>
								<input type="text" v-model="first_name" class="form-control" placeholder="<?php echo esc_attr__('Enter first name', "tolips"); ?>"/>
								<span v-if="errors['first_name']" style="color: red">{{errors['first_name']}}</span>
							</div>
						</div>

						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
								<label> <?php echo  esc_html__('Last name', "tolips"); ?></label>
								<input type="text" v-model="last_name" class="form-control" placeholder="<?php echo esc_attr__('Enter last name', "tolips"); ?>"/>
								<span v-if="errors['last_name']" style="color: red">{{errors['last_name']}}</span>
							</div>
						</div>

						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
								<label> <?php echo  esc_html__('Email', "tolips"); ?></label>
								<input type="email" v-model="email" class="form-control" placeholder="<?php echo esc_attr__('Enter email', "tolips"); ?>"/>
								<span v-if="errors['email']" style="color: red">{{errors['email']}}</span>
							</div>
						</div>

						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo  esc_html__('Nickname', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.nickname.value" class="form-control" placeholder="<?php echo esc_attr__('Enter Nickname', 'tolips'); ?>"/>
	                  </div>
						</div>

						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo  esc_html__('Position', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.position.value" class="form-control" placeholder="<?php echo esc_attr__('Enter Position', 'tolips'); ?>"/>
	                  </div>
						</div>

						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo esc_html__('Mobile Phone', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.phone_mobile.value" class="form-control" placeholder="<?php echo esc_attr__('Enter Mobile Phone', 'tolips'); ?>"/>
	                  </div>
						</div>

						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo esc_html__('Office Phone', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.phone_office.value" class="form-control" placeholder="<?php echo esc_attr__('Office Phone', 'tolips'); ?>"/>
	                  </div>
						</div>

						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo esc_html__('Fax', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.fax.value" class="form-control" placeholder="<?php echo esc_attr__('Fax', 'tolips'); ?>"/>
	                  </div>
						</div>

						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo esc_html__('Website', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.url.value" class="form-control" placeholder="<?php echo esc_attr__('Website', 'tolips'); ?>"/>
	                  </div>
						</div>

						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo esc_html__('Address', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.address.value" class="form-control" placeholder="<?php echo esc_attr__('Address', 'tolips'); ?>"/>
	                  </div>
						</div>

						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo esc_html__('Latitude', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.latitude.value" class="form-control" placeholder="<?php echo esc_attr__('Latitude Map', 'tolips'); ?>"/>
	                  </div>
						</div>
						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo esc_html__('Longitude', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.longitude.value" class="form-control" placeholder="<?php echo esc_attr__('Longitude Map', 'tolips'); ?>"/>
	                  </div>
						</div>
						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo esc_html__('License', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.license.value" class="form-control" placeholder="<?php echo esc_attr__('License', 'tolips'); ?>"/>
	                  </div>
						</div>
						<div class="stm-col-12 stm-col-xl-6 stm-col-lg-6">
							<div class="ulisting-form-gruop">
	                     <label><?php echo esc_html__('Tax Number', 'tolips'); ?></label>
	                     <input type="text" v-model="user_meta.tax_number.value" class="form-control" placeholder="<?php echo esc_attr__('Tax Number', 'tolips'); ?>"/>
	                  </div>
						</div>
						<div class="stm-col-12 stm-col-xl-12 stm-col-lg-12">
							<div class="ulisting-form-gruop">
	                     <label><?php echo esc_html__('Description', 'tolips'); ?></label>
	                     <textarea type="text" v-model="user_meta.description.value" class="form-control" placeholder="<?php echo esc_attr__('Enter Profile About', 'tolips'); ?>"></textarea>
	                  </div>
						</div>

						<?php do_action("ulisting-profile-edit-form", ['user' => $user])?>

					</div>
				</div>
			</div>	
		</div> 

		<hr>

		<div class="profile-social">
			<div class="stm-row">
				
				<div class="stm-col-12 stm-col-xl-12 stm-col-lg-12">
					<div class="profile-title">
						<h3><?php echo esc_html__('Social', 'tolips') ?></h3>
						<div class="des"><?php echo esc_html__('Add social links', 'tolips') ?></div>
					</div>
				</div>

				<div class="stm-col-12 stm-col-xl-12 stm-col-lg-12">
					<div class="stm-row">
						<?php foreach ($user->get_social() as $k => $v):?>
							<div class="stm-col-12 stm-col-md-6">
								<div class="ulisting-form-gruop">
									<label> <?php echo esc_attr($v['name']); ?></label>
									<input type="text" v-model="user_meta.<?php echo esc_attr($k)?>.value" class="form-control" placeholder="<?php echo esc_attr($v['name']); ?>"/>
								</div>
							</div>
						<?php endforeach;?>

						<div class="stm-col-12 stm-col-md-6">
							<div class="ulisting-form-gruop">
								<label> <?php echo esc_html__('Linkedin', 'tolips'); ?></label>
								<input type="text" v-model="user_meta.linkedin.value" class="form-control" placeholder="<?php echo esc_attr__('Linkedin', 'tolips'); ?>"/>
							</div>
						</div>

						<div class="stm-col-12 stm-col-md-12">
							<button @click="edit" type="button" class="btn-theme btn-medium"><?php echo esc_html__('Update', "tolips"); ?></button>
							<div class="btn-inline margin-top-20" v-if="loading"><?php echo esc_html__('Loading...', 'tolips') ?></div>
							<div class="alert alert-success margin-top-20" v-if="message"  v-bind:class="status" >{{message}}</div>
						</div>

					</div>

				</div>

			</div>	
		</div>

	</div>
		

	<div class="listing-profile-change-password">

		<div class="stm-row change-password">
			<div class="stm-col-12 stm-col-xl-3 stm-col-lg-4">
				<div class="profile-title">
					<h3><?php echo esc_html__('Change Password', 'tolips') ?></h3>
					<div class="des"><?php echo esc_html__('Add your information.', 'tolips') ?></div>
				</div>
			</div>

			<div class="stm-col-12 stm-col-xl-9 stm-col-lg-8">
				<div class="stm-row">
					<div class="stm-col-12 stm-col-md-12">
						<div class="ulisting-form-gruop"><label> <?php echo  esc_html__('Old password', "tolips"); ?></label>
						<input v-model="old_password" type="password" placeholder="<?php echo esc_attr__('Old password', "tolips"); ?>" class="form-control"></div>
						<span v-if="password_errors['old_password']" style="color: red">{{password_errors['old_password']}}</span>
					</div>

					<div class="stm-col-12 stm-col-md-12">
						<div class="ulisting-form-gruop"><label> <?php echo  esc_html__('New password', "tolips"); ?></label>
							<input v-model="new_password" type="password" placeholder="<?php echo esc_attr__('New password', "tolips"); ?>" class="form-control"></div>
						<span v-if="password_errors['new_password']" style="color: red">{{password_errors['new_password']}}</span>
					</div>

					<div class="stm-col-12 stm-col-md-12">
						<div class="ulisting-form-gruop"><label> <?php echo  esc_html__('Confirmation new password', "tolips"); ?></label>
						<input v-model="new_password_confirmation" type="password" placeholder="<?php echo esc_attr__('Confirmation new password', "tolips"); ?>" class="form-control"></div>
						<span v-if="password_errors['new_password_confirmation']" style="color: red">{{password_errors['new_password_confirmation']}}</span>
					</div>

					<div class="stm-col-12 stm-col-md-12">
						<div class="clearfix">
							<p v-if="password_loading" class="btn-inline margin-top-20"><?php echo  esc_html__('Load...', "tolips"); ?></p>
							<button v-if="!password_loading" @click="update_password" type="button" class="btn-theme btn-medium"><?php echo esc_html__('Update password', "tolips"); ?></button>
							<p v-if="password_message"  v-bind:class="password_status" >{{password_message}}</p>
						</div>
					</div>
				</div>	
			</div>	
		</div>
	</div>	


</div>








