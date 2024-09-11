<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Class Finance_Offer
 *
 * This class extends the Base_Importer and is responsible for handling finance offers.
 *
 * @package Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */
class Finance_Offer extends Base_Importer {
    private $name = 'kubota-finance';

    public function __construct($api_client) {
        parent::__construct($api_client, $this->name, true);

        // Register custom post type
        add_action('init', [$this, 'create_custom_post_types']);

        // Add custom fields 
        add_action('carbon_fields_register_fields', [$this, 'register_fields']);

        // Register shortcodes
        add_action('init', [$this, 'register_shortcodes']);
    }

    
    /**
     * Creates custom post types for the Kubota Connect plugin.
     *
     * This function is responsible for registering custom post types
     * used within the Kubota Connect plugin to manage finance offers.
     *
     * @return void
     */
    public function create_custom_post_types() {
        register_post_type($this->name, [
            'labels'      => ['name' => __('Kubota Finance'), 'singular_name' => __('Finance Offer')],
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => ['slug' => 'kubota-finance-offers'],
            'supports'    => ['title', 'editor'],
            'menu_icon'   => 'dashicons-money-alt',
        ]);
    }

    /**
     * Registers custom fields for the Kubota Connect Finance Offers.
     *
     * This method is responsible for adding custom fields to the finance offers
     * section within the Kubota Connect plugin. It ensures that the necessary
     * fields are available for users to input and manage finance offer data.
     *
     * @return void
     */
    public function register_fields() {
        Container::make('post_meta', __('Finance Offer Details'))
            ->where('post_type', '=', $this->name)
            ->add_fields([
                Field::make('text', 'finance_type', __('Offer Type'))
                    ->set_attribute('readOnly', true)
                    ->set_width( 30 ),
                Field::make('text', 'finance_rate_type', __('Rate Type'))
                    ->set_attribute('readOnly', true)
                    ->set_width( 30 ),
                Field::make('text', 'offer_expiry_date', 'Offer Expiry Date')
                    ->set_width(30)
                    ->set_attribute('readOnly', true),
                Field::make('text', 'finance_rate', __('Rate in %'))
                    ->set_attribute( 'type', 'number' )
                    ->set_width( 30 )
                    ->set_attribute('readOnly', true),
                Field::make('text', 'finance_term_in_months', 'Term in months')
                    ->set_attribute( 'type', 'number' )
                    ->set_width( 30 )
                    ->set_attribute('readOnly', true),
                Field::make('text', 'finance_deposit', 'Deposit minimum in %')
                    ->set_attribute( 'type', 'number' )
                    ->set_width( 30 )
                    ->set_attribute('readOnly', true),
                Field::make('textarea', 'finance_terms', 'Terms & Conditions')
                    ->set_rows( 5 )
                    ->set_attribute('readOnly', true),
            ]);

        $image_handler = new Image_Handler();
        $image_handler->create_image_field($this->name);

        $hero_image_handler = new Image_Handler('hero_image');
        $hero_image_handler->create_hero_image_field($this->name);
    }

    /**
     * Registers the shortcodes used by the Kubota Connect Finance Offers plugin.
     *
     * This method is responsible for defining and registering the shortcodes
     * that will be available for use within the WordPress site. Shortcodes
     * allow users to easily embed custom content and functionality within
     * their posts and pages.
     *
     * @return void
     */
    public function register_shortcodes() {
        add_shortcode('kubota-connect-finance-offer-type', [$this, 'shortcode_finance_offer_type']);
        add_shortcode('kubota-connect-finance-rate-type', [$this, 'shortcode_finance_rate_type']);
        add_shortcode('kubota-connect-finance-offer-expiry-date', [$this, 'shortcode_finance_expiry_date']);
        add_shortcode('kubota-connect-finance-rate', [$this, 'shortcode_finance_rate']);
        add_shortcode('kubota-connect-finance-term-in-months', [$this, 'shortcode_finance_term_in_months']);
        add_shortcode('kubota-connect-finance-deposit', [$this, 'shortcode_finance_deposit']);
        add_shortcode('kubota-connect-finance-terms', [$this, 'shortcode_finance_terms']);
        add_shortcode('kubota-connect-finance-offer', [$this, 'finance_offer_shortcode']);
    }

