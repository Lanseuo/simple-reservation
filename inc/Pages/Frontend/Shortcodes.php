<?php
/**
 * @package SimpleReservation
 */
namespace Inc\Pages\Frontend;
use Inc\Base\BaseController;

class Shortcodes extends BaseController {
    function register() {
        add_shortcode( 'simplereservation', [$this, 'render_simple_reservation']);
    }

    function render_simple_reservation( $attr ) {
        require_once( "$this->plugin_path/templates/frontend.php" );
    }
}