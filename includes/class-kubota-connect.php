<?php
class Kubota_Connect {
    private $plugin_name = 'kubota-connect';
    private $version = '1.0.0';

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_scriptes']);

        // Instantiate the admin class
        new Kubota_Connect_Admin($this->plugin_name, $this->version);
    }

    public function enqueue_public_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../public/css/kubota-connect-public.css', array(), $this->version, 'all');
        
        // // Add Swiper CSS
        // wp_register_style( 'SwiperCSS', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css' );
        // wp_enqueue_style('SwiperCSS');
        
    }

    public function enqueue_public_scriptes() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../public/js/kubota-connect-public.js', array(), $this->version, 'all');

        // // Add Swiper JS
        // wp_register_script( 'SwiperJS', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',["jquery"],false,true);
        // wp_enqueue_script('SwiperJS');
    }
    
}