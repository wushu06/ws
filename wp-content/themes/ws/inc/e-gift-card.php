<?php

// MOVED TO PLUGIN
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

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}



/*
 * add barcode meta to evry item in the order based on item qty
 */
add_action( 'woocommerce_checkout_create_order_line_item', 'custom_checkout_create_order_line_item', 10, 4 );
function custom_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {

    if($item->get_product_id() != 13593  ){
        for ($i=1; $i <= $item->get_quantity(); $i++){

            $item->add_meta_data( 'barcode', 'Ready to scan', false );
        }
    }
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
            if ($item->get_product_id() == 13593) {
                $found = true;
            }
            $t = $t + $item->get_quantity();
        }
    }

    if(!$found & $t != 0){
        $order->add_product(wc_get_product(13593), $t);
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
if (!wp_next_scheduled('sftp_ws_report_setting' )) {
    // wp_schedule_event(time(), '2min', 'sftp_ws_report_setting' );
    wp_schedule_event(time(), 'daily', 'sftp_ws_report_setting' );
}
add_action('sftp_ws_report_setting' , 'ws_custom_order_report_export' );


function ws_custom_order_report_export(){
    ini_set('memory_limit', '1024M');
    $fileName = ABSPATH.'wp-content/uploads/ws/'.date( 'Y-m-d' ).'-order-report.csv';
    $fp = fopen($fileName, 'w');

    $yesterday = date( 'Y-m-d', strtotime( '-1 days' ) );
    $today = date( 'Y-m-d' );

    $query = new WC_Order_Query( array(
        'limit' => -1,
        'orderby' => 'date',
        'date_created' => $today,


    ) );
    $args = array(
        'status' => 'completed', // change later to processing
    );
    $orders = $query->get_orders($args);

    $price = 0;
    foreach( $orders as $order ){

        $check = get_post_meta($order->get_id(), 'order_type', true);


        if($check == 'customer') {
            foreach ($order->get_items() as $item) {
                foreach ($item->get_formatted_meta_data('_', true) as $meta_id => $meta) {


                    if ($meta->key == 'pa_price') {
                        $price = $meta->value;
                    }
                    if ($meta->key == 'barcode' && $meta->value != 'Ready to scan') {
                        // echo $price.' = '.$meta->value.'<br>';
                        $token = encrypt_decrypt('decrypt', $meta->value);
                        fputcsv($fp, [
                            '<CUSTOMERACCOUNT Action="AddChange" AccountType="SVC" AccountNumber="',
                            $token,
                            '" ActivityType="BACT" ActivityAmount="' . $price . '" BalanceType="MO"/>',
                            '<CUSTOMERACCOUNT Action="AddChange" AccountType="SVC" AccountNumber="' . $token. '" ActivityType="BACT" ActivityAmount="' . $price . '" BalanceType="MO"/>'
                        ]);
                    }
                }

            }
        }

    }

//Close the file handle.
    fclose($fp);
    require_once(ABSPATH . '/wp-content/themes/ws/sftp/Net/SFTP.php');
    require_once(ABSPATH . '/wp-content/themes/ws/sftp/Crypt/RC4.php');
    $sftp = new \Net_SFTP('178.128.37.187');
    if (!$sftp->login('master_zbycjdkcbs', 'Urzbt3tS')) {
        wp_mail( 'nour@thebiggerboat.co.uk', 'Failed email', 'Automatic scheduled email from WordPress to test cron');
        // exit('Login Failed');
    } else{
        //  wp_mail( 'nour@thebiggerboat.co.uk', 'Success email', 'Automatic scheduled email from WordPress to test cron');
        $sftp->put('applications/qhbgyxgwwx/public_html/ws/'.date( 'Y-m-d' ).'-order-report.csv', $fileName, NET_SFTP_LOCAL_FILE);

    }


    return;
}

/*
 * add button to action column to download invoice pdf
 */

use \Spipu\Html2Pdf\Html2Pdf;
add_action( 'admin_head', 'customer_second_payment_reminder_button_css' );
function customer_second_payment_reminder_button_css() {
    global $pagenow;

    if( $pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'shop_order' ) {
        echo '<style>.wc-action-button-'.'custom_pdf'.'::after { font-family: woocommerce !important; content: "\e02e" !important; color: #3ffca9; }</style>';
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
        );


   // }
    return $actions;
}

add_action( 'wp_ajax_customer_second_payment_reminder', 'get_customer_second_payment_reminder' );
function get_customer_second_payment_reminder() {
    if ( current_user_can('edit_shop_orders') && check_admin_referer('customer-second-payment-reminder') &&
        isset($_GET['order_id']) && get_post_type( absint( wp_unslash($_GET['order_id']) ) ) === 'shop_order' ) {
        $order_id = absint( wp_unslash($_GET['order_id']) );
        $order    = wc_get_order($order_id);

        if( is_a($order, 'WC_Order') ) {
            download_custom_invoice_pdf($order);
        }
    }
    // wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=shop_order' ) );
    //  exit;
}

/*
 * customer download pdf - from backend
 */
function download_custom_invoice_pdf($order){
    
  
    require_once(ABSPATH . '/wp-content/themes/ws/Html2Pdf/vendor/autoload.php');

    $html2pdf = new Html2Pdf();
    $logo = get_template_directory_uri().'/img/waterstones-logo.png';

    //$order = wc_get_order( 13620 );
    $type = get_post_meta($order->get_id(), 'order_type', true);

        $header = '<p>Waterstones Customer Accounts, Price\'s Gate, <br>Homer Road, Solihul B91 3QQ <br>Helpline Number: 01159071899<br>
                    Email: waterstoneshelpline@encompassprint.co.uk</p>';
    if($type == 'corporate'){
             $header = '<p>Waterstones Customer Accounts, Price\'s Gate, <br>Homer Road, Solihul B91 3QQ <br>
                    Email: waterstoneshelpline@encompassprint.co.uk</p>';
    }
    $item_quantity = 0;
    foreach ($order->get_items() as $item) {
        //TODO: change the envelope id with the live id
        if ($item->get_product_id() != 13593 && !has_term('e-cards', 'product_cat',$item->get_product_id()) ) {
            $item_quantity = $item_quantity + $item->get_quantity();
        }
    }
    if ($item_quantity >= 7) {
            $letter = '<p class="test">Large letter</p>';
    } else{
        //@TODO: make it electornice
        $letter = '<p>Normal letter</p>';
    }

    $str = <<<EOF
<style type="text/css">
<!--
    h1 {color: #000033} 
    
    div.standard
    {
        padding-left: 5mm;
    }
    .test {
        background-color: red;
    }
-->
</style>
    <page>
    <div><img  alt="logo" src="{$logo}"  width="200"/></div>
    <div>{$header}</div>
    <h3>ID: #{$order->get_id()} type: {$type} </h3>
    <div>{$letter} </div>
<h3>total: {$order->get_total()}</h3>
<div style="border: dashed 1mm #000000; padding: 10mm; margin: 5mm;">
<h3>Billing</h3>
<p>cusomter id: {$order->get_customer_id()}</p>
<p>cusomter note: {$order->get_customer_note()}</p>

<p> {$order->get_billing_first_name()}</p>
<p> {$order->get_billing_last_name()}</p>
<p> {$order->get_billing_company()}</p>
<p> {$order->get_billing_address_1()}</p>
<p> {$order->get_billing_address_2()}</p>
<p> {$order->get_billing_city()}</p>
<p> {$order->get_billing_postcode()}</p>
<p> {$order->get_billing_country()}</p>
<p> {$order->get_billing_email()}</p>
<p> {$order->get_billing_phone()}</p>
</div>
<div style="border: dashed 1mm #000000;  padding: 10mm; margin: 5mm;">
<h3>Shipping</h3>
<p>{$order->get_shipping_first_name() }  {$order->get_shipping_last_name()}</p>

<p> {$order->get_shipping_company()}</p>
<p> {$order->get_shipping_address_1()}</p>
<p> {$order->get_shipping_address_2()}</p>
<p> {$order->get_shipping_city()}</p>
<p> {$order->get_shipping_postcode()}</p>
<p> {$order->get_shipping_country()}</p>

</div>
</page>
<page>
<h1>Order items</h1>
</page>
EOF;
    $html2pdf->writeHTML( $str);

    if($order->get_items()) {
        foreach ($order->get_items() as $item) {
            $html2pdf->writeHTML('<p> ' . $item->get_name() . '</p>');
            $url = get_the_post_thumbnail_url($item->get_product_id());
            if ($url) {
                $html2pdf->writeHTML('<p> <img  alt="Embedded Image" src="' . $url . '"  width="200"/> </p>');
            }
            foreach ($item->get_formatted_meta_data('_', true) as $meta_id => $meta) {
                if (
                    $meta->key == 'barcode' && $meta->value != 'Ready to scan'
                ) {
                    $token = encrypt_decrypt('decrypt', $meta->value);

                    $html2pdf->writeHTML('<div style="border: dashed 1mm #000000; padding: 10mm;"><barcode  type="C128" value="' . $token . '" label="label" style="width:30mm; height:6mm; color: #000; font-size: 4mm"></barcode></div>');

                }
            }
        }
    }
    $latest_notes = wc_get_order_notes( array(
        'order_id' => $order->get_id(),
        'limit'    => 1,
        'orderby'  => 'date_created_gmt',
    ) );

    $latest_note = current( $latest_notes );

    if ( isset( $latest_note->content ) ) {
        $html2pdf->writeHTML('<p style="border: dashed 1mm #000000"> gift message: ' .$latest_note->content . '</p>');
    }


    $html2pdf->output('document_name.pdf', 'D');


}

/*
 * Download pdf from customer account
 */
function prefix_send_email_to_admin() {
    if(isset($_POST['order'])) {

        require_once(ABSPATH . '/wp-content/themes/ws/Html2Pdf/vendor/autoload.php');

        $html2pdf = new Html2Pdf();


        $order = wc_get_order($_POST['order']);
        $prod_title = explode('- £',$_POST['name']);


        $str = <<<EOF
<style type="text/css">
<!--
    h1 {color: #000033} 
       .main {
       position: relative;
       }
    
    .price {
        text-align: center;
        margin-top: 300px;
        
    }
    .barcode {
     text-align: center;
        margin-top: 50px;
    }
    .test {
        background-color: red;
    }
    .ab {
    
    overflow:    hidden; 
    background-attachment: fixed ;
    background-repeat: no-repeat;
    background-size: contain;
    background-position: center;
    text-align:  center;
    font-weight: normal;
             background-image: url(https://staging.waterstonesgiftcards.com/wp-content/uploads/2019/05/755x1105.png);
    }
-->
</style>
    <page class="main"  backimg="{$_POST['thumb']}">
    <div class="price">
    <h3 >{$prod_title[0]}</h3>
    <h3 >£{$_POST['price']}</h3>
</div>
<div class="barcode">
    <p><barcode  type="C128" value="{$_POST['code']}" label="label" style="width:100mm; height:8mm; color: #000; font-size: 2mm"></barcode><br/>
    </p>
</div>
</page>

EOF;
        $html2pdf->writeHTML($str);
        $html2pdf->output($prod_title[0].$_POST['price'].'_'.$_POST['order'].'.pdf', 'D');
    }
}
add_action( 'admin_post_nopriv_contact_form', 'prefix_send_email_to_admin' );
add_action( 'admin_post_contact_form', 'prefix_send_email_to_admin' );


/*
 * send order received email with new statuses
 */
add_action( 'woocommerce_order_status_to_scan', 'bbloomer_status_custom_notification', 20, 2 );
add_action( 'woocommerce_order_status_to_scan_special', 'bbloomer_status_custom_notification', 20, 2 );
add_action( 'woocommerce_order_status_avs_failed', 'bbloomer_status_custom_notification', 20, 2 );
add_action( 'woocommerce_order_status_failed_special', 'bbloomer_status_custom_notification', 20, 2 );

function bbloomer_status_custom_notification( $order_id, $order ) {

    $heading = 'Order Recieved';
    $subject = 'Order Received';

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

/*
 * replace order note
 */
function md_custom_woocommerce_checkout_fields( $fields )
{
    $fields['order']['order_comments']['placeholder'] = '';
    $fields['order']['order_comments']['label'] = 'Gift message';

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'md_custom_woocommerce_checkout_fields' );


/*
 * encrypt barcodes on status complete
 */

add_action( 'woocommerce_order_status_completed', 'your_function', 10, 1);
function your_function($order_id)
{

    $order = wc_get_order($order_id);

    foreach ($order->get_items() as $item_id => $item) {

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
}


/*
 * Send barcode image by email
 * the barcode function is in function.php
 */

add_action( 'woocommerce_email_after_order_table', 'send_img', 20, 4 );

function send_img($order, $sent_to_admin, $plain_text, $email){
    // if ( $email->id == 'customer_completed_order' ) {

    require_once(ABSPATH . '/wp-content/themes/ws/pdf/fpdf.php');
    require_once(ABSPATH . '/wp-content/themes/ws/pdf/vendor/autoload.php');



    foreach( $order->get_items() as $item_id => $item ){

        // Get the common data in an array:
        $item_product_data_array = $item->get_data();
        // $item_product_data_array['name'];

        // Get the special meta data in an array:
        $item_product_meta_data_array = $item->get_meta_data();

        $meta_value = $item->get_meta( 'barcode', true );
        if($item->get_meta( 'barcode', true ) && has_term('e-cards', 'product_cat',$item->get_product_id())){
            /*require_once(ABSPATH . '/wp-content/themes/ws/pdf/vendor/autoload.php');
            $barcode = new \Com\Tecnick\Barcode\Barcode();
            $bobj = $barcode->getBarcodeObj('C128C', "{$meta_value}", 450, 70, 'black', array(0, 0, 0, 0));
            $imageData = $bobj->getPngData();*/
            foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                if($meta->value !== 'Ready to scan') {
                    //$image1 = '<img alt="Embedded Image" src="data:image/png;base64,' . base64_encode($imageData) . '" width="200"/>';
                    $token = encrypt_decrypt('decrypt', $meta->value);
                    $url = get_template_directory_uri() . '/barcode.php?size=50&text=' . $token;
                    $thumb = get_the_post_thumbnail_url($item_product_data_array['id']);
                    $message = '<table style="width:100%;margin: 10px 0; border-style: dotted;">';
                    $message .= '<tr><td><img src="' . $thumb . '" width="200" /></td>';
                    $message .= '<tr><td>' .  $token . '</td>';
                    //TODO: change link to live website
                    $message .= '<tr><td><a href="https://staging.waterstonesgiftcards.com/my-account/view-order/'.trim(str_replace("#", "", $order->get_order_number())).'">To print off your E-gift card please click here</a></td>';
                    $message .= "<td><table><tr><td>";
                    $message .= $item_product_data_array['name'] . '</td></tr><tr><td>';
                    $message .= "<img src='" . $url . "'></td></tr></table>";
                    $message .= '</td></tr></table>';
                    echo $message;
                }
            }
        }

    }

    // }
}



/*
 * adding css and js to admin
 */
add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
    echo '<style>
   input[value="barcode"]{
    display: none;
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
   $('.edit-order-item.tips').on('click', function(){
$('.meta_items').each(function(){
    $(this).find('textarea').attr('value', '');
});
});
});
</script>
EOT;
}, PHP_INT_MAX );


?>