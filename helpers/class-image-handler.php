<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Image_Handler {
    private $image_type;
    private $image_urls;

     /**
     * Constructor for Image_Handler.
     *
     * @param string $image_type The type of image, e.g., 'image' or 'hero-image'.
     */
    public function __construct($image_type = 'image') {
        $this->image_type = $image_type;

        $this->retrieve_image_urls($image_type);

        // Register shortcodes
        add_action('init', [$this, 'register_shortcodes']);
    }

    /**
     * Register shortcodes for responsive images.
     */
    public function register_shortcodes() {
        add_shortcode('kubota-connect-image', [$this, 'shortcode_image']);
        add_shortcode('kubota-connect-hero-image', [$this, 'shortcode_hero_image']);
    }

    /**
     * Retrieve image URLs from the database based on the image type.
     */
    private function retrieve_image_urls($image_type, $id = null) {
        $post_id = ($id) ? $id : get_the_ID();

        if ($post_id) {
            $this->image_urls['small']  = carbon_get_post_meta($post_id, $image_type . '_small');
            $this->image_urls['medium'] = carbon_get_post_meta($post_id, $image_type . '_medium');
            $this->image_urls['large']  = carbon_get_post_meta($post_id, $image_type . '_large');
            $this->image_urls['xlarge'] = carbon_get_post_meta($post_id, $image_type . '_xlarge');
        }
    }

    /**
     * Set the image URLs from an array.
     *
     * @param array $image_urls An associative array with keys 'small', 'medium', 'large', and 'xlarge'.
     */
    public function set_image_urls($image_urls) {
        $this->image_urls = $image_urls;
    }

    /**
     * Get the image URL for a specific size.
     *
     * @param string $size The size of the image ('small', 'medium', 'large', 'xlarge').
     * @return string|null The URL of the image or null if not set.
     */
    public function get_image_url($size) {
        return isset($this->image_urls[$size]) ? esc_url($this->image_urls[$size]) : null;
    }

    /**
     * Save the image URLs to the database using Carbon Fields.
     *
     * @param int $post_id The ID of the post.
     */
    public function save_image_urls_to_post($post_id, $image_urls, $image_type = 'image') {
        foreach ($image_urls as $size => $url) {
            carbon_set_post_meta($post_id, $image_type . '_' . $size, esc_url_raw($url));
        }
    }

    public function save_image_urls_to_term($term_id, $image_urls, $image_type = 'image') {
        foreach ($image_urls as $size => $url) {
            carbon_set_term_meta($term_id, $image_type . '_' . $size, esc_url_raw($url));
        }
    }

    public function create_image_field($post_type) {
        return Container::make('post_meta', __('Image'))
            ->set_context( 'side' )
            ->set_priority( 'low' )
            ->where('post_type', '=', $post_type)
            ->add_fields([
                Field::make('html', 'image', __('Image'))
                    ->set_html($this->get_image_preview_html())
                    ->set_width(100),
                Field::make('text', 'image_small', ''),
                Field::make('text', 'image_medium', ''),
                Field::make('text', 'image_large', ''),
                Field::make('text', 'image_xlarge', ''),
            ]);
    }

    public function create_category_image_field() {
        return Container::make('term_meta', __('Image'))
            ->where( 'term_taxonomy', '=', 'kubota_category' )
            ->add_fields([
                Field::make('html', 'image', __('Image'))
                    ->set_html($this->get_image_preview_html())
                    ->set_width(100),
                Field::make('text', 'image_small', ''),
                Field::make('text', 'image_medium', ''),
                Field::make('text', 'image_large', ''),
                Field::make('text', 'image_xlarge', ''),
            ]);
    }

    private function get_image_preview_html() {
        $image_url = $this->get_image_url('medium');

        if ($image_url) {
            return '<img src="' . esc_url($image_url) . '" alt="Image" style="max-width: 300px; height: auto;" />';
        }
        return '<div class="image-preview">
                    <p>No image available.</p>
                </div>';
    }

    public function create_hero_image_field($post_type) {
        return Container::make('post_meta', __('Hero Image'))
            ->where('post_type', '=', $post_type)
            ->set_context( 'carbon_fields_after_title' )
            ->add_fields([
                Field::make('html', 'image', __('Hero Image'))
                    ->set_html($this->get_hero_image_preview_html())
                    ->set_width(500),
                Field::make('text', 'hero-image_small', ''),
                Field::make('text', 'hero-image_medium', ''),
                Field::make('text', 'hero-image_large', ''),
                Field::make('text', 'hero-image_xlarge', ''),
            ]);
    }

    private function get_hero_image_preview_html() {
        $image_url = $this->get_image_url('hero_medium');

        if ($image_url) {
            return '<img src="' . esc_url($image_url) . '" alt="Hero Image" style="max-width: 100%; height: auto;" />';
        }
        return '<p>No image available.</p>';
    }

    /**
     * Shortcode to output responsive image.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML markup for the responsive image.
     */
    public function shortcode_image($atts) {
        $material = isset($atts['theme']) && $atts['theme'] === 'material';
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();

        $this->retrieve_image_urls('image', $post_id);
        return $this->get_responsive_image_html($material);
    }

    /**
     * Shortcode to output responsive hero image.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML markup for the responsive hero image.
     */
    public function shortcode_hero_image($atts) {
        $this->image_type = 'hero-image';

        $material = isset($atts['theme']) && $atts['theme'] === 'material';
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();

        $this->retrieve_image_urls('hero-image', $post_id);
        return $this->get_responsive_image_html($material);
    }

    /**
     * Generate HTML markup for the responsive image.
     *
     * @return string HTML markup.
     */
    private function get_responsive_image_html($material = false) {
        $small  = $this->get_image_url('small');
        $medium = $this->get_image_url('medium');
        $large  = $this->get_image_url('large');
        $xlarge = $this->get_image_url('xlarge');

        if (!$medium) {
            return '<p>No image available.</p>';
        }

        if ($material) {
            return '<div class="flex justify-center">
                        <img 
                            src="' . esc_url( $medium ).'" 
                            srcset="' . esc_url( $small) . ' 352w, ' . esc_url( $medium) . ' 768w, ' . esc_url( $large) . ' 1024w, ' . esc_url( $xlarge) . ' 1632w"
                            sizes="100vw"
                            alt="Kubota"
                            class="rounded-lg shadow-lg"
                            loading="lazy"
                        />
                    </div>';
        } else {
            return '<div class="kubota-connect-image">
                        <img 
                            src="' . esc_url( $medium ).'" 
                            srcset="' . esc_url( $small) . ' 352w, ' . esc_url( $medium) . ' 768w, ' . esc_url( $large) . ' 1024w, ' . esc_url( $xlarge) . ' 1632w"
                            sizes="100vw"
                            alt="Kubota"
                            loading="lazy"
                        />
                    </div>';
        }
    }
}
