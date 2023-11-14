<?php

namespace uListing\Classes;

use uListing\Admin\Classes\StmEmailTemplateManager;
use uListing\Classes\StmUser;
use uListing\Classes\Vendor\Validation;

class StmListingAuth {


	public static function stm_listing_login() {
		$result = array(
			'status' => 'error'
		);

		$request_body      = file_get_contents( 'php://input' );
		$data              = json_decode( $request_body, true );
		$data_for_validate = $data;
		$validator         = new Validation();
		$data_for_validate = $validator->sanitize( $data_for_validate );
		$validator->validation_rules( array(
			'login'    => 'required',
			'password' => 'required',
		) );

		$validated_data = $validator->run( $data_for_validate );

		if ( $validated_data === false ) {
			$result['errors'] = $validator->get_errors_array();
			wp_send_json( $result );
			die;
		}

		$currentUser = get_user_by( 'login', $data['login'] );

		if ( ! $currentUser ) {
			wp_send_json( [
				'status'  => 'error',
				'message' => esc_html__( 'Wrong Username or Password', "ulisting" )
			] );
		}
		$user = self::checkVerifiedUser( $data );
		if ( ! $user ) {
			wp_send_json( [
				'status'  => 'error',
				'message' => esc_html__( 'You must confirm your email sent to your mail', "ulisting" )
			] );
		}

		$user = wp_signon( [
			'user_login'    => $data['login'],
			'user_password' => $data['password']
		],
			is_ssl()
		);

		if ( is_wp_error( $user ) ) {
			$result['message'] = esc_html__( 'Wrong Username or Password', "ulisting" );
		} else {
			$result['message'] = esc_html__( 'Successfully logged in. Redirecting...', "ulisting" );
			$result['status']  = 'success';
		}
		wp_send_json( $result );
		die;
	}

	public static function checkVerifiedUser( $data ) {
		$user         = get_user_by( 'login', $data['login'] );
		$user_confirm = get_option( 'uListing-email-store' );

		if ( $user->allcaps['email_confirmation'] != 'true' && $user_confirm['user-confirm']['is_active'] == 0 ) {
			return true;
		}

		$user_meta = get_user_meta( $user->ID );

		if ( ! $user_meta['verified'] ) {
			return false;
		}

		return $user_meta;
	}

	public static function stm_listing_register() {
		$result = array(
			'errors'  => [],
			'message' => null,
			'status'  => 'error'
		);

		$request_body = file_get_contents( 'php://input' );
		$data         = json_decode( $request_body, true );

		if ( ! get_option( 'users_can_register' ) ) {
			$result['message'] = esc_html__( 'User registration is not allowed in this site.', 'ulisting' );
			wp_send_json( $result );
		}

		$data_for_validate = $data;
		$validator         = new Validation();
		$data_for_validate = $validator->sanitize( $data_for_validate );
		$validator->validation_rules( array(
			'email'           => 'required|valid_email',
			'first_name'      => 'required|max_len,50|min_len,3',
			'last_name'       => 'required|max_len,50|min_len,3',
			'login'           => 'required|max_len,50|min_len,3',
			'password'        => 'required|max_len,50|min_len,8',
			'password_repeat' => 'required|equalsfield,password',
			'role'            => 'required',
		) );

		$validated_data = $validator->run( $data_for_validate );

		if ( $validated_data === false ) {
			$result['errors'] = $validator->get_errors_array();
			wp_send_json( $result );
			die;
		}

		extract( $data );
		/**
		 * @var $email ;
		 * @var $first_name ;
		 * @var $last_name ;
		 * @var $login ;
		 * @var $password ;
		 * @var $password_repeat ;
		 * @var $role ;
		 * @var $agency_id ;
		 */

		// Check if User Role is allowed
		$userRole = new UlistingUserRole();
		if ( ! in_array( $role, array_keys( $userRole->roles ) ) && $role != 'agent'  ) {
			$result['message'] = esc_html__( 'This user role is not allowed.', 'ulisting' );
			wp_send_json( $result );
		}

		$user = wp_create_user( $login, $password, $email );

		if ( is_wp_error( $user ) ) {
			$result['message'] = $user->get_error_message();
		} else {
			if ( $user = new StmUser( $user ) ) {
				do_action( "ulisting_profile_edit", [ 'user' => $user, 'data' => $validated_data ] );
				wp_update_user( array(
					'ID'         => $user->ID,
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'role'       => $role
				) );
			}

			$result['status'] = 'success';


			if ( isset( $agency_id ) ) {
				update_user_meta( $user->ID, 'agency_id', $agency_id );
			} else {
				$user_confirm = get_option( 'uListing-email-store' );
				if ( $userRole->roles[ $role ]['capabilities']['email_confirmation'] == 'false' && $user_confirm['user-confirm']['is_active'] == 0 ) {
					wp_signon(
						array(
							'user_login'    => $login,
							'user_password' => $password,
						),
						is_ssl() );
					$result['message'] = esc_html__( 'Registration completed successfully.', "ulisting" );
					$result['reload']  = 1;
				} else {
					$result['message'] = esc_html__( 'Registration successfully. You must confirm your email sent to your mail', "ulisting" );
					$result['reload']  = 0;
				}
			}

			// the row that we need

			$args = [
				'user_role'  => $role,
				'user_email' => $email,
				'user_id'    => $user->ID,
				'user_name'  => $first_name . ' ' . $last_name
			];

			StmEmailTemplateManager::send_email_confirm( $args, $data, $userRole->roles[ $role ]['capabilities'], 'email-confirm' );
			StmEmailTemplateManager::uListing_send_email( $args, 'user-confirm' );
			StmEmailTemplateManager::uListing_send_email( $args, 'user-created', true );
		}
		wp_send_json( $result );
	}

