<?php
require_once(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/Html2Pdf/vendor/autoload.php');
use \Spipu\Html2Pdf\Html2Pdf;

/*
 * print action handler
 */
add_action('wp_ajax_invoiceprint', 'download_custom_invoice_pdf');
add_action('wp_ajax_nopriv_invoiceprint', 'download_custom_invoice_pdf');


/*
 * admin download invoice action
 */

add_action( 'wp_ajax_customer_second_payment_reminder', 'get_customer_second_payment_reminder' );
function get_customer_second_payment_reminder() {
    if ( current_user_can('edit_shop_orders') && check_admin_referer('customer-second-payment-reminder') &&
        isset($_GET['order_id']) && get_post_type( absint( wp_unslash($_GET['order_id']) ) ) === 'shop_order' ) {
        $order_id = absint( wp_unslash($_GET['order_id']) );
        $order    = wc_get_order($order_id);

        if( is_a($order, 'WC_Order') ) {
            download_custom_invoice_pdf($order, true);
        }
    }
    // wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=shop_order' ) );
    //  exit;
}



/*
 * Admin download pdf - from backend
 */
function download_custom_invoice_pdf($order, $all = false){


    $sep = '<div class="t-separator"></div>';
    if (isset($_POST['post_id'])) {
        $order = wc_get_order((int)$_POST['post_id']);
        $sep = '';

    }

    $html2pdf = new Html2Pdf();
    $logo = get_template_directory_uri().'/img/waterstones-logo.png';

    //$order = wc_get_order( 13620 );
    $type = get_post_meta($order->get_id(), 'order_type', true);

    $header = '<p style="font-size:14px;line-height:18px;">Waterstones Customer Accounts, Prince\'s Gate, <br>Homer Road,<br> Solihul<br> B91 3QQ <br>Helpline Number: 01159071899<br>
                    Email: waterstoneshelpline@encompassprint.co.uk</p>';
    if($type == 'corporate'){
        $header = '<p style="font-size:14px;line-height:18px;">Waterstones Customer Accounts, Prince\'s Gate, <br>Homer Road,<br> Solihul B91 3QQ <br>
                    Email: waterstoneshelpline@encompassprint.co.uk</p>';
    }
    $item_quantity = 0;
    $electronic = true;
    foreach ($order->get_items() as $item) {
        //TODO: change the envelope id with the live id
        $envelope = !empty(get_option("ws_settings")) && array_key_exists("envelope",get_option("ws_settings")) ? get_option("ws_settings")["envelope"] : '' ;
        if ($item->get_product_id() != $envelope  && !has_term('e-cards', 'product_cat',$item->get_product_id()) ) {
            $item_quantity = $item_quantity + $item->get_quantity();
        }
        if (!has_term('e-cards', 'product_cat',$item->get_product_id()) ) {
            $electronic = false;
        }
    }
    if ($item_quantity >= 7) {
        $letter = '<p style="font-size:20px;line-height:24px;" class="test">Large letter</p>';
    } else{
        //@TODO: make it electornice
        if( $electronic ) {
            $letter = '<p style="font-size:20px;line-height:24px;">Electronic letter</p>';
        }else{
            $letter = '<p style="font-size:20px;line-height:24px;">Normal letter</p>';
        }

    }
    $note = '';
    $latest_notes = wc_get_order_notes( array(
        'order_id' => $order->get_id(),
        'limit'    => 1,
        'orderby'  => 'date_created_gmt',
    ) );

    $latest_note = current( $latest_notes );

    if ( isset( $latest_note->content ) ) {
        //$html2pdf->writeHTML('<p style="border: dashed 1mm #000000"> gift messaging: ' .$latest_note->content . '</p>');
    }
    if($order->get_customer_note() && $order->get_customer_note() !== ''){
        $note = $order->get_customer_note();

    }

    global $wpdb;

    $postage_name = '';
    $results = $wpdb->get_results( "SELECT order_item_name FROM  wp_woocommerce_order_items WHERE order_id = {$order->get_id()} AND order_item_type = 'shipping'", OBJECT );

        foreach ($results as $res){

          $postage_name = $res->order_item_name;

        }



    $str = <<<EOF
<style type="text/css">
<!--
    body {
      
    }

    table, .container, .header-h4 {
      width: 90%;
      margin: 20px auto 0;
      position: relative;
      color:#000000;
    }
    .table_header {
    margin-bottom: 20px;
   
    }
    .label-print {
     padding-top: 40px;
    }
    table td {
      width: 50%;   
    }
    .customer_details h4 {
      margin: 2px 0;
    }
     .table_info {
     position: relative;
     }
     .table_info p{
      margin: 2px 0;
     }
    .table_info h4 {
       margin: 2px 0;
    }
    .letter {
      background: #fafafa;
      text-align: center;
      padding: 5px 5px;
      margin-top: 20px;
    }

    .table_order {
       border-right: 1px solid #666;
       border-bottom: 1px solid #666;
    }

    .table_order  th, .table_order td {
      padding: 8px;
      text-align: left;
      border-top: 1px solid #666;
    }
    .table_order  th,  .table_order td{
       border-left: 1px solid #6666;
    }
    .table_order_product  th, .table_order_product td {
      border: none;
      padding: 0;
    }
    
    .table_order td {
      width: 80px;
    }
    .table_order p {
      margin-bottom: 5px;
    }
    .t-separator {
        display: block;
        clear: both;
        width: 100%;
        height: 150px;
    }
    .sub-parag {
        font-size: 14px;
        line-height: 18px;
        color: #333;
    }
    .table_order_product {
      margin: 0;
    }
-->
</style>
 <div>

     <table class="table_header"  cellpadding="0" cellspacing="0">
     <tr >
      <td>
        <div class="header">
        <div><img  alt="logo" src="{$logo}"  width="200"/></div>
        <div><p>{$header}</p></div>
        </div>
      </td>
      
      
      
      <td>
        <div class="customer_details" style="border:0px solid #ff0000;padding-left:50px;">
          <h4>{$order->get_shipping_first_name()} {$order->get_shipping_last_name()} </h4>
          <h4>{$order->get_shipping_company()}</h4>
          <h4>{$order->get_shipping_address_1()}</h4>
          <h4>{$order->get_shipping_address_2()}</h4>
          <h4>{$order->get_shipping_city()}</h4>
          <h4>{$order->get_shipping_postcode()}</h4>
          <h4>{$order->get_shipping_country()}</h4>
       
        </div>
      </td>
     </tr>
      
    </table> <!-- table header -->
    
   
    
    <table class="table_info" cellpadding="0" cellspacing="0"> 
    <tr>       
              <td class="header ">     
                    <h4 >Ref: {$order->get_billing_first_name()} {$order->get_billing_last_name()}</h4>                
                    <h4>Order number: #{$order->get_id()}</h4>   
                    <p>Delivery address:</p>
                    <p> {$order->get_billing_company()}</p>
                    <p>{$order->get_shipping_address_1()}</p>
                    <p> {$order->get_shipping_address_2()}</p>
                    <p> {$order->get_shipping_city()}</p>
                    <p> {$order->get_shipping_postcode()}</p>
                    <p> {$order->get_shipping_country()}</p>
                 
              </td> 
              <td class="customer_details label-print" style="padding-left:80px;">
                             
                  <div class="letter" style="border:1px solid #666666;">            
                    <h2>{$letter}</h2>
                  </div>  
              </td>        
      </tr>   
    </table><!-- table info -->
   
     <table class="table_instr" cellpadding="0" cellspacing="0">
        <tr>
              <td style="min-width: 300px; width: 100%">
                  <div>            
                         <p style="font-size:12px;"><strong>Instructions:</strong> Below are the details of the cards you have received. Each line represents the individual cards ordered. Within each line you will find the card design, value 
                           and card number. These details can be used to identify the physical cards enclosed within. If you have ordered an eGift, you will have already received those details on a separate email.</p>                  
                  </div>
             </td>
         </tr>
     </table>

</div>


EOF;
    if (isset($_POST['post_id'])) {
        echo $str;
    }else{
        $html2pdf->writeHTML( $str);
    }

    if ( isset($_POST['all']) && $_POST['all'] =='yes' || $all) :
    //$table = '<h4 class="header-h4">Pick list</h4>';
    $table = '<table class="table_order" cellpadding="0" cellspacing="0">
              <thead>
                <tr>
                  <th style="font-size:14px;">Product</th>
                  <th style="font-size:14px;">Price</th>
                  <th style="font-size:14px;">Qty</th>
                  <th style="font-size:14px;">Total</th>
                </tr>
              </thead>
            <tbody>';

    if($order->get_items()) {
        foreach ($order->get_items() as $item ) {
            $table .= '<tr>'; //start
            $table .='<td style="width: 350px; "><table class="table_order_product" cellpadding="0" cellspacing="0">';
            $url = get_the_post_thumbnail_url($item->get_product_id());
            if ($url) {
               // Client asked for image to be removed
               // $table .='<td style="width: 110px; " ><img  alt="Embedded Image" src="' . $url . '"  width="100"/> </td>';
            }
            $table .= '<td style="margin-bottom: 10px; width: 200px;"><p style="font-size: 14px;line-height: 18px;"><strong>' . $item->get_name() . '</strong></p>';
            foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                if (
                    $meta->key == 'barcode' && $meta->value != 'Ready to scan'
                ) {
                    /*$token = encrypt_decrypt('decrypt', $meta->value);
                    $url = get_template_directory_uri() . '/barcode.php?size=50&text=' .$token ;
                    $table .='<div style="margin-bottom: 10px;"><img src="' . $url . '"></div>';
                    $table .='<div>'.$token.'</div>';*/
                    $table .='<div ><barcode   type="C128" value="' . encrypt_decrypt('decrypt', $meta->value) . '" label="none" style="width:30mm; height:6mm; color: #000; font-size:
                     3mm;"></barcode></div>';
                    $table .='<div class="sub-parag">Barcode No. '. encrypt_decrypt('decrypt', $meta->value).'</div>';

                }
                if($meta->key == 'pin' && $meta->value !== '3 digit pin') {
                    $table .='<div style="margin-bottom: 10px;" class="sub-parag">Pin: '.$meta->value.'</div>';
                }
            }
            //$table .= '<div class="sub-parag">sku: '.wc_get_product( $item->get_product_id() )->get_sku().'</div>';
            $table .= '</td>';
            $table .= '</table></td>'; // first column
            $table .='<td style="font-size:14px;">£'. $order->get_item_total( $item ).'</td>'; // price
            $table .='<td style="font-size:14px;">'. $item->get_quantity().'</td>'; // qty
            $table .='<td style="font-size:14px;">£'. $item->get_total().'</td>'; // total
            $table .= '</tr>';

        }
    }
    $table .= '<tr>'; // delivery start
    $table .= '<td style="font-size:14px;">Delivery: ' . $postage_name . '</td><td></td>';
    $table .= '<td></td>';
    $table .= '<td style="font-size:14px;">£'. $order->get_total_shipping().'</td>';
    $table .='</tr>'; // delivery total


    $table .= '<tr>'; // total start
    $table .= '<td></td><td></td>';
    $table .= '<td style="font-size:14px;">'.$order->get_item_count().'</td>';
    $table .= '<td style="font-size:14px;">£'.$order->get_total().'</td>';
    $table .='</tr>'; // end total

    $table .='</tbody></table>'; // end
        else:

    // physical cards only
   // $table .= '<h4 class="header-h4">Physical cards</h4>';
    $table = '<table class="table_order" cellpadding="0" cellspacing="0">
              <thead>
                <tr>
                  <th style="font-size:14px;">Product </th>
                  <th style="font-size:14px;">Price</th>
                  <th style="font-size:14px;">Qty</th>
                  <th style="font-size:14px;">Total</th>
                </tr>
              </thead>
            <tbody>';
    if($order->get_items()) {
        foreach ($order->get_items() as $item ) {
            if (!has_term('e-cards', 'product_cat',$item->get_product_id()) ) {
                $table .= '<tr>'; //start
                $table .='<td style="width: 350px; "><table class="table_order_product" cellpadding="0" cellspacing="0">';
                $url = get_the_post_thumbnail_url($item->get_product_id());
                if ($url) {
                    // Client asked for image to be removed
                    // $table .='<td style="width: 110px; " ><img  alt="Embedded Image" src="' . $url . '"  width="100"/> </td>';
                }
                $table .= '<td style="margin-bottom: 10px; width: 200px;"><p style="font-size: 14px;line-height: 18px;"><strong>' . $item->get_name() . '</strong></p>';
                foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                    if (
                        $meta->key == 'barcode' && $meta->value != 'Ready to scan'
                    ) {
                        /*$token = encrypt_decrypt('decrypt', $meta->value);
                        $url = get_template_directory_uri() . '/barcode.php?size=50&text=' .$token ;
                        $table .='<div style="margin-bottom: 10px;"><img src="' . $url . '"></div>';
                        $table .='<div>'.$token.'</div>';*/
                        $table .='<div ><barcode   type="C128" value="' . encrypt_decrypt('decrypt', $meta->value) . '" label="none" style="width:30mm; height:6mm; color: #000; font-size:
                     3mm;"></barcode></div>';
                        $table .='<div class="sub-parag">Barcode No. '. encrypt_decrypt('decrypt', $meta->value).'</div>';

                    }
                    if($meta->key == 'pin' && $meta->value !== '3 digit pin') {
                        $table .='<div style="margin-bottom: 10px;" class="sub-parag">Pin: '.$meta->value.'</div>';
                    }
                }
                //$table .= '<div class="sub-parag">sku: '.wc_get_product( $item->get_product_id() )->get_sku().'</div>';
                $table .= '</td>';
                $table .= '</table></td>'; // first column
                $table .='<td style="font-size:14px;">£'. $order->get_item_total( $item ).'</td>'; // price
                $table .='<td style="font-size:14px;">'. $item->get_quantity().'</td>'; // qty
                $table .='<td style="font-size:14px;">£'. $item->get_total().'</td>'; // total
                $table .= '</tr>';
            }

        }
    }
            $table .= '<tr>'; // delivery start
            $table .= '<td style="font-size:14px;">Delivery: ' . $postage_name . '</td><td></td>';
            $table .= '<td></td>';
            $table .= '<td style="font-size:14px;">£'. $order->get_total_shipping().'</td>';
            $table .='</tr>'; // delivery total


            $table .= '<tr>'; // total start
            $table .= '<td></td><td></td>';
            $table .= '<td style="font-size:14px;">'.$order->get_item_count().'</td>';
            $table .= '<td style="font-size:14px;">£'.$order->get_total().'</td>';
            $table .='</tr>'; // end total

            $table .='</tbody></table>'; // end
    $table .='</tbody></table>'; // end

    endif;

    $table .='
    <table class="table_instr" >
          <td style="width: 100%">
              <div>            
                     <p style="font-size:18px;"><strong>Gift Message: </strong>' . $note . '</p>                  
              </div>
         </td>
     </table>';



    if (isset($_POST['post_id'])) {
        echo $table;
    }else{
        $html2pdf->writeHTML($table);
        $html2pdf->output('order-'.$order->get_id().'.pdf', 'D');
    }
    die();
}


