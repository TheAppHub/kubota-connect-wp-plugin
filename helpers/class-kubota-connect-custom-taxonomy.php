<?php

/**
 * Kubota Connect Custom Taxonomy
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/helpers
 */

/**
 * Kubota Connect Custom Taxonomy
 *
 * This class is used to create custom taxonomy.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/helpers
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 */
	class Kubota_Connect_Taxonomy {

		private $args;

		private $taxonomy;

		private $post_type;

		public function __construct( $post_type, $singular, $plural ) {

			$this->post_type = $post_type;

			$this->taxonomy = strtolower( $plural );

			$plural   = ucfirst( strtolower( $plural ) );
			$singular = ucfirst( strtolower( $singular ) );

			$labels = array(
				'name'              => _x( $plural, $this->taxonomy, 'kubota' ),
				'singular_name'     => _x( $singular, $this->taxonomy, 'kubota' ),
				'search_items'      => __( 'Search ' . $plural, 'kubota' ),
				'all_items'         => __( 'All ' . $plural, 'kubota' ),
				'parent_item'       => __( 'Parent ' . $singular, 'kubota' ),
				'parent_item_colon' => __( 'Parent ' . $singular . ':', 'kubota' ),
				'edit_item'         => __( 'Edit ' . $singular, 'kubota' ),
				'update_item'       => __( 'Update ' . $singular, 'kubota' ),
				'add_new_item'      => __( 'Add New ' . $singular, 'kubota' ),
				'new_item_name'     => __( 'New ' . $singular, 'kubota' ),
				'menu_name'         => __( $singular, 'kubota' ),
			);

			$this->args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $singular ),
			);

			add_action( 'init', [ $this, 'register' ], 0 );
		}


		public function register() {

			register_taxonomy( $this->taxonomy, [$this->post_type], $this->args );
		}

		public function isHierachical( $value ) {

			$this->args['hierarchical'] = (boolean) $value;
		}

		public function showInRest( $value ) {

			$this->args['show_in_rest'] = (boolean) $value;
		}

	}