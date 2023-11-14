<?php
Redux::setSection( $opt_name, array(
	'title' 		=> esc_html__('Login & Register Page', 'tolips'),
	'icon' 		=> 'el-icon-wrench',
	'fields' 	=> array(
		array(
        'id'  	  	=> 'login_info',
        'type'   	=> 'info',
        'icon'   	=> true,
        'raw' 	  	=> '<h3 style="margin: 0;">' . esc_html__( 'Login Tab Settings', 'tolips' ) . '</h3>',
      ),
		array(
			'id'        => 'lt_login_background',
			'type'      => 'media',
		  	'title'     => esc_html__('Background Login Tab', 'tolips'),
		  	'url' 		=> true,
		  	'default' 	=> '',
		),
		array(
		  	'id'        => 'lt_login_title',
		  	'type'      => 'text',
		  	'title'     => esc_html__( 'Login Title', 'tolips' ),
		  	'default'   => esc_html__('Bienvenido', 'tolips')
		),
		array(
		  	'id'        => 'lt_login_desc',
		  	'type'      => 'text',
		  	'title'     => esc_html__( 'Login Description', 'tolips' ),
		  	'default'   => esc_html__('Sign in to continue access.', 'tolips')
		),

		array(
        'id'  			=> 'register_info',
        'type'   		=> 'info',
        'icon'   		=> true,
        'raw' 			=> '<h3 style="margin: 0;">' . esc_html__( 'Register Tab Settings', 'tolips' ) . '</h3>',
      ),
		array(
			'id'        => 'lt_register_background',
			'type'      => 'media',
		  'title'      => esc_html__('Background Register Tab', 'tolips'),
		  	'url' 		=> true,
		  	'default' 	=> '',
		),
		array(
		  	'id'        => 'lt_register_title',
		  	'type'      => 'text',
		  	'title'     => esc_html__( 'Register Title', 'tolips' ),
		  	'default'   => esc_html__('Bienvenido', 'tolips')
		),
		array(
		  	'id'        => 'lt_register_desc',
		  	'type'      => 'text',
		  	'title'     => esc_html__( 'Descripción Registro', 'tolips' ),
		  	'default'   => esc_html__('Regístrate para continuar con el acceso.', 'tolips')
		),
	)
));