    /**
     * Shortcode handler for displaying finance offer types.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content to display the finance offer types.
     */
    public function shortcode_finance_offer_type($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'finance_type'));
    }

    /**
     * Shortcode handler for displaying finance rate type.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content to display the finance rate type.
     */
    public function shortcode_finance_rate_type($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'finance_rate_type'));
    }

    /**
     * Shortcode handler for displaying the finance expiry date.
     *
     * @param array $atts Shortcode attributes.
     * @return string The finance expiry date.
     */
    public function shortcode_finance_expiry_date($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        $expiry_date = carbon_get_post_meta($post_id, 'offer_expiry_date');
        $formatted_date = date('d/m/Y', strtotime($expiry_date));
        return esc_html($formatted_date);
    }

    /**
     * Shortcode handler for displaying finance rate.
     *
     * This function processes the shortcode [finance_rate] and returns the finance rate information.
     *
     * @param array $atts Shortcode attributes.
     * @return string The finance rate information to be displayed.
     */
    public function shortcode_finance_rate($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'finance_rate'));
    }

    /**
     * Shortcode handler for displaying finance term in months.
     *
     * @param array $atts Shortcode attributes.
     * @return string The finance term in months.
     */
    public function shortcode_finance_term_in_months($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'finance_term_in_months'));
    }

    /**
     * Shortcode handler for displaying finance deposit information.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content to display the finance deposit information.
     */
    public function shortcode_finance_deposit($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'finance_deposit'));
    }

    /**
     * Shortcode handler for displaying finance terms.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content to display finance terms.
     */
    public function shortcode_finance_terms($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        return esc_html(carbon_get_post_meta($post_id, 'finance_terms'));
    }

    /**
     * Shortcode handler for displaying finance offers.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content to display the finance offers.
     */
    public function finance_offer_shortcode($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();

        $atts = shortcode_atts([
            'id' => null,
            'theme' => null,
        ], $atts, 'finance_offer');

        $title = get_the_title($post_id);
        $offer_type = carbon_get_post_meta($post_id, 'finance_type');
        $rate_type = carbon_get_post_meta($post_id, 'finance_rate_type');
        $rate = carbon_get_post_meta($post_id, 'finance_rate');
        $term = carbon_get_post_meta($post_id, 'finance_term_in_months');
        $deposit = carbon_get_post_meta($post_id, 'finance_deposit');
        $terms = carbon_get_post_meta($post_id, 'finance_terms');

        $expiry_date = carbon_get_post_meta($post_id, 'offer_expiry_date');
        $formatted_date = date('d/m/Y', strtotime($expiry_date));

        $class_h2 = '';
        $class_p = '';

        if ($atts['theme'] === 'material') {
            $class_h2 = 'text-lg font-semibold mb-2';
            $class_h2 .= ' text-[#E4551C]';

            $class_p = 'mb-4';
            $class_p .= ' text-gray-700';
        }

        $output = "
            <div class='finance-offer'>
            <h2 class='$class_h2'>" . esc_html($title) . " (" . esc_html($offer_type) . ")</h2>
            <p class='$class_p'>Rate Type: " . esc_html($rate_type) . "</p>
            <p class='$class_p'>Rate: " . esc_html($rate) . "%</p>
            <p class='$class_p'>Term: " . esc_html($term) . " months</p>
            <p class='$class_p'>Deposit: " . esc_html($deposit) . "%</p>
            <p class='$class_p'>Expiry Date: " . esc_html($formatted_date) . "</p>
            <h3 class='$class_h2'>Terms & Conditions</h3>
            <p class='$class_p'>" . esc_html($terms) . "</p>
            </div>
        ";

        return $output;
    }

    /**
     * Retrieves the endpoint URL for the finance offers.
     *
     * This method constructs and returns the endpoint URL used to fetch
     * finance offers data. The endpoint is typically defined based on
     * the specific requirements of the Kubota Connect plugin.
     *
     * @return string The endpoint URL for finance offers.
     */
    protected function get_endpoint() {
        return '/finance'; // Endpoint for finance offers API
    }

    /**
     * Save post meta data for a given post.
     *
     * This function is responsible for saving the meta data associated with a post.
     *
     * @param int $post_id The ID of the post for which the meta data is being saved.
     * @param mixed $item The meta data item to be saved.
     * @return void
     */
    protected function save_post_meta($post_id, $item) {
        parent::save_post_meta($post_id, $item);
        // Add finance offer-specific meta fields
        carbon_set_post_meta($post_id, 'finance_type', sanitize_text_field($item['type'][0]));
        carbon_set_post_meta($post_id, 'finance_rate_type', sanitize_text_field($item['rateType']));
        carbon_set_post_meta($post_id, 'finance_rate', sanitize_text_field($item['rate']));
        carbon_set_post_meta($post_id, 'finance_term_in_months', sanitize_text_field($item['termInMonths']));
        carbon_set_post_meta($post_id, 'finance_deposit', sanitize_text_field($item['depositInProcent']));
        carbon_set_post_meta($post_id, 'finance_terms', sanitize_textarea_field($item['terms']));
        carbon_set_post_meta($post_id, 'offer_expiry_date', sanitize_textarea_field($item['offerExpiryDate']));

        $image_handler = new Image_Handler();
        $image_urls = [
            'small'  => $item['image']['small'],
            'medium' => $item['image']['medium'],
            'large'  => $item['image']['large'],
            'xlarge' => $item['image']['xlarge']
        ];
        $image_handler->save_image_urls_to_post($post_id, $image_urls);

        $image_urls = [
            'small'  => $item['heroImage']['small'],
            'medium' => $item['heroImage']['medium'],
            'large'  => $item['heroImage']['large'],
            'xlarge' => $item['heroImage']['xlarge']
        ];
        $image_handler->save_image_urls_to_post($post_id, $image_urls, 'hero-image');
    }
}