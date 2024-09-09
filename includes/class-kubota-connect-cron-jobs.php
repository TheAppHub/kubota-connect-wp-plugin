<?php

class Kubota_Connect_Cron {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;

        if ($this->api_key) {
            add_action('init', [$this, 'schedule_cron_jobs']);
            add_action('kubota-connect_import_products_event', [$this, 'import_products']);
            add_action('kubota-connect_import_categories_event', [$this, 'import_categories']);
            add_action('kubota-connect_import_finance_offers_event', [$this, 'import_finance_offers']);
            add_action('kubota-connect_import_highlights_event', [$this, 'import_highlights']);
        }
    }

    public function schedule_cron_jobs() {
        $this->schedule_single_cron_job('kubota-product', 'kubota-connect_import_products_event');
        $this->schedule_single_cron_job('kubota-finance', 'kubota-connect_import_finance_offers_event');
        $this->schedule_single_cron_job('kubota-highlight', 'kubota-connect_import_highlights_event');
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

    public function import_products() {
        $product = new Product(new API_Client('https://api.kubota.io/dealers/v1'));
        $product->import();
    }

    public function import_finance_offers() {
        $finance_offer = new Finance_Offer(new API_Client('https://api.kubota.io/dealers/v1'));
        $finance_offer->import();
    }

    public function import_highlights() {
        $highlight = new Highlight(new API_Client('https://api.kubota.io/dealers/v1'));
        $highlight->import();
    }
}