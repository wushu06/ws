<?php
require_once(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/Html2Pdf/vendor/autoload.php');
use \Spipu\Html2Pdf\Html2Pdf;
/*
 * Download pdf from customer account
 */
add_action( 'admin_post_nopriv_contact_form', 'prefix_send_email_to_admin' );
add_action( 'admin_post_contact_form', 'prefix_send_email_to_admin' );

function prefix_send_email_to_admin() {
    if(isset($_POST['order'])) {
        $order = wc_get_order($_POST['order']);
        tbb_generate_pdf_attchement(
            $order,
            $_POST['number'] ,
            $_POST['name'],
            $_POST['thumb'],
            $_POST['code'],
            $_POST['pin'],
            $_POST['price'],
            $_POST['order'],
            '',
            'D'
        );
    }
}

function tbb_generate_pdf_attchement($order, $msg, $name, $thumb, $barcode, $pin, $price, $order_id,$email, $output) {

    require_once(ABSPATH . 'wp-content/plugins/waterstones-gift-cards/Html2Pdf/vendor/autoload.php');

    $html2pdf = new Html2Pdf();
    $prod_title = explode('- £',$name);
    $code =    $barcode;

    if($output == 'D'){
        // for the download pdf from customer account
        $order = wc_get_order($order);

        $msgs = [];
        foreach ($order->get_items() as $item_id => $item) {
            if($item->get_name() == $name) {
                foreach ($item->get_formatted_meta_data() as $meta_id => $meta) {
                    if ($meta->key == 'giftee-msg') {
                        $msgs[] = $meta->value;
                    }
                }
            }

        }
        $msg = array_key_exists($msg, $msgs) ? $msgs[$msg] : '';
    }

    $str = <<<EOF
<style type="text/css">
<!--
    h1 {color: #000033} 
       .main {
       position: relative;
       }
    
    .price {
        text-align: center;
        margin-top: 180px;
        
    }
    .barcode {
        text-align: center;
        margin-top: 30px;
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
    <page class="main"  backimg="{$thumb}">
    <div class="price">
    <h3 >{$prod_title[0]}</h3>
    <h3 >£{$price}</h3>
    <h3 >Pin: {$pin}</h3>
</div>
<div class="barcode">
    <p><barcode  type="C128" value="{$code}" label="label" style="width:100mm; height:8mm; color: #000; font-size: 2mm"></barcode><br/>
    </p>
</div>
</page>

EOF;
    $html2pdf->writeHTML($str);
    if($output == 'F'){
        $path = ABSPATH.'wp-content/uploads/ws/pdfs/';
        $html2pdf->output( $path.'giftee-'.$order_id.$email.'.pdf', 'F');
        return $path.'giftee-'.$order_id.$email.'.pdf';
    }else{
        $html2pdf->output($prod_title[0].$price.'_'.$order_id.'.pdf', 'D');
    }


}



?>