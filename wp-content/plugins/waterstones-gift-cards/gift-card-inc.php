<?php

/*
 * encrypt and decrypt
 */
function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'This is my secret key';
    $secret_iv = 'This is my secret iv';
    // hash
    $key = hash('sha256', $secret_key);
    $sum = (string)$string;
    $last_four = $sum[strlen($sum)-9].$sum[strlen($sum)-8].$sum[strlen($sum)-7].$sum[strlen($sum)-6].$sum[strlen($sum)-5].$sum[strlen($sum)-4].$sum[strlen($sum)-3].$sum[strlen($sum)-2].$sum[strlen($sum)-1];
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 10);
    if ( $action == 'encrypt' ) {
        if (strpos($string, 'ws***') !== false) {
            $output = $string;
        }else{
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
            $output = "ws***" . $output.' '.$last_four;

        }
    } else if( $action == 'decrypt' ) {
        if (strpos($string, 'ws***') !== false) {
            $output = str_replace("ws***","",$string);
            $output = substr($output, 0, strpos($output, ' '));
            $after_space = substr($output, strpos($output, " ") + 1);
            $output = openssl_decrypt(base64_decode($output), $encrypt_method, $key, 0, $iv);
        }else{
            $output = $string;
        }


    }
    return $output;
}



/*
 * add barcode meta to evry item in the order based on item qty
 */
add_action( 'woocommerce_checkout_create_order_line_item', 'custom_checkout_create_order_line_item', 10, 4 );
function custom_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {

    $envelope = !empty(get_option("ws_settings")) && array_key_exists("envelope",get_option("ws_settings")) ? get_option("ws_settings")["envelope"] : '' ;

    if($item->get_product_id() != 18085 ){
        for ($i=1; $i <= $item->get_quantity(); $i++){

            $item->add_meta_data( 'barcode', 'Ready to scan', false );
            $item->add_meta_data( 'pin', '3 digit pin', false );
        }

        if(isset($_COOKIE['giftee']) && !empty($_COOKIE['giftee'])) {
            $old_ar = json_decode(stripslashes($_COOKIE['giftee']));
            foreach ($old_ar as $cookie_arr) {
                foreach ($cookie_arr as $key => $value) {
                    if ($item["variation_id"] == $key) {
                        foreach ($value as $gift) {
                            $item->add_meta_data( 'giftee-email', $gift->email, false );
                            $item->add_meta_data( 'giftee-msg', $gift->msg == ''  ? 'No message' : $gift->msg , false );
                        }
                    }
                }
            }
        }
    }
    
    $host = parse_url(get_option('siteurl'), PHP_URL_HOST);

    setcookie('gift-wrapped', '', -1, '/', $host);
    unset($_COOKIE['gift-wrapped']);

    setcookie('gift', '', -1, '/', $host);
    unset($_COOKIE['gift']);

    //setcookie('giftee', '', -1, '/', $host);
    //unset($_COOKIE['giftee']);

}
add_action( 'woocommerce_payment_complete', 'so_payment_complete' );
function so_payment_complete( $order_id ){
    $host = parse_url(get_option('siteurl'), PHP_URL_HOST);
    setcookie('giftee', '', -1, '/', $host);
    unset($_COOKIE['giftee']);
}

/*
 * add evelope to every order except e-gift cards
 */

add_action( 'woocommerce_checkout_order_processed', 'add_evelope_after_new_order', 10, 3  );
function add_evelope_after_new_order($order_id, $posted_data, $order){
    $t = 0;
    $found = false;
    foreach($order->get_items() as $item) {
        if( !has_term('e-cards', 'product_cat',$item->get_product_id())) {
            if ($item->get_product_id() == 18085) {
                $found = true;
            }
            $t = $t + $item->get_quantity();
        }
    }

    if(!$found & $t != 0){
        $order->add_product(wc_get_product(18085), $t);
    }
}





/*
 * cron for sftp report
 */
