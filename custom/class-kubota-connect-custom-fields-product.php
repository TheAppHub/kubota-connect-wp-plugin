<?php

/**
 * Add custom fields to products
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Add custom fields to products
 *
 * This class adds custom fields to the Kubota product page.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 */
class Kubota_Connect_Custom_Fields_Product{

	/**
	 * The name of the custom post type
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type    The name of the custom post type
	 */
	private $single_name = 'product';

	/**
	 * The plural name of the custom post type
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type    The plural name of the custom post type
	 */
	private $plural_name = 'groups';
}