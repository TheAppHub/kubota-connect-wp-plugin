<?php

/**
 * Handles Kubota Connect Http calls
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Handles Kubota Connect Http calls
 *
 * This class defines all code necessary to make HTTP calls to Kubota Connect API.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 */

class Kubota_Connect_Http {

	/**
	 * The URL of API
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_version    The url of Kubota Connect dealer API
	 */
	private $api_url;

	/**
	 * The API key token used to authenticate the dealership with Kubota Connect API
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_key_token    The API key token
	 */
	private $api_key_token;

	public function __construct( $api_version,  $api_key_token) {
		$this->api_url = 'https://api.kubota.io/dealers/v' . $api_version . '/';
	}

	/**
	 * Test connection to Kubota Connect API
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function test_connection($token) {
		$endpoint = '';
		$data = $this->call_api( $endpoint, array(), trim($token));

		// return $data;
		return $data;
	}

	/**
	 * Get Kubota Products
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function get_products() {
		$endpoint = 'products';
		$data = $this->call_api( $endpoint );

		return $data;
	}

	/**
	 * Get Kubota Product by ID
	 * 
	 * @param int $product_id 	Product ID
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function get_product( $product_id, $params = array()) {
		$endpoint = 'products/' . $product_id;
		$data = $this->call_api( $endpoint , $params);

		return $data;
	}

	/**
	 * Get Kubota Categories
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function get_categories() {
		$endpoint = 'categories';
		$data = $this->call_api( $endpoint );

		return $data;
	}

	/**
	 * Get Kubota Category by ID
	 * 
	 * @param int $category_id 	Category ID
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function get_category( $category_id ) {
		$endpoint = 'categories/' . $category_id;
		$data = $this->call_api( $endpoint );

		return $data;
	}

	/**
	 * Get Kubota Finance Offers
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function get_finance_offers() {
		$endpoint = 'finance';
		$data = $this->call_api( $endpoint );

		return $data;
	}

	/**
	 * Get Kubota Finance Offer by ID
	 * 
	 * @param int $offer_id 	Offer ID
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function get_finance_offer( $offer_id ) {
		$endpoint = 'finance/' . $offer_id;
		$data = $this->call_api( $endpoint );

		return $data;
	}

	/**
	 * Get Kubota Slides
	 * 
	 * @since    1.0.0
	 * 
	 */
	public function get_slides() {
		$endpoint = 'highlights';
		$data = $this->call_api( $endpoint );

		return $data;
	}


	/**
	 * Call Kubota Connect API 
	 * 
	 * @param string $endpoint 	API endpoint	
	 * @param array $data		API filter data
	 * @param string $token		API token
	 * 
	 * @since    1.0.0
	 * 
	 * @return object
	 */
	private function call_api( $endpoint, $params = array(), $token = null ) {
		$url = $this->api_url . $endpoint;

		if ( !empty( $params ) ) {
			$query = http_build_query( $params );
			$url .= '?' . $query;	
		}
		
		$args = array(
			'Content-Type' => 'application/json',
			'headers' => $this->get_header($token),
		);

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );
		$data->statusCode = wp_remote_retrieve_response_code( $response );
		$data->token = $token;

		return $data;
	}

	private function get_header($token = null){
		$kc_token = ($token) ? $token : $this->api_key_token;

		$headers = array(
			'Authorization' => 'ApiKey ' . $kc_token,
			'Content-Type' => 'application/json',
		);

		return $headers;
	}
	
}