// 1- set custom cron
function sap_cron_recurrence_interval($schedules) {

    if(!isset($schedules["2min"])){
        $schedules["2min"] = array(
            'interval' => 120,
            'display' => __('Once every 2 minutes'));
    }
    if(!isset($schedules["3min"])){
        $schedules["3min"] = array(
            'interval' => 240,
            'display' => __('Once every 3 minutes'));
    }
    if(!isset($schedules["30min"])){
        $schedules["30min"] = array(
            'interval' => 30*60,
            'display' => __('Once every 30 minutes'));
    }
    if(!isset($schedules["hourly"])){
        $schedules["hourly"] = array(
            'interval' => 60*60,
            'display' => __('Once every hour'));
    }
    return $schedules;
}
add_filter('cron_schedules','sap_cron_recurrence_interval');

//2- exc cron
if (!wp_next_scheduled('sftp_ws_report_settings' )) {
    // wp_schedule_event(time(), '2min', 'sftp_ws_report_settings' );
   // wp_schedule_event(time(), 'daily', 'sftp_ws_report_settings' );
    $option = get_option("ws_settings");
    if(!empty(get_option("ws_settings")) && array_key_exists("time",get_option("ws_settings")) ){
       // wp_schedule_event(strtotime( date("Y-m-d").' 16:00:00' ), 'daily', 'sftp_ws_report_settings' );
        wp_schedule_event(strtotime( $option['time'] ), 'daily', 'sftp_ws_report_settings' );

    }
}
add_action('sftp_ws_report_settings' , 'ws_custom_order_report_export' );
//wp_clear_scheduled_hook( 'sftp_ws_report_settings' );

