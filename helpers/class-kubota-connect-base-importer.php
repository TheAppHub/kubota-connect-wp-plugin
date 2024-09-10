<?php
class Base_Importer {
    protected $api_client;
    protected $post_type;
    protected $fetch_details;

    public function __construct($api_client, $post_type, $fetch_details = false) {
        $this->api_client = $api_client;
        $this->post_type = $post_type;
        $this->fetch_details = $fetch_details;
    }

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
                    continue;
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
                    continue;
                }
            }
            $this->process_item($item);
        }

        $this->remove_deleted_items($all_data);

        return true;
    }


    protected function fetch_item_details($id) {
        $endpoint = $this->get_item_details_endpoint($id);
        return $this->api_client->fetch_data($endpoint);
    }

    protected function get_item_details_endpoint($id) {
        return $this->get_endpoint() . '/' . $id;
    }

    protected function process_item($item) {
        $existing_post_id = $this->get_existing_post_id($item);

        if ($existing_post_id) {
            $this->update_post($existing_post_id, $item);
        } else {
            $this->create_post($item);
        }
    }

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

    protected function update_post($post_id, $item) {
        // Sometimes the API uses 'title' instead of 'name'
        $post_title = (array_key_exists('name', $item)) ? $item['name'] : $item['title'];

        wp_update_post([
            'ID'         => sanitize_text_field($post_id),
            'post_title' => sanitize_text_field($post_title),
        ]);

        $this->save_post_meta($post_id, $item);
    }

    protected function save_post_meta($post_id, $item) {
        // Default meta fields; can be overridden in child classes
        update_post_meta($post_id, 'external_id', $item['id']);
        // Add more fields specific to the child class
    }

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

    protected function get_endpoint() {
        // Should be implemented in child classes
        return '';
    }

    protected function combine_endpoint_and_parameters($endpoint, $param) {
        if ($param) {
            $endpoint .= '?' . http_build_query($param);
        }
        return $endpoint;
    }

    protected function get_endpoint_parameters() {
        // Should be implemented in child classes, return an array of parameters
        return [];
    }
}