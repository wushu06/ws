<?php
namespace Init;

class Menu
{


    public function __construct()
    {

        // create custom plugin settings menu
        add_action('admin_menu', array($this, 'ws_menu_page'));
        add_action('admin_enqueue_scripts',  array($this, 'ws_enqueue_style'));
    }



    public function ws_menu_page() {

        //create new top-level menu
        // add_menu_page('My Cool Plugin Settings', 'Cool Settings', 'administrator', __FILE__, 'my_cool_plugin_settings_page' , plugins_url('/images/icon.png', __FILE__) );
        add_menu_page( 
            'WS gift cards',
            'WS gift cards',
            'manage_options',
            'ws-gift-cards',
            array($this, 'ws_gift_cards') ,
            PLUGIN_URL.'/assets/gift2.png'
        );

        //call register settings function
        add_action( 'admin_init', array($this,'register_my_cool_plugin_settings') );
    }

    public function register_my_cool_plugin_settings() {
        //register our settings
        register_setting( 'ws-settings-group', 'ws_settings' );
        register_setting( 'ws-settings-group', 'ws_settings_sftp' );
        // register_setting( 'ws-settings-group', 'option_etc' );
    }

    function ws_enqueue_style($hook){
        wp_enqueue_style( 'font',   "https://fonts.googleapis.com/css?family=Libre+Barcode+128&display=swap");

        wp_enqueue_script( 'my-js-date', PLUGIN_URL . "/assets/jquery.datetimepicker.full.min.js", true );
        wp_enqueue_script( 'my-js', PLUGIN_URL . "/assets/app.js", true );



        if ($hook != 'toplevel_page_ws-gift-cards') {
            return;
        }
        wp_enqueue_style( 'style-date',  PLUGIN_URL . "/assets/jquery-ui.min.css");

        wp_enqueue_style( 'style-date-ui',  PLUGIN_URL . "/assets/jquery.datetimepicker.min.css");
        wp_enqueue_style( 'style',  PLUGIN_URL . "/assets/style.css");
    }

    public function ws_gift_cards() {
        require_once(PLUGIN_PATH . '/template/main.php');
    }


}