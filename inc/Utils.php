<?php
/**
 * @package SimpleReservation
 */

namespace Inc;
use Inc\Base\BaseController;
use DateTime;

class Utils extends BaseController {
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

        $weekday = self::get_weekday( $date );
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
            $weekday = self::get_weekday( $reservation->date );

            if ( $weekday == $repeat_weekday ) {
                $results[] = $reservation;
            }
        }
        return $results;
    }

    static function get_weekday( $date ) {
        return date('N', strtotime( $date ) ) - 1; // 0 is Monday
    }

    static function to_beautiful_date( $date ) {
        return substr( $date , 6 ).'.'.substr ( $date, 4, 2 ).'.'.substr ( $date, 0, 4 );
    }
}