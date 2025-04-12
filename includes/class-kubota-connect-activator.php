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
		self::set_default_import_schedules();
        self::schedule_cron_jobs();
	}


	/**
     * Set default import schedules for Kubota products, finance offers, and highlights.
     *
     * @since    1.0.0
     */
    public static function set_default_import_schedules() {
        if (get_option('kubota-product_schedule') === false) {
            update_option('kubota-product_schedule', 'weekly');
        }
        if (get_option('kubota-finance_offer_schedule') === false) {
            update_option('kubota-finance_offer_schedule', 'weekly');
        }
        if (get_option('kubota-highlight_schedule') === false) {
            update_option('kubota-highlight_schedule', 'daily');
        }
    }

    /**
	 * Schedule default cron jobs.
	 *
	 * @since    1.0.0
	 */
	public static function schedule_cron_jobs() {
		if (!wp_next_scheduled('kubota_product_import_hook')) {
			wp_schedule_event(time(), 'weekly', 'kubota_product_import_hook');
		}
		if (!wp_next_scheduled('kubota_finance_offer_import_hook')) {
			wp_schedule_event(time(), 'weekly', 'kubota_finance_offer_import_hook');
		}
		if (!wp_next_scheduled('kubota_highlight_import_hook')) {
			wp_schedule_event(time(), 'daily', 'kubota_highlight_import_hook');
		}
	}
}
