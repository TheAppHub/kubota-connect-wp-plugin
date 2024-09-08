<?php
class Kubota_Connect {
    private $plugin_name = 'kubota-connect';
    private $version = '1.0.0';

    private $product;
    private $category;
    private $finance_offer;
    private $highlight;
    private $api_key;
    private $password_manager;

    public function __construct() {
        $this->password_manager = new Kubota_Connect_Password_Manager();
        $api_client = new API_Client('https://api.kubota.io/dealers/v1', $this->password_manager);
        $this->api_key = $api_client->get_api_key();

        $this->product = new Product($api_client, 'product');
        $this->category = new Kubota_Connect_Category($api_client, 'product_category');
        $this->finance_offer = new Finance_Offer($api_client, 'finance_offer');
        $this->highlight = new Highlight($api_client, 'highlights');

        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_scriptes']);

        if ($this->api_key) {
            add_action('init', [$this, 'schedule_cron_jobs']);
            add_action('kubota-connect_import_products_event', [$this->product, 'import']);
            add_action('kubota-connect_import_categories_event', [$this->category, 'import']);
            add_action('kubota-connect_import_finance_offers_event', [$this->finance_offer, 'import']);
            add_action('kubota-connect_import_highlights_event', [$this->highlight, 'import']);
        }

        // Instantiate the admin class
        new Kubota_Connect_Admin($this->plugin_name, $this->version, $this->api_key);
    }

    public function enqueue_public_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../public/css/kubota-connect-public.css', array(), $this->version, 'all');
    }

    public function enqueue_public_scriptes() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../public/js/kubota-connect-public.js', array(), $this->version, 'all');
    }

    public function schedule_cron_jobs() {
        $this->schedule_single_cron_job('category', 'kubota-connect_import_categories_event');
        $this->schedule_single_cron_job('product', 'kubota-connect_import_products_event');
        $this->schedule_single_cron_job('finance_offer', 'kubota-connect_import_finance_offers_event');
        $this->schedule_single_cron_job('highlight', 'kubota-connect_import_highlights_event');
    }

    private function schedule_single_cron_job($cpt, $hook) {
        $schedule = get_option($cpt . '_schedule', 'daily');

        $timestamp = wp_next_scheduled($hook);
        if ($timestamp) {
            wp_unschedule_event($timestamp, $hook);
        }

        $next_run_time = $this->get_next_run_time($schedule);
        wp_schedule_event($next_run_time, $schedule, $hook);
    }

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
}