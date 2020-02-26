<?php
namespace Init;

class Includes
{


    public function __construct()
    {
        add_filter( 'woocommerce_email_actions', array($this,'so_27112461_woocommerce_email_actions') );
        add_filter( 'woocommerce_email_classes', array($this,'ws_completed_tracked_email') );
        $this->files_to_include();
    }


    public function so_27112461_woocommerce_email_actions( $actions ){
        $actions[] = 'woocommerce_order_status_tracked';
        return $actions;
    }


    function ws_completed_tracked_email( $email_classes ) {

        // include our custom email class
        require_once( PLUGIN_PATH.'/emails/class-completed-tracked.php' );


        // add the email class to the list of email classes that WooCommerce loads
        $email_classes['WC_Completed_Tracled_Email'] = new \WC_Completed_Tracled_Email();

        return $email_classes;

    }

    public function files_to_include()
    {
        require_once(PLUGIN_PATH.'/status-manager.php');
        require_once(PLUGIN_PATH.'/pdf-handler-admin.php');
        require_once(PLUGIN_PATH.'/pdf-hanlder-frontend.php');
        require_once(PLUGIN_PATH.'/gift-card-inc.php');
        require_once(PLUGIN_PATH.'/cookie-handler.php');
        require_once(PLUGIN_PATH.'/orders-columns.php');

    }

}