<?php

function tbb_ajax_print_invoice()
{


    if (isset($_POST['post_id'])){
        $order = wc_get_order((int)$_POST['post_id']);

        $logo = get_template_directory_uri().'/img/waterstones-logo.png';

        //$order = wc_get_order( 13620 ;
        $type = get_post_meta($order->get_id(), 'order_type', true);

        $header = '<p>Waterstones Customer Accounts, Price\'s Gate, <br>Homer Road, Solihul B91 3QQ <br>Helpline Number: 01159071899<br>
                    Email: waterstoneshelpline@encompassprint.co.uk</p>';
        if($type == 'corporate'){
            $header = '<p>Waterstones Customer Accounts, Price\'s Gate, <br>Homer Road, Solihul B91 3QQ <br>
                    Email: waterstoneshelpline@encompassprint.co.uk</p>';
        }
        $item_quantity = 0;
        $electronic = true;
        foreach ($order->get_items() as $item) {
            //TODO: change the envelope id with the live id

            if ($item->get_product_id() != 13593 && !has_term('e-cards', 'product_cat',$item->get_product_id()) ) {
                $item_quantity = $item_quantity + $item->get_quantity();
            }
            if (!has_term('e-cards', 'product_cat',$item->get_product_id()) ) {
                $electronic = false;
            }
        }
        if ($item_quantity >= 7) {
            $letter = '<p class="test">Large letter</p>';
        } else{
            //@TODO: make it electornice
            if( $electronic ) {
                $letter = '<p>Electronic letter</p>';
            }else{
                $letter = '<p>Normal letter</p>';
            }

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
    <section>
    <div><img  alt="logo" src="{$logo}"  width="200"/></div>
    <div>{$header}</div>
    <h3>ID: #{$order->get_id()} type: {$type} </h3>
    <div>{$letter} </div>
<h3>total: {$order->get_total()}</h3>
<div style="border: dashed 1mm #000000; padding: 10mm; margin: 5mm;">
<h3>Billing</h3>
<p>cusomter id: {$order->get_customer_id()}</p>
<p>gift messaging: {$order->get_customer_note()}</p>

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
</section>
<section>
<h1>Order items</h1>
</section>
EOF;
        echo $str;

        if($order->get_items()) {
            foreach ($order->get_items() as $item) {
                echo'<p> ' . $item->get_name() . '</p>';
                $url = get_the_post_thumbnail_url($item->get_product_id());
                if ($url) {
                    echo'<p> <img  alt="Embedded Image" src="' . $url . '"  width="200"/> </p>';
                }
                foreach ($item->get_formatted_meta_data('_', true) as $meta_id => $meta) {
                    if (
                        $meta->key == 'barcode' && $meta->value != 'Ready to scan'
                    ) {
                        $token = encrypt_decrypt('decrypt', $meta->value);

                        echo '<div style="border: dashed 1mm #000000; padding: 10mm;"><barcode  type="C128" value="' . $token . '" label="label" style="width:30mm; height:6mm; color: #000; font-size: 4mm"></barcode></div>';

                    }
                }
            }
        }
        $latest_notes = wc_get_order_notes( array(
            'order_id' => $order->get_id(),
            'limit'    => 1,
            'orderby'  => 'date_created_gmt',
        )) ;

        $latest_note = current( $latest_notes ) ;


        if($order->get_customer_note() && $order->get_customer_note() !== ''){
            echo '<p style="border: dashed 1mm #000000"> gift messaging: ' .$order->get_customer_note() . '</p>';

        }

    }
}


add_action('wp_ajax_invoiceprint', 'tbb_ajax_print_invoice');
add_action('wp_ajax_nopriv_invoiceprint', 'tbb_ajax_print_invoice');







