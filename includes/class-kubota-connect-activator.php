<?php

/**
 * Fired during plugin activation
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 */
class Kubota_Connect_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_filter('cron_schedules', [self::class, 'add_custom_schedules']);
	}

	function add_custom_schedules() {
		if (!isset($schedules['fortnightly'])) {
			$schedules['fortnightly'] = [
				'interval' => 1209600, // 14 days in seconds
				'display'  => __('Fortnightly')
			];
		}
	
		if (!isset($schedules['monthly'])) {
			$schedules['monthly'] = [
				'interval' => 2592000, // 30 days in seconds
				'display'  => __('Monthly')
			];
		}
	
		return $schedules;
	}
}
