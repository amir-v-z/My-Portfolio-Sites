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
define( 'DB_NAME', 'amir_v_z' );

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
define( 'AUTH_KEY',         ';wpzegQKF{qOx?[t/(&aq+I/|8My9P8fgtWYuO`3_nCc5-?2r@A=G`zF#ctoZbLq' );
define( 'SECURE_AUTH_KEY',  '#jrKD ]~ZSFuob}zm(ztn;{?L[up3GPsVz.rq;i!Q&_gpWvCH<hR7itPm<*WDEw9' );
define( 'LOGGED_IN_KEY',    'i+)T&i?uJUik}t4ez#[p!R9Lk=FOx)Og}@S~z&pwx<.w.3Qd3^~y:._bxe]OX.P7' );
define( 'NONCE_KEY',        'i[Sg2(z>mlWt]ABRJcVZny)*A)=4N.vd(ifo/4d$!]k-W,m3~&NODn-COeMzk$v1' );
define( 'AUTH_SALT',        'RSA))cF-5MY?gKzAi.?#iYo1tByVpi}TMxi2s`;s)?;z48XZiPk^v6AT{a#xovf$' );
define( 'SECURE_AUTH_SALT', 'AzLR>?vf(r$LzNw0<d$r=6)!{)o FW^F%z^n)Lr{XOtd9~#{,TbyZCQk?X1-g|Bk' );
define( 'LOGGED_IN_SALT',   'Z*c+gQ<9c; 0/2I2kx6u}L)d.(K)6gMgnI] 0j:2;3I?=S/D%Y/J07Tlf.ixR1i*' );
define( 'NONCE_SALT',       '#!md-NM|_[9J%x^09T2v<?,1V9e5r=V)B+6:e78 +`PkaQ P8+|`ksZk#`ePh6&v' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'amr_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
