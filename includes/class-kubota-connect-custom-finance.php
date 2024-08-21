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

 use Kubota\Helpers\CarbonFields;
 use Carbon_Fields\Container;
 use Carbon_Fields\Field;

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
		$finance = new Kubota_Connect_Post_Type($this->single_name, $this->plural_name);
		$finance->showInMenu(false);
		$finance->supports(['title', 'excerpt']);
		$finance->setMenuPosition(3);
		$finance->setLabel('name', 'Kubota Finance');
		$finance->setLabel('singular_name', 'Finance');
		$finance->setLabel('menu_name', 'Finance');
		$finance->setLabel('name_admin_bar', 'Finance');
		$finance->setLabel('add_new', 'Add new Finance');
		$finance->setLabel('new_item', 'New Finance');
		$finance->setLabel('edit_item', 'Edit Finance');
		$finance->setLabel('view_item', 'View Finance');
		$finance->setLabel('view_items', 'View Finance');
		$finance->setLabel('all_items', 'All Finance');
		$finance->setLabel('search_items', 'Search Finance');
		$finance->setLabel('parent_item_colon', 'Parent Finance');
		$finance->setLabel('not_found', 'No Finance found');
		$finance->setLabel('not_found_in_trash', 'No Finance found in Trash');
		$finance->setTaxonomies([]);

		/**
		 * Add fields to finance
		 */
		add_action( 'carbon_fields_register_fields', [$this, 'add_text'] );
		add_action( 'carbon_fields_register_fields', [$this, 'attach_hero_image'] );
		add_action( 'carbon_fields_register_fields', [$this, 'attach_finance_details'] );
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

	function attach_hero_image(){
		Container::make('post_meta', 'Images')
		->where('post_type', '=', $this->plural_name)
		->set_context( 'side' )
		->add_fields([
			Field::make( 'hidden', 'finance_hero_image', 'Hero Image' ),
			Field::make( 'html', 'finance_hero_image_html' )
    			->set_html( '<img src="' . $this->get_image('finance_hero_image') . '">' )
		]);
	}

	function attach_finance_details(){
		Container::make('post_meta', 'Finance Details')
		->where( 'post_type', '=', $this->plural_name )
		->add_fields([
			Field::make('text', 'finance_type', 'Advertising Offer for')
				->set_attribute( 'readOnly', 'true' ),
			
			// Comparison rate
			Field::make('text', 'finance_comparison_rate', 'Comparison rate in %')
				->set_attribute( 'placeholder', 'Example: 0' )
				->set_attribute( 'type', 'number' )
				->set_width( 30 )
				->set_conditional_logic([[
					'field' => 'finance_type',
					'value' => 'consumer',
					'compare' => 'INCLUDES'
				]]),

			// Comparison rate
			Field::make('text', 'finance_finance_rate', 'Finance rate in %')
				->set_attribute( 'placeholder', 'Example: 0' )
				->set_attribute( 'type', 'number' )
				->set_width( 30 )
				->set_conditional_logic([[
					'field' => 'finance_type',
					'value' => 'consumer',
					'compare' => 'EXCLUDES'
				]])
				->set_attribute( 'readOnly', 'true' ),


			// Deposit
			Field::make('text', 'finance_deposit', 'Deposit minimum in %')
			->set_attribute( 'placeholder', 'Deposit minimum in %' )
			->set_attribute( 'type', 'number' )
			->set_width( 30 )
			->set_attribute( 'readOnly', 'true' ),


			// Term
			Field::make('text', 'finance_term', 'Term in months')
			->set_attribute( 'placeholder', 'months' )
			->set_attribute( 'type', 'number' )
			->set_width( 30 )
			->set_attribute( 'readOnly', 'true' ),


			// Additional information
			Field::make('textarea', 'finance_additional_details', 'Additional details')
			->set_rows( 5 )
			->set_attribute( 'readOnly', 'true' ),
	
			// Finance rate type
			Field::make( 'hidden', 'finance_rate_type', '' ),
		]);
	}

	private function get_image($image_key){
		$post_id = $_GET['post'] ?? $_POST['id'] ?? $_POST['post_ID']; 

		$hero_image_string = carbon_get_post_meta($post_id, $image_key);

		if(empty($hero_image_string)){
			return null;
		}

		return json_decode($hero_image_string)['small'];
	}
}