<?php

/**
 * Add CPT products
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Add CPT products with custom fields and taxonomies
 *
 * This class adds custom fields to the Kubota product page.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 */

use Kubota\Helpers\CarbonFields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Kubota_Connect_Product extends Kubota_Connect_Carbon_Fields{

	/**
	 * The name of the custom post type
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type    The name of the custom post type
	 */
	private $single_name = 'kubota-product';

	/**
	 * The plural name of the custom post type
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type    The plural name of the custom post type
	 */
	private $plural_name = 'kubota-products';

	public function register(){
		/**
		 * Create Products
		 */
		$product = new Kubota_Connect_Post_Type($this->single_name, $this->plural_name);
		$product->showInMenu(false);
		$product->supports(['title', 'thumbnail', 'excerpt']);
		$product->setMenuPosition(3);
		$product->setLabel('name', 'Kubota Products');
		$product->setLabel('singular_name', 'Product');
		$product->setLabel('menu_name', 'Products');
		$product->setLabel('name_admin_bar', 'Product');
		$product->setLabel('add_new', 'Add new Product');
		$product->setLabel('new_item', 'New Product');
		$product->setLabel('edit_item', 'Edit Product');
		$product->setLabel('view_item', 'View Product');
		$product->setLabel('view_items', 'View Products');
		$product->setLabel('all_items', 'All Products');
		$product->setLabel('search_items', 'Search Products');
		$product->setLabel('parent_item_colon', 'Parent Product');
		$product->setLabel('not_found', 'No Product found');
		$product->setLabel('not_found_in_trash', 'No Product found in Trash');

		$application = new Kubota_Connect_Taxonomy($this->plural_name, 'brand', 'brands');
        $application->isHierachical(false);

		/**
		 * Change meta box labels
		 */
		// $product->setLabel('featured_image', 'Product Hero Image');
		// $product->setLabel('set_featured_image', 'Set product image');
		// $product->setLabel('remove_featured_image', 'Remove product image');
		// $product->setLabel('use_featured_image', 'Use product image');

		/**
		 * Add fields to Product Groups
		 */
		add_action( 'carbon_fields_register_fields', [$this, 'attach_studio_image'] );
		
	}

	function attach_studio_image(){
		Container::make( 'post_meta', 'Studio Image' )
		->set_context( 'side' )
		->where( 'post_type', '=', $this->plural_name )
		->add_fields( [
			Field::make( 'image', 'studio_image', __( 'Clipped Product Image' ) )
			->set_value_type( 'url' )
			->set_help_text( 'Please select an image that was uploaded to one of the connected models.' ),
		]);
	}

	
}