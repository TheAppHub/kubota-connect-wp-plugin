<?php

/**
 * Add CPT finance offers
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Add CPT finance offers with custom fields and taxonomies
 *
 * This class adds custom fields to the Kubota product page.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 */
class Kubota_Connect_Finance{

	/**
	 * The name of the custom post type
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type    The name of the custom post type
	 */
	private $single_name = 'kubota-finance';

	/**
	 * The plural name of the custom post type
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type    The plural name of the custom post type
	 */
	private $plural_name = 'kubota-finance';

	public function register(){
		/**
		 * Create Product Groups
		 */
		$product = new Kubota_Connect_Post_Type($this->single_name, $this->plural_name);
		$product->isPublic(true);
		// $product->supports(['title', 'thumbnail', 'excerpt']);
		// $product->setMenuPosition(3);
		// $product->setLabel('name', 'Products');
		// $product->setLabel('singular_name', 'Product');
		// $product->setLabel('menu_name', 'Products');
		// $product->setLabel('name_admin_bar', 'Product');
		// $product->setLabel('add_new', 'Add new Product');
		// $product->setLabel('new_item', 'New Product');
		// $product->setLabel('edit_item', 'Edit Product');
		// $product->setLabel('view_item', 'View Product');
		// $product->setLabel('view_items', 'View Products');
		// $product->setLabel('all_items', 'All Products');
		// $product->setLabel('search_items', 'Search Products');
		// $product->setLabel('parent_item_colon', 'Parent Product');
		// $product->setLabel('not_found', 'No Product found');
		// $product->setLabel('not_found_in_trash', 'No Product found in Trash');
	}
}