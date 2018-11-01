<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Pages;
use Inc\Base\BaseController;
use WP_Error;

class Frontend extends BaseController {
    function render_simple_reservation( $attr ) {
        return require_once( "$this->plugin_path/templates/frontend.php" );
    }

    function register() {
        add_shortcode( 'simplereservation', [$this, 'render_simple_reservation']);

        add_action( 'rest_api_init', function () {
            register_rest_route( 'simplereservation' , '/info', [
                'methods' => 'GET',
                'callback' => [$this, 'info']
            ]);

            register_rest_route( 'simplereservation' , '/rooms', [
                'methods' => 'GET',
                'callback' => [$this, 'get_rooms']
            ]);

            register_rest_route( 'simplereservation' , '/rooms/(?P<room_id>\d+)/reservations', [
                'methods' => 'GET',
                'callback' => [$this, 'get_reservations']
            ]);

            register_rest_route( 'simplereservation' , '/rooms/(?P<room_id>\d+)/reservations', [
                'methods' => 'POST',
                'callback' => [$this, 'add_reservation']
            ]);

            register_rest_route( 'simplereservation' , '/rooms/(?P<room_id>\d+)/reservations/(?P<reservation_id>\d+)', [
                'methods' => 'DELETE',
                'callback' => [$this, 'delete_reservation']
            ]);
        });
    }

    function info( $request ) {
        if ( ! is_user_logged_in() ) {
            return new WP_Error( 'not_authorized', 'Zugriff verweigert.', [ 'status' => 401 ] );
        }

        if ( current_user_can( 'manage_options' ) ) {
            $is_admin = true;
            $users = array_map([$this, "user_id_and_name_callback"], get_users());
        } else {
            $is_admin = false;
            $users = [];
        }

        return [
            'user_id'  => wp_get_current_user()->ID,
            'username' => wp_get_current_user()->data->display_name,
            'is_admin' => $is_admin,
            'users'    => $users
        ];
    }

    function get_rooms( $request ) {
        if ( ! is_user_logged_in() ) {
            return new WP_Error( 'not_authorized', 'Zugriff verweigert.', [ 'status' => 401 ] );
        }

        global $wpdb;

        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}simple_reservation_rooms", OBJECT );

