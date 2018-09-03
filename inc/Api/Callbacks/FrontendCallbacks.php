<?php
/**
 * @package SimpleReservation
 */
// namespace Inc\Api\Callbacks;

// use Inc\Base\BaseController;

class FrontendCallbacks {
    function __construct() {
        
    }

    function get_rooms() {
        global $wpdb;

        return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}simple_reservation_rooms", OBJECT );
    }
}