<?php

/**
 * Add CPT highlights
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Add CPT highlights with custom fields and taxonomies
 *
 * This class adds custom fields to the Kubota highlight page.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 */

 use Kubota\Helpers\CarbonFields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Kubota_Connect_Highlight{

	/**
	 * The name of the custom post type
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type    The name of the custom post type
	 */
	private $single_name = 'kubota-highlight';

	/**
	 * The plural name of the custom post type
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type    The plural name of the custom post type
	 */
	private $plural_name = 'kubota-highlights';

	public function register(){
		/**
		 * Create Highlight Groups
		 */
		$highlights = new Kubota_Connect_Post_Type($this->single_name, $this->plural_name);
		$highlights->showInMenu(false);
		$highlights->supports(['title', 'thumbnail']);
		$highlights->setMenuPosition(3);
		$highlights->setLabel('name', 'Kubota Highlights');
		$highlights->setLabel('singular_name', 'Highlight');
		$highlights->setLabel('menu_name', 'Highlights');
		$highlights->setLabel('name_admin_bar', 'Highlight');
		$highlights->setLabel('add_new', 'Add new Highlight');
		$highlights->setLabel('new_item', 'New Highlight');
		$highlights->setLabel('edit_item', 'Edit Highlight');
		$highlights->setLabel('view_item', 'View Highlight');
		$highlights->setLabel('view_items', 'View Highlights');
		$highlights->setLabel('all_items', 'All Highlights');
		$highlights->setLabel('search_items', 'Search Highlights');
		$highlights->setLabel('parent_item_colon', 'Parent Highlight');
		$highlights->setLabel('not_found', 'No Highlight found');
		$highlights->setLabel('not_found_in_trash', 'No Highlight found in Trash');
		$highlights->setTaxonomies([]);

		/**
		 * Change meta box labels
		 */
		$highlights->setLabel('featured_image', 'Highlight Image');
		$highlights->setLabel('set_featured_image', 'Set highlight image');
		$highlights->setLabel('remove_featured_image', 'Remove highlight image');
		$highlights->setLabel('use_featured_image', 'User highlight image');

		/**
		 * Add fields to highlights
		 */
		add_action( 'carbon_fields_register_fields', [$this, 'add_text'] );
		add_action( 'carbon_fields_register_fields', [$this, 'attach_highlight_info'] );
		add_action( 'carbon_fields_register_fields', [$this, 'attach_layout_info'] );

	}

	function add_text(){
		Container::make('post_meta', 'Kubota Connect')
		->where( 'post_type', '=', $this->plural_name )
		->set_context( 'side' )
		->add_fields( [
			Field::make( 'html', 'kc_information_text' )
    			->set_html( '<p>Kubota highlights get updated automatically.</p><p>If you do not want this highlight to be displayed on your website, please set the status to draft.</p>' )
				->set_classes( 'kc-post-desc' )
		] );
	}

	function attach_highlight_info(){
		Container::make('post_meta', 'Copy')
		->where( 'post_type', '=', $this->plural_name )
		->add_fields( [
			Field::make( 'text', 'highlight_info', 'Text' )
				->set_attribute( 'readOnly', 'true' ),
			Field::make( 'text', 'highlight_link-text', 'Button Text' )
				->set_attribute( 'readOnly', 'true' ),
            Field::make( 'text', 'highlight_link', 'Link' )
				->set_attribute( 'readOnly', 'true' ),
		] );
	}

	function attach_layout_info(){
		Container::make('post_meta', 'Highlight Background Colour')
		->where( 'post_type', '=', $this->plural_name )
		->add_fields([
			Field::make( 'radio', 'highlight_color', __( 'Banner Colour' ) )
			->set_classes( 'prevent-select' )
			->add_options( array(
				'white' => __( 'AG (white)' ),
				'black' => __( 'CE (black)' ),
			) )
		]);
	}

}