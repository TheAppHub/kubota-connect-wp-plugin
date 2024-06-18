<?php

/**
 * Handles Kubota Connect Data
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Handles Kubota Connect Data
 *
 * This call defines all code necessary to handle data from Kubota Connect API and store it in the database.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 */
class Kubota_Connect_Data_Manager {

	/**
	 * The HTTP client used to make requests to the Kubota Connect API
	 */
	private $client;

	/**
	 * The data encryption class used to encrypt and decrypt data
	 */
	private $password_manager;

	/**
	 * The key for the API Key Token in the database
	 */
	private $api_key_token = 'kc_api_key_token'; // This is the key for the API Key Token in the database

	private $product_sync = 'kc_product_sync'; // This is the key for the Product Sync in the database
	private $finance_sync = 'kc_finance_sync'; // This is the key for the Finance Sync in the database
	private $finance_first_of_month = 'kc_finance_first_of_month'; // This is the key for the Finance First of Month in the database
	private $highlight_sync = 'kc_highlight_sync'; // This is the key for the Highlight Sync in the database
	private $dealer_name = 'kc_dealer_name'; // This is the key for the Highlight Sync in the database

	public function __construct() {
		$this->password_manager = new Kubota_Connect_Password_Manager();

		$api_version = '1';
		$api_key_token = $this->get_api_key_token();
		$this->client = new Kubota_Connect_Http($api_version, $api_key_token);
	}

	/**
	 * This function will register the settings for Kubota Connect within WordPress.
	 * 
	 * This function will be called by the Kubota_Connect_Loader class.
	 *
	 * @since    1.0.0
	 */
	public function register_options(){
		register_setting('kc-api-key-token', $this->api_key_token);
		register_setting('kc-settings-group', $this->product_sync);
		register_setting('kc-settings-group', $this->finance_sync);
		register_setting('kc-settings-group', $this->finance_first_of_month);
		register_setting('kc-settings-group', $this->highlight_sync);
		register_setting('kc-settings-group', $this->dealer_name);
	}

	/**
	 * Get the API Key Token
	 *
	 * This function will return the API Key Token
	 * 
	 * @since    1.0.0
	 */
	public function get_kc_api_key_token(){
		$hardcoded_token = $this->constant_key_is_defined();
		if($hardcoded_token) {
			return '************'; // Return a placeholder for the hardcoded token (12 asterisks)
		}

		$token_inDB = $this->get_key_token_from_db();
		if($token_inDB !== '') {
			return '**********'; // Return a placeholder for the token in the database (10 asterisks)
		}

		return '';
	}

	/**
	 * Get the Kubota Connect options
	 * 
	 * This function will return the sync options saved in the database for Kubota Connect.
	 * If the options are not saved in the database, it will return the default values.
	 *
	 * @since    1.0.0
	 */
	public function get_kc_options(){
		$current_options = array();

		try {
			$current_options = array(
				$this->product_sync => $this->get_option( $this->product_sync ),
				$this->finance_sync => $this->get_option( $this->finance_sync ),
				$this->finance_first_of_month => $this->get_option( $this->finance_first_of_month ),
				$this->highlight_sync => $this->get_option( $this->highlight_sync ),
				$this->dealer_name => $this->get_option( $this->dealer_name ),
			);
		} catch (Exception $e) {
			$current_options = array(
				$this->product_sync => $this->get_defaults($this->product_sync),
				$this->finance_sync => $this->get_defaults($this->finance_sync),
				$this->finance_first_of_month => $this->get_defaults($this->finance_first_of_month),
				$this->highlight_sync => $this->get_defaults($this->highlight_sync),
				$this->dealer_name => $this->get_defaults($this->dealer_name),
			);
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
		$option = get_option( $option_name );
		return $option ? $option : $this->get_defaults($option_name);
	}


	/**
	 * Defaults for Kubota Connect options
	 *
	 * @since    1.0.0
	 */
	private function get_defaults($option_name) {
		$defaults = array(
			$this->api_key_token => '',
			$this->product_sync => 'monthly',
			$this->finance_sync => 'fortnighlty',
			$this->finance_first_of_month => '',
			$this->highlight_sync => 'daily',
			$this->dealer_name=> '',
		);

		return $defaults[$option_name];
	}

	/**
	 * Test connection to Kubota Connect API
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function test_connection($token) {
		$data = $this->client->test_connection($token);

		if($data->statusCode == 200) {
			$data->message = 'You have succesfully connected ' . $data->name . ' to Kubota Connect. You can start syncing data now.';

			// Save the dealer name in the database
			update_option($this->dealer_name, $data->name);

			// Save the token in the database
			$encrypt_token = $this->password_manager->encrypt($token);
			update_option($this->api_key_token, $encrypt_token);
		} else {
			delete_option($this->dealer_name);
			delete_option($this->api_key_token);

		}

		return $data;
	}

	/**
	 * Sync All data 
	 * 
	 * @since    1.0.0
	 */
	public function sync_all_data() {
		return $this->get_api_key_token();
	}

	/**
	 * Get the API Key Token
	 * 
	 * This function will return the API Key Token from the wp-config.php file if it is defined there.
	 * If it is not defined in the wp-config.php file, it will return the value saved in the database.
	 * If the value is saved in the database, it will decrypt the token and return it.
	 * If the value is not saved in the database, it will return an empty string.
	 *
	 * @since    1.0.0
	 */
	private function get_api_key_token( ) {
		// If it is defined, return the API Key Token
		if($this->constant_key_is_defined()) {
			return $this->get_constant_key();
		}

		// If it is not defined in the wp-config.php file, check if a value is saved in the database
		return $this->get_key_token_from_db();
	}
	/**
	 * Check if the constant API key token is defined in wp-config.php
	 * 
	 * @since    1.0.0
	 * 
	 * @return boolean
	 */
	private function constant_key_is_defined(){
		return defined('KC_API_KEY_TOKEN');
	}

	/**
	 * Get the constant API key token from wp-config.php
	 * 
	 * @since    1.0.0
	 * 
	 * @return string
	 */
	private function get_constant_key(){
		return KC_API_KEY_TOKEN;
	}

	private function get_key_token_from_db(){
		$token = get_option( $this->api_key_token, '' );
		
		// If the value is saved in the database, decrypt the token and return it
		if($token !== '') {
			return $this->password_manager->decrypt($token);
		}

		return '';
	}

}
