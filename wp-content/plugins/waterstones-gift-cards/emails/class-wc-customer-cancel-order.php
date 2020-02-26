<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class WC_Customer_Cancel_Order
 */
class WC_Customer_Cancel_Order extends WC_Email {
    /**
     * Create an instance of the class.
     *
     * @access public
     * @return void
     */
    function __construct() {
        // Email slug we can use to filter other data.
        $this->id          = 'wc_customer_cancelled_order';
        $this->title       = __( 'Cancelled Order to Customer', 'custom-wc-email' );
        $this->description = __( 'An email sent to the customer when an order is cancelled.', 'custom-wc-email' );
        // For admin area to let the user know we are sending this email to customers.
        $this->customer_email = true;
        $this->heading     = __( 'Order Cancelled', 'custom-wc-email' );
        // translators: placeholder is {blogname}, a variable that will be substituted when email is sent out
        $this->subject     = sprintf( _x( '[%s] Order Cancelled', 'default email subject for cancelled emails sent to the customer', 'custom-wc-email' ), '{blogname}' );

        // Template paths.
        $this->template_html  = 'emails/e-gifts.php';
       // $this->template_plain = 'emails/plain/wc-customer-cancelled-order.php';
        $this->template_base  = CUSTOM_WC_EMAIL_PATH . 'templates/';

        // Action to which we hook onto to send the email.
        add_action( 'save_post', array( $this, 'trigger' ) );
        //add_action( 'woocommerce_order_status_on-hold_to_cancelled_notification', array( $this, 'trigger' ) );
        parent::__construct();
    }

    function trigger( $order_id ) {
        wp_mail('nour@thebiggerboat.co.uk', 'send', 'trigge');
        $this->object = wc_get_order( $order_id );
        if ( version_compare( '3.0.0', WC()->version, '>' ) ) {
            $order_email = $this->object->billing_email;
        } else {
            $order_email = $this->object->get_billing_email();
        }
        $this->recipient = $order_email;
        if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
            return;
        }
        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    public function get_content_html() {
        return wc_get_template_html( $this->template_html, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => false,
            'email'			=> $this
        ), '', $this->template_base );
    }
    /**
     * Get content plain.
     *
     * @return string
     */
    public function get_content_plain() {
        return wc_get_template_html( $this->template_plain, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => true,
            'email'			=> $this
        ), '', $this->template_base );
    }
}