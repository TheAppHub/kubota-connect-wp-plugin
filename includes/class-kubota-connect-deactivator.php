<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */
class Kubota_Connect_Deactivator {

    /**
     * Perform actions during plugin deactivation.
     *
     * This method clears scheduled hooks, deletes custom post type data,
     * and removes plugin options.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Clear scheduled hooks
        wp_clear_scheduled_hook('kubota-connect_import_products_event');
        wp_clear_scheduled_hook('kubota-connect_import_finance_offers_event');
        wp_clear_scheduled_hook('kubota-connect_import_highlights_event');
        wp_clear_scheduled_hook('kubota-connect_import_categories_event');

        // Delete custom post type data
        self::delete_cpt_data('product');
        self::delete_cpt_data('finance_offer');
        self::delete_cpt_data('highlight');
        self::delete_cpt_data('category');

        // Delete plugin options
        delete_option('kc_api_key');
        delete_option('product_schedule');
        delete_option('finance_offer_schedule');
        delete_option('highlight_schedule');
        delete_option('category_schedule');
    }

    /**
     * Delete all posts of a custom post type.
     *
     * @param string $cpt The custom post type to delete.
     */
    private static function delete_cpt_data($cpt) {
        $posts = get_posts(array(
            'post_type' => $cpt,
            'numberposts' => -1,
            'post_status' => 'any'
        ));

        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }
}