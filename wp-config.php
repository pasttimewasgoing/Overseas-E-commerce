<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '123456' );

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

define( 'AUTH_KEY',         '}^`8:mlGe_Qf}g]8eq,|Rn?$y!d9;RJwSljmXmfW =]WRopAIy<+ %2!j-N_=nJF' );
define( 'SECURE_AUTH_KEY',  'r10ayo]Cr.,o[9(t0w&YI:vm~)3u3GW^CM&F]H4~CJjsofl@]^q[J194yW#XBHX~' );
define( 'LOGGED_IN_KEY',    'kWg4#izIM~}4x0Ff@,G<]0,4.C}z-p3?Av-QBzj]`NDrlRBUJY>S/O`fQ&Jiuw8;' );
define( 'NONCE_KEY',        'a5jV8<t&HaA]oq$A$?lgv,?#MR|%^c<emA=6E%dMbPczs[-n)R.Ln&O?.0n[gs-W' );
define( 'AUTH_SALT',        '!@kEqo*kwH5^82~*Xn.%#?l8FDoFxXuT;w(dufj?m5xGySdy,Pa:?kouAx|v[keo' );
define( 'SECURE_AUTH_SALT', 'Hy.^b0P8+<G{c%@;*E5QYiO(XhLW;t*0(GCiT]<ytBDW#uz*+q2F),%O.EH)T>+B' );
define( 'LOGGED_IN_SALT',   'lYkais{kP-YNFmrXrk2&Vu4 M6D[v5)u^11Q/5Ycuq8F;z44/a+,VXq1kavsy?F8' );
define( 'NONCE_SALT',       'rWOgl^Iq6?6O{@~!P+bD ;wdiFjWJv^l?4=6[(dSemh-9Nw[w^0)`/YyD$c@}`{H' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
