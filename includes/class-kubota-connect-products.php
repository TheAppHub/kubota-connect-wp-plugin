<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Class Product
 *
 * This class extends the Base_Importer class and is responsible for handling product-related functionalities
 * within the Kubota Connect plugin.
 *
 * @package Kubota_Connect
 */
class Product extends Base_Importer {
    private $name = 'kubota-product';

    /**
     * Constructor for the Kubota_Connect_Products class.
     *
     * @param object $api_client An instance of the API client used to interact with external services.
     */
    public function __construct($api_client) {
        parent::__construct($api_client, $this->name, true);

        // Register custom post type
        add_action('init', [$this, 'create_custom_post_type']);

        // Add custom fields 
        add_action('carbon_fields_register_fields', [$this, 'register_product_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_model_fields']);
        // add_action('carbon_fields_register_fields', [$this, 'register_product_template_field']);

        // Register shortcodes
        add_action('init', [$this, 'register_shortcodes']);

        // Load custom template
        add_filter('template_include', [$this, 'load_custom_single_template']);
        add_filter('theme_page_templates', [$this, 'register_custom_archive_template']);
        add_filter('template_include', [$this, 'load_custom_archive_template']);
    }

    /**
     * Creates a custom post type for the Kubota Connect plugin.
     *
     * This function registers a new custom post type to be used within the
     * Kubota Connect plugin. It sets up the necessary labels, supports, and
     * other arguments required for the custom post type.
     *
     * @return void
     */
    public function create_custom_post_type() {
        register_post_type($this->name, [
            'labels'      => ['name' => __('Kubota Products'), 'singular_name' => __('Product')],
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => ['slug' => 'kubota-products'],
            'supports'    => ['title', 'editor'],
            'menu_icon'   => 'dashicons-car',
        ]);
    }

    /**
     * Registers the product template field.
     *
     * This function is responsible for registering a custom field
     * for the product template within the Kubota Connect plugin.
     *
     * @return void
     */
    public function register_product_template_field() {
        Container::make('post_meta', 'Template Selection')
            ->set_context( 'side' )
            ->set_priority( 'high' )
            ->where('post_type', '=', $this->name)
            ->add_fields([
                Field::make('select', 'product_template', 'Product Template')
                    ->set_options([
                        '' => 'Default Template',
                        'single-product-template' => 'Kubota Product Template',
                    ])
            ]);
    }
    
    /**
     * Loads a custom template for single product pages.
     *
     * This function checks if a custom template for single product pages
     * should be used and returns the appropriate template file path.
     *
     * @param string $template The path to the current template file.
     * @return string The path to the custom template file if applicable, otherwise the original template path.
     */
    public function load_custom_single_template($template) {
        if (is_singular($this->name)) {
            $product_template = carbon_get_the_post_meta('product_template');
            if ($product_template) {
                $template_path = plugin_dir_path(dirname(__FILE__)) . 'public/' . $product_template . '.php';
                error_log($template_path);
                if (file_exists($template_path)) {
                    error_log('Template found');
                    return $template_path;
                }
            }
        }
        return $template;
    }

    /**
     * Registers a custom archive template.
     *
     * This function hooks into the template selection process and allows for the 
     * registration of a custom archive template for the plugin.
     *
     * @param array $templates An array of existing templates.
     * @return array Modified array of templates including the custom archive template.
     */
    public function register_custom_archive_template($templates) {
        $templates['archive-product-template.php'] = 'Kubota Product Archive Template';
        return $templates;
    }

    /**
     * Loads a custom archive template for the plugin.
     *
     * This function is responsible for loading a custom archive template
     * when certain conditions are met. It overrides the default template
     * with a custom one provided by the plugin.
     *
     * @param string $template The path to the current template.
     * @return string The path to the custom archive template if conditions are met, otherwise the original template.
     */
    public function load_custom_archive_template($template){
        if (get_page_template_slug() == 'archive-product-template.php') {
            $template = plugin_dir_path(__FILE__) . '../public/archive-product-template.php';
        }
        return $template;
    }

