<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://theapphub.com.au
 * @since             1.0.0
 * @package           Kubota_Connect
 *
 * @wordpress-plugin
 * Plugin Name:       Kubota Connect
 * Plugin URI:        https://api.kubota.io/dealer-api-docs
 * Description:       Effortlessly connect your WordPress site to Kubota Connect and retrieve dealer-specific products, finance offers, and promotion slides. Enhance your website's functionality by showcasing the latest Kubota equipment, offers, and promotions tailored specifically to your dealership.
 * Version:           0.9.0
 * Author:            The App Hub
 * Author URI:        https://theapphub.com.au/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kubota-connect
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'KUBOTA_CONNECT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kubota-connect-activator.php
 */
function activate_kubota_connect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-activator.php';
	Kubota_Connect_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kubota-connect-deactivator.php
 */
function deactivate_kubota_connect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-deactivator.php';
	Kubota_Connect_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_kubota_connect' );
register_deactivation_hook( __FILE__, 'deactivate_kubota_connect' );

/**
 * Define Carbon Fields directory
 */
define( 'Carbon_Fields\DIR', plugin_dir_path( __FILE__ ) . '/vendor/htmlburger/carbon-fields/' );

/**
 * Include the Composer autoload file
 */
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) :
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
endif;

add_action('after_setup_theme', function () {
	\Carbon_Fields\Carbon_Fields::boot();
});

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kubota_connect() {

	require_once plugin_dir_path( __FILE__ ) . 'helpers/class-kubota-connect-base-importer.php';
	require_once plugin_dir_path( __FILE__ ) . 'helpers/class-image-handler.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-config.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-key-manager.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-api-client.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-password-manager.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-categories.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-products.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-finance-offers.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-highlights.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-activator.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-deactivator.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kubota-connect-cron-jobs.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/class-kubota-connect-admin.php';

	$plugin = new Kubota_Connect();
}

run_kubota_connect();
