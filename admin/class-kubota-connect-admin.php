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

	/**
	 * The data manager used to handle data from the Kubota Connect data
	 * 
	 * @since    1.0.0
	 * @access   private
	 * @var      Kubota_Connect_Data_Manager    $data_manager    The data manager used to handle data from the Kubota Connect data.
	 */
	 private $data_manager;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->data_manager = new Kubota_Connect_Data_Manager();
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
		// Register the settings for Kubota Connect
		// This will be handled by the Kubota_Connect_Data_Manager class
		$this->data_manager->register_options();
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

		// Add a submenu for the Kubota Connect highlights page
		add_submenu_page(
			'kubota-connect',
			'Kubota Connect Highlights',
			'Highlights',
			'manage_options',
			'edit.php?post_type=kubota-highlights',
		);

		// Add a submenu for the Kubota Categories
		add_submenu_page(
			'kubota-connect',
			'Kubota Categories',
			'Categories',
			'manage_options',
			'edit-tags.php?taxonomy=category&post_type=kubota-products'
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
		$api_key_token = $this->data_manager->get_kc_api_key_token();
		$sync_options = $this->data_manager->get_kc_options();
		
		ob_start(); // started buffer

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/kubota-connect-admin-settings.php';

		$template = ob_get_contents(); // reading content

		ob_end_clean(); // closing and cleaning buffer

		echo $template;
	}

	// Add the AJAX actions for the Kubota Connect plugin

	/**
	 * Test the connection to the Kubota Connect API
	 * 
	 * This function will test the connection to the Kubota Connect API using the API Key Token provided.
	 * It will return a response with the status of the connection.
	 *
	 * @since    1.0.0
	 */
	public function kubota_connect_test_connection(){
		$data = $_POST['data'];
		$token = $data['token'];

		$response = $this->data_manager->test_connection($token);

		wp_send_json( $response );
	}

	/**
	 * Sync all data with the Kubota Connect API
	 * 
	 * This function will sync all data with the Kubota Connect API.
	 * It will return a response with the status of the sync.
	 *
	 * @since    1.0.0
	 */
	public function kubota_connect_sync_all_data(){
		$response = $this->data_manager->sync_all_data();

		$statusCode = $response['statusCode'] ? $response['statusCode'] : 500;
		$message = $response['message'] ? $response['message'] : 'The Kubota data was synced successfully!';

		$result = array(
			'statusCode' => $statusCode,
			'message' => $message
		);
		
		wp_send_json( $result );		
	}


}
