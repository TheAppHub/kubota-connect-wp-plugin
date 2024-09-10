<?php

class API_Key_Manager {
	/**
	 * Retrieve the API key.
	 *
	 * @return string The API key.
	 */
	public static function get_api_key() {
		// Check if the API key is defined in wp-config.php
		if (defined('KC_API_KEY_TOKEN')) {
			return KC_API_KEY_TOKEN;
		}

		// Retrieve API key from the database
		return get_option('kc_api_key', '');
	}

    
    /**
     * Sets the API key.
     *
     * @param string $api_key The API key to be set.
     */
    public static function set_api_key($api_key) {
        return update_option('kc_api_key', $api_key);
    }
}