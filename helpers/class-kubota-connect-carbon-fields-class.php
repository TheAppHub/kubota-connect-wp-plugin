<?php

/**
 * Carbon Fields class
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 */

/**
 * Carbon Fields class
 *
 * This class is used to define Carbon Fields.
 *
 * @since      1.0.0
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/includes
 * @author     The App Hub <kubota-connect@theapphub.com.au>
 */

 use Carbon_Fields\Field;
 
 abstract class Kubota_Connect_Carbon_Fields {

    abstract public function register();

        function __construct(){
            add_action( 'after_setup_theme', [$this, 'load'] );
        }

        function load() {
            \Carbon_Fields\Carbon_Fields::boot();
        }
    }