	public static function stm_listing_profile_edit() {

		$result = array(
			'errors'  => [],
			'message' => null,
			'status'  => 'error'
		);

		if ( ! StmVerifyNonce::verifyAjaxNonce() || ! is_user_logged_in() ) {
			wp_send_json( $result );
		}

		$validator         = new Validation();
		$data_for_validate = $validator->sanitize( array_merge( apply_filters( 'ulisting_sanitize_array', $_POST ), apply_filters( 'ulisting_sanitize_array', $_FILES ) ) );

		$validator->validation_rules( array(
			'user_id'    => 'required',
			'email'      => 'required|valid_email',
			'first_name' => 'required|max_len,50|min_len,3',
			'last_name'  => 'required|max_len,50|min_len,3',
			'avatar'     => 'extension,png;jpg'
		) );

		$validated_data = $validator->run( $data_for_validate );

		if ( $validated_data === false ) {
			$result['errors'] = $validator->get_errors_array();
			wp_send_json( $result );
			die;
		}

		extract( $validated_data );
		/**
		 * @var $user_id ;
		 * @var $email ;
		 * @var $first_name ;
		 * @var $last_name ;
		 */

		if ( $user = new StmUser( $user_id ) and $user->ID and $user->ID == get_current_user_id() ) {

			do_action( "ulisting_profile_edit", [ 'user' => $user, 'data' => $validated_data ] );

			$result['status']  = 'success';
			$result['message'] = esc_html__( 'Profile update completed successfully.', "ulisting" );

			if ( isset( $_FILES['avatar'] ) ) {
				$avatar = $user->updateAvatar( $_FILES['avatar'] );
				if ( isset( $avatar['error'] ) ) {
					$result['status']           = 'error';
					$result['errors']['avatar'] = $avatar['message'];
				} else {
					$result['url_avatar'] = $avatar['url'];
				}
			}

			wp_update_user( array(
				'ID'         => $user->ID,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'user_email' => $email,
			) );

			foreach ( $validated_data['user_meta'] as $k => $val ) {
				update_user_meta( $user->ID, $k, apply_filters( 'uListing-sanitize-data', $val ) );
			}

		} else {
			$result['message'] = esc_html__( 'User not found', "ulisting" );
			wp_send_json( $result );
			die;
		}

		wp_send_json( $result );
		die;

	}


}
