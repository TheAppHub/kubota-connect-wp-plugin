<?php
	/**
	 * Kubota Connect CustomPost Type
	 *
	 * @link       https://theapphub.com.au
	 * @since      1.0.0
	 *
	 * @package    Kubota_Connect
	 * @subpackage Kubota_Connect/helpers
	 */

	/**
	 * Kubota Connect CustomPost Type
	 *
	 * This class is used to create custom post types.
	 *
	 * @since      1.0.0
	 * @package    Kubota_Connect
	 * @subpackage Kubota_Connect/helpers
	 * @author     The App Hub <kubota-connect@theapphub.com.au>
	 */
	class Kubota_Connect_Post_Type {

		/**
		 * The arguments for the custom post type
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      array    $args    The arguments for the custom post type
		 */
		private $args;

		/**
		 * The name of the custom post type
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $post_type    The name of the custom post type
		 */
		private $post_type;

		/**
		 * The title placeholder for the custom post type
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $titlePlaceholder    The title placeholder for the custom post type
		 */
		private $titlePlaceholder;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string    $singular       The singular name of the custom post type
		 * @param      string    $plural    The plural name of the custom post type
		 */
		public function __construct( $singular, $plural ) {
			$this->post_type = strtolower( $plural );

			$singular = str_replace("-"," ", $singular);
			$plural = str_replace("-"," ", $plural);

			$plural   = ucfirst( strtolower( $plural ) );
			$singular = ucfirst( strtolower( $singular ) );

			$labels = [
				'name'               => __( $plural ),
				'singular_name'      => __( $singular ),
				'menu_name'          => __( $plural, 'admin menu' ),
				'name_admin_bar'     => __( $singular, 'add new on admin bar' ),
				'add_new'            => __( 'Add New' . $singular ),
				'add_new_item'       => __( 'Add New ' . $singular ),
				'new_item'           => __( 'New ' . $singular ),
				'edit_item'          => __( 'Edit ' . $singular ),
				'view_item'          => __( 'View ' . $singular ),
				'view_items'         => __( 'View ' . $plural ),
				'all_items'          => __( 'All ' . $plural ),
				'search_items'       => __( 'Search ' . $plural ),
				'parent_item_colon'  => __( 'Parent ' . $plural . ':' ),
				'not_found'          => __( 'No ' . $plural . ' found.' ),
				'not_found_in_trash' => __( 'No ' . $plural . ' found in Trash.' ),
			];

			$this->args = [
				'label'              => __( $plural, '' ),
				'labels'             => $labels,
				'description'        => __( 'Description.' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
                'show_in_menu'       => true,
                'show_in_rest'       => true,
				'menu_icon'          => 'dashicons-admin-post',
				'query_var'          => true,
				'rewrite'            => [ 'slug' => $this->post_type ],
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 5, // below post
				'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
				'taxonomies'		=> ['category'],
			];

			add_action( 'init', [ $this, 'register' ] );
		}

		/**
		 * Register the custom post type
		 *
		 * @since    1.0.0
		 */
		public function register() {
			register_post_type( $this->post_type, $this->args );
		}


		public function setDescription( $value ) {

			$this->args['description'] = (string) $value;
		}


		public function isPublic( $value ) {

			$this->args['public'] = (boolean) $value;
		}


		public function isPubliclyQueryable( $value ) {

			$this->args['publicly_queryable'] = (boolean) $value;
		}


		public function showUi( $value ) {

			$this->args['show_ui'] = (boolean) $value;
		}


		public function showInMenu( $value ) {

			$this->args['show_in_menu'] = (boolean) $value;
		}


		public function setMenuIcon( $value ) {

			$this->args['menu_icon'] = (string) $value;
		}


		public function setQueryVar( $value ) {

			$this->args['query_var'] = (boolean) $value;
		}


		public function setCapabilityType( $value ) {

			$this->args['capability_type'] = (string) $value;
		}

		public function hasArchive( $value ) {

			$this->args['has_archive'] = (boolean) $value;
		}


		public function isHierachical( $value ) {

			$this->args['hierarchical'] = (boolean) $value;
		}


		public function setMenuPosition( $value ) {

			$this->args['menu_position'] = (int) $value;
		}


		public function supports( $value ) {

			$this->args['supports'] = (array) $value;

		}

		public function setTaxonomies( $value ) {

			$this->args['taxonomies'] = (array) $value;
		}

		public function changeTitlePlaceholder($title)
		{
			$screen = get_current_screen();

			if( isset( $screen->post_type ) ) {
				if ( $this->post_type == $screen->post_type ){

					if($this->titlePlaceholder){
						$title = $this->titlePlaceholder;
					}
				}
			}

			return $title;
		}

		public function setTitlePlaceholder($title)
		{
			$title = $this->titlePlaceholder;

			add_filter( 'enter_title_here', [$this, 'changeTitlePlaceholder'] );
		}

		public function setCapType($value) {

			$this->args['capability_type'] = (array) $value;
		}

		public function setCapabilities($value) {

			$this->args['capabilities'] = (array) $value;
		}
		
		public function setMetaCap($value) {

			$this->args['map_meta_cap'] = (bool) $value;
		}

		public function setLabel($label, $value) {

			$this->args['labels'][$label] = $value; 
		}
	}