function ws_custom_order_report_export(){
    ini_set('memory_limit', '1024M');
    $fileName = ABSPATH.'wp-content/uploads/ws/'.date( 'Y-m-d' ).'-order-report.csv';
    $fp = fopen($fileName, 'w');


    $yesterday = date('Y-m-d', strtotime('-1 days'));
    $today = date('Y-m-d');


    $query = new WC_Order_Query(array(
        'limit' => -1,
        'orderby' => 'date',
        'date_created' => $yesterday,


    ));
    $args = array(
        'status' => 'processing', // change later to processing
    );
    $orders = $query->get_orders();

    $price = 0;
    foreach ($orders as $order) {
        $status = ['completed', 'tracked'];
        if( in_array( $order->get_status(), $status) ){
    
            $check = get_post_meta($order->get_id(), 'order_type', true);
            if ($check == 'customer' || $check == 'admin') {
                foreach ($order->get_items() as $item) {
                    if (!has_term('e-cards', 'product_cat', $item->get_product_id())) {
                        foreach ($item->get_formatted_meta_data('_', true) as $meta_id => $meta) {

                            if ($meta->key == 'pa_price') {
                                $price = $meta->value;
                            }
                            if ($meta->key == 'barcode' && $meta->value != 'Ready to scan') {
                                // echo $price.' = '.$meta->value.'<br>';
                                $token = encrypt_decrypt('decrypt', $meta->value);
                                fputcsv($fp, [
                                    '<CUSTOMERACCOUNT Action="AddChange" AccountType="SVC" AccountNumber="',
                                    trim($token),
                                    '" ActivityType="BACT" ActivityAmount="' . $price . '" BalanceType="MO"/>',
                                    '<CUSTOMERACCOUNT Action="AddChange" AccountType="SVC" AccountNumber="' . trim($token) . '" ActivityType="BACT" ActivityAmount="' . $price . '" BalanceType="MO"/>'
                                ]);

                                $log = "Order #" . $order->get_id() . '-' . date("F j, Y, g:i a") . PHP_EOL .
                                    "-------------------------" . PHP_EOL;
                                //Save string to log, use FILE_APPEND to append.
                                file_put_contents(PLUGIN_PATH.  '/logs/log_' . date("j.n.Y") . '.log', $log, FILE_APPEND);

                            }
                        }

                    }
                }
            }
        }

    }


//Close the file handle.
    fclose($fp);
    require_once(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/sftp/Net/SFTP.php');
    require_once(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/sftp/Crypt/RC4.php');
    $option = get_option("ws_settings");
    $sftp = new \Net_SFTP($option['ip']);
    $email = explode(',', $option['email']);
    //$email = 'ian.bottomore@encompassprint.co.uk';
    if (!$sftp->login($option['username'], $option['password'])) {
        $email = explode(',', $option['email']);
        wp_mail( $email, 'Failed email', 'Automatic scheduled email from WordPress to test cron');
        // exit('Login Failed');
    } else{
      // $sftp->put('/'.date( 'Y-m-d' ).'-order-report.csv', $fileName, NET_SFTP_LOCAL_FILE);
       if($fileName) {
            $mail_attachment = array($fileName);
            $headers = array('Content-Type: text/html; charset=UTF-8');
          //  wp_mail($email, 'Sftp waterstones report', 'Automatic scheduled email from waterstonesgiftcards', $headers, $mail_attachment);

        }
    }


    return;
}


/*
 * add button to action column to download invoice pdf
 */
require_once(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/Html2Pdf/vendor/autoload.php');
use \Spipu\Html2Pdf\Html2Pdf;
add_action( 'admin_head', 'customer_second_payment_reminder_button_css' );
function customer_second_payment_reminder_button_css() {
    global $pagenow;

    if( $pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'shop_order' ) {
        echo '<style>.wc-action-button-'.'custom_pdf'.'::after { font-family: woocommerce !important; content: "\e02e" !important; color: red; }</style>';
    }
}
add_filter( 'woocommerce_admin_order_actions', 'add_customer_second_payment_reminder_button', 100, 2 );
function add_customer_second_payment_reminder_button( $actions, $order ) {

    // if($order->get_status() =='completed' ||$order->get_status() =='processing' ) {

    $actions['custom_pdf'] = array(
        'url' => wp_nonce_url(
            admin_url('admin-ajax.php?action=customer_second_payment_reminder&order_id=' . $order->get_id()),
            'customer-second-payment-reminder'
        ),
        'name' => __('Download custom invoice pdf', 'ws'),
        'action' => 'custom_pdf',
        'all'=> 'yes'
    );


    // }
    return $actions;
}





/*
 * send order received email with new statuses
 */
/*add_action( 'woocommerce_order_status_to_scan', 'tbb_status_custom_notification', 20, 2 );
add_action( 'woocommerce_order_status_to_scan_special', 'tbb_status_custom_notification', 20, 2 );
add_action( 'woocommerce_order_status_avs_failed', 'tbb_status_custom_notification', 20, 2 );
add_action( 'woocommerce_order_status_failed_special', 'tbb_status_custom_notification', 20, 2 );
add_action( 'woocommerce_order_status_processing', 'tbb_status_custom_notification', 20, 2 );*/



/*add_action('woocommerce_order_status_changed', 'woo_order_status_change_custom', 10, 3);
function woo_order_status_change_custom($this_get_id, $this_status_transition_from, $this_status_transition_to, $instance){
    if($this_status_transition_from == 'avs_failed'){
        wp_mail('nour@thebiggerboat.co.uk', 'test',$this_status_transition_from );

        remove_action( 'woocommerce_order_status_to_scan', 'tbb_status_custom_notification', 20, 2 );
        remove_action( 'woocommerce_order_status_to_scan_special', 'tbb_status_custom_notification', 20, 2 );
        return;
    }


}*/
add_action( 'woocommerce_order_status_to_scan', 'tbb_status_custom_notification', 20, 2 );
add_action( 'woocommerce_order_status_to_scan_special', 'tbb_status_custom_notification', 20, 2 );
add_action( 'woocommerce_order_status_to_scan_e_gift', 'tbb_status_custom_notification', 20, 2 );
add_action( 'woocommerce_order_status_avs_failed_e_gift', 'tbb_status_custom_notification', 20, 2 );
//add_action( 'woocommerce_order_status_avs_failed', 'tbb_status_custom_notification', 20, 2 );
//add_action( 'woocommerce_order_status_failed_special', 'tbb_status_custom_notification', 20, 2 );
//add_action( 'woocommerce_order_status_processing', 'tbb_status_custom_notification', 20, 2 );
function tbb_status_custom_notification( $order_id, $order ) {

    $heading = 'Order Status';
    $subject = 'Order Status';

    // Get WooCommerce email objects
    $mailer = WC()->mailer()->get_emails();

    // Use one of the active emails e.g. "Customer_Completed_Order"
    // Wont work if you choose an object that is not active
    // Assign heading & subject to chosen object
    $mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
    $mailer['WC_Email_Customer_Processing_Order']->settings['heading'] = $heading;
    $mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
    $mailer['WC_Email_Customer_Processing_Order']->settings['subject'] = $subject;
    $mailer['WC_Email_Customer_Processing_Order']->template_html  = 'emails/customer-processing-order-2.php';

    // Send the email with custom heading & subject
    $mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );

    // To add email content use https://businessbloomer.com/woocommerce-add-extra-content-order-email/
    // You have to use the email ID chosen above and also that $order->get_status() == "refused"

}

/*
 * send order received email with Completed tracked
 */

/*add_action( 'woocommerce_order_status_tracked', 'tracked_status_custom_notification', 20, 2 );
function tracked_status_custom_notification( $order_id, $order ) {

    $log  = "Error: tracked ". $order_id.'-'.date("F j, Y, g:i a").PHP_EOL.
        "-------------------------".PHP_EOL;
    //Save string to log, use FILE_APPEND to append.
    file_put_contents(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/log_'.date("j.n.Y").'.log', $log, FILE_APPEND);


    $heading = 'Your tracking code';
    $subject = 'Your tracking code';

    // Get WooCommerce email objects
    $mailer = WC()->mailer()->get_emails();

    // Use one of the active emails e.g. "Customer_Completed_Order"
    // Wont work if you choose an object that is not active
    // Assign heading & subject to chosen object
    $mailer['WC_Email_Customer_Completed_Order']->heading = $heading;
    $mailer['WC_Email_Customer_Completed_Order']->settings['heading'] = $heading;
    $mailer['WC_Email_Customer_Completed_Order']->subject = $subject;
    $mailer['WC_Email_Customer_Completed_Order']->settings['subject'] = $subject;

    // Send the email with custom heading & subject
    $mailer['WC_Email_Customer_Completed_Order']->trigger( $order_id );

    // To add email content use https://businessbloomer.com/woocommerce-add-extra-content-order-email/
    // You have to use the email ID chosen above and also that $order->get_status() == "refused"

}*/
/*
 * replace order note
 */
function md_custom_woocommerce_checkout_fields( $fields )
{
    $fields['order']['order_comments']['placeholder'] = '';
    $fields['order']['order_comments']['label'] =
        '<span style="color: red;">Not applicable for ecards</span><br><br><strong>Gift message</strong><br> For gift cards only. Your message will be printed on the delivery note sent with the cards to the recipient';

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'md_custom_woocommerce_checkout_fields' );


/*
 * encrypt barcodes on status complete
 */

add_action( 'woocommerce_order_status_completed', 'tbb_order_complete_action', 10, 1);



function tbb_order_complete_action($order_id)
{

    $order = wc_get_order($order_id);
    $pure = true;
    foreach ($order->get_items() as $item_id => $item) {
        if( !has_term('e-cards', 'product_cat',$item->get_product_id())){
            $pure= false;
        }
        $meta_value = $item->get_meta('barcode', true);
        if ($item->get_meta('barcode', true)) {
            foreach ($item->get_formatted_meta_data() as $meta_id => $meta) {
                if ($meta->value !== 'Ready to scan' && $meta->value !== '') {

                    $encrypted_txt = encrypt_decrypt('encrypt', $meta->value);

                    wc_update_order_item_meta($item_id, 'barcode', $encrypted_txt, $meta->value);
                }
            }
        }

    }
    // send email to giftee only if it is pure ecard order
    if($pure){
        bhi_wc_process_order_meta_box_action($order_id);
    }

}
add_action( 'woocommerce_order_status_processing', 'tbb_order_processing_action', 10, 1);
function tbb_order_processing_action($order_id)
{

    $order = wc_get_order($order_id);
    $mixed = false;
    foreach ($order->get_items() as $item_id => $item) {
       if( has_term('e-cards', 'product_cat',$item->get_product_id())){
           $mixed = true;
       }
    }

    // order has ecard so send email on processing
   if($mixed){
       bhi_wc_process_order_meta_box_action($order_id);
   }

}

/*
 * Send giftee email
 */
/**
 * Add a custom action to order actions select box on edit order page
 *
 * @param array $actions order actions array to display
 * @return array - updated actions
 */


/**
 * Send Email to Customer with Estimated Ship Date
 *
 * @param \WC_Order $order
 */
function bhi_wc_process_order_meta_box_action( $order_id ) {
    function get_custom_email_html( $order, $heading = false, $mailer, $description ) {

        $template = 'emails/e-cards.php';

        return wc_get_template_html( $template, array(
            'order'         => $order,
            'email_heading' => $heading,
            'sent_to_admin' => false,
            'plain_text'    => false,
            'email'         => $mailer,
            'data'          => $description

        ) );

    }


    $order = wc_get_order($order_id);
    $arr =[];
    $barcode = [];
    $pins =[];
    $is_sent = [];
    foreach ($order->get_items() as $item_id => $item) {

        $meta_value = $item->get_meta('giftee-email', true);


        $key = '';
        $email = '';
        if ($item->get_meta('giftee-email', true)) {
            foreach ($item->get_formatted_meta_data() as $meta_id => $meta) {
                if($meta->key == 'barcode' ){
                    $barcode[] =  $meta->value ;
                }
                if($meta->key == 'pin' ){
                    $pins[] =  $meta->value ;
                }
                if($meta->key == 'giftee-email'){
                    $key = $meta_id ;
                    $item_product_data_array =   $item->get_data();
                    $email                   =   $meta->value;
                    $arr[$key]['product']    =   $item->get_data();
                    $arr[$key]['name']       =   $item->get_data()['name'];
                    $arr[$key]['thumb']      =   get_field('pdf_background', $item->get_product_id())['url'];
                    $arr[$key]['price']      =   wc_get_product( $item_product_data_array["variation_id"] )->get_price();
                    $arr[$key]['email']      =   $email;

                }

                if($meta->key == 'giftee-msg' && $email !==''){
                    $arr[$key]['msg'] =  $meta->value ;

                }
                // fixing empty metadata issue
                if($meta->key == 'giftee-msg' && $meta->value == 'No message'){
                    $arr[$key]['msg'] = '' ;
                }

            }
            $i = 0;
            foreach ($arr as $key => $msg) {

                $arr[$key]['barcode'] =  $barcode[$i] ;
                $arr[$key]['pin'] =  $pins[$i] ;

                $i++;
            }



        }

    }

    if(!empty($arr)){

        foreach ($arr as $key=>$msg){


           $path = tbb_generate_pdf_attchement(
               $order,
               $msg['msg'],
               $msg['name'],
               $msg['thumb'],
               encrypt_decrypt('decrypt',$msg['barcode']),
               $msg['pin'],
               $msg['price'],
               $order_id,
               $msg['email'],
               'F'
           );

            // $mailer['WC_Email_Customer_Completed_Order']->settings['description']  = $arr[$email];
            // load the mailer class
           $mailer = WC()->mailer();

            //format the email
            $recipient = $msg['email'];
            $subject = __("E-gift card", 'theme_name');
            $content = get_custom_email_html( $order, $subject, $mailer, $arr[$key] );
            $headers = "Content-Type: text/html\r\n";
            $attachements = $path;
            //send the email through wordpress
            $is_sent[$recipient] = $mailer->send( $recipient, $subject, $content, $headers, $attachements );

        }

    }
    //do_action('check_emails', $is_sent);

}

add_action('check_emails', 'check_emails_action');
function check_emails_action($recipients){
    wp_mail('nour@thebiggerboat.co.uk', 'emails check', print_r($recipients, true));

}



/*
 * Send barcode image by email
 * the barcode function is in function.php
 */

add_action( 'woocommerce_email_after_order_table', 'ws_send_img_with_barcode', 20, 4 );

function ws_send_img_with_barcode($order, $sent_to_admin, $plain_text, $email){
     if ( $email->id == 'customer_completed_order' || $email->id == 'customer_processing_order' ) {





    foreach( $order->get_items() as $item_id => $item ){

            // Get the common data in an array:
            $item_product_data_array = $item->get_data();
            // $item_product_data_array['name'];

            // Get the special meta data in an array:
            $item_product_meta_data_array = $item->get_meta_data();
            $barcode = '';
            $meta_value = $item->get_meta( 'barcode', true );
                if($item->get_meta( 'barcode', true ) && has_term('e-cards', 'product_cat',$item->get_product_id())){
                    echo '<table style="width:100%;margin: 10px 0; border:1px solid #ccc"><tr><td>';
                    /*require_once(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/pdf/vendor/autoload.php');
                    $barcode = new \Com\Tecnick\Barcode\Barcode();
                    $bobj = $barcode->getBarcodeObj('C128C', "{$meta_value}", 450, 70, 'black', array(0, 0, 0, 0));
                    $imageData = $bobj->getPngData();*/
                    foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {

                        if($meta->key == 'barcode' && $meta->value !== 'Ready to scan') {
                            //$image1 = '<img alt="Embedded Image" src="data:image/png;base64,' . base64_encode($imageData) . '" width="200"/>';
                           $barcode = $meta->value;

                        }
                        if($meta->key == 'pin' && $meta->value !== '3 digit pin') {
                            //$image1 = '<img alt="Embedded Image" src="data:image/png;base64,' . base64_encode($imageData) . '" width="200"/>';
                            tbb_generate_barcode($barcode, $meta->value, $item_product_data_array, $order, true);

                        }

                      /*  if($meta->key == 'pin'){
                            echo '<table>';
                            echo '<tr><td><strong>Pin</strong>: '.$meta->value.'</td></tr>';
                            echo '</table>';
                        }*/
                        echo '<table>';
                        echo '<tr><td>';
                        if($meta->key == 'giftee-email'){
                            echo '<strong>Email</strong>: '.$meta->value.'<br/>';

                        }
                        if($meta->key == 'giftee-msg' && $email !==''){
                            echo '<strong>Message</strong>: '.$meta->value.'<br/>';
                        }
                        echo '</td></tr>';
                        echo '</table>';

                    }
                    echo '</td></tr></table>';
                }

        }
     }
}


function tbb_generate_barcode($code, $pin, $item_product_data_array, $order, $account_link){
    @$token = encrypt_decrypt('decrypt', $code);
    $url = get_template_directory_uri() . '/barcode.php?size=50&text=' . $token;
    $p_id =   wc_get_product($item_product_data_array['product_id']) ? wc_get_product($item_product_data_array['product_id'])->get_parent_id() : 0;
    $thumb = $p_id ? get_the_post_thumbnail_url($p_id) : get_the_post_thumbnail_url($item_product_data_array['product_id']);
    $style = $account_link ? '1px solid #ccc;' : 'none';

    $message = '<table style="border-bottom:  '.$style.'">';
    $message .= '<tr>';  // first row
    $message .= '<td>'.$item_product_data_array['name'].'<br/>';
    $message .= $account_link ?
        '<a href="'.get_site_url().'/my-account/view-order/'.trim(str_replace("#", "", $order->get_order_number())).'">To print off your E-gift card please click here</a>'
        : ''; // third row
    $message .= '</td>';
    $message .= '</tr>';

    $message .= '<tr>';  // second row
    $message .= "<td ><img src='" . $url . "'     width='auto' height='40'/><br><span style='text-align: center; letter-spacing: 5px; display: block;'>" .  $token . "</span></td>";
    $message .= "<td style='text-align: center;' >Pin: ".$pin."</td>";
    $message .= '<td><img src="' . $thumb . '" width="100" /></td>';
    $message .= '</tr>';
    $message .= '</table>';


    echo $message;
}

/*
 * adding css and js to admin
 */
add_action('admin_head', 'tbb_admin_custom_script');

function tbb_admin_custom_script() {
    echo '<style>
    input[value="barcode"],
    input[value="pin"],
    input[value="giftee-email"],
      input[value="giftee-msg"],
    input[value="gift_wrapped"]{
   /* display: none;*/
       pointer-events: none; 
      border: none;
      background: #ccc;
   } 
   .delete-order-item.tips {
    display: none;
   }
  </style>';
}

add_action( 'admin_print_scripts', function() {
    // I'm using NOWDOC notation to allow line breaks and unescaped quotation marks.
    echo <<<'EOT'
<script type="text/javascript">
jQuery(function($){
    var sortingArr = [
        'mark_picked',
        'mark_to_scan_special',
        'mark_to_scan_e_gift',
        'mark_to_scan',       
        'mark_processing',
        'mark_completed' ,
        'mark_tracked', 
        'mark_wc-missing' ,
        'mark_cancelled' ,
        'mark_refunded' ,
        'mark_on-hold',
        'mark_avs_failed',
        'mark_avs_failed_e_gift',
        'mark_failed_special',   
        'write_downloads',
        'trash',
        'remove_personal_data'
        ];
  
    var select = $('#bulk-action-selector-top');
      select.html(select.find('option').sort(function(x, y) {   
        return sortingArr.indexOf($(x).val()) - sortingArr.indexOf($(y).val());
      }));
  
    $('.display_meta td p').each(function() {
            
            if($(this).html().indexOf("ws***") >= 0){
                var before = $(this).html().split(" ")[1]
                $(this).html('##########'+before)	
            }    
         });

   $('input[value="barcode"]').each( function(){
      $(this).next('textarea').on('change', function() {         
          console.log($(this).val().length)
          if( $(this).val().length != 19){
            $(this).css('borderColor', 'red')              
        }else{
              $(this).css('borderColor', '#ddd')    
        }
      })
      
      
   });
    
   $('.edit-order-item.tips').on('click', function(){
       /* $(this).closest('.item ').find('.meta_items').each(function(){
             $(this).find('textarea').attr('value', '');
        });*/
         
         $('textarea').on('click', function(){
            if($(this).attr('value') == 'Ready to scan'){
                 $(this).attr('value', '');
                 $(this).attr('maxlength', '19');
                 $(this).attr('minlength', '19');
            }
            if($(this).attr('value') == '3 digit pin'){
                 $(this).attr('value', '');
            }
          });
        $('input').each(function(){
           if($(this).val() == 'giftee_email' || $(this).val() == 'gift_wrapped' || $(this).val() == 'barcode' ){
                         //$(this).prop('disabled', true);
              }
        });
    });
});
</script>
EOT;
}, PHP_INT_MAX );

add_action('tbb_after_email_footer', 'tbb_after_email_footer_callback');
function tbb_after_email_footer_callback($order){
    $check = false;
    foreach ($order->get_items() as $item) {
            foreach ($item->get_formatted_meta_data('_', true) as $meta_id => $meta) {
                if ($meta->key == 'barcode' && $meta->value != 'Ready to scan') {
                    $check = true;
                }
            }
    }

    if($check){
        $output = '<div style="font-size: 11px; line-height: 12px; margin-top: 20px;"><p style="margin-bottom: 0;">Summary Terms &amp; Conditions for Waterstones Electronic Gift Cards ("Gift Cards")';
        $output .= '<ol><li >Validly activated gift cards can be used as full or part payment for goods in Waterstones shops across the UK, the Isle of Man and Jersey and for purchases made online at Waterstones.com';
        $output .= '</li><li>Gift Cards cannot be used to purchase any other vouchers or tokens and cannot be exchanged for cash and are non-refundable.';
        $output .='</li><li>The gift card is for personal use only and cannot be sold nor used for commercial purposes.';
        $output .='</li><li>If a card is not used (meaning used to purchase goods or presented for a balance enquiry) for any consecutive 24 month period the balance will reduce to zero and the Gift Card will be suspended.';
        $output .='</li><li>Waterstones is not liable for lost or stolen Gift Cards, but please contact our Customer Support team to see if we can help.';
         $output .='</li><li>The full terms and conditions applicable to all Waterstones Gift Cards can be found at Waterstones.com/giftcards.';
        $output .='</li></ol>Gift Cards are issued by Waterstones Booksellers Ltd, 203-206 Piccadilly London W1J 9HD (Company number 00610095).</div>';
        echo $output;
    }


}

/*
 * orderes editable
 */

add_filter( 'wc_order_is_editable', 'wc_make_processing_orders_editable', 10, 2 );
function wc_make_processing_orders_editable( $is_editable, $order ) {
    if (
        $order->get_status() == 'processing'
        || $order->get_status() == 'avs_failed'
        || $order->get_status() == 'to_scan'
        || $order->get_status() == 'to_scan_special'
        || $order->get_status() == 'failed_special'
        || $order->get_status() == 'to_scan_e_gift'
        || $order->get_status() == 'avs_failed_e_gift'
    ) {
        $is_editable = true;
    }

    return $is_editable;
}

/*
 * disable qty on basket for ecards
 */
function change_quantity_input( $product_quantity, $cart_item_key, $cart_item ) {
        $product_id = $cart_item['product_id'];
    if( has_term('e-cards', 'product_cat',$product_id)) {
        return '<h5>' . $cart_item['quantity'] . '</h5>';
    }

    return $product_quantity;
}
add_filter( 'woocommerce_cart_item_quantity', 'change_quantity_input', 10, 3);

/*
 *  change out of stock text
 */
add_filter( 'woocommerce_get_availability', 'wcs_custom_get_availability', 1, 2);
function wcs_custom_get_availability( $availability, $_product ) {

    // Change Out of Stock Text
    if ( ! $_product->is_in_stock() ) {
        $availability['availability'] = __( 'Insufficient stock available. Would you like to choose a different design and/or denomination', 'woocommerce');
    }
    return $availability;
}

function tbb_ajax_order_range()
{



    $yesterday =  date('Y-m-d 00:00:00', strtotime('yesterday'));
    $after =  $_POST['after'];
    $before = $_POST['before'];
    $today = date('Y-m-d 00:00:00', strtotime('today'));


    $query = new WC_Order_Query(array(
         'limit' => -1,
         'orderby' => 'date',
        'date_completed' => $today,
        'date_query' => array(
             'after' => $after,
             'before' => $before
         )

     ));

    $orders = $query->get_orders();

    $price = 0;
    $i = 0;
    try {
        $html = '<table cellpadding="10"> ';
        foreach ($orders as $order) {
            $status = [ 'completed','tracked'];
            if( in_array( $order->get_status(), $status) ) {
                foreach ($order->get_items() as $item) {
                    if (!has_term('e-cards', 'product_cat', $item->get_product_id())) {
                        foreach ($item->get_formatted_meta_data('_', true) as $meta_id => $meta) {

                            if ($meta->key == 'pa_price') {
                                $price = $meta->value;
                            }
                            if ($meta->key == 'barcode' && $meta->value != 'Ready to scan') {
                                $i++;
                                $html .= '<tr><td style="padding: 10px;  border: 1px solid black;">' . $i . '. Order number : <strong><a href="'.site_url().'/wp-admin/post.php?post='.$order->get_id().'&action=edit">#' . $order->get_id() .
                                    '</a></strong> created@ ' .
                                    $order->get_date_created()
                                        ->format('Y F j, g:i a') . ' modiefied @'. $order->get_date_modified().'</td></tr>';
                            }
                        }
                    }
                }
            }
        }
        $html .= '</table>';
        $html .= '<p>total: '.$i.'</p>';
        echo $html;
    }
    catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }




}

add_action('wp_ajax_orderajaxrange', 'tbb_ajax_order_range');
add_action('wp_ajax_nopriv_orderajaxrange', 'tbb_ajax_order_range');
