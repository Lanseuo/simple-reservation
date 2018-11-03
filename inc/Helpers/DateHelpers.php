<?php
/**
 * @package Date
 */
namespace Inc\Helpers;
use Inc\Base\BaseController;

class DateHelpers extends BaseController {
    static function get_weekday( $date ) {
        return date('N', strtotime( $date ) ) - 1; // 0 is Monday
    }

    static function to_beautiful_date( $date ) {
        return substr( $date , 6 ).'.'.substr ( $date, 4, 2 ).'.'.substr ( $date, 0, 4 );
    }
}