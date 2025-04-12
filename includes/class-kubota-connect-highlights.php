<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Class Highlight
 *
 * This class extends the Base_Importer class and is part of the Kubota Connect plugin.
 * It is responsible for handling the highlights functionality within the plugin.
 *
 * @package Kubota_Connect
 */
class Highlight extends Base_Importer {
    private $name = 'kubota-highlight';

    /**
     * Constructor for the Kubota_Connect_Highlights class.
     *
     * @param object $api_client An instance of the API client used for making requests.
     */
    public function __construct($api_client) {
        parent::__construct($api_client, $this->name);
        error_log('Highlight constructor');

        // Register custom post type
        add_action('init', [$this, 'create_custom_post_type']);

        // Add custom fields 
        add_action('carbon_fields_register_fields', [$this, 'register_fields']);

        // Register shortcodes
        add_action('init', [$this, 'register_shortcodes']);
    }

    /**
     * Creates a custom post type for the plugin.
     *
     * This function registers a new custom post type to be used within the Kubota Connect plugin.
     * It sets up the necessary labels, supports, and other arguments required for the custom post type.
     *
     * @return void
     */
    public function create_custom_post_type() {
        register_post_type($this->name, [
            'labels'      => ['name' => __('Kubota Highlights'), 'singular_name' => __('Highlight')],
            'public'      => true,
            'has_archive' => false,
            'rewrite'     => ['slug' => 'kubota-highlights'],
            'supports'    => ['title'],
            'menu_icon'   => 'dashicons-star-filled',
        ]);
    }

    /**
     * Registers custom fields for the Kubota Connect Highlights plugin.
     *
     * This method is responsible for defining and registering the custom fields
     * used within the Kubota Connect Highlights plugin.
     *
     * @return void
     */
    public function register_fields() {
        Container::make('post_meta', __('Highlight Details'))
            ->where('post_type', '=', $this->name)
            ->add_fields([  
                Field::make('text', 'description', __('Description'))
                    ->set_attribute('readOnly', true),
                Field::make('text', 'button_text', __('Button Text'))
                    ->set_attribute('readOnly', true),
                Field::make('text', 'background_colour', __('Slide Colour'))
                    ->set_attribute('readOnly', true),
                Field::make('text', 'link', __('Link'))
                    ->set_attribute('readOnly', true),
            ]);

        $image_handler = new Image_Handler();
        $image_handler->create_image_field($this->name);
    }

    /**
     * Retrieves the endpoint URL for the highlights feature.
     *
     * This method constructs and returns the endpoint URL that is used
     * to fetch highlight data for the Kubota Connect plugin.
     *
     * @return string The endpoint URL.
     */
    protected function get_endpoint() {
        return '/highlights'; // Endpoint for highlights API
    }

    /**
     * Save post meta data.
     *
     * This method saves the meta data for a given post.
     *
     * @param int $post_id The ID of the post for which the meta data is being saved.
     * @param mixed $item The meta data item to be saved.
     */
    protected function save_post_meta($post_id, $item) {
        parent::save_post_meta($post_id, $item);
        // Add highlight-specific meta fields
        carbon_set_post_meta($post_id, 'description', sanitize_text_field($item['description']));
        carbon_set_post_meta($post_id, 'button_text', sanitize_text_field($item['buttonText']));
        carbon_set_post_meta($post_id, 'background_colour', sanitize_text_field($item['backgroundColour']));
        // carbon_set_post_meta($post_id, 'link', sanitize_text_field($item['link']));

        // Use Image_Handler to save images
        $image_handler = new Image_Handler();
        $image_urls = [
            'small'  => $item['image']['small'],
            'medium' => $item['image']['medium'],
            'large'  => $item['image']['large'],
            'xlarge' => $item['image']['xlarge']
        ];
        $image_handler->save_image_urls_to_post($post_id, $image_urls);
    }

    /**
     * Registers the shortcodes used by the Kubota Connect plugin.
     *
     * This method is responsible for adding the necessary shortcodes
     * that are utilized within the Kubota Connect plugin to enhance
     * the functionality and user experience.
     *
     * @return void
     */
    public function register_shortcodes() {
        add_shortcode('kubota-connect-hightlight-description', [$this, 'shortcode_description']);
        add_shortcode('kubota-connect-hightlight-button-text', [$this, 'shortcode_button_text']);
        add_shortcode('kubota-connect-hightlight-background-colour', [$this, 'shortcode_background_colour']);
        add_shortcode('kubota-connect-hightlight-link', [$this, 'shortcode_link']);
        add_shortcode('kubota-connect-hightlight-slider', [$this, 'shortcode_highlight_slider']);
    }

    /**
     * Generates the shortcode description.
     *
     * @param array $atts Shortcode attributes.
     * @return string The description generated by the shortcode.
     */
    public function shortcode_description($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'description'));
    }

    /**
     * Generates the text for a button shortcode.
     *
     * @param array $atts An associative array of attributes passed to the shortcode.
     * @return string The text to be displayed on the button.
     */
    public function shortcode_button_text($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'button_text'));
    }

    /**
     * Shortcode handler for setting the background colour.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output with the specified background colour.
     */
    public function shortcode_background_colour($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'background_colour'));
    }

    /**
     * Generates a shortcode link.
     *
     * @param array $atts Shortcode attributes.
     * @return string The generated link.
     */
    public function shortcode_link($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_url(carbon_get_post_meta($post_id, 'link'));
    }

    /**
     * Generates the highlight slider shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output of the highlight slider.
     */
    public function shortcode_highlight_slider($atts) {

        /**
         * Checks if Elementor is loaded or if the 'swiper' or 'swiper-js' scripts are not enqueued.
         * If Elementor is loaded, the scripts will not be added.
         */
        if ( did_action( 'elementor/loaded' ) || ( !wp_script_is( 'swiper', 'enqueued' ) && !wp_script_is( 'swiper-js', 'enqueued' ) ) ) {
            // Register Swiper
            wp_register_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
            wp_register_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], false, true);
            // Enqueue Swiper
            wp_enqueue_style('swiper');
            wp_enqueue_script('swiper');
        }

        $query = new WP_Query([
            'post_type'      => $this->name,
            'posts_per_page' => -1,
        ]);

        ob_start();
        include plugin_dir_path(__FILE__) . '../public/templates/highlight-slider-template.php';
        $output = ob_get_clean();

        $script = '
            var swiper = new Swiper(".kubota-highlight-slider", {
                slidesPerView: 1,
                loop: true,
                speed: 600,
                effect: "fade",
                autoplay: {
                    delay: 5000,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
            });
        ';

        wp_add_inline_script('swiper', $script,'after');

        return $output;
    }
} 