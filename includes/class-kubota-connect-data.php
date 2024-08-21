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
	private $api_key_token_in_db = 'kc_api_key_token'; // This is the key for the API Key Token in the database

	private $product_sync = 'kc_product_sync'; // This is the key for the Product Sync in the database
	private $finance_sync = 'kc_finance_sync'; // This is the key for the Finance Sync in the database
	private $finance_first_of_month = 'kc_finance_first_of_month'; // This is the key for the Finance First of Month in the database
	private $highlight_sync = 'kc_highlight_sync'; // This is the key for the Highlight Sync in the database
	private $dealer_name = 'kc_dealer_name'; // This is the key for the Highlight Sync in the database

	public function __construct() {
		$this->password_manager = new Kubota_Connect_Password_Manager();

		$api_version = '1';
		$this->client = new Kubota_Connect_Http($api_version, $this->get_api_key_token());
	}

	/**
	 * This function will register the settings for Kubota Connect within WordPress.
	 * 
	 * This function will be called by the Kubota_Connect_Loader class.
	 *
	 * @since    1.0.0
	 */
	public function register_options(){
		register_setting('kc-api-key-token', $this->api_key_token_in_db);
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
			return '************************************************'; // Return a placeholder for the hardcoded token (12 asterisks)
		}

		$token_inDB = $this->get_key_token_from_db();
		if($token_inDB !== '') {
			return '************************************************'; // Return a placeholder for the token in the database (10 asterisks)
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
			$this->api_key_token_in_db => '',
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
			update_option($this->api_key_token_in_db, $encrypt_token);
		} else {
			$data->message = 'The connection to Kubota Connect was not successful. Please check your API Key Token and try again.';
			delete_option($this->dealer_name);
			delete_option($this->api_key_token_in_db);

		}

		return $data;
	}

	/**
	 * Sync All data 
	 * 
	 * @since    1.0.0
	 */
	public function sync_all_data() {
		$message = 'The Kubota data was synced successfully!<br>';

		try {
			// $ag_response = $this->sync_products('agriculture');
			// $message .= $ag_response;

			// $ce_response = $this->sync_products('construction');
			// $message .= $ce_response;

			$finance_response = $this->sync_finance();
			$message .= $finance_response;

			$highlights_response = $this->sync_highlights();
			$message .= $highlights_response;
		} catch (Exception $e) {
			return array(
				'statusCode' => 500,
				'message' => 'The Kubota data was not synced successfully. Error: ' . $e->getMessage()
			);
		}

		// Return the message
		return array(
			'statusCode' => 200,
			'message' => $message
		);
	}

	private function sync_products($category){
		$post_type = 'kubota-products';

		$response = $this->client->get_products($category);

		$products_counter = 0;

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$this->remove_outdated($post_type, $response);

		foreach($response as $product) {

			$data = array(
				'post_title' => $product->name,
				'post_name' => $product->id,
				'post_type' => $post_type,
				'post_content' => $product->description,
				'post_status' => 'publish',
			);

			// Check if the product exists in the database
			$product_in_db = false;
			$product_in_db = $this->post_exists_in_db($product->id, $post_type);
			
			if($product_in_db) {
			// If the product exists, update the post
				$data['ID'] = $product_in_db[0]->ID;
				$update = wp_update_post($data, true);
			} else {
			// If the product does not exist, create a new post
				$update = wp_insert_post($data, true);
			}

			$products_counter++;

			if (is_wp_error($update)) {
				return array(
					'statusCode' => 500,
					'message' => 'Kubota finance offers were not synced successfully. Error: ' . $update->get_error_message()
				);
			}
		}

		return '<strong>' . $products_counter . ' ' . $category  . ' products</strong> were created or updated.<br>';

	}

	private function sync_finance(){
		$post_type = 'kubota-finance';

		$response = $this->client->get_finance_offers();

		$finances_counter = 0;

		$this->remove_outdated($post_type, $response);

		foreach($response as $finance) {
			$data = array(
				'post_title' => $finance->name,
				'post_name' => $finance->id,
				'post_type' => $post_type,
				'post_content' => '',
				'post_status' => 'publish',
			);

			// Check if the finance offer exists in the database
			$finance_in_db = false;
			$finance_in_db = $this->post_exists_in_db($finance->id, $post_type);
			
			if($finance_in_db) {
			// If the finance offer exists, get the ID and update the post
				$data['ID'] = $finance_in_db[0]->ID;
				$post_id = wp_update_post($data, true);
				if( has_post_thumbnail( $post_id ) ){
					$attachment_id = get_post_thumbnail_id( $post_id );
					wp_delete_attachment($attachment_id, true);
				}
			} else {
				// If the finance offer does not exist, create a new post
				$data['post_status'] = 'publish';
				$post_id = wp_insert_post($data, true);
			}

		
			if (is_wp_error($post_id)) {
				return array(
					'statusCode' => 500,
					'message' => 'Kubota finance offers were not synced successfully. Error: ' . $post_id->get_error_message()
				);
			}

			// Update custom fields 
			// carbon_set_post_meta( $post_id , 'finance_hero_image', $finance->description );
			if($finance->type) carbon_set_post_meta( $post_id , 'finance_type', $finance->type );
			if($finance->rate) carbon_set_post_meta( $post_id , 'finance_comparison_rate', $finance->rate );
			if($finance->depositInProcent) carbon_set_post_meta( $post_id , 'finance_deposit', $finance->depositInProcent );
			if($finance->termInMonths) carbon_set_post_meta( $post_id , 'finance_term', $finance->termInMonths );
			if($finance->terms) carbon_set_post_meta( $post_id , 'finance_additional_details', $finance->terms );
			if($finance->rateType) carbon_set_post_meta( $post_id , 'finance_rate_type', $finance->rateType );
	

			// Update image 
			$image_url = $finance->image->xlarge;
			$image_id = media_sideload_image( $image_url, $post_id, $finance->name, 'id' );
			update_post_meta( $image_id, '_wp_attachment_image_alt', $finance->name );
			set_post_thumbnail( $post_id, $image_id );

			$finances_counter++;
		}

		$message = ($finances_counter == 0) ? 'No finance offers were created or updated.<br>' : '<strong>'. $finances_counter . ' finance offers</strong> were created or updated.<br>';
		if($finances_counter == 1) {
			$message = '<strong>'. $finances_counter . ' finance offer</strong> was created or updated.<br>';
		}
		
		return $message;

	}

	private function sync_highlights(){
		$post_type = 'kubota-highlights';

		$response = $this->client->get_highlights();

		$highlights_counter = 0;

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		foreach($response as $highlight) {
			$data = array(
				'post_title' => $highlight->title,
				'post_name' => $highlight->id,
				'post_type' => $post_type,
			);

			// Check if the highlight exists in the database
			$highlight_in_db = false;
			$highlight_in_db = $this->post_exists_in_db($highlight->id, $post_type);
			
			if($highlight_in_db) {
			// If the highlight exists, update the post
				$data['ID'] = $highlight_in_db[0]->ID;
				$post_id = wp_update_post($data, true);
				if( has_post_thumbnail( $post_id ) ){
					$attachment_id = get_post_thumbnail_id( $post_id );
					wp_delete_attachment($attachment_id, true);
				}
			} else {
			// If the highlight does not exist, create a new post
				$data['post_status'] = 'publish';
				$post_id = wp_insert_post($data, true);
			}

			if (is_wp_error($post_id)) {
				return array(
					'statusCode' => 500,
					'message' => 'Kubota highlights were not synced successfully. Error: ' . $post_id->get_error_message()
				);
			}

			// Update custom fields 
			if($highlight->description) carbon_set_post_meta( $post_id , 'highlight_info', $highlight->description );
			if($highlight->buttonText) carbon_set_post_meta( $post_id , 'highlight_link-text', $highlight->buttonText );
			if($highlight->link) carbon_set_post_meta( $post_id , 'highlight_link', $highlight->link );
			if($highlight->backgroundColour) carbon_set_post_meta( $post_id , 'highlight_color', $highlight->backgroundColour );

			// Update image 
			$image_url = $highlight->image->xlarge;
			$image_id = media_sideload_image( $image_url, $post_id, $highlight->title, 'id' );
			update_post_meta( $image_id, '_wp_attachment_image_alt', $highlight->title );
			set_post_thumbnail( $post_id, $image_id );

			$highlights_counter++;	
		}

		$message = ($highlights_counter == 0) ? 'No highlights were created or updated.<br>' : '<strong>'. $highlights_counter . ' highlights</strong> were created or updated.<br>';
		if($highlights_counter == 1) {
			$message = '<strong>'. $highlights_counter . ' highlight</strong> was created or updated.<br>';
		}
		
		return $message;
	
	}

	private function post_exists_in_db($the_slug, $post_type){
		$args = array(
			'name'			=> $the_slug,
			'post_type'		=> $post_type,
			'numberposts'	=> 1
		  );

		return get_posts($args);
	}

	private function remove_outdated($post_type, $new_data){
		$args = array(
			'post_type' => $post_type,
			'numberposts' => -1
		);

		$posts = get_posts($args);

		foreach($posts as $post) {
			$found = false;
			foreach($new_data as $data) {
				if($post->post_name == $data->id) {
					$found = true;
					break;
				}
			}

			if(!$found) {
				wp_delete_post($post->ID, true);
			}
		}
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
		$token = get_option( $this->api_key_token_in_db);
		
		// If the value is saved in the database, decrypt the token and return it
		if($token !== '') {
			return $this->password_manager->decrypt($token);
		}

		return '';
	}

}
