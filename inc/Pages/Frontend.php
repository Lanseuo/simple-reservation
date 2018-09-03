<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Pages;
use Inc\Base\BaseController;

class Frontend extends BaseController {
    function render_simple_reservation( $attr ) {
        // shortcode_atts([ 'title' => 'SimpleReservation' ], $attr);
        return require_once( "$this->plugin_path/templates/frontend.php" );
        // return $attr['title'].'!';
    }

    function register() {
        add_shortcode( 'simplereservation', [$this, 'render_simple_reservation']);
    }
}