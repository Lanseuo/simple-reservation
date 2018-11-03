<?php
/**
 * @package SimpleReservation
 */
namespace Inc\Pages\Admin;
use Inc\Base\BaseController;

class SettingsLinks extends BaseController {
    function register() {
        add_filter( 'plugin_action_links_' . $this->plugin, [$this, 'settings_link'] );
    }

    function settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=simple_reservation">Settings</a>';
        array_push( $links, $settings_link );
        return $links;
    }
}