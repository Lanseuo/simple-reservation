<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Pages;

use \Inc\Api\Callbacks\AdminCallbacks;
use \Inc\Api\SettingsApi;
use \Inc\Base\BaseController;

class Admin extends BaseController {
    public $settings_api;
    public $callbacks;
    
    function register() {
        $this->settings_api = new SettingsApi();
        $this->callbacks = new AdminCallbacks();

        $this->add_admin_pages();
    }

    function add_admin_pages() {
        $pages = [
            [
                'page_title' => 'SimpleReservation',
                'menu_title' => 'Reservierungen',
                'capability' => 'manage_options',
                'menu_slug'  => 'simple_reservation',
                'callback'   => [$this->callbacks, 'admin_general'],
                'icon_url'   => 'dashicons-building',
                'position'   => 110
            ]
        ];

        $subpages = [
            [
                'parent_slug' => 'simple_reservation',
                'page_title'  => 'Rooms',
                'menu_title'  => 'Rooms',
                'capability'  => 'manage_options',
                'menu_slug'   => 'simple_reservation_rooms',
                'callback'    => [$this->callbacks, 'admin_rooms'],
            ],
            [
                'parent_slug' => 'simple_reservation',
                'page_title'  => 'About the plugin',
                'menu_title'  => 'About',
                'capability'  => 'manage_options',
                'menu_slug'   => 'simple_reservation_about',
                'callback'    => [$this->callbacks, 'admin_about'],
            ]
        ];

        $this->settings_api->register_admin_menu( $pages, 'General', $subpages );
    }
}