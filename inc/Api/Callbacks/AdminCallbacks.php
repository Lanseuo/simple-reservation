<?php
/**
 * @package SimpleReservation
 */
namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class AdminCallBacks extends BaseController {
    function admin_general() {
        return require_once( "$this->plugin_path/templates/admin.php" );
    }

    function admin_rooms() {
        return require_once( "$this->plugin_path/templates/admin_rooms.php" );
    }
    
    function admin_about() {
        return require_once( "$this->plugin_path/templates/admin_about.php" );
    }

    function simple_reservation_settings( $input ) {
        return $input;
    }

    function render_input_text( $args ) {
        $option_id = $args['option_id'];
        $placeholder = $args['placeholder'];
        $value = esc_attr( get_option( $option_id ) );
        echo '<input type="text" class="regular-text" name="'.$option_id.'" value="'.$value.'" placeholder="'.$placeholder.'">';
    }
}