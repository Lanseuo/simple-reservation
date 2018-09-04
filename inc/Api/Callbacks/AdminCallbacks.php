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

    function get_room( $id ) {
        global $wpdb;
        $rooms = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}simple_reservation_rooms WHERE id = {$id}", OBJECT );
        return $rooms[0];
    }

    function action() {
        if ( ! isset($_POST['action']) ) return;

        switch ( $_POST['action'] ) {
            case 'start_add_room':
                break;
            case 'add_room':
                $this->add_room( $_POST['name'], $_POST['description'] );
                break;
            case 'start_edit_room':
                break;
            case 'edit_room':
                $this->edit_room( $_POST['id'], $_POST['name'], $_POST['description'] );
                break;
            case 'delete_room':
                $this->delete_room( $_POST['id'] );
                break;
            default:
                die('Action "'.$_POST['action'].'" was not found');
        }
    }

    function add_room( $name, $description ) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix.'simple_reservation_rooms',
            [
                'name'        => $name,
                'description' => $description
            ],
            [ '%s', '%s']
        );

        if ($result) {
            add_settings_error( 'simple_reservation', 'simple_reservation', 'Room '.$name.' was added.', 'updated' );
        } else {
            add_settings_error( 'simple_reservation', 'simple_reservation', 'There was an error during adding room '.$name.'.', 'error' );
        }
    }

    function edit_room( $id, $name, $description ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix.'simple_reservation_rooms',
            [
                'name'        => $name,
                'description' => $description
            ],
            [ 'id' => $id ],
            [ '%s', '%s' ],
            [ '%d' ]
        );

        if ($result) {
            add_settings_error( 'simple_reservation', 'simple_reservation', 'Room '.$name.' was updated.', 'updated' );
        } else {
            add_settings_error( 'simple_reservation', 'simple_reservation', 'There was an error during editing room '.$name.'.', 'error' );
        }
    }

    function delete_room( $id ) {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix.'simple_reservation_rooms',
            [ 'id' => $id ],
            [ '%d']
        );

        if ($result) {
            add_settings_error( 'simple_reservation', 'simple_reservation', 'Room was deleted.', 'updated' );
        } else {
            add_settings_error( 'simple_reservation', 'simple_reservation', 'There was an error during deleting room.', 'error' );
        }
    }
}