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
        // TODO: Production: https://vuejs.org/js/vue.min.js
        wp_enqueue_script( 'simple_reservation_vuejs', 'https://vuejs.org/js/vue.js' );
        wp_enqueue_script( 'simple_reservation_vuex', 'https://unpkg.com/vuex@2.0.0' );
        wp_enqueue_script( 'simple_reservation_axios', 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js' );
        wp_enqueue_script( 'simple_reservation_service', $this->plugin_url . 'assets/frontend/service.js' );
        wp_enqueue_script( 'simple_reservation_script', $this->plugin_url . 'assets/frontend/store.js', null, null, true );
        wp_enqueue_script( 'simple_reservation_store', $this->plugin_url . 'assets/frontend/script.js', null, null, true );
    }

    function enqueue_admin() {
        wp_enqueue_style( 'simple_reservation_style', $this->plugin_url . 'assets/admin/style.css' );
        wp_enqueue_script( 'simple_reservation_script', $this->plugin_url . 'assets/admin/script.js' );
    }
}