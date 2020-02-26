<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );



?>

<?php /* translators: %s: Customer first name */ ?>
    <p><?php printf( esc_html__( 'Hi, you\'ve been sent a gift card by %s', 'woocommerce' ), esc_html( $order->get_billing_first_name().' '.$order->get_billing_last_name() ) ); ?></p>

<?php /* translators: %s: Site title */ ?>

<?php

if($data && !empty($data)) {

    echo '<p>Message: ' . $data['msg'] . '</p>';



    require_once(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/pdf/fpdf.php');
    require_once(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/pdf/vendor/autoload.php');

    echo '<div style="border: 1px solid #ccc; padding: 10px;">';
    tbb_generate_barcode($data['barcode'],$data['pin'], $data['product'], $order, false);
    echo '</div>';
    //$image1 = '<img alt="Embedded Image" src="data:image/png;base64,' . base64_encode($imageData) . '" width="200"/>';
  /*  $token = encrypt_decrypt('decrypt', $data['barcode']);
    $url = get_template_directory_uri() . '/barcode.php?size=50&text=' . $token;
    $thumb = get_the_post_thumbnail_url($data['product']['id']);
    $message = '<table style="width:100%;margin: 10px 0; border-style: dotted;">';
    $message .= '<tr><td><img src="' . $thumb . '" width="200" /></td>';
    $message .= '<tr><td>' .  $token . '</td>';
    //TODO: change link to live website
    $message .= '<tr><td><a href="https://staging.waterstonesgiftcards.com/my-account/view-order/'.trim(str_replace("#", "", $order->get_order_number())).'">To print off your E-gift card please click here</a></td>';
    $message .= "<td><table><tr><td>";
    $message .= $data['product']['name'] . '</td></tr><tr><td>';
    $message .= "<img src='" . $url . "'></td></tr></table>";
    $message .= '</td></tr></table>';
    echo $message;*/




}
/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
//do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
//do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

?>
   <!-- <p>
        <?php /*esc_html_e( 'Thank you for shopping with Waterstones.', 'woocommerce' ); */?>
    </p>-->
<?php
do_action('tbb_after_email_footer', $order);
echo '<div style="width:100%; height: 20px;"></div>';
/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
//do_action( 'woocommerce_email_footer', $email );
