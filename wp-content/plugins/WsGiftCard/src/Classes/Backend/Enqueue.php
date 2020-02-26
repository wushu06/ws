<?php
namespace WS\Classes\Backend;
use WS\Classes\AbstractSettings;

/**
 * Class Enqueue
 * @package WS\Backend
 */
class Enqueue extends AbstractSettings
{

    /**
     * Enqueue constructor.
     */
    public function __construct()
    {
         add_action('admin_enqueue_scripts',  array($this, 'wsEnqueueStyle'));
    }

    /**
     * @param $hook
     */
    function wsEnqueueStyle($hook){
        wp_enqueue_script( 'my-js', WS_ASSETS. "/app.js", false );

        if ($hook != 'toplevel_page_ws-gift-cards') {
            return;
        }
        wp_enqueue_style( 'font',   "https://fonts.googleapis.com/css?family=Libre+Barcode+128&display=swap");
        wp_enqueue_style( 'style',  WS_ASSETS . "/style.css");
    }

}