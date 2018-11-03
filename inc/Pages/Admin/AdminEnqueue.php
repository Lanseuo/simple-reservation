<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Pages\Admin;
use Inc\Base\BaseController;

class AdminEnqueue extends BaseController {
    function register() {
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin'] );  
    }

    function enqueue_admin() {
        wp_enqueue_style( 'simple_reservation_style', $this->plugin_url . 'assets/admin/dist/style.min.css' );
        wp_enqueue_script( 'simple_reservation_script', $this->plugin_url . 'assets/admin/dist/main.min.js', null, null, true );
    }
}