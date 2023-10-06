<?php
function tolips_user_data($user){
  	$data = array(
		'nickname'        => isset($user->nickname) ? $user->nickname : '',
      'position'        => isset($user->position) ? $user->position : '',
      'phone_mobile'    => isset($user->phone_mobile) ? $user->phone_mobile : '',
      'phone_office'    => isset($user->phone_office) ? $user->phone_office : '',
      'website_url'     => isset($user->website_url) ? $user->website_url : '',
      'address'         => isset($user->address) ? $user->address : '',
      'location'        => isset($user->location) ? $user->location : '',
      'description'     => isset($user->description) ? $user->description : '',
      'linkedin'        => isset($user->linkedin) ? $user->linkedin : '',
      'user_email'      => isset($user->user_email) ? $user->user_email : '',
  	);
  	return $data;
}