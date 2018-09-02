<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Base;

use \Inc\Base\BaseController;

class Enqueue extends BaseController {
    function register() {
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue'] );  

    }

    function enqueue() {
        wp_enqueue_style( 'simple_reservation_style', $this->plugin_url . 'assets/style.css' );
        wp_enqueue_script( 'simple_reservation_sscript', $this->plugin_url . 'assets/script.js' );
    }
}