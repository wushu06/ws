<?php

add_action('controll_email_settings', 'enable_disable_processing');

function enable_disable_processing($enable){
    wp_mail('nour@thebiggerboat.co.uk', 'here '.$enable, 'arr');
    $wc_emails = WC_Emails::instance();

    foreach ( $wc_emails->get_emails() as $email_id => $email ) {

        $key = 'woocommerce_' . $email->id . '_settings';
        $value = get_option( $key );
        if($email->id  == 'customer_processing_order'){
            if ( isset( $value['enabled'] ) ) {
                $value['enabled'] = $enable;
            }
            update_option( $key, $value );

        }



    }
}


add_action('change_avs_trigger', 'change_avs_callback', 20, 2);

function change_avs_callback($order_id)
{
    global $wpdb;
    $results = $wpdb->get_results("SELECT comment_content FROM  wp_comments WHERE comment_post_ID = {$order_id}", OBJECT);

    $AVS = '';
    foreach ($results as $res) {
        if (strpos($res->comment_content, 'AVS :') !== false) {
            $lines = explode(" \n ", $res->comment_content);
            $keys = explode(" : ", $lines[0]);
            $AVS = $keys[10];
        }


    }
    $order = wc_get_order($order_id);
    $g_option = get_option("ws_settings");
    $enable = !empty($g_option) && array_key_exists("enable",$g_option)  ? $g_option["enable"] : '';
    if($enable) {

        $found = false;
        $eCard = false;

        $results = $wpdb->get_results("SELECT order_item_name FROM  wp_woocommerce_order_items WHERE order_id = {$order_id}", OBJECT);

        $option = get_option("ws_settings");


        if (!empty($option) && array_key_exists("price", $option) && array_key_exists("avs", $option)) {
            $amount = get_option("ws_settings")['price'];
            $email = explode(',', $option['email']);
            $avs_check = $option['avs'];
            $id = admin_url('post.php?post=' . $order_id . '&action=edit');
            $headers = array('Content-Type: text/html; charset=UTF-8');
            foreach ($results as $res) {
                if ($res->order_item_name == 'Postage, Packing and Special Delivery (RMND)') {
                    $found = true;
                }
                /* if ($res->order_item_name == 'eCard') {
                     $eCard = true;
                 }*/
                if (strpos($res->order_item_name, 'eGift') !== false) {
                    $eCard = true;
                }
            }

            if ($AVS == $avs_check && $order->get_total() <= $amount) {
                if (!$found) {
                    if ($eCard) {
                        $order->update_status('to_scan_e_gift', 'avs update');
                    } else {
                        $order->update_status('to_scan', 'avs update');
                    }

                    /*wp_mail($email, 'AVS ' . $AVS . ' total: ' . $order->get_total(),
                        'Avs has passed for order id number ' . $order_id . ' , not transaction ID <br> <a href="'.$id.'">View Order</a>',
                        $headers );*/
                } else {
                    $order->update_status('to_scan_special', 'avs update');
                    wp_mail($email, 'AVS -Special- ' . $AVS . ' total: ' . $order->get_total(), 'Avs has passed for order id number ' . $order_id . ' , not transaction ID <br> <a href="' . $id . '">View Order</a>',
                        $headers);
                }

            } else {

                if (!$found) {
                    if ($eCard) {
                        $order->update_status('avs_failed_e_gift', 'avs update');
                        wp_mail($email, 'AVS  ' . $AVS . ' total: ' . $order->get_total(), 'Avs E-gift has failed for order id number ' . $order_id . ' , not transaction ID <br> <a href="' . $id . '">View Order</a>',
                            $headers);
                    } else {
                        $order->update_status('avs_failed', 'avs update');
                        wp_mail($email, 'AVS ' . $AVS . ' total: ' . $order->get_total(), 'Avs has failed for order id number ' . $order_id . ' , not transaction ID <br> <a href="' . $id . '">View Order</a>',
                            $headers);
                    }

                } else {
                    $order->update_status('failed_special', 'avs update');
                    wp_mail($email, 'AVS -Special-' . $AVS . ' total: ' . $order->get_total(), 'Avs has failed for order id number ' . $order_id . ' , not transaction ID <br> <a href="' . $id . '">View Order</a>',
                        $headers);
                }


            }
        }
    }
}

add_action( 'woocommerce_order_status_processing', 'tbb_status_custom_notification2', 20, 2 );
function tbb_status_custom_notification2( $order_id, $order ) {

    $heading = 'Order Processing';
    $subject = 'Order Processing';

    // Get WooCommerce email objects
    $mailer = WC()->mailer()->get_emails();

    // Use one of the active emails e.g. "Customer_Completed_Order"
    // Wont work if you choose an object that is not active
    // Assign heading & subject to chosen object
    $mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
    $mailer['WC_Email_Customer_Processing_Order']->settings['heading'] = $heading;
    $mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
    $mailer['WC_Email_Customer_Processing_Order']->settings['subject'] = $subject;

    // Send the email with custom heading & subject
    $mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );

    // To add email content use https://businessbloomer.com/woocommerce-add-extra-content-order-email/
    // You have to use the email ID chosen above and also that $order->get_status() == "refused"

}


add_action( 'woocommerce_order_status_changed', 'grab_order_old_status', 10, 4 );
function grab_order_old_status( $order_id, $status_from, $status_to, $order ) {
   if($status_to == 'pre_processing'){
       do_action( 'change_avs_trigger', $order_id);
   }
}
add_filter( 'woocommerce_payment_complete_order_status', 'mollie_wc_autocomplete_paid_orders', 10, 2 );
function mollie_wc_autocomplete_paid_orders( $order_status, $order_id ) {

   // $order = wc_get_order( $order_id );

    if ( $order_status == 'processing'  ) {
        return 'pre_processing';

    }

    return $order_status;
}




?>