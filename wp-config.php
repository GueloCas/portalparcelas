<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'portalparcelas' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'jt|XbWefW0p0r92cRoz-7UWf-qew6w[`|txp&dC;pHuabJJN%wn}pkkuE%R|1nS)' );
define( 'SECURE_AUTH_KEY',  '1/*h <]zl80+PM?|Vy~SjjA`9X Rabs~&_`zNe$%+qHJ#n Cozrd8q/&-_$819fq' );
define( 'LOGGED_IN_KEY',    '-5u%ix*hBB4wO3R**e0<:+h3cMtElWaM=Z1SlYN[U/c$a:uygq #zf6yn72m3!Mn' );
define( 'NONCE_KEY',        'e76~8J#H1V{m4S_,_`Cc<D,[ow33BV-(w5R?8+-K-:*sWY*a/eL_K9P 9[v1f]?%' );
define( 'AUTH_SALT',        'ZZ(*W8LKr![*nL3=iDG(;7@E)}yDq_h# .j*ew~,*]ZKvxg2Nn(zW9&o^[-}DE:$' );
define( 'SECURE_AUTH_SALT', '9.C-j5SrJ&3A%U<r/+ #RF#;hz@.sW_XQmrY;azZ.w&%V?f~*<a!SN#tU5+Z=}Zl' );
define( 'LOGGED_IN_SALT',   '0Pv{{9)uP1B-E(C&^-cN~ip1ww4.^Na#eBo7#fWsnUABT}(p{xx2(xMw%%VBE)E-' );
define( 'NONCE_SALT',       'jCDxM9P<~!7XSXIEOzJVi[t+T(/jW6?&kIz>K-Q.]b4-*O?{Bl0tem;Jl}.6(#$l' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */

ini_set('display_errors','Off');
ini_set('display_reporting', E_ALL);
define( 'WP_DEBUG', false );
define('WP_DEBUG_DISPLAY', false);
/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
