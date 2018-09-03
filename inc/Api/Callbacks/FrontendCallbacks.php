<?php
/**
 * @package SimpleReservation
 */
// namespace Inc\Api\Callbacks;

// use Inc\Base\BaseController;

class FrontendCallbacks {
    function action() {
        if ( ! isset($_POST['action']) ) return;

        switch ( $_POST['action'] ) {
            case 'add_reservation':
                $this->add_reservation(
                    $_POST['room_id'],
                    $_POST['date'],
                    $_POST['time_id'],
                    $_POST['description']
                );
                break;
            case 'delete_reservation':
                $this->delete_reservation( $_POST['id'] );
                break;
            default:
                die('Action "'.$_POST['action'].'" was not found');
        }
    }

    function get_rooms() {
        global $wpdb;

        return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}simple_reservation_rooms", OBJECT );
    }

    function get_reservation( $room_id, $date, $time_id ) {
        global $wpdb;

        $results = $wpdb->get_results( "
            SELECT {$wpdb->prefix}simple_reservation_reservations.*, {$wpdb->prefix}users.display_name as user
            FROM {$wpdb->prefix}simple_reservation_reservations
            JOIN {$wpdb->prefix}users
            ON {$wpdb->prefix}simple_reservation_reservations.user_id = {$wpdb->prefix}users.ID
            WHERE
                room_id=$room_id
                AND date='$date'
                AND time_id=$time_id
        ", OBJECT );
        return $results ? $results[0] : null;
    }

    function add_reservation( $room_id, $date, $time_id, $description ) {
        if ( ! is_user_logged_in()) die('You are not logged in!');

        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix.'simple_reservation_reservations',
            [
                'room_id'     => $room_id,
                'date'        => $date,
                'time_id'     => $time_id,
                'description' => $description,
                'user_id'     => wp_get_current_user()->ID
            ],
            [ '%d', '%s', '%d', '%s', '%d' ]
        );

        return $result ? true : false;
    }

    function delete_reservation( $id ) {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix.'simple_reservation_reservations',
            [
                'id'      => $id,
                'user_id' => wp_get_current_user()->ID
            ],
            [ '%d', '%d' ]
        );

        return $result ? true : false;
    }
}