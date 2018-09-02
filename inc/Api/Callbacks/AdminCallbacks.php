<?php
/**
 * @package SimpleReservation
 */
namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class AdminCallBacks extends BaseController {
    function admin_general() {
        return require_once( "$this->plugin_path/templates/admin.php" );
    }

    function admin_confirm() {
        return "Hallo";
    }
}