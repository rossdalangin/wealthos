<?php
/**
 * Plugin Name: WealthOS
 * Plugin URI:  https://wealthos.local
 * Description: Your Personal Wealth Operating System. An integrated educational and financial-planning system for WordPress.
 * Version:     1.0.0
 * Author:      WealthOS Team
 * Author URI:  https://wealthos.local
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wealthos
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP:      8.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WEALTHOS_VERSION', '1.0.0' );
define( 'WEALTHOS_FILE', __FILE__ );
define( 'WEALTHOS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WEALTHOS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoload classes or load core includes.
 */
require_once WEALTHOS_PATH . 'includes/class-wealthos.php';
require_once WEALTHOS_PATH . 'includes/class-wealthos-database.php';
require_once WEALTHOS_PATH . 'includes/class-wealthos-security.php';
require_once WEALTHOS_PATH . 'includes/class-wealthos-settings.php';
require_once WEALTHOS_PATH . 'includes/class-wealthos-api.php';

/**
 * Register activation hook.
 */
register_activation_hook( __FILE__, array( 'WealthOS_Database', 'activate' ) );

/**
 * Register deactivation hook.
 */
register_deactivation_hook( __FILE__, array( 'WealthOS_Database', 'deactivate' ) );

/**
 * Initialize the plugin.
 */
function wealthos_init() {
	return WealthOS::instance();
}

add_action( 'plugins_loaded', 'wealthos_init' );