        return [
            'rooms' => $results
        ];
    }

    function get_reservations ( $request ) {
        if ( ! is_user_logged_in() ) {
            return new WP_Error( 'not_authorized', 'Zugriff verweigert.', [ 'status' => 401 ] );
        }

        global $wpdb;

        $room_id = $request['room_id'];

        $results = $wpdb->get_results( "
            SELECT {$wpdb->prefix}simple_reservation_reservations.*, {$wpdb->prefix}users.display_name as user
            FROM {$wpdb->prefix}simple_reservation_reservations
            JOIN {$wpdb->prefix}users
            ON {$wpdb->prefix}simple_reservation_reservations.user_id = {$wpdb->prefix}users.ID
            WHERE room_id=$room_id
        ", OBJECT );

        return [
            'reservations' => $results
        ];
    }

    function add_reservation( $request ) {
        if ( ! is_user_logged_in() ) {
            return new WP_Error( 'not_authorized', 'Zugriff verweigert.', [ 'status' => 401 ] );
        }

        global $wpdb;

        $room_id = $request['room_id'];
        $date = $request['date'];
        $time_id = $request['time_id'];
        $description = $request['description'];
        $length = $request['length'];

        // Avoid duplicate reservations
        $duplicate_reservations = $wpdb->get_results( "
            SELECT * FROM {$wpdb->prefix}simple_reservation_reservations
            WHERE
                room_id=$room_id
                AND date='$date'
                AND time_id=$time_id
        ", OBJECT );
        if ( $duplicate_reservations ) {
            return new WP_Error( 'malform', 'Doppelte Reservierungen sind nicht möglich.', [ 'status' => 400 ] );
        }

        $max_length = $this->get_max_length( $room_id, $date, $time_id );
        if ( $length > $max_length ) {
            return new WP_Error( 'malform', 'Überschneidende Reservierungen sind nicht möglich.', [ 'status' => 400 ] );
        }

        if ( current_user_can( 'manage_options' ) ) {
            // TODO: Check whether user id exists
            $user_id = $request["user_id"];
        } else {
            $user_id = wp_get_current_user()->ID;
        }

        $result = $wpdb->insert(
            $wpdb->prefix.'simple_reservation_reservations',
            [
                'room_id'     => $room_id,
                'date'        => $date,
                'time_id'     => $time_id,
                'description' => $description,
                'user_id'     => $user_id,
                'length'      => $length
            ],
            [ '%d', '%s', '%d', '%s', '%d' ]
        );

        $reservations_results = $wpdb->get_results( "
            SELECT {$wpdb->prefix}simple_reservation_reservations.*, {$wpdb->prefix}users.display_name as user
            FROM {$wpdb->prefix}simple_reservation_reservations
            JOIN {$wpdb->prefix}users
            ON {$wpdb->prefix}simple_reservation_reservations.user_id = {$wpdb->prefix}users.ID
            WHERE room_id=$room_id
        ", OBJECT );

        if ( $result ) {
            return [
                'code'         => 'success',
                'message'      => 'Die Reservierung wurde hinzugefügt.',
                'reservations' => $reservations_results
            ];
        } else {
            return new WP_Error( 'database_error', 'Beim Hinzufügen der Reservierung ist ein Problem aufgetreten.', [ 'status' => 500 ] );
        }
    }

    function delete_reservation( $request ) {
        if ( ! is_user_logged_in() ) {
            return new WP_Error( 'not_authorized', 'Zugriff verweigert.', [ 'status' => 401 ] );
        }

        $room_id = $request['room_id'];
        $reservation_id = $request['reservation_id'];

        if ( current_user_can( 'manage_options' ) ) {
            $where = [ 'id' => $reservation_id ];
            $where_format = [ '%d' ];
        } else {
            $where = [
                'id'      => $reservation_id,
                'user_id' => wp_get_current_user()->ID  // only delete own reservations
            ];
            $where_format = [ '%d', '%d' ];
        }

        global $wpdb;

        $result = $wpdb->delete(
            $wpdb->prefix.'simple_reservation_reservations',
            $where,
            $where_format
        );

        $reservations_results = $wpdb->get_results( "
            SELECT {$wpdb->prefix}simple_reservation_reservations.*, {$wpdb->prefix}users.display_name as user
            FROM {$wpdb->prefix}simple_reservation_reservations
            JOIN {$wpdb->prefix}users
            ON {$wpdb->prefix}simple_reservation_reservations.user_id = {$wpdb->prefix}users.ID
            WHERE room_id=$room_id
        ", OBJECT );

        if ( $result ) {
            return [
                'code'         => 'success',
                'message'      => 'Die Reservierung wurde entfernt.',
                'reservations' => $reservations_results
            ];
        } else {
            return new WP_Error( 'database_error', 'Beim Entfernen der Reservierung ist ein Problem aufgetreten.', [ 'status' => 500 ] );
        }
    }

    function get_max_length( $room_id, $date, $time_id ) {
        global $wpdb;

        $reservations_after_reservation = $wpdb->get_results( "
            SELECT * FROM {$wpdb->prefix}simple_reservation_reservations
            WHERE
                room_id=$room_id
                AND date='$date'
                AND time_id>$time_id
        ", OBJECT );

        $max_lengths = [];

        // Until end of day
        $max_lengths[] = 10 - $time_id;

        // Until next lesson
        foreach ( $reservations_after_reservation as $reservation ) {
            $max_lengths[] = $reservation->time_id - $time_id;
        }

        return min( $max_lengths );
    }

    function user_id_and_name_callback( $user ) {
        return [
            'id' => $user->ID,
            'name' => $user->display_name
        ];
    }
}