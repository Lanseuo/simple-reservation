<?php
/**
 * @package SimpleReservation
 */
namespace Inc\Api;

class SettingsApi {
    // public $admin_pages = [];
    // public $admin_subpages = [];
    // public $settings = [];
    // public $sections = [];
    // public $fields = [];
    
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

    function register_custom_fields( array $settings, array $sections, array $fields ) {
        $this->settings = $settings;
        $this->sections = $sections;
        $this->fields = $fields;

        if ( ! empty( $this->settings ) ) {
            add_action( 'admin_init', [$this, 'add_custom_fields'] );
        }
    }

    function add_custom_fields() {
        foreach ($this->settings as $setting) {   
            register_setting( $setting['option_group'], $setting['option_name'], isset($setting['callback']) ? $setting['callback'] : '' );
        }

        foreach ($this->sections as $section) {   
            add_settings_section( $section['id'], $section['title'], isset($section['callback']) ? $section['callback'] : '', $section['page'] );
        }
        
        foreach ($this->fields as $field) {   
            add_settings_field( $field["id"], $field["title"], isset($field['callback']) ? $field['callback'] : '', $field['page'], $field['section'], isset($field['args']) ? $field['args'] : '' );
        }
    }
}