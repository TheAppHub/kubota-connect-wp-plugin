<?php

/**
 * Register custom post types
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Register custom post types
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 * @since      1.0.0
 */
class Kubota_Connect_Post_Types{

    /**
     * Register the custom post type
     *
     * @since    1.0.0
     */

    public function register_post_types( $fields ) {

        $labels = array(
            'name'                  => $fields['plural'],
            'singular_name'         => $fields['singular'],
            'menu_name'             => $fields['menu_name'],
            'new_item'              => sprintf( __( 'New %s', 'kubota-connect' ), $fields['singular'] ),
            'add_new_item'          => sprintf( __( 'Add new %s', 'kubota-connect' ), $fields['singular'] ),
            'edit_item'             => sprintf( __( 'Edit %s', 'kubota-connect' ), $fields['singular'] ),
            'view_item'             => sprintf( __( 'View %s', 'kubota-connect' ), $fields['singular'] ),
            'view_items'            => sprintf( __( 'View %s', 'kubota-connect' ), $fields['plural'] ),
            'search_items'          => sprintf( __( 'Search %s', 'kubota-connect' ), $fields['plural'] ),
            'not_found'             => sprintf( __( 'No %s found', 'kubota-connect' ), strtolower( $fields['plural'] ) ),
            'not_found_in_trash'    => sprintf( __( 'No %s found in trash', 'kubota-connect' ), strtolower( $fields['plural'] ) ),
            'all_items'             => sprintf( __( 'All %s', 'kubota-connect' ), $fields['plural'] ),
            'archives'              => sprintf( __( '%s Archives', 'kubota-connect' ), $fields['singular'] ),
            'attributes'            => sprintf( __( '%s Attributes', 'kubota-connect' ), $fields['singular'] ),
            'insert_into_item'      => sprintf( __( 'Insert into %s', 'kubota-connect' ), strtolower( $fields['singular'] ) ),
            'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'kubota-connect' ), strtolower( $fields['singular'] ) ),

            /* Labels for hierarchical post types only. */
            'parent_item'           => sprintf( __( 'Parent %s', 'kubota-connect' ), $fields['singular'] ),
            'parent_item_colon'     => sprintf( __( 'Parent %s:', 'kubota-connect' ), $fields['singular'] ),

            /* Custom archive label.  Must filter 'post_type_archive_title' to use. */
			'archive_title'        => $fields['plural'],
        );

        $args = array(
            'labels'             => $labels,
            'description'        => ( isset( $fields['description'] ) ) ? $fields['description'] : '',
            'public'             => ( isset( $fields['public'] ) ) ? $fields['public'] : true,
            'publicly_queryable' => ( isset( $fields['publicly_queryable'] ) ) ? $fields['publicly_queryable'] : true,
            'exclude_from_search'=> ( isset( $fields['exclude_from_search'] ) ) ? $fields['exclude_from_search'] : false,
            'show_ui'            => ( isset( $fields['show_ui'] ) ) ? $fields['show_ui'] : true,
            'show_in_menu'       => ( isset( $fields['show_in_menu'] ) ) ? $fields['show_in_menu'] : true,
            'query_var'          => ( isset( $fields['query_var'] ) ) ? $fields['query_var'] : true,
            'show_in_admin_bar'  => ( isset( $fields['show_in_admin_bar'] ) ) ? $fields['show_in_admin_bar'] : true,
            'capability_type'    => ( isset( $fields['capability_type'] ) ) ? $fields['capability_type'] : 'post',
            'has_archive'        => ( isset( $fields['has_archive'] ) ) ? $fields['has_archive'] : true,
            'hierarchical'       => ( isset( $fields['hierarchical'] ) ) ? $fields['hierarchical'] : true,
            'supports'           => ( isset( $fields['supports'] ) ) ? $fields['supports'] : array(
                    'title',
                    'editor',
                    'excerpt',
                    'author',
                    'thumbnail',
                    'comments',
                    'trackbacks',
                    'custom-fields',
                    'revisions',
                    'page-attributes',
                    'post-formats',
            ),
            'menu_position'      => ( isset( $fields['menu_position'] ) ) ? $fields['menu_position'] : 21,
            'menu_icon'          => ( isset( $fields['menu_icon'] ) ) ? $fields['menu_icon']: 'dashicons-admin-generic',
            'show_in_nav_menus'  => ( isset( $fields['show_in_nav_menus'] ) ) ? $fields['show_in_nav_menus'] : true,
            'show_in_rest'       => ( isset( $fields['show_in_rest'] ) ) ? $fields['show_in_rest'] : true,
        );

        if ( isset( $fields['rewrite'] ) ) {

            /**
             *  Add $this->plugin_name as translatable in the permalink structure,
             *  to avoid conflicts with other plugins.
             */
            $args['rewrite'] = $fields['rewrite'];
        }

        if ( $fields['custom_caps'] ) {
             /**
             * Provides more precise control over the capabilities than the defaults.  By default, WordPress
             * will use the 'capability_type' argument to build these capabilities.  More often than not,
             * this results in many extra capabilities that you probably don't need.  The following is how
             * I set up capabilities for many post types, which only uses three basic capabilities you need
             * to assign to roles: 'manage_examples', 'edit_examples', 'create_examples'.  Each post type
             * is unique though, so you'll want to adjust it to fit your needs.
             */
        }

        $args['capabilities'] = array(

            // Meta capabilities
            'edit_post'                 => 'edit_' . strtolower( $fields['singular'] ),
            'read_post'                 => 'read_' . strtolower( $fields['singular'] ),
            'delete_post'               => 'delete_' . strtolower( $fields['singular'] ),

            // Primitive capabilities used outside of map_meta_cap():
            'edit_posts'                => 'edit_' . strtolower( $fields['plural'] ),
            'edit_others_posts'         => 'edit_others_' . strtolower( $fields['plural'] ),
            'publish_posts'             => 'publish_' . strtolower( $fields['plural'] ),
            'read_private_posts'        => 'read_private_' . strtolower( $fields['plural'] ),

            // Primitive capabilities used within map_meta_cap():
            'delete_posts'              => 'delete_' . strtolower( $fields['plural'] ),
            'delete_private_posts'      => 'delete_private_' . strtolower( $fields['plural'] ),
            'delete_published_posts'    => 'delete_published_' . strtolower( $fields['plural'] ),
            'delete_others_posts'       => 'delete_others_' . strtolower( $fields['plural'] ),
            'edit_private_posts'        => 'edit_private_' . strtolower( $fields['plural'] ),
            'edit_published_posts'      => 'edit_published_' . strtolower( $fields['plural'] ),
            'create_posts'              => 'edit_' . strtolower( $fields['plural'] ),
        );

        /**
         * Adding map_meta_cap will map the meta correctly.
         * @link https://wordpress.stackexchange.com/questions/108338/capabilities-and-custom-post-types/108375#108375
         */
        // $args['map_meta_cap'] = true;

        /**
         * Assign capabilities to users
         * Without this, users - even admins - can not see post type.
         */
        $this->assign_capabilities( $args['capabilities'], $fields['custom_caps_users'] );

        /**
         * Register Taxnonmies if any
         * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
         */
        if ( isset( $fields['taxonomies'] ) && is_array( $fields['taxonomies'] ) ) {

            foreach ( $fields['taxonomies'] as $taxonomy ) {

                $this->register_single_post_type_taxnonomy( $taxonomy );

            }

        }
	    
	    register_post_type( $fields['slug'], $args );
    }

