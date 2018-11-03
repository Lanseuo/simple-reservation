<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Pages\Frontend;
use Inc\Base\BaseController;

class FrontendEnqueue extends BaseController {
    function register() {
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_frontend'] );
    }

    function enqueue_frontend() {
        if ( WP_DEBUG === true ) { 
            wp_enqueue_script( 'simple_reservation_vuejs', 'https://vuejs.org/js/vue.js' );
            wp_enqueue_script( 'simple_reservation_vuex', 'https://unpkg.com/vuex@2.0.0' );
        } else {
            wp_enqueue_script( 'simple_reservation_vuejs', 'https://vuejs.org/js/vue.min.js' );
            wp_enqueue_script( 'simple_reservation_vuex', 'https://unpkg.com/vuex@2.0.0/dist/vuex.min.js' );
        }
        wp_enqueue_script( 'simple_reservation_axios', 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js' );

        wp_enqueue_style( 'simple_reservation_style', $this->plugin_url . 'assets/frontend/dist/style.min.css' );
        wp_enqueue_script( 'simple_reservation_script', $this->plugin_url . 'assets/frontend/dist/main.min.js', null, null, true );
    }
}