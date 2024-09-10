<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Kubota_Connect_Category extends Base_Importer {
    protected $api_client;

    public function __construct(API_Client $api_client) {
        $this->api_client = $api_client;

        // Register custom taxonomies
        add_action('init', [$this, 'create_custom_taxonomies']);

        // Add Carbon Fields
        add_action('carbon_fields_register_fields', [$this, 'register_custom_fields']);

        // Register shortcode
        add_shortcode('kubota-connect-category-description', [$this, 'category_description_shortcode']);
    }

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

    public function import() {
        $response = $this->api_client->fetch_data($this->get_endpoint());  // Replace with your actual API endpoint

        if (isset($response['error'])) {
            return $response['error']; // Return the error message to be displayed
        }

        if (!empty($response)) {
            foreach ($response as $category_data) {

                $item = $this->fetch_item_details($category_data['id']);
                if (is_wp_error($item)) {
                    // Handle and display API error message for individual item
                    echo '<div class="notice notice-error"><p><strong>Error:</strong> ' . esc_html($item->get_error_message()) . '</p></div>';
                    continue;
                }
                
                $this->import_single_category($item);
            }
        }
    }

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
    

    protected function get_endpoint() {
        return '/categories'; // Endpoint for products API
    }
}
