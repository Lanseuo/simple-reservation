<?php
/**
 * @package SimpleReservation
 */
namespace Inc\Pages\Admin;
use Inc\Base\BaseController;

class AdminCallbacks extends BaseController {
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