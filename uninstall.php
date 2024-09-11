<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Clear scheduled hooks
wp_clear_scheduled_hook('kubota-connect_import_products_event');
wp_clear_scheduled_hook('kubota-connect_import_finance_offers_event');
wp_clear_scheduled_hook('kubota-connect_import_highlights_event');
wp_clear_scheduled_hook('kubota-connect_import_categories_event');

// Delete custom post type data
delete_cpt_data('product');
delete_cpt_data('finance_offer');
delete_cpt_data('highlight');
delete_cpt_data('category');

// Delete plugin options
delete_option('kc_api_key');
delete_option('product_schedule');
delete_option('finance_offer_schedule');
delete_option('highlight_schedule');
delete_option('category_schedule');


function delete_cpt_data($cpt) {
	$posts = get_posts(array(
		'post_type' => $cpt,
		'numberposts' => -1,
		'post_status' => 'any'
	));

	foreach ($posts as $post) {
		wp_delete_post($post->ID, true);
	}
}