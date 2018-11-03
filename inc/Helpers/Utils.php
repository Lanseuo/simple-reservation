<?php
/**
 * @package SimpleReservation
 */
namespace Inc\Helpers;
use Inc\Base\BaseController;

class Utils extends BaseController {
    static function user_id_and_name_callback( $user ) {
        return [
            'id' => $user->ID,
            'name' => $user->display_name
        ];
    }
}