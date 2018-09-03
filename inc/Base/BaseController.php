<?php
/**
 * @package SimpleReservation
 */
namespace Inc\Base;

class BaseController {
    public $public;
    public $plugin_path;
    public $public_url;

    function __construct() {
        $this->plugin = plugin_basename( dirname( __FILE__, 3 ) ) . '/simple-reservation.php';
        $this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
        $this->plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) );
    }
}