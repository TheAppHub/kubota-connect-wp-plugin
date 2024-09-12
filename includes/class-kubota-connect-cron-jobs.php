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
        $this->api_key = API_Key_Manager::get_api_key();
        $this->api_url = Kubota_Connect_Config::getConfig('api_url');

        if ($this->api_key) {
            add_action('init', [$this, 'schedule_cron_jobs']);
            add_action('kubota-connect_import_products_event', [$this, 'import_products']);
            add_action('kubota-connect_import_categories_event', [$this, 'import_categories']);
            add_action('kubota-connect_import_finance_offers_event', [$this, 'import_finance_offers']);
            add_action('kubota-connect_import_highlights_event', [$this, 'import_highlights']);
        }
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

        $timestamp = wp_next_scheduled($hook);
        if ($timestamp) {
            wp_unschedule_event($timestamp, $hook);
        }

        $next_run_time = $this->get_next_run_time($schedule);
        wp_schedule_event($next_run_time, $schedule, $hook);
    }

    /**
     * Retrieves the next run time for a given schedule.
     *
     * @param string $schedule The schedule for which to get the next run time.
     * @return int The timestamp of the next run time.
     */
    private function get_next_run_time($schedule) {
        $current_time = current_time('timestamp');

        if ($schedule === 'monthly') {
            $next_run_time = strtotime('first day of next month 04:00:00');
        } elseif ($schedule === 'fortnightly') {
            $next_run_time = strtotime('first day of next month 04:00:00');
            if ($current_time > $next_run_time) {
                $next_run_time = strtotime('+14 days', $next_run_time);
            }
        } else {
            $next_run_time = strtotime('04:00:00 tomorrow');
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
        $product = new Product(new API_Client($this->api_url));
        $product->import();
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
        $finance_offer = new Finance_Offer(new API_Client($this->api_url));
        $finance_offer->import();
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
        $highlight = new Highlight(new API_Client($this->api_url));
        $highlight->import();
    }
}