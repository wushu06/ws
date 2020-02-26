<?php
/*
Plugin Name: Waterstones gift cards 02
Description: Waterstones gift cards functions - phase two
Author: TBB
Version: 2.0
*/

require "vendor/autoload.php";
use WS\Setup\Install;
use WS\Classes\Backend\Menu;
use WS\Classes\Backend\Enqueue;
use WS\Classes\Backend\PostType;
use WS\Classes\Backend\Extend;
use WS\Classes\Frontend\Order;
use WS\Controller\Request;
use WS\Controller\Save;
use WS\Controller\Delete;

if (!class_exists('WsGiftCard')) {
    /**
     * Class Init
     */
    class WsGiftCard
    {
        /**
         * Init constructor.
         */
        public function __construct()
        {
            $this->initPaths();
            $this->initInstall();
            $this->initMenu();
            $this->initEnqueue();
            $this->initPostType();
            $this->initExtend();
            $this->initOrder();
            $this->initRequest();
        }

        /**
         * @return string
         */
        public function initPaths()
        {
            defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');
            define('WS_PATH', realpath(dirname(__FILE__)));
            define('WS_VIEW', realpath(dirname(__FILE__)) . '/src/view');
            define('WS_URL', plugin_dir_url(__FILE__));
            define('WS_ASSETS', plugin_dir_url(__FILE__) . '/src/view/backend/assets');
        }

        /**
         * @return Install
         */
        public function initInstall()
        {
            if (!class_exists('Install')) {
                new Install(__FILE__);
            }
        }

        /**
         * @return Menu
         */
        public function initMenu()
        {
            if (!class_exists('Menu')) {
                new Menu();
            }
        }

        /**
         * @return Enqueue
         */
        public function initEnqueue()
        {
            if (!class_exists('Enqueue')) {
                new Enqueue();
            }
        }

        /**
         * @return PostType
         */
        public function initPostType()
        {
            if (!class_exists('PostType')) {
                new PostType();
            }
        }

        /**
         * @return Extend
         */
        public function initExtend()
        {
            if (!class_exists('Extend')) {
                new Extend();
            }
        }

        /**
         * @return Order
         */
        public function initOrder()
        {
            if (!class_exists('Order')) {

                new Order();


            }
        }

        /**
         * @return Request
         */
        public function initRequest()
        {
                ob_start();
                    new Save; new Delete;
                ob_clean();

        }
    }

    new WsGiftCard();
}