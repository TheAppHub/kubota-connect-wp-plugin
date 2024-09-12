<?php
/**
 * Class Base_Importer
 *
 * This class serves as the base importer for the Kubota Connect plugin.
 * It provides foundational functionality for importing data into the system.
 *
 * @package Kubota_Connect
 * @subpackage Helpers
 */
class Base_Importer {
    protected $api_client;
    protected $post_type;
    protected $fetch_details;

    /**
     * Constructor for the Kubota_Connect_Base_Importer class.
     *
     * @param object $api_client   The API client instance used for making requests.
     * @param string $post_type    The post type that this importer will handle.
     * @param bool   $fetch_details Optional. Whether to fetch detailed information. Default is false.
     */
    public function __construct($api_client, $post_type, $fetch_details = false) {
        $this->api_client = $api_client;
        $this->post_type = $post_type;
        $this->fetch_details = $fetch_details;
    }

    /**
     * Imports data into the system.
     *
     * This method handles the import process for the Kubota Connect plugin.
     * It reads data from a specified source and processes it to be integrated
     * into the WordPress environment.
     *
     * @return void
     */
    public function import() {
        $all_data = [];
        $parameters = $this->get_endpoint_parameters();

        // If no parameters are provided, fetch data without parameters
        if (empty($parameters)) {
            $endpoint = $this->get_endpoint();
            $data = $this->api_client->fetch_data($endpoint);

            if (is_wp_error($data)) {
                // Handle and display API error message
                echo '<div class="notice notice-error"><p><strong>Error:</strong> ' . esc_html($data->get_error_message()) . '</p></div>';
                return false;
            } else {
                $all_data = array_merge($all_data, $data);
            }
        } else {
            // Loop through each parameter and fetch data
            foreach ($parameters as $param) {
                $endpoint = $this->combine_endpoint_and_parameters($this->get_endpoint(), $param);
                $data = $this->api_client->fetch_data($endpoint);

                if (is_wp_error($data)) {
                    // Handle and display API error message
                    echo '<div class="notice notice-error"><p><strong>Error:</strong> ' . esc_html($data->get_error_message()) . '</p></div>';
                    return false;
                    // continue;
                }

                $all_data = array_merge($all_data, $data);
            }
        }

        foreach ($all_data as $item) {
            if ($this->fetch_details) {
                $item = $this->fetch_item_details($item['id']);
                if (is_wp_error($item)) {
                    // Handle and display API error message for individual item
                    echo '<div class="notice notice-error"><p><strong>Error:</strong> ' . esc_html($item->get_error_message()) . '</p></div>';
                    return false;
                    // continue;
                }
            }
            $this->process_item($item);
        }

        $this->remove_deleted_items($all_data);

        return true;
    }


    /**
     * Fetches the details of an item based on its ID.
     *
     * @param int $id The ID of the item to fetch details for.
     * @return array The details of the item.
     */
    protected function fetch_item_details($id) {
        $endpoint = $this->get_item_details_endpoint($id);
        return $this->api_client->fetch_data($endpoint);
    }

    /**
     * Retrieves the endpoint URL for fetching item details.
     *
     * @param int $id The unique identifier of the item.
     * @return string The endpoint URL for the specified item.
     */
    protected function get_item_details_endpoint($id) {
        return $this->get_endpoint() . '/' . $id;
    }

    /**
     * Processes a single item.
     *
     * This method is responsible for handling the processing logic of an individual item.
     *
     * @param mixed $item The item to be processed.
     * @return void
     */
    protected function process_item($item) {
        $existing_post_id = $this->get_existing_post_id($item);

        if ($existing_post_id) {
            $this->update_post($existing_post_id, $item);
        } else {
            $this->create_post($item);
        }
    }

