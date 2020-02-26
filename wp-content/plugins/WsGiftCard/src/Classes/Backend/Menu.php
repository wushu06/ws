<?php
namespace WS\Classes\Backend;
use WS\Classes\AbstractSettings;

/**
 * Class Menu
 * @package WS\Backend
 */
class Menu extends AbstractSettings
{

    /**
     * Menu constructor.
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'wsMenuPage'));
    }

    /**
     * @inheritDoc
     */
    public function wsMenuPage()
    {
        add_menu_page(
            'WS gift cards',
            'WS gift cards',
            'manage_options',
            'ws-gift-cards',
            array($this, 'wsGiftCards')
        );

        //call register settings function
        add_action( 'admin_init', array($this,'wsMenuSettings') );
    }

    /**
     * @inheritDoc
     */
    public function wsMenuSettings()
    {
        //register our settings
        register_setting( 'ws-settings-group', 'ws_settings' );
    }


    /**
     * @return string
     */
    public function wsGiftCards()
    {
        require_once(WS_VIEW . '/backend/main.php');
    }

}