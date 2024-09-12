<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Class Kubota_Connect_Category
 *
 * This class extends the Base_Importer class and is responsible for handling
 * category-related functionalities within the Kubota Connect plugin.
 *
 * @package Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */
class Kubota_Connect_Category extends Base_Importer {
    protected $api_client;

    /**
     * Constructor for the Kubota_Connect_Categories class.
     *
     * @param API_Client $api_client An instance of the API_Client class.
     */
    public function __construct(API_Client $api_client) {
        $this->api_client = $api_client;

        // Register custom taxonomies
        add_action('init', [$this, 'create_custom_taxonomies']);

        // Add Carbon Fields
        add_action('carbon_fields_register_fields', [$this, 'register_custom_fields']);

        // Register shortcode
        add_shortcode('kubota-connect-category-description', [$this, 'category_description_shortcode']);
    }

    /**
     * Creates custom taxonomies for the plugin.
     *
     * This function is responsible for registering custom taxonomies
     * used within the Kubota Connect plugin. It should be called during
     * the initialization phase to ensure that the taxonomies are available
     * for use throughout the plugin.
     *
     * @return void
     */
    public function create_custom_taxonomies() {
        register_taxonomy('kubota_category', 'kubota-product', [
            'labels' => [
                'name'          => 'Kubota Categories',
                'singular_name' => 'Kubota Category',
            ],
            'hierarchical' => true,
            'show_ui'      => true,
            'show_admin_column' => true,
        ]);
    }

    /**
     * Registers custom fields for the Kubota Connect Categories.
     *
     * This function is responsible for defining and registering custom fields
     * that are used within the Kubota Connect Categories. It ensures that the
     * necessary fields are available and properly configured for use within
     * the plugin.
     *
     * @return void
     */
    public function register_custom_fields() {
        Container::make('term_meta', 'Kubota Category Fields')
            ->where('term_taxonomy', '=', 'kubota_category')
            ->add_fields([
                Field::make('rich_text', 'kubota_category_description', 'Kubota Description')
                    ->set_help_text('This description is provided by the Kubota. Please do not edit, as this will be overwritten when the API is next contacted. If you would like to add additional information, please add it to the "Description" field above.'),
            ]);

        $image_handler = new Image_Handler();
        $image_handler->create_category_image_field();
    }

    /**
     * Imports data related to Kubota Connect categories.
     *
     * This method handles the import process for categories within the Kubota Connect plugin.
     * It ensures that the necessary data is fetched and processed accordingly.
     *
     * @return void
     */
    public function import() {
        $response = $this->api_client->fetch_data($this->get_endpoint());  // Replace with your actual API endpoint

        if (is_wp_error($response)) {
            echo '<div class="notice notice-error"><p><strong>Error:</strong> ' . esc_html($response->get_error_message()) . '</p></div>';
            return;
        }

        if (!empty($response)) {
            foreach ($response as $category_data) {

                $item = $this->fetch_item_details($category_data['id']);
                if (is_wp_error($item)) {
                    // Handle and display API error message for individual item
                    echo '<div class="notice notice-error"><p><strong>Error:</strong> ' . esc_html($item->get_error_message()) . '</p></div>';
                    return false;
                }
                
                $this->import_single_category($item);
            }
        }
    }

    /**
     * Imports a single category into the system.
     *
     * @param array $category_data The data of the category to be imported.
     * @param int $parent_id The ID of the parent category. Default is 0.
     *
     * @return void
     */
    private function import_single_category($category_data, $parent_id = 0) {
        // Ensure description is set
        $description = isset($category_data['description']) ? $category_data['description'] : '';

        // Check if the category already exists by slug
        $existing_category = get_term_by('slug', $category_data['id'], 'kubota_category');

        // If the category exists, update it. Otherwise, create a new one.
        if ($existing_category) {
            wp_update_term($existing_category->term_id, 'kubota_category', [
                'name'        => $category_data['name'],
                'parent'      => $parent_id,
            ]);

            carbon_set_term_meta($existing_category->term_id, 'kubota_category_description', $description);


            if($category_data['image']){
                // Add product-specific meta fields
                $image_handler = new Image_Handler();
                $image_urls = [
                    'small'  => $category_data['image']['small'],
                    'medium' => $category_data['image']['medium'],
                    'large'  => $category_data['image']['large'],
                    'xlarge' => $category_data['image']['xlarge']
                ];
                $image_handler->save_image_urls_to_term($existing_category->term_id, $image_urls, 'image');
            }    
         } else {
            $new_category = wp_insert_term($category_data['name'], 'kubota_category', [
                'slug'        => $category_data['id'],
                'parent'      => $parent_id,
            ]);

            $new_category_id = $new_category['term_id'];

            // Set Carbon Fields custom field for the new category
            carbon_set_term_meta($new_category_id, 'kubota_category_description', $description);

            if($category_data['image']){
            // Add product-specific meta fields
                $image_handler = new Image_Handler();
                $image_urls = [
                    'small'  => $category_data['image']['small'],
                    'medium' => $category_data['image']['medium'],
                    'large'  => $category_data['image']['large'],
                    'xlarge' => $category_data['image']['xlarge']
                ];
                $image_handler->save_image_urls_to_term($new_category_id, $image_urls, 'image');
            }
        }

        // Recursively import subcategories
        if (!empty($category_data['subCategories'])) {
            foreach ($category_data['subCategories'] as $subcategory_data) {
                $this->import_single_category($subcategory_data, $existing_category ? $existing_category->term_id : $new_category_id);
            }
        }
    }

    /**
     * Shortcode handler for displaying category description.
     *
     * @param array $atts Shortcode attributes.
     * @return string The category description.
     */
    public function category_description_shortcode($atts) {
        // Extract attributes and set default value for 'theme'
        $atts = shortcode_atts([
            'id' => null,
            'theme' => 'default',
        ], $atts);
    
        $term_id = $atts['id'] ? (int) $atts['id'] : get_queried_object_id();
        $description = carbon_get_term_meta($term_id, 'kubota_category_description');
    
        if (!$description) {
            return ''; // Return empty if no description is found
        }
    
        // Apply Tailwind CSS Material styles if theme is set to 'material'
        if ($atts['theme'] === 'material') {
            // Adding Material Tailwind styles to H2, H3, and p tags
            $description = preg_replace(
                [
                    '/<h2>(.*?)<\/h2>/i',
                    '/<h3>(.*?)<\/h3>/i',
                    '/<p>(.*?)<\/p>/i'
                ],
                [
                    '<h2 class="text-2xl font-bold text-gray-900">$1</h2>',
                    '<h3 class="text-xl font-semibold text-gray-800">$1</h3>',
                    '<p class="text-base text-gray-700">$1</p>'
                ],
                $description
            );
        }
    
        return $description;
    }
    

    /**
     * Retrieves the endpoint URL for the Kubota Connect Categories.
     *
     * This method constructs and returns the endpoint URL used for accessing
     * the Kubota Connect Categories within the plugin.
     *
     * @return string The endpoint URL.
     */
    protected function get_endpoint() {
        return '/categories'; // Endpoint for products API
    }
}
