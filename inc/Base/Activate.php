<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Base;

class Activate {
    public static function activate() {        
        global $wpdb;

        flush_rewrite_rules();
        self::create_tables();

        // TODO: Set default options
        if ( ! get_option( 'simple_reservation' ) ) {
            update_option( 'simple_reservation', []);
        }
    }

    public static function create_tables() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();

        $rooms_table_name = $wpdb->prefix.'simple_reservation_rooms';
        $rooms_sql = "CREATE TABLE $rooms_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            description text DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta( $rooms_sql );

        $reservations_table_name = $wpdb->prefix.'simple_reservation_reservations';
        $users_table_name = $wpdb->prefix.'users';
        $reservations_sql = "CREATE TABLE $reservations_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            date text NOT NULL,
            time_id int NOT NULL,
            description text DEFAULT '' NOT NULL,
            user_id bigint(20) NOT NULL,
            room_id bigint(20) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta( $reservations_sql );
    }
}