<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Pages;
use Inc\Base\BaseController;
use Inc\Utils;
use WP_Error;

class Frontend extends BaseController {
    function render_simple_reservation( $attr ) {
        require_once( "$this->plugin_path/templates/frontend.php" );
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

            register_rest_route( 'simplereservation' , '/test', [
                'methods' => 'GET',
                'callback' => [$this, 'test_get']
            ]);
        });
    }

    function test_get( $request ) {
        return Utils::get_reservation( $request['room_id'], $request['date'], $request['time_id'] );
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

        $repeat_weekly = $request['repeat_weekly'];
        $repeat_weekday = $request['repeat_weekday'];

        if ( $repeat_weekly ) {
            // Don't store unnecessary information
            $date = '';

            $duplicate_reservations = Utils::get_reservations_during_repeating_reservation( $room_id, $repeat_weekday, $time_id );
            if ( $duplicate_reservations ) {
                $dates = [];
                foreach ( $duplicate_reservations as $reservation ) {
                    $dates[] = Utils::to_beautiful_date( $reservation->date );
                }
                return new WP_Error( 'malform', 'Reservierung konnte nicht hinzugefügt werden, da sie sich mit Reservierungen an folgenden Tagen überschneidet: '.join(', ', $dates).'.', [ 'status' => 400 ] );
            }

        } else {
            // Don't store unnecessary information
            $repeat_weekday = '';

            // Avoid duplicate reservations
            $duplicate_reservations = Utils::get_reservation( $room_id, $date, $time_id );
            if ( $duplicate_reservations ) {
                return new WP_Error( 'malform', 'Doppelte Reservierungen sind nicht möglich.', [ 'status' => 400 ] );
            }
        }

        if ( $repeat_weekly ) {
            $max_length = $this->get_max_length_repeating( $room_id, $repeat_weekday, $time_id );
            if ( $length > $max_length ) {
                return new WP_Error( 'malform', 'Überschneidende Reservierungen sind nicht möglich.', [ 'status' => 400 ] );
            }
        } else {
            $max_length = $this->get_max_length( $room_id, $date, $time_id );
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

    function get_max_length( $room_id, $date, $time_id ) {
        global $wpdb;

        $max_lengths = [];

        // Until end of day
        $max_lengths[] = 10 - $time_id;

        $reservations_on_day_non_repeating = $wpdb->get_results( "
            SELECT * FROM {$wpdb->prefix}simple_reservation_reservations
            WHERE
                repeat_weekly=0
                AND room_id=$room_id
                AND date='$date'
        ", OBJECT );

        $weekday = Utils::get_weekday( $date );
        $reservations_on_day_repeating = $wpdb->get_results( "
            SELECT * FROM {$wpdb->prefix}simple_reservation_reservations
            WHERE
                repeat_weekly=1
                AND repeat_weekday=$weekday
                AND room_id=$room_id
        ", OBJECT );

        $reservations_on_day = array_merge( $reservations_on_day_non_repeating, $reservations_on_day_repeating );

        foreach ( $reservations_on_day as $reservation ) {
            // reservation has to be after
            if ( $reservation->time_id > $time_id ) {
                // Until next lesson
                $max_lengths[] = $reservation->time_id - $time_id;
            }
        }

        return min( $max_lengths );
    }

    function get_max_length_repeating( $room_id, $repeat_weekday, $time_id ) {
        global $wpdb;

        $max_lengths = [];

        // Until end of day
        $max_lengths[] = 10 - $time_id;

        $reservations_after_reservation_non_repeating = $wpdb->get_results( "
            SELECT * FROM {$wpdb->prefix}simple_reservation_reservations
            WHERE
                repeat_weekly=0
                AND room_id=$room_id
                AND time_id>$time_id
        ", OBJECT );

        foreach ( $reservations_after_reservation_non_repeating as $reservation ) {
            if ( Utils::get_weekday( $reservation->date ) == $repeat_weekday ) {
                // Until next lesson
                $max_lengths[] = $reservation->time_id - $time_id;
            }
        }

        $reservations_after_reservation_repeating = $wpdb->get_results( "
            SELECT * FROM {$wpdb->prefix}simple_reservation_reservations
            WHERE
                repeat_weekly=1
                AND room_id=$room_id
                AND repeat_weekday=$repeat_weekday
                AND time_id>$time_id
        ", OBJECT );

        foreach ( $reservations_after_reservation_repeating as $reservation ) {
            if ( Utils::get_weekday( $reservation->date ) == $repeat_weekday ) {
                // Until next lesson
                $max_lengths[] = $reservation->time_id - $time_id;
            }
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