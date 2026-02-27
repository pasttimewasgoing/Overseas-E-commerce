<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

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

define( 'AUTH_KEY',         '8p?uGd%~k=,9BHv+J*XlfjI!ti`Ne8Vj u-~B~,9vSUMm,-;xo~ K@ca0ucl^,4[' );
define( 'SECURE_AUTH_KEY',  'hD7v +Xp^C)D9ViC3qZlWh!#I=L+uzOG1{Elid@2$BPz5+ojNk9/HbLM;MW}DqO:' );
define( 'LOGGED_IN_KEY',    ': .-vY`e9Hhf>r;Z.CCbv>,dw8[Z|#?7wzcvk?0U.h,$y}YmFCHU_;P{TO*o[J^}' );
define( 'NONCE_KEY',        'mE=[DVjbo,mdK jxm>v$eESqEhIetisx2dZ/3(!M87l0p)I}Ln{w@VD!mA%!rBJ|' );
define( 'AUTH_SALT',        'GQUKtF?sH8f}$anaH4w^n.AY?3C]7LUv4s*GXt](uXY8el:EIl+bpt4,1.=B.xqN' );
define( 'SECURE_AUTH_SALT', 'zZ5X&>=fy}DH]c;K}r%n&O|1iB$gF0E@)Po:UGsmWl;J]t[=*Zg~3G,Lw]qlnvy~' );
define( 'LOGGED_IN_SALT',   'I>2f{<?D/awH#L-LM ]2e3-;2jetw[::N4-|SS{SwiWPy>7f}~<|8w-TQlQZ<6hd' );
define( 'NONCE_SALT',       'E]~g*HS!KVU-)]1&kk~?{1:G^c7qg X&@z N67czj$Ec>e:i}!nFRNmulD5j*^&I' );


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