    private function register_single_post_type_taxnonomy( $tax_fields ) {
        $labels = array(
            'name'                       => $tax_fields['plural'],
            'singular_name'              => $tax_fields['single'],
            'menu_name'                  => $tax_fields['plural'],
            'all_items'                  => sprintf( __( 'All %s' , 'kubota-connect' ), $tax_fields['plural'] ),
            'edit_item'                  => sprintf( __( 'Edit %s' , 'kubota-connect' ), $tax_fields['single'] ),
            'view_item'                  => sprintf( __( 'View %s' , 'kubota-connect' ), $tax_fields['single'] ),
            'update_item'                => sprintf( __( 'Update %s' , 'kubota-connect' ), $tax_fields['single'] ),
            'add_new_item'               => sprintf( __( 'Add New %s' , 'kubota-connect' ), $tax_fields['single'] ),
            'new_item_name'              => sprintf( __( 'New %s Name' , 'kubota-connect' ), $tax_fields['single'] ),
            'parent_item'                => sprintf( __( 'Parent %s' , 'kubota-connect' ), $tax_fields['single'] ),
            'parent_item_colon'          => sprintf( __( 'Parent %s:' , 'kubota-connect' ), $tax_fields['single'] ),
            'search_items'               => sprintf( __( 'Search %s' , 'kubota-connect' ), $tax_fields['plural'] ),
            'popular_items'              => sprintf( __( 'Popular %s' , 'kubota-connect' ), $tax_fields['plural'] ),
            'separate_items_with_commas' => sprintf( __( 'Separate %s with commas' , 'kubota-connect' ), $tax_fields['plural'] ),
            'add_or_remove_items'        => sprintf( __( 'Add or remove %s' , 'kubota-connect' ), $tax_fields['plural'] ),
            'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s' , 'kubota-connect' ), $tax_fields['plural'] ),
            'not_found'                  => sprintf( __( 'No %s found' , 'kubota-connect' ), $tax_fields['plural'] ),
        );

        $args = array(
        	'label'                 => $tax_fields['plural'],
        	'labels'                => $labels,
        	'hierarchical'          => ( isset( $tax_fields['hierarchical'] ) )          ? $tax_fields['hierarchical']          : true,
        	'public'                => ( isset( $tax_fields['public'] ) )                ? $tax_fields['public']                : true,
        	'show_ui'               => ( isset( $tax_fields['show_ui'] ) )               ? $tax_fields['show_ui']               : true,
        	'show_in_nav_menus'     => ( isset( $tax_fields['show_in_nav_menus'] ) )     ? $tax_fields['show_in_nav_menus']     : true,
        	'show_tagcloud'         => ( isset( $tax_fields['show_tagcloud'] ) )         ? $tax_fields['show_tagcloud']         : true,
        	'meta_box_cb'           => ( isset( $tax_fields['meta_box_cb'] ) )           ? $tax_fields['meta_box_cb']           : null,
        	'show_admin_column'     => ( isset( $tax_fields['show_admin_column'] ) )     ? $tax_fields['show_admin_column']     : true,
        	'show_in_quick_edit'    => ( isset( $tax_fields['show_in_quick_edit'] ) )    ? $tax_fields['show_in_quick_edit']    : true,
        	'update_count_callback' => ( isset( $tax_fields['update_count_callback'] ) ) ? $tax_fields['update_count_callback'] : '',
        	'show_in_rest'          => ( isset( $tax_fields['show_in_rest'] ) )          ? $tax_fields['show_in_rest']          : true,
        	'rest_base'             => $tax_fields['taxonomy'],
        	'rest_controller_class' => ( isset( $tax_fields['rest_controller_class'] ) ) ? $tax_fields['rest_controller_class'] : 'WP_REST_Terms_Controller',
        	'query_var'             => $tax_fields['taxonomy'],
        	'rewrite'               => ( isset( $tax_fields['rewrite'] ) )               ? $tax_fields['rewrite']               : true,
        	'sort'                  => ( isset( $tax_fields['sort'] ) )                  ? $tax_fields['sort']                  : '',
        );

        $args = apply_filters( $tax_fields['taxonomy'] . '_args', $args );

        register_taxonomy( $tax_fields['taxonomy'], $tax_fields['post_types'], $args );
    }

    public function assign_capabilities( $caps_map, $users  ) {

        foreach ( $users as $user ) {

            $user_role = get_role( $user );

            foreach ( $caps_map as $cap_map_key => $capability ) {

                $user_role->add_cap( $capability );

            }

        }

    }

    /**
     * Create post types
     */
    public function create_custom_post_types() {
       /**
         * Register Kubota Products, Finance and Highlights
         *
         * @since    1.0.0
         */

         $post_types_fields = array(
            // Products
            array(
                'slug'              => 'kubota-products',
                'singular'          => 'Kubota Product',
                'plural'            => 'Kubota Products',
                'menu_name'         => 'Products',
                'description'       => 'Kubota Products',
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => false,
                'query_var'         => true,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'capability_type'   => 'post',
                'has_archive'       => 'kubota',
                'hierarchical'      => false,
                'supports'          => array( 'title', 'thumbnail', 'custom-fields' ),
                'menu_position'     => 5,
                'show_in_rest'      => true,
                // 'taxonomies'        => array( 'category', 'brand' ),
                'custom_caps'       => true,
                'custom_caps_users' => array( 'administrator', 'editor' ),
            ),
            // Finance
            array(
                'slug'              => 'kubota-finance',
                'singular'          => 'Kubota Finance',
                'plural'            => 'Kubota Finance',
                'menu_name'         => 'Finance',
                'description'       => 'Kubota Finance',
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => false,
                'query_var'         => true,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'capability_type'   => 'post',
                'has_archive'       => 'kubota-finance',
                'hierarchical'      => false,
                'supports'          => array( 'title', 'editor', 'thumbnail' ),
                'menu_position'     => 5,
                'show_in_rest'      => true,
                'custom_caps'       => true,
                'custom_caps_users' => array( 'administrator', 'editor' ),
            ),
            // Highlights
            array(
                'slug'              => 'kubota-highlights',
                'singular'          => 'Kubota Highlight',
                'plural'            => 'Kubota Highlights',
                'menu_name'         => 'Highlights',
                'description'       => 'Kubota Highlights',
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => false,
                'query_var'         => true,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'capability_type'   => 'post',
                'has_archive'       => false,
                'hierarchical'      => false,
                'supports'          => array( 'title', 'thumbnail' ),
                'menu_position'     => 5,
                'show_in_rest'      => true,
                'rewrite'           => array( 'slug' => 'highlights' ),
                'custom_caps'       => true,
                'custom_caps_users' => array( 'administrator', 'editor' ),
            ),
        );

        foreach ( $post_types_fields as $post_type_fields ) {
            $this->register_post_types( $post_type_fields );
        }
    }
}
?>
