<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Base;

use \Inc\Base\BaseController;

class Enqueue extends BaseController {
    function register() {
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_frontend'] );
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin'] );  
    }

    function enqueue_frontend() {
        wp_enqueue_style( 'simple_reservation_style', $this->plugin_url . 'assets/frontend/style.css' );
        wp_enqueue_script( 'simple_reservation_sscript', $this->plugin_url . 'assets/frontend/script.js' );
    }

    function enqueue_admin() {
        wp_enqueue_style( 'simple_reservation_style', $this->plugin_url . 'assets/admin/style.css' );
        wp_enqueue_script( 'simple_reservation_sscript', $this->plugin_url . 'assets/admin/script.js' );
    }
}