    /**
     * Registers custom product fields for the Kubota Connect plugin.
     *
     * This method is responsible for adding custom fields to the product
     * registration process within the Kubota Connect plugin. These fields
     * can be used to store additional information about products.
     *
     * @return void
     */
    public function register_product_fields() {
        Container::make('post_meta', 'Product Details')
            ->where('post_type', '=', $this->name)
            ->add_fields([
                Field::make('textarea', 'product_description', 'Product Description')->set_width(100),
                Field::make('complex', 'product_features', 'Features')
                ->set_layout('tabbed-horizontal')
                ->add_fields([
                    Field::make('text', 'feature_name', 'Feature Name')->set_width(50),
                    Field::make('textarea', 'feature_description', 'Feature Description')->set_width(50),
                ]),
                Field::make('text', 'product_brochure', 'Brochure URL')->set_width(100),
                Field::make('complex', 'additional_documents', 'Additional Documents')
                ->set_layout('tabbed-horizontal')
                ->add_fields([
                    Field::make('text', 'doc_title', 'Document Title')->set_width(50),
                    Field::make('text', 'doc_file', 'Document File URL')->set_width(50),
                ]),
            ]);

        $image_handler = new Image_Handler();
        $image_handler->create_image_field($this->name);

        $hero_image_handler = new Image_Handler('hero_image');
        $hero_image_handler->create_hero_image_field($this->name);
    }

    /**
     * Registers the custom fields for the Kubota Connect product models.
     *
     * This function is responsible for defining and registering the custom fields
     * associated with the product models in the Kubota Connect plugin. These fields
     * are used to store additional metadata for the product models.
     *
     * @return void
     */
    public function register_model_fields() {
        Container::make('post_meta', 'Model Details')
            ->where('post_type', '=', $this->name)
            ->add_fields([
                Field::make('complex', 'product_models', 'Models')
                ->set_layout('tabbed-horizontal')
                ->add_fields([
                    Field::make('text', 'model_code', 'Model Code')->set_width(25),
                    Field::make('complex', 'model_specs', 'Specifications')
                        ->set_layout('tabbed-horizontal')
                        ->add_fields([
                            Field::make('text', 'spec_section', 'Section')->set_width(100),
                            Field::make('text', 'spec_name', 'Name')->set_width(33),
                            Field::make('text', 'spec_value', 'Value')->set_width(33),
                            Field::make('text', 'spec_unit', 'Unit (if applicable)')->set_width(33),
                            
                        ]),
                ]),
            ]);
    }

    /**
     * Registers the shortcodes used by the Kubota Connect plugin.
     *
     * This method is responsible for defining and registering the shortcodes
     * that will be available for use within the WordPress site. Shortcodes
     * allow users to easily embed custom content and functionality within
     * their posts and pages.
     *
     * @return void
     */
    public function register_shortcodes() {
        add_shortcode('kubota-connect-product-description', [$this, 'shortcode_product_description']);
        add_shortcode('kubota-connect-product-features', [$this, 'shortcode_product_features']);
        add_shortcode('kubota-connect-product-brochure', [$this, 'shortcode_product_brochure']);
        add_shortcode('kubota-connect-product-documents', [$this, 'shortcode_product_documents']);
        add_shortcode('kubota-connect-product-model-names', [$this, 'shortcode_product_model_names']);
        add_shortcode('kubota-connect-product-specs-table', [$this, 'shortcode_product_models_table']);
    }

    /**
     * Imports product data into the system.
     *
     * This function handles the import of product data from an external source
     * and processes it to be stored within the system. It ensures that the data
     * is correctly formatted and saved.
     *
     * @return void
     */
    public function import() {
        // Import categories first using the Kubota_Connect_Category class
        $category_importer = new Kubota_Connect_Category($this->api_client, 'product_category');
        $category_importer->import();

        // Then import products
        parent::import();
    }

