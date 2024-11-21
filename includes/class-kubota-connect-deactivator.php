<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */
class Kubota_Connect_Deactivator {
    
    public static function deactivate() {
		error_log('Kubota Connect plugin deactivated');
	}
}