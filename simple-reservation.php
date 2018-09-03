<?php
/**
 * @package SimpleReservation
 */

/*
Plugin Name: Simple Reservation
Plugin URI: https://github.com/Lanseuo/simple-reservation
Description: This plugin makes reservations on your website very simple.
Version: 1.0.0
Author: Lucas Hild
Author URI: https://lucas-hild.de
License: MIT
Text Domain: simple-reservation
*/

/*
MIT License

Copyright (c) 2018 Lucas Hild

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

defined( 'ABSPATH' ) or die;

if ( file_exists( dirname(__FILE__) . '/vendor/autoload.php' ) ) {
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

// use Inc\Base\Activate;
// use Inc\Base\Deactivate;

// register_activation_hook( __FILE__, ['Activate', 'activate'] );
// register_deactivation_hook( __FILE__, ['Deactivate', 'deactivate'] );

register_activation_hook( __FILE__, 'activate_alecaddd_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_alecaddd_plugin' );

function activate_alecaddd_plugin() {
	Inc\Base\Activate::activate();
}
function deactivate_alecaddd_plugin() {
	Inc\Base\Deactivate::deactivate();
}


if ( class_exists( 'Inc\\Init' ) ) {
    Inc\Init::register_services();
}