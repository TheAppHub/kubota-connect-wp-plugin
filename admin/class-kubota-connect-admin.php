<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/admin
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 */
class Kubota_Connect_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Kubota_Connect_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kubota_Connect_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kubota-connect-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Kubota_Connect_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kubota_Connect_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kubota-connect-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * This function will register the settings for Kubota Connect within WordPress.
	 * 
	 * This function will be called by the Kubota_Connect_Loader class.
	 *
	 * @since    1.0.0
	 */
	public function register_options(){
		register_setting('kc-settings-group', 'kc_api_key_token');
		register_setting('kc-settings-group', 'kc_product_sync');
		register_setting('kc-settings-group', 'kc_finance_sync');
		register_setting('kc-settings-group', 'kc_finance_first_of_month');
		register_setting('kc-settings-group', 'kc_slider_sync');
	}

	/**
	 * Add a Kubota Connect menu item to the admin menu
	 * 
	 * This function will add a menu item to the admin menu for Kubota Connect.
	 * It will also add submenus for the Kubota Connect settings, products, finance and slider pages.
	 * 
	 * This function will be called by the Kubota_Connect_Loader class.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {
		add_menu_page(
			'Kubota Connect',
			'Kubota Connect',
			'manage_options',
			'kubota-connect',
			array( $this, 'display_admin_settings_page' ),
			'dashicons-kubota-connect',
			24
		);

		// Add a submenu for the Kubota Connect settings page
		add_submenu_page(
			'kubota-connect',
			'Kubota Connect Settings',
			'Settings',
			'manage_options',
			'kubota-connect',
			array( $this, 'display_admin_settings_page' )
		);

		// Add a submenu for the Kubota Connect products page
		add_submenu_page(
			'kubota-connect',
			'Kubota Connect Products',
			'Products',
			'manage_options',
			'edit.php?post_type=kubota-products'
		);

		// Add a submenu for the Kubota Connect finance page
		add_submenu_page(
			'kubota-connect',
			'Kubota Connect Finance',
			'Finance Offers',
			'manage_options',
			'edit.php?post_type=kubota-finance',
		);

		// Add a submenu for the Kubota Connect slider page
		add_submenu_page(
			'kubota-connect',
			'Kubota Connect Slider',
			'Slider',
			'manage_options',
			'edit.php?post_type=kubota-slides',
		);

		
	}

	/**
	 * Display the Kubota Connect settings page
	 * 
	 * This function will display the Kubota Connect settings page in the admin area.
	 * It will include the settings form and the options saved in the database.
	 *
	 * @since    1.0.0
	 */
	public function display_admin_settings_page() {
		$options = $this->get_kc_options();
		
		ob_start(); // started buffer

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/kubota-connect-admin-settings.php';

		$template = ob_get_contents(); // reading content

		ob_end_clean(); // closing and cleaning buffer

		echo $template;
	}

	/**
	 * Get the Kubota Connect options
	 * 
	 * This function will return the options saved in the database for Kubota Connect.
	 * If the options are not saved in the database, it will return the default values.
	 *
	 * @since    1.0.0
	 */
	public function get_kc_options(){
		$current_options = array();

		try {
			$current_options = array(
				'kc_token' => $this->get_api_key_token() ? $this->get_api_key_token() : $this->get_defaults('kc_token'),
				'kc_product_sync' => $this->get_option( 'kc_product_sync' ),
				'kc_finance_sync' => $this->get_option( 'kc_finance_sync' ),
				'kc_finance_first_of_month' => $this->get_option( 'kc_finance_first_of_month' ),
				'kc_slider_sync' => $this->get_option( 'kc_slider_sync' ),
			);
		} catch (Exception $e) {
			$current_options['token'] = '';
		}

		return $current_options;
	}

	/**
	 * Get the Kubota Connect options
	 *
	 * This function will return the value saved in the database for the option name provided.
	 * If the option is not saved in the database, it will return the default value.
	 *
	 * @since    1.0.0
	 */
	private function get_option($option_name) {
		return get_option( $option_name ) ? get_option( $option_name ) : $this->get_defaults($option_name);
	}

	/**
	 * Get the API Key Token
	 * 
	 * This function will return the API Key Token from the wp-config.php file if it is defined there.
	 * If it is not defined in the wp-config.php file, it will return the value saved in the database.
	 *
	 * @since    1.0.0
	 */
	private function get_api_key_token( ) {
		$isDefined = defined("KC_API_KEY_TOKEN");
		if($isDefined) return "KC_API_KEY_TOKEN";

		$savedInDB = get_option( 'kc_api_key_token' );
		if($savedInDB) return $savedInDB;

		return '';
	}

	/**
	 * Defaults for Kubota Connect options
	 *
	 * @since    1.0.0
	 */
	private function get_defaults($option_name) {
		$defaults = array(
			'kc_token' => '',
			'kc_product_sync' => 'monthly',
			'kc_finance_sync' => 'fortnighlty',
			'kc_finance_first_of_month' => '',
			'kc_slider_sync' => 'daily',
		);

		return $defaults[$option_name];
	}

}
