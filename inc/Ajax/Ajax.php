<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Ajax;
use Inc\Base\BaseController;
use Inc\Helpers\DateHelpers;
use Inc\Helpers\ReservationHelpers;
use Inc\Helpers\Utils;
use WP_Error;

class Ajax extends BaseController {
    function register() {
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
            $users = array_map( [ new Utils(), 'user_id_and_name_callback'], get_users() );
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

        $repeat_weekly = $request['repeat_weekly'];
        $repeat_weekday = $request['repeat_weekday'];

        if ( $repeat_weekly ) {
            // Don't store unnecessary information
            $date = '';

            $duplicate_reservations = ReservationHelpers::get_reservations_during_repeating_reservation( $room_id, $repeat_weekday, $time_id );
            if ( $duplicate_reservations ) {
                $dates = [];
                foreach ( $duplicate_reservations as $reservation ) {
                    $dates[] = DateHelpers::to_beautiful_date( $reservation->date );
                }
                return new WP_Error( 'malform', 'Reservierung konnte nicht hinzugefügt werden, da sie sich mit Reservierungen an folgenden Tagen überschneidet: '.join(', ', $dates).'.', [ 'status' => 400 ] );
            }

        } else {
            // Don't store unnecessary information
            $repeat_weekday = '';

            // Avoid duplicate reservations
            $duplicate_reservations = ReservationHelpers::get_reservation( $room_id, $date, $time_id );
            if ( $duplicate_reservations ) {
                return new WP_Error( 'malform', 'Doppelte Reservierungen sind nicht möglich.', [ 'status' => 400 ] );
            }
        }

        if ( $repeat_weekly ) {
            $max_length = ReservationHelpers::get_max_length_repeating( $room_id, $repeat_weekday, $time_id );
            if ( $length > $max_length ) {
                return new WP_Error( 'malform', 'Überschneidende Reservierungen sind nicht möglich.', [ 'status' => 400 ] );
            }
        } else {
            $max_length = ReservationHelpers::get_max_length( $room_id, $date, $time_id );
            if ( $length > $max_length ) {
                return new WP_Error( 'malform', 'Überschneidende Reservierungen sind nicht möglich.', [ 'status' => 400 ] );
            }
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
                'room_id'        => $room_id,
                'date'           => $date,
                'time_id'        => $time_id,
                'description'    => $description,
                'user_id'        => $user_id,
                'length'         => $length,
                'repeat_weekly'  => $repeat_weekly,
                'repeat_weekday' => $repeat_weekday 
            ],
            [ '%d', '%s', '%d', '%s', '%d', '%d', '%d' ]
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
}