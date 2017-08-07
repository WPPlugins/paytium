<?php

/**
 * Paytium
 *
 * @package     PT
 * @author      David de Boer <david@davdeb.com>
 * @license     GPL-2.0+
 * @link        http://www.paytium.nl
 * @copyright   2015-2017 David de Boer
 * @copyright   Paytium is based on Stripe Checkout by Phil Derksen and Stripe Checkout Companion by Kyle M. Brown
 * @copyright   2014-2015 Phil Derksen for Stripe Checkout
 * @copyright   2014-2015 Kyle M. Brown for Stripe Checkout Companion
 *
 * @wordpress-plugin
 * Plugin Name: Paytium
 * Plugin URI: http://www.paytium.nl
 * Description: Easily add iDEAL to your WordPress site! For iDEAL buttons, forms with custom fields & donations
 * Version: 1.5.1
 * Author: David de Boer
 * Author URI: http://www.davdeb.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: paytium
 * Domain Path: /languages/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( ! defined( 'PT_MAIN_FILE' ) ) {
	define( 'PT_MAIN_FILE', __FILE__ );
}

if ( ! defined( 'PT_PATH' ) ) {
	define( 'PT_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'PT_URL' ) ) {
	define( 'PT_URL', plugins_url( '', __FILE__ ) . '/' );
}

if ( ! defined( 'PT_VERSION' ) ) {
	define( 'PT_VERSION', '1.5.1' );
}

if ( ! defined( 'PT_NAME' ) ) {
	define( 'PT_NAME', 'Paytium' );
}

if ( ! defined( 'PT_PACKAGE' ) ) {
	define( 'PT_PACKAGE', 'paytium' );
}

if ( ! class_exists( 'Paytium' ) ) {
	require_once( PT_PATH . 'class-paytium.php' );
}

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( PT_MAIN_FILE, array ( 'Paytium', 'activate' ) );

// Check for required and recommended versions of PHP before loading plugin.
// https://github.com/WPupdatePHP/wp-update-php
if ( ! class_exists( 'WPUpdatePhp' ) ) {
	require_once( PT_PATH . 'libraries/WPUpdatePhp.php' );
}

$updatePhp = new WPUpdatePhp( '5.3', '5.4' );
$updatePhp->set_plugin_name( 'Paytium' );

// Show admin notice and don't execute rest of plugin if it doesn't meet required version of PHP.
// Note the plugin will still be active.
if ( $updatePhp->does_it_meet_required_php_version( PHP_VERSION ) ) {

	// TODO Uncomment recommended admin notice once it can be hidden by user.
	// Show admin notice for recommended version of PHP, but if required version still met continue loading plugin.
	//$updatePhp->does_it_meet_recommended_php_version( PHP_VERSION );

	function Paytium() {

		$paytium = Paytium::get_instance();

		require_once( PT_PATH . 'includes/class-shortcode-tracker.php' );
		Paytium_Shortcode_Tracker::get_instance();

		return $paytium;

	}


	Paytium();

}

/**
 * Install Paytium.
 *
 * Execute code when Paytium is activated.
 *
 * @since 1.0.0
 */
function install_paytium() {

	// Default settings
	add_option( 'paytium_enable_remember', 1 );
	add_option( 'paytium_uninstall_save_settings', 1 );
	add_option( 'paytium_always_enqueue', 1 );

}

register_activation_hook( __FILE__, 'install_paytium' );
