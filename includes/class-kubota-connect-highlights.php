<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Highlight extends Base_Importer {
    private $name = 'kubota-highlight';

    public function __construct($api_client) {
        parent::__construct($api_client, $this->name);

        // Register custom post type
        add_action('init', [$this, 'create_custom_post_type']);

        // Add custom fields 
        add_action('carbon_fields_register_fields', [$this, 'register_fields']);

        // Register shortcodes
        add_action('init', [$this, 'register_shortcodes']);
    }

    public function create_custom_post_type() {
        register_post_type($this->name, [
            'labels'      => ['name' => __('Kubota Highlights'), 'singular_name' => __('Highlight')],
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => ['slug' => 'kubota-highlights'],
            'supports'    => ['title'],
            'menu_icon'   => 'dashicons-star-filled',
        ]);
    }

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

    protected function get_endpoint() {
        return '/highlights'; // Endpoint for highlights API
    }

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

    public function register_shortcodes() {
        add_shortcode('kubota-connect-hightlight-description', [$this, 'shortcode_description']);
        add_shortcode('kubota-connect-hightlight-button-text', [$this, 'shortcode_button_text']);
        add_shortcode('kubota-connect-hightlight-background-colour', [$this, 'shortcode_background_colour']);
        add_shortcode('kubota-connect-hightlight-link', [$this, 'shortcode_link']);
        add_shortcode('kubota-connect-hightlight-slider', [$this, 'shortcode_highlight_slider']);
    }

    public function shortcode_description($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'description'));
    }

    public function shortcode_button_text($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'button_text'));
    }

    public function shortcode_background_colour($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'background_colour'));
    }

    public function shortcode_link($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_url(carbon_get_post_meta($post_id, 'link'));
    }

    public function shortcode_highlight_slider($atts) {
            // Register Swiper
            wp_register_style('SwiperCSS', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
            wp_register_script('SwiperJS', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], false, true);
            // Enqueue Swiper
            wp_enqueue_style('SwiperCSS');
            wp_enqueue_script('SwiperJS');
    
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

            wp_add_inline_script('SwiperJS', $script,'after');
    
            return $output;
        }
} 