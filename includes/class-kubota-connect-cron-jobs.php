<?php

/**
 * Class Kubota_Connect_Cron
 *
 * This class handles the cron jobs for the Kubota Connect plugin.
 *
 * @package Kubota_Connect
 */
class Kubota_Connect_Cron {
    private $api_key;
    private $api_url;

    /**
     * Constructor for the Kubota_Connect_Cron_Jobs class.
     *
     * @param string $api_key The API key used for authentication.
     */
    public function __construct() {
        error_log('Cron jobs constructor');

        $this->api_key = API_Key_Manager::get_api_key();
        $this->api_url = Kubota_Connect_Config::getConfig('api_url');

        add_action('init', [$this, 'schedule_cron_jobs']);
        
        $this->init_cron_jobs();
    }

    public function init_cron_jobs(){
        add_action('kubota-connect_import_products_event', [$this, 'import_products']);
        add_action('kubota-connect_import_finance_offers_event', [$this, 'import_finance_offers']);
        add_action('kubota-connect_import_highlights_event', [$this, 'import_highlights']);
    }

    /**
     * Schedules the cron jobs for the Kubota Connect plugin.
     *
     * This method sets up the necessary cron jobs to ensure that the plugin's
     * scheduled tasks are executed at the appropriate times.
     *
     * @return void
     */
    public function schedule_cron_jobs() {
        $this->schedule_single_cron_job('kubota-product', 'kubota-connect_import_products_event');
        $this->schedule_single_cron_job('kubota-finance_offer', 'kubota-connect_import_finance_offers_event');
        $this->schedule_single_cron_job('kubota-highlight', 'kubota-connect_import_highlights_event');
    }

    /**
     * Schedules a single cron job for a custom post type (CPT).
     *
     * @param string $cpt  The custom post type for which the cron job is being scheduled.
     * @param string $hook The hook to be used for the cron job.
     *
     * @return void
     */
    private function schedule_single_cron_job($cpt, $hook) {
        $schedule = get_option($cpt . '_schedule', 'daily');

        if ($schedule === 'never') {
            error_log("Cron job for $cpt is set to never, skipping scheduling.");
            return;
        }

        $timestamp = wp_next_scheduled($hook);
        if ($timestamp) {
            error_log("Unscheduling existing event for $hook.");
            wp_unschedule_event($timestamp, $hook);
        }

        $next_run_time = $this->get_next_run_time($schedule);
        error_log("Scheduling cron job $hook to run at " . date('Y-m-d H:i:s', $next_run_time));
        wp_schedule_event($next_run_time, $schedule, $hook);
    }


    /**
     * Retrieves the next run time for a given schedule.
     *
     * @param string $schedule The schedule identifier.
     * @return int The timestamp of the next run time.
     */
    private function get_next_run_time($schedule) {
        $current_time = current_time('timestamp');

        switch ($schedule) {
            case 'monthly':
                $next_run_time = strtotime('first day of next month 04:00:00');
                break;
            case 'fortnightly':
                $next_run_time = strtotime('first day of next month 04:00:00');
                if ($current_time > $next_run_time) {
                    $next_run_time = strtotime('+14 days', $next_run_time);
                }
                break;
            case 'weekly':
                $next_run_time = strtotime('next Monday 04:00:00');
                break;
            case 'daily':
            default:
                $next_run_time = strtotime('04:00:00 tomorrow');
                break;
        }

        return $next_run_time;
    }

    /**
     * Imports products into the system.
     *
     * This method is responsible for importing products from an external source
     * into the system. It handles the retrieval, processing, and storage of product
     * data to ensure that the product information is up-to-date and accurate.
     *
     * @return void
     */
    public function import_products() {
        $this->handle_import('Product');
    }

    /**
     * Imports finance offers from an external source.
     *
     * This method is responsible for fetching and importing finance offers
     * into the system. It is typically run as a scheduled cron job.
     *
     * @return void
     */
    public function import_finance_offers() {
        $this->handle_import('Finance_Offer');
    }

    /**
     * Imports highlights data.
     *
     * This function is responsible for importing highlights data into the system.
     * It is typically called as part of a scheduled cron job.
     *
     * @return void
     */
    public function import_highlights() {
        error_log('Importing highlights');
       $this->handle_import('Highlight');
    }

    /**
    * Handles the import process for different types of data.
    *
    * @param string $type      The type of data being imported (e.g., 'highlight', 'finance_offer').
    * @param string $class_name The class responsible for handling the import (e.g., 'Highlight', 'Finance_Offer').
    */
    private function handle_import($class_name) {
        if(!$this->api_key) {
            error_log('API Key not set. Skipping import.');
            return;
        }

        // Set the user ID for the current user
        $user_id = get_option('kubota_connect_installer_user_id');
        if ($user_id) {
            wp_set_current_user($user_id);
        }

        // Ensure proper context for cron jobs
        if (defined('DOING_CRON') && DOING_CRON) {
            if (!function_exists('is_user_logged_in')) {
                function is_user_logged_in() {
                    return false; // Set default value for cron job context
                }
            }
        }

        // Boot Carbon Fields only when necessary
        \Carbon_Fields\Carbon_Fields::boot();

        add_action('carbon_fields_fields_registered', function () use ($class_name) {
            if ($this->api_key) {
                $importer = new $class_name(new API_Client($this->api_url));
                $importer->import();
            }
        });
    }
}