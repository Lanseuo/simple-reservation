<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Base;

class Activate {
    public static function activate() {        
        global $wpdb;

        flush_rewrite_rules();

        $table_name = $wpdb->prefix.'simple_reservation_rooms';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            description text DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
          
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        // TODO: Set default options
        if ( ! get_option( 'simple_reservation' ) ) {
            update_option( 'simple_reservation', []);
        }
    }
}