<?php
/**
 * @package SimpleReservation
 */

namespace Inc\Pages;

use \Inc\Api\Callbacks\AdminCallbacks;
use \Inc\Api\SettingsApi;
use \Inc\Base\BaseController;

class Admin extends BaseController {
    public $settings;
    public $callbacks;
    
    function register() {
        $this->settings = new SettingsApi();
        $this->callbacks = new AdminCallbacks();

        $pages = [
            [
                'page_title' => 'SimpleReservation',
                'menu_title' => 'SimpleReservation',
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
                'page_title'  => 'Confirm Events',
                'menu_title'  => 'Confirm Events',
                'capability'  => 'manage_options',
                'menu_slug'   => 'simple_reservation_confirm',
                'callback'    => [$this->callbacks, 'admin_confirm'],
            ]
        ];

        $this->settings->add_pages( $pages )->with_sub_page( 'General' )->add_sub_pages( $subpages )->register();
    }
}