    /**
     * Retrieves the endpoint URL for the Kubota Connect Products.
     *
     * This method is responsible for returning the specific endpoint URL
     * used to interact with the Kubota Connect Products API.
     *
     * @return string The endpoint URL.
     */
    protected function get_endpoint() {
        return '/products'; // Endpoint for products API
    }

    /**
     * Retrieves the parameters required for the endpoint.
     *
     * This method fetches and returns the necessary parameters that are used
     * to interact with the specified endpoint in the Kubota Connect plugin.
     *
     * @return array An associative array of endpoint parameters.
     */
    protected function get_endpoint_parameters() {
        return [
            ['category' => 'agriculture'],
            ['category' => 'construction'],
        ];
    }


    /**
     * Save post meta data for a given post.
     *
     * This method is responsible for saving meta information for a specific post.
     *
     * @param int $post_id The ID of the post for which meta data is being saved.
     * @param mixed $item The meta data to be saved for the post.
     * @return void
     */
    protected function save_post_meta($post_id, $item) {
        parent::save_post_meta($post_id, $item);

        carbon_set_post_meta($post_id, 'product_models', $item['models']);
        carbon_set_post_meta($post_id, 'product_description', $item['description']);
        carbon_set_post_meta($post_id, 'product_brochure', $item['brochure']);

        // Filter categories to only include agriculture and construction
        $filtered_categories = $this->filter_allowed_categories($item['categories']);
        
        // Associate product with filtered categories
        $category_ids = $this->get_category_ids($filtered_categories);
        if (!empty($category_ids)) {
            wp_set_post_terms($post_id, $category_ids, 'kubota_category');
        } else {
            wp_set_post_terms($post_id, [], 'kubota_category');
        }

        // Add model data
        $models = [];
        foreach ($item['models'] as $model) {
            $specs = [];
            foreach ($model['specs'] as $spec) {
                $specs[] = [
                    'spec_name' => $spec['name'],
                    'spec_value' => $spec['value'],
                    'spec_unit' => $spec['unit'] ?? '',
                    'spec_section' => $spec['section'],
                ];
            }

            $models[] = [
                'model_code' => $model['modelCode'],
                'model_specs' => $specs,
            ];
        }
        carbon_set_post_meta($post_id, 'product_models', $models);

        // Add features
        $features = [];
        foreach ($item['features'] as $feature) {
            $features[] = [
                'feature_name' => $feature['name'],
                'feature_description' => $feature['description'],
            ];
        }
        carbon_set_post_meta($post_id, 'product_features', $features);

        // Add additional documents
        $documents = [];
        foreach ($item['additionalDocuments'] as $document) {
            $documents[] = [
                'doc_title' => $document['title'],
                'doc_file' => $document['file'],
            ];
        }
        carbon_set_post_meta($post_id, 'additional_documents', $documents);

        // Add product-specific meta fields
        $image_handler = new Image_Handler();

        $hero_image_urls = [
            'small'  => $item['heroImage']['small'],
            'medium' => $item['heroImage']['medium'],
            'large'  => $item['heroImage']['large'],
            'xlarge' => $item['heroImage']['xlarge']
        ];
        $image_handler->save_image_urls_to_post($post_id, $hero_image_urls, 'hero-image');

        $image_urls = [
            'small'  => $item['image']['small'],
            'medium' => $item['image']['medium'],
            'large'  => $item['image']['large'],
            'xlarge' => $item['image']['xlarge']
        ];
        $image_handler->save_image_urls_to_post($post_id, $image_urls, 'image');
    }

    /**
     * Filters the provided categories to include only the allowed categories.
     *
     * @param array $categories An array of categories to be filtered.
     * @return array The filtered array containing only the allowed categories.
     */
    private function filter_allowed_categories($categories) {
        $allowed_categories = ['agriculture', 'construction'];
        $filtered_categories = [];

        foreach ($categories as $category) {
            if (in_array($category['id'], $allowed_categories) || $this->is_subcategory_allowed($category, $allowed_categories)) {
                $filtered_categories[] = $category;
            }
        }

        return $filtered_categories;
    }

