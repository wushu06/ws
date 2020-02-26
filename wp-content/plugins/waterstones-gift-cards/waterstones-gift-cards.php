<?php
/*
Plugin Name: Waterstones gift cards
Description: Waterstones gift cards functions
Author: TBB
Version: 1.0
*/

defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );
define('PLUGIN_PATH', realpath(dirname(__FILE__)));
define('PLUGIN_URL', plugin_dir_url( __FILE__));

require_once(PLUGIN_PATH . '/classes/Menu.php');
require_once(PLUGIN_PATH . '/classes/Includes.php');

$g_option = get_option("ws_settings");
$enable = !empty($g_option) && array_key_exists("enable",$g_option)  ? $g_option["enable"] : '';

if (class_exists('Init\Menu')) {
    new Init\Menu();
}

if (class_exists('Init\Includes') && $enable) {
    new Init\Includes();
}


