<?php
/**
 * Class API_Client
 *
 * This class handles the interactions with the Kubota Connect API.
 * It provides methods to send requests and process responses from the API.
 *
 * @package Kubota_Connect
 */
class API_Client {
    private $api_url;
    private $api_key;

    /**
     * Constructor for the Kubota_Connect_API_Client class.
     *
     * Initializes the API client with the provided API URL.
     *
     * @param string $api_url The base URL for the API.
     */
    public function __construct($api_url) {
        $this->api_url = $api_url;
        $this->api_key = API_Key_Manager::get_api_key();
    }

    /**
     * Fetches data from the specified API endpoint.
     *
     * @param string $endpoint The API endpoint to fetch data from.
     * @return mixed The data retrieved from the API endpoint.
     */
    public function fetch_data($endpoint) {
        if (empty($this->api_key || $this->api_key === '')) {
            return new WP_Error('missing_api_key', 'API key is missing.');
        }
        error_log("Fetching Data");

        $response = wp_remote_get($this->api_url . $endpoint, [
            'headers' => [
                'Authorization' => 'ApiKey ' . $this->api_key,
            ],
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        if(wp_remote_retrieve_response_code($response) === 404){
            return new WP_Error('api_error', 'There was a problem with the API request. Please contact support.');
        }

        if(wp_remote_retrieve_response_code($response) === 401){
            return new WP_Error('api_error', 'Unauthorized API request. Please check your API key.');
        }

        if(wp_remote_retrieve_response_code($response) !== 200){
            return new WP_Error('api_error', wp_remote_retrieve_response_message($response));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', 'Failed to parse JSON response.');
        }

        return $data;
    }
}
