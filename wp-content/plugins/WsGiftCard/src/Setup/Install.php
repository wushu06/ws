<?php
namespace WS\Setup;

/**
 * Class Install
 * @package WS\Setup
 */
class Install
{

    /**
     * Install constructor.
     * @param $file
     */
    public function __construct($file)
    {
        register_activation_hook($file, array($this, 'eGiftEmailsTable') );
    }

    /**
     * @return \wpdb::query
     */
    public function eGiftEmailsTable()
    {
        global $table_prefix, $wpdb;

        $table_name = $wpdb->prefix . 'egits_emails';
        $wp_track_table = $table_prefix . "$table_name ";

        #Check to see if the table exists already, if not, then create it
        if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table)
        {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
              id mediumint(11) NOT NULL AUTO_INCREMENT,
              email varchar(55) NOT NULL,
              date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              message text NOT NULL,
              product_name text NOT NULL,
              product_price text NOT NULL,
              product_image text NOT NULL,
              barcode text NOT NULL,
              pin text NOT NULL,
              PRIMARY KEY  (id)
            ) $charset_collate;";

            $wpdb->query($sql);
        }
    }

}