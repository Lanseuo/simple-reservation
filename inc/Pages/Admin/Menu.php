<?php
/**
 * @package SimpleReservation
 */
namespace Inc\Pages\Admin;
use Inc\Base\BaseController;
use Inc\Pages\Admin\AdminCallbacks;

class Menu extends BaseController {
    function register() {
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
                'callback'   => [$this, 'admin_general'],
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
                'callback'    => [$this, 'admin_rooms'],
            ],
            [
                'parent_slug' => 'simple_reservation',
                'page_title'  => 'About the plugin',
                'menu_title'  => 'About',
                'capability'  => 'manage_options',
                'menu_slug'   => 'simple_reservation_about',
                'callback'    => [$this, 'admin_about'],
            ]
        ];

        $this->register_admin_menu( $pages, 'General', $subpages );
    }
    
    function admin_general() {
        return require_once( "$this->plugin_path/templates/admin.php" );
    }

    function admin_rooms() {
        return require_once( "$this->plugin_path/templates/admin_rooms.php" );
    }
    
    function admin_about() {
        return require_once( "$this->plugin_path/templates/admin_about.php" );
    }

    function register_admin_menu( $pages, $first_subpage_title, $subpages ) {
        $this->admin_pages = $pages;
        
        if ( ! empty($this->admin_pages ) ) {
            $admin_page = $this->admin_pages[0];
            
            $subpage = [
                [
                    'parent_slug' => $admin_page['menu_slug'],
                    'page_title'  => $admin_page['page_title'],
                    'menu_title'  => $first_subpage_title ? $first_subpage_title : $admin_page['menu_title'],
                    'capability'  => $admin_page['capability'],
                    'menu_slug'   => $admin_page['menu_slug'],
                    'callback'    => $admin_page['callback']
                ]
            ];
            
            $this->admin_subpages = $subpage;
        }

        $this->admin_subpages = array_merge( $this->admin_subpages, $subpages );
        
        if ( ! empty( $this->admin_pages ) ) {
            add_action( 'admin_menu', [$this, 'add_admin_menu'] );
        }  
    }

    function add_admin_menu() {
        foreach ( $this->admin_pages as $page ) {
            add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
        }

        foreach ( $this->admin_subpages as $page ) {
            add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'] );
        }
    }
}