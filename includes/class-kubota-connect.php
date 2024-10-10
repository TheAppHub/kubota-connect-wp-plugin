<?php
class Kubota_Connect {
    private $plugin_name;
    private $version;

    /**
     * Constructor for the Kubota_Connect class.
     * 
     * Initializes the Kubota_Connect class.
     */
    public function __construct() {
        $this->plugin_name = Kubota_Connect_Config::getConfig('plugin_name');
        $this->version = Kubota_Connect_Config::getConfig('version');

        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_scriptes']);

        // Instantiate the admin class
        new Kubota_Connect_Admin();

        // Initialize the cron jobs
        new Kubota_Connect_Cron();
    
       
       // do_action('kubota-connect_import_highlights_event');
    }

    /**
     * Enqueues the public-facing stylesheet files.
     *
     * This function is responsible for adding the necessary CSS files
     * to the public-facing side of the WordPress site. It ensures that
     * the styles are properly loaded and applied to the frontend.
     *
     * @return void
     */
    public function enqueue_public_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../public/css/kubota-connect-public.css', array(), $this->version, 'all');   
    }

    /**
     * Enqueues the public scripts for the plugin.
     *
     * This function is responsible for loading all the necessary JavaScript files
     * that are required for the public-facing side of the plugin.
     *
     * @return void
     */
    public function enqueue_public_scriptes() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../public/js/kubota-connect-public.js', array(), $this->version, 'all');
    }
}