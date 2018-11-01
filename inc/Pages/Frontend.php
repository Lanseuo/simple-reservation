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

    function get_rooms( $request ) {
        global $wpdb;

        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}simple_reservation_rooms", OBJECT );

        return [
            'rooms' => $results
        ];
    }

    function get_reservations ( $request ) {
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
            return new WP_Error( 'not_authorized', 'Du bist nicht angemeldet', [ 'status' => 401 ] );
        }

        global $wpdb;

        $room_id = $request['room_id'];
        $date = $request['date'];
        $time_id = $request['time_id'];
        $description = $request['description'];
        $length = $request['length'];


        $result = $wpdb->insert(
            $wpdb->prefix.'simple_reservation_reservations',
            [
                'room_id'     => $room_id,
                'date'        => $date,
                'time_id'     => $time_id,
                'description' => $description,
                'user_id'     => wp_get_current_user()->ID,
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

        if ( $result && $reservations_results ) {
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
        // TODO: Add room_id to query
        // TODO: Check authentication

        $room_id = $request['room_id'];
        $reservation_id = $request['reservation_id'];

        global $wpdb;

        $result = $wpdb->delete(
            $wpdb->prefix.'simple_reservation_reservations',
            [ 'id'      => $reservation_id ],
            [ '%d', '%d' ]
        );

        $reservations_results = $wpdb->get_results( "
            SELECT {$wpdb->prefix}simple_reservation_reservations.*, {$wpdb->prefix}users.display_name as user
            FROM {$wpdb->prefix}simple_reservation_reservations
            JOIN {$wpdb->prefix}users
            ON {$wpdb->prefix}simple_reservation_reservations.user_id = {$wpdb->prefix}users.ID
            WHERE room_id=$room_id
        ", OBJECT );

        if ( $result && $reservations_results ) {
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