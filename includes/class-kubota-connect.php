<?php
class Kubota_Connect {
    private $plugin_name;
    private $version;

    public function __construct() {
        $config = include plugin_dir_path(dirname(__FILE__)) . 'config.php';
        $this->plugin_name = $config['plugin_name'];
        $this->version = $config['version'];

        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_scriptes']);

        // Instantiate the admin class
        new Kubota_Connect_Admin($this->plugin_name, $this->version);
    }

    public function enqueue_public_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../public/css/kubota-connect-public.css', array(), $this->version, 'all');   
    }

    public function enqueue_public_scriptes() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../public/js/kubota-connect-public.js', array(), $this->version, 'all');
    }
}