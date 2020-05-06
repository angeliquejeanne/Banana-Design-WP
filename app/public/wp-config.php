<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'owcN0ODiY2xZRjFL97s4DJfVCUKorsKfd5KDveNti+O2E2G7Waam3N4lPzs11B7bYxJbFoOrv7LgC8qw6p+kDw==');
define('SECURE_AUTH_KEY',  'w/KKj15nlG7b0GVBMiLNLfmuo6YgEtc1Q383o9hgdU/G+jca9JWsPcK9Q0NwqGG+HdHJNv0QjFO1NIJ/P7x4nA==');
define('LOGGED_IN_KEY',    'OVAR2TSEZVgAf4cRZo/fkmVNfHQzrGxmVteWdaCCMnrt0/TfSjfDjCMfYmyyX6Az02KAY5vc8irhD2sTiqjU1g==');
define('NONCE_KEY',        '+Zf78vqqGN7gFmdgqe/3U40zeB0L+MuwisM3OCUVN2sxMGE3jKflh7NW2nNN++wcDBZD6UtsjjiqfjYshIKUFQ==');
define('AUTH_SALT',        'N+bajmWH/BnuZ8Zcq8E0vX49N7IS/yNo/V1DuvyjaTGnpexSrTKBVNml+FeNubKNoJ1N6I+UwyuJOe6U7VwiPw==');
define('SECURE_AUTH_SALT', 'X6DjEZPug3oBXLKNm3yqht77gBMs7N33zYi3wZ8eCZzvUWKAaNDMDqpF4H0xowfNn/kpDTyTuMyAyNaZ7asR0g==');
define('LOGGED_IN_SALT',   'HYX0TmzbWB/mQ8hPWQ7200h7YCXkYVWqGA/EMr4Q2pTaWhfLjpXWXxLHiDVz+/3rsnXNtPb6Lwv0Xp9c2Zrk/A==');
define('NONCE_SALT',       'aIY9Xs+dCGwO5OEFzNONtaoVNaMxbLJysA9oS0dX7X3xMO6lh2ExH1VfMHmtynMOI132tL55zNaKQOhrUNZrKQ==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
