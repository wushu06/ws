<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * A custom Completed Tracked WooCommerce Email class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Completed_Tracled_Email extends WC_Email {


    /**
     * Set email defaults
     *
     * @since 0.1
     */
    public function __construct() {


        // Triggers for this email.






        // set ID, this simply needs to be a unique name
        $this->id = 'ws_completed_tracked';
        $this->customer_email = true;
        // this is the title in WooCommerce Email settings
        $this->title = 'Completed Tracked';

        // this is the description in WooCommerce email settings
        $this->description = 'Completed Tracked Notification emails are sent when tracking code is added';

        // these are the default heading and subject lines that can be overridden using the settings
        $this->heading = 'Your tracking code';
        $this->subject = 'Your tracking code';

        // these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
        $this->template_html  = 'emails/completed-tracked.php';
        $this->template_plain = 'emails/plain/completed-tracked.php';
        $this->placeholders   = array(
            '{site_title}'   => $this->get_blogname(),
            '{order_date}'   => '',
            '{order_number}' => '',
        );
        // Trigger on new paid orders


        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct();

       /* add_action( 'woocommerce_order_status_completed_tracked_notification', array( $this, 'trigger' ), 10, 2 );
        add_action( 'woocommerce_order_status_tracked', array( $this, 'trigger' ), 10, 2 );
        add_action( 'woocommerce_order_status_completed_tracked', array( $this, 'trigger' ), 10, 2 );*/
        add_action( 'woocommerce_order_status_tracked_notification', array( $this, 'trigger' ), 10, 2 );

       // add_action( 'ws_completed_tracked_email_notification', array( $this, 'trigger' ) );
        // Call parent constructor.
        parent::__construct();

    }





    public function trigger( $order_id, $order = false ) {
        $log  = "Error: tracked plugin ". $order_id.'-'.date("F j, Y, g:i a").PHP_EOL.
            "-------------------------".PHP_EOL;
        //Save string to log, use FILE_APPEND to append.
        file_put_contents(PLUGIN_PATH.  '/logs/tracking/log_'.date("j.n.Y").'.log', $log, FILE_APPEND);


        $this->setup_locale();

        if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
            $order = wc_get_order( $order_id );
        }

        if ( is_a( $order, 'WC_Order' ) ) {
            $this->object                         = $order;
            $this->recipient                      = $this->object->get_billing_email();
            $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
            $this->placeholders['{order_number}'] = $this->object->get_order_number();
        }

        if ( $this->is_enabled() && $this->get_recipient() ) {
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        $this->restore_locale();
    }
    /**
     * Get email subject.
     *
     * @since  3.1.0
     * @return string
     */
    public function get_default_subject() {
        return __( 'Your {site_title} order is now complete', 'woocommerce' );
    }

    /**
     * Get email heading.
     *
     * @since  3.1.0
     * @return string
     */
    public function get_default_heading() {
        return __( 'Thanks for shopping with us', 'woocommerce' );
    }

    /**
     * Get content html.
     *
     * @return string
     */
    public function get_content_html() {
        return wc_get_template_html(
            $this->template_html, array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false,
                'email'         => $this,
            )
        );
    }

    /**
     * Get content plain.
     *
     * @return string
     */
    public function get_content_plain() {
        return wc_get_template_html(
            $this->template_plain, array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => true,
                'email'         => $this,
            )
        );
    }

    /**
     * Initialize Settings Form Fields
     *
     * @since 2.0
     */
    public function init_form_fields() {



        $this->form_fields = array(
            'enabled'    => array(
                'title'   => __( 'Enable/Disable', 'woocommerce' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable this email notification', 'woocommerce' ),
                'default' => 'yes',
            ),
            'subject'    => array(
                'title'       => __( 'Subject', 'woocommerce' ),
                'type'        => 'text',
                'desc_tip'    => true,
                /* translators: %s: list of placeholders */
                'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
                'placeholder' => $this->get_default_subject(),
                'default'     => '',
            ),
            'heading'    => array(
                'title'       => __( 'Email heading', 'woocommerce' ),
                'type'        => 'text',
                'desc_tip'    => true,
                /* translators: %s: list of placeholders */
                'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
                'placeholder' => $this->get_default_heading(),
                'default'     => '',
            ),
            'email_type' => array(
                'title'       => __( 'Email type', 'woocommerce' ),
                'type'        => 'select',
                'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
                'default'     => 'html',
                'class'       => 'email_type wc-enhanced-select',
                'options'     => $this->get_email_type_options(),
                'desc_tip'    => true,
            ),
        );
    }


} // end \ws_completed_tracked_Email class
