<?php
/**
 * @package SimpleReservation
 */

namespace Inc;

final class Init {
    static function get_services() {
        return [
            Ajax\Ajax::class,
            Pages\Admin\AdminEnqueue::class,
            Pages\Admin\Menu::class,
            Pages\Admin\SettingsLinks::class,
            Pages\Frontend\FrontendEnqueue::class,
            Pages\Frontend\Shortcodes::class
        ];
    }

    static function register_services() {
        foreach ( self::get_services() as $class ) {
            $service = self::instantiate( $class );
            if ( method_exists( $service, 'register' ) ) {
                $service->register();
            }
        }
    }

    private static function instantiate( $class ) {
        return new $class();
    }
}