    /**
     * Checks if a given category is allowed based on a list of allowed categories.
     *
     * @param string $category The category to check.
     * @param array $allowed_categories An array of allowed categories.
     * @return bool True if the category is allowed, false otherwise.
     */
    private function is_subcategory_allowed($category, $allowed_categories) {
        if (in_array($category['id'], $allowed_categories)) {
            return true;
        }

        if (!empty($category['subCategories']) && is_array($category['subCategories'])) {
            foreach ($category['subCategories'] as $subcategory) {
                if ($this->is_subcategory_allowed($subcategory, $allowed_categories)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Retrieve category IDs.
     *
     * This function retrieves the IDs of categories, optionally filtered by a parent category ID.
     *
     * @param array $categories An array of categories to search through.
     * @param int $parent_id Optional. The parent category ID to filter by. Default is 0.
     * @return array An array of category IDs.
     */
    function get_category_ids($categories, $parent_id = 0) {
        $category_ids = [];
    
        foreach ($categories as $category) {
            // Get or create the term
            $term = term_exists($category['name'], 'kubota_category', $parent_id);
            if (!$term || is_wp_error($term)) {
                $term = wp_insert_term($category['name'], 'kubota_category', [
                    'parent' => $parent_id,
                    'slug' => $category['id']
                ]);
            }
    
            if (!is_wp_error($term)) {
                $category_ids[] = (int) $term['term_id'];
    
                // Recursively handle subcategories
                if (!empty($category['subCategories']) && is_array($category['subCategories'])) {
                    $sub_category_ids = $this->get_category_ids($category['subCategories'], $term['term_id']);
                    $category_ids = array_merge($category_ids, $sub_category_ids);
                }
            }
        }
    
        return $category_ids;
    }

    /**
     * Shortcode handler for displaying product descriptions.
     *
     * This function processes the shortcode for product descriptions and returns
     * the appropriate content based on the provided attributes.
     *
     * @param array $atts An associative array of attributes passed to the shortcode.
     * @return string The content to display for the product description.
     */
    public function shortcode_product_description($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        $description = carbon_get_post_meta($post_id, 'product_description');
        return wpautop($description);
    }

    /**
     * Shortcode handler for displaying product features.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content to display product features.
     */
    public function shortcode_product_features($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        $features = carbon_get_post_meta($post_id, 'product_features');
        if (!$features) return '';
    
        $theme = isset($atts['theme']) && $atts['theme'] === 'material' ? 'material' : 'default';
    
        // Choose wrapper and list item classes based on the theme
        
        if ($theme === 'material') {
            
            $output = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">';
            foreach ($features as $feature) {
                $output .= '<div class="bg-white shadow-lg rounded-lg p-6 transition-transform transform hover:scale-[1.02] hover:shadow-xl">
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">' . esc_html($feature['feature_name']) . '</h3> 
                                <div class="text-gray-600 text-sm space-y-2 border-t border-gray-200 pt-4">' . $feature['feature_description'] . '</div>
                            </div>';
            }
            $output .= '</div>';
        } else {
            $output = '<ul class="kubota-connect-product-features">';
            foreach ($features as $feature) {
                $output .= '<li><h3>' . esc_html($feature['feature_name']) . ':</h3> ' . $feature['feature_description'] . '</li>';
            }
            $output .= '</ul>';
        }
    
        return $output;
    }
    

    /**
     * Shortcode handler for displaying a product brochure.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content for the product brochure.
     */
    public function shortcode_product_brochure($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        $brochure_url = carbon_get_post_meta($post_id, 'product_brochure');
        if (!$brochure_url) return '';
    
        $theme = isset($atts['theme']) && $atts['theme'] === 'material' ? 'material' : 'default';
    
        // Choose link classes based on the theme
        $link_classes = $theme === 'material' 
            ? 'inline-block px-4 py-2 bg-[#E4551C] text-white rounded-md hover:bg-[#b74315] transition-colors duration-300'
            : 'kubota-connect-product-brochure';
    
        return '<a href="' . esc_url($brochure_url) . '" class="' . esc_attr($link_classes) . '" target="_blank" rel="noopener">Download Brochure</a>';
    }
    
    /**
     * Shortcode handler for displaying product documents.
     *
     * This function processes the shortcode [product_documents] and outputs
     * the relevant product documents based on the provided attributes.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content to display the product documents.
     */
    public function shortcode_product_documents($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        $documents = carbon_get_post_meta($post_id, 'additional_documents');
        if (!$documents) return '';
    
        $theme = isset($atts['theme']) && $atts['theme'] === 'material' ? 'material' : 'default';
    
        // Choose list and link classes based on the theme
        $button_classes = $theme === 'material' 
            ? 'inline-block px-4 py-2 bg-[#E4551C] text-white rounded-md hover:bg-[#b74315] transition-colors duration-300'
            : 'kubota-connect-product-documents-button';
    
        $list_item_classes = $theme === 'material' 
            ? 'flex items-center w-full px-4 py-2 leading-tight transition-all rounded-lg outline-none text-start hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900'
            : '';
    
        $link_classes = $theme === 'material'
            ? 'relative h-10 max-h-[40px] w-10 max-w-[40px] select-none rounded-lg text-center align-middle font-sans text-xs font-medium uppercase text-blue-gray-500 transition-all hover:bg-blue-gray-500/10 active:bg-blue-gray-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none'
            : '';
    
        // Output for button
        $output = '<button class="' . esc_attr($button_classes) . '" onclick="openDialog()">';
        $output .= 'More Downloads';
        $output .= '</button>';
    
        // Output for dialog
        $output .= '<div id="document-dialog" class="tw-fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex justify-center items-center z-50 hidden">';
        $output .= '<div class="relative flex flex-col text-gray-700 bg-white shadow-md w-96 rounded-xl bg-clip-border">';
        $output .= '<div class="flex items-center p-4 pl-6 font-sans text-xl antialiased font-semibold leading-snug shrink-0 text-blue-gray-900">
                        More documents
                    </div>';
        $output .= '<nav class="flex min-w-[240px] flex-col gap-1 p-2 pb-4 font-sans text-base font-normal text-blue-gray-700">';
        
        foreach ($documents as $doc) {
            $output .= '<a href="' . esc_url($doc['doc_file']) . '" target="_blank" rel="noopener">';
            $output .= '<div class="' . esc_attr($list_item_classes) . '">';
            $output .= esc_html($doc['doc_title']);
            $output .= '<div class="grid ml-auto place-items-center justify-self-end">';
            // SVG icon
            $output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">';
            $output .= '<path fill-rule="evenodd" d="M13 11.15V4a1 1 0 1 0-2 0v7.15L8.78 8.374a1 1 0 1 0-1.56 1.25l4 5a1 1 0 0 0 1.56 0l4-5a1 1 0 1 0-1.56-1.25L13 11.15Z" clip-rule="evenodd"/>';
            $output .= '<path fill-rule="evenodd" d="M9.657 15.874 7.358 13H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2.358l-2.3 2.874a3 3 0 0 1-4.685 0ZM17 16a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z" clip-rule="evenodd"/>';
            $output .= '</svg>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</a>';
        }
    
        $output .= '</nav>';
        $output .= '</div>';
        $output .= '</div>';
    
    
        return $output;
    }
    
    
    /**
     * Shortcode handler for displaying product model names.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content to display product model names.
     */
    public function shortcode_product_model_names($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        $models = carbon_get_post_meta($post_id, 'product_models');
        if (!$models) return '';
    
        $theme = isset($atts['theme']) && $atts['theme'] === 'material' ? 'material' : 'default';
    
        // Choose list classes based on the theme
        $ul_classes = $theme === 'material' 
            ? 'flex flex-wrap space-x-2' 
            : 'kubota-connect-product-models';
    
        $li_classes = $theme === 'material' 
            ? "after:content-[','] last:after:content-none"
            : '';
    
        $output = '<ul class="' . esc_attr($ul_classes) . '">';
        foreach ($models as $model) {
            $output .= '<li class="' . esc_attr($li_classes) . '">' . esc_html($model['model_code']) . '</li>';
        }
        $output .= '</ul>';
    
        return $output;
    }
    
    

    /**
     * Generates a table of product models based on the provided attributes.
     *
     * This function is used as a shortcode to display a table of product models.
     *
     * @param array $atts An associative array of attributes passed to the shortcode.
     * @return string The HTML content for the product models table.
     */
    public function shortcode_product_models_table($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
        $models = carbon_get_post_meta($post_id, 'product_models');
        if (!$models) return '';
    
        $theme = isset($atts['theme']) && $atts['theme'] === 'material' ? 'material' : 'default';

        $tr_classes = $theme === 'material' 
            ? 'hover:bg-slate-50' 
            : '';
    
        $td_classes = $theme === 'material' 
            ? 'pt-4 pr-4 pb-4 border-slate-200' 
            : '';

        $td_p_classes = $theme === 'material' 
            ? 'block text-sm text-slate-800' 
            : '';
        
        $sections = [];
    
        // Group specifications by section and spec name
        foreach ($models as $model) {
            foreach ($model['model_specs'] as $spec) {
                $section = $spec['spec_section'];
                $spec_name = $spec['spec_name'];
                $sections[$section][$spec_name][$model['model_code']] = [
                    'spec_value' => $spec['spec_value'],
                    'spec_unit'  => $spec['spec_unit'],
                ];
            }
        }
    
        // Initialize output
        $output = '';
    
        foreach ($sections as $section => $specs) {
            $section_id = esc_attr(sanitize_title($section));
            
            if ($theme === 'material') {
                // Accordion item structure
                $output .= '<div class="border-b border-slate-200">';
                $output .= '<button onclick="toggleAccordion(\'' . $section_id . '\')" class="w-full flex justify-between items-center py-5 text-slate-800 outline-none">';
                $output .= '<span>' . esc_html($section) . '</span>';
                $output .= '<span id="icon-' . $section_id . '" class="text-slate-800 transition-transform duration-300">';
                $output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg>';
                $output .= '</span>';
                $output .= '</button>';
                $output .= '<div id="content-' . $section_id . '" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">';
                $output .= '<div class="pb-5 text-sm text-slate-500 overflow-x-auto">';
            } else {
                // Regular table header for default theme
                $output .= '<thead><tr><th colspan="3" class="kubota-connect-specs-table">' . esc_html($section) . '</th></tr></thead>';
                $output .= '<tbody>';
            }
    
            // Add model names as table headers
            $output .= '<table class="w-full min-w-max table-auto text-left text-sm text-gray-700 border-collapse">';
            $output .= '<thead><tr><th>' . esc_html($section) . '</th>';
            $first_spec = reset($specs);
            foreach ($first_spec as $model_code => $value) {
                $output .= '<th>' . esc_html($model_code) . '</th>';
            }
            $output .= '</tr></thead><tbody>';
    
            // Add specs as rows
            foreach ($specs as $spec_name => $models_data) {
                $output .= '<tr class="'.$tr_classes.'"><td class="'.$td_classes.'"><p class="'.$td_p_classes.'">' . esc_html($spec_name) . '</p></td>';
                foreach ($first_spec as $model_code => $value) {
                    $spec_value = isset($models_data[$model_code]['spec_value']) ? esc_html($models_data[$model_code]['spec_value']) : '';
                    $spec_unit = isset($models_data[$model_code]['spec_unit']) ? esc_html($models_data[$model_code]['spec_unit']) : '';
                    $output .= '<td class="'.$td_classes.'">' . $spec_value . ' ' . $spec_unit . '</td>';
                }
                $output .= '</tr>';
            }
            $output .= '</tbody></table>';
    
            if ($theme === 'material') {
                $output .= '</div></div></div>'; // Close accordion item
            } else {
                $output .= '</tbody>';
            }
        }
    
        return $output;
    }
}