    /**
     * Retrieves the ID of an existing post based on the provided item.
     *
     * @param mixed $item The item used to determine the existing post ID.
     * @return int|null The ID of the existing post if found, or null if not found.
     */
    protected function get_existing_post_id($item) {
        $args = [
            'post_type'   => $this->post_type,
            'meta_key'    => 'external_id',
            'meta_value'  => $item['id'],
            'fields'      => 'ids',
            'numberposts' => 1,
        ];
        $posts = get_posts($args);

        return $posts ? $posts[0] : null;
    }

    /**
     * Creates a new post based on the provided item data.
     *
     * @param array $item An associative array containing the data for the new post.
     * @return int|WP_Error The ID of the newly created post on success, or a WP_Error object on failure.
     */
    protected function create_post($item) {
        // Sometimes the API uses 'title' instead of 'name'
        $post_title = (array_key_exists('name', $item)) ? $item['name'] : $item['title'];

        $post_id = wp_insert_post([
            'post_type'   => $this->post_type,
            'post_title'  => sanitize_text_field($post_title),
            'post_name'  => $item['id'],
            'post_status' => 'publish',
        ]);

        if ($post_id) {
            $this->save_post_meta($post_id, $item);
        }
    }

    /**
     * Updates an existing post with new data.
     *
     * This method takes a post ID and an item containing new data, and updates the post accordingly.
     *
     * @param int $post_id The ID of the post to be updated.
     * @param array $item An associative array containing the new data for the post.
     * 
     * @return void
     */
    protected function update_post($post_id, $item) {
        // Sometimes the API uses 'title' instead of 'name'
        $post_title = (array_key_exists('name', $item)) ? $item['name'] : $item['title'];

        wp_update_post([
            'ID'         => sanitize_text_field($post_id),
            'post_title' => sanitize_text_field($post_title),
        ]);

        $this->save_post_meta($post_id, $item);
    }

    /**
     * Saves metadata for a given post.
     *
     * This function updates or adds metadata for a specified post ID using the provided item data.
     *
     * @param int $post_id The ID of the post for which metadata is being saved.
     * @param array $item An associative array containing the metadata to be saved.
     * @return void
     */
    protected function save_post_meta($post_id, $item) {
        // Default meta fields; can be overridden in child classes
        update_post_meta($post_id, 'external_id', $item['id']);
        // Add more fields specific to the child class
    }

    /**
     * Removes deleted items from the provided data.
     *
     * This method processes the given data and removes any items that are marked as deleted.
     *
     * @param array $data The data array from which deleted items need to be removed.
     * @return array The filtered data array with deleted items removed.
     */
    protected function remove_deleted_items($data) {
        $imported_ids = array_column($data, 'id');

        $args = [
            'post_type'      => $this->post_type,
            'meta_key'       => 'external_id',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];
        $posts = get_posts($args);

        foreach ($posts as $post_id) {
            $external_id = get_post_meta($post_id, 'external_id', true);

            if (!in_array($external_id, $imported_ids)) {
                wp_trash_post($post_id);
            }
        }
    }

    /**
     * Retrieves the endpoint URL for the importer.
     *
     * This method is used to get the specific endpoint URL that the importer will use
     * to fetch or send data. The endpoint URL is typically defined in the configuration
     * or settings of the importer.
     *
     * @return string The endpoint URL.
     */
    protected function get_endpoint() {
        // Should be implemented in child classes
        return '';
    }

    /**
     * Combines the given endpoint with the provided parameters.
     *
     * @param string $endpoint The API endpoint to which parameters will be appended.
     * @param array $param An associative array of parameters to be combined with the endpoint.
     * @return string The full URL with the endpoint and parameters combined.
     */
    protected function combine_endpoint_and_parameters($endpoint, $param) {
        if ($param) {
            $endpoint .= '?' . http_build_query($param);
        }
        return $endpoint;
    }

    /**
     * Retrieves the endpoint parameters for the importer.
     *
     * This method is protected and is used to obtain the necessary parameters
     * required for connecting to the endpoint.
     *
     * @return array An associative array of endpoint parameters.
     */
    protected function get_endpoint_parameters() {
        // Should be implemented in child classes, return an array of parameters
        return [];
    }
}