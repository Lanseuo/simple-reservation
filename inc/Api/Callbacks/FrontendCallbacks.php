<?php
/**
 * @package SimpleReservation
 */
// namespace Inc\Api\Callbacks;

// use Inc\Base\BaseController;

class FrontendCallbacks {
    function get_rooms() {
        global $wpdb;

        return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}simple_reservation_rooms", OBJECT );
    }

    function get_period( $room_id, $date, $time_id ) {
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

    static function add_reservation( $room_id, $date, $time_id, $description ) {
        global $wpdb;
        $wpdb->insert(
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
    }
}