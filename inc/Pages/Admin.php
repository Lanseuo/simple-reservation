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
        $this->add_custom_fields();
    }

    function add_admin_pages() {
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
                'page_title'  => 'About the plugin',
                'menu_title'  => 'About',
                'capability'  => 'manage_options',
                'menu_slug'   => 'simple_reservation_about',
                'callback'    => [$this->callbacks, 'admin_about'],
            ]
        ];

        $this->settings_api->register_admin_menu( $pages, 'General', $subpages );
    }

    function add_custom_fields() {
        $settings = [
            [
                'option_group' => 'simple_reservation_settings',
                'option_name'  => 'text_example',
                'callback'     => [$this->callbacks, 'simple_reservation_settings']
            ]
        ];

        $sections = [
            [
                'id'       => 'simple_reservation_index',
                'title'    => 'Settings',
                'callback' => function () { echo 'Introduction'; },
                'page'     => 'simple_reservation'
            ]
        ];

        $fields = [
            [
                'id'       => 'text_example',
                'title'    => 'Text Example',
                'callback' => [$this->callbacks, 'render_input_text'],
                'page'     => 'simple_reservation',
                'section'  => 'simple_reservation_index',
                'args'     => [
                    'placeholder' => 'Test eingeben ...',
                    'option_id'   => 'text_example'
                ]
            ]
        ];

        $this->settings_api->register_custom_fields( $settings, $sections, $fields );
    }
}