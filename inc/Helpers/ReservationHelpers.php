<?php
/**
 * @package SimpleReservation
 */
namespace Inc\Helpers;
use Inc\Base\BaseController;
use Inc\Helpers\DateHelpers;

class ReservationHelpers extends BaseController {
    static function get_reservation( $room_id, $date, $time_id ) {
        global $wpdb;

        $reservations = $wpdb->get_results( "
            SELECT * FROM {$wpdb->prefix}simple_reservation_reservations
            WHERE
                repeat_weekly=0
                AND room_id=$room_id
                AND date='$date'
                AND time_id=$time_id
        ", OBJECT );

        $weekday = DateHelpers::get_weekday( $date );
        $repeating_reservations = $wpdb->get_results( "
            SELECT * FROM {$wpdb->prefix}simple_reservation_reservations
            WHERE
                repeat_weekly=1
                AND room_id=$room_id
                AND repeat_weekday=$weekday
                AND time_id=$time_id
        ", OBJECT );

        $all_reservations = array_merge( $reservations, $repeating_reservations );

        if ( $all_reservations ) {
            return $all_reservations[0];
        }
    }

    static function get_reservations_during_repeating_reservation( $room_id, $repeat_weekday, $time_id ) {
        global $wpdb;
        
        $reservations_with_similar_attributes = $wpdb->get_results( "
            SELECT * FROM {$wpdb->prefix}simple_reservation_reservations
            WHERE
                repeat_weekly=0
                AND room_id=$room_id
                AND time_id=$time_id
        ", OBJECT );

        $results = [];

        foreach ( $reservations_with_similar_attributes as $reservation ) {
            $weekday = DateHelpers::get_weekday( $reservation->date );

            if ( $weekday == $repeat_weekday ) {
                $results[] = $reservation;
            }
        }
        return $results;
    }
    
    static function get_max_length( $room_id, $date, $time_id ) {
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

        $weekday = DateHelpers::get_weekday( $date );
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

    static function get_max_length_repeating( $room_id, $repeat_weekday, $time_id ) {
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
            if ( DateHelpers::get_weekday( $reservation->date ) == $repeat_weekday ) {
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
            if ( DateHelpers::get_weekday( $reservation->date ) == $repeat_weekday ) {
                // Until next lesson
                $max_lengths[] = $reservation->time_id - $time_id;
            }
        }

        return min( $max_lengths );
    }
}