/*
 * batch download
 */


// Adding to admin order list bulk dropdown a custom action 'custom_downloads'
//add_filter( 'bulk_actions-edit-shop_order', 'downloads_bulk_actions_edit_product', 20, 1 );
function downloads_bulk_actions_edit_product( $actions ) {
    $actions['write_downloads'] = __( 'Download custom invoice', 'woocommerce' );
    return $actions;
}

// Make the action from selected orders
add_filter( 'handle_bulk_actions-edit-shop_order', 'downloads_handle_bulk_action_edit_shop_order', 10, 3 );
function downloads_handle_bulk_action_edit_shop_order( $redirect_to, $action, $post_ids ) {
    if ( $action !== 'write_downloads' )
        return $redirect_to; // Exit

    global $attach_download_dir, $attach_download_file; // ???

    $processed_ids = array();
    $log  = "Error: ". print_r($post_ids, true).'-'.date("F j, Y, g:i a").PHP_EOL.
        "-------------------------".PHP_EOL;
    //Save string to log, use FILE_APPEND to append.
    file_put_contents(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/log_'.date("j.n.Y").'.log', $log, FILE_APPEND);

    foreach ( $post_ids as $post_id ) {
        $order = wc_get_order( $post_id );
        $order_data = $order->get_data();
        download_custom_invoice_pdf($order);
    }

}

add_action( 'admin_menu', 'yoursite_admin_orders_url' );
function yoursite_admin_orders_url() {

    global $submenu; // Get submenu array

    // Verify the target menu url and append the processing query
    if ( isset( $submenu["woocommerce"][1][2] ) && "edit.php?post_type=shop_order" == $submenu["woocommerce"][1][2] ) {
        $submenu["woocommerce"][1][2] .= "&post_status=wc-to_scan";
    }
}