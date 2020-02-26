<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use Spipu\Html2Pdf\Html2Pdf;
if( isset($_POST['name1'])  ) {
    echo 'xxx';
    require('Html2Pdf/vendor/autoload.php');

    $html2pdf = new Html2Pdf();


    //$order = wc_get_order( 13620 );

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
    <page><h1>ID: #{$order->get_id()}</h1>
<h3>total: {$order->get_total()}</h3>
<h3>Billing</h3>
<p>cusomter id: {$order->get_customer_id()}</p>
<p>cusomter note: {$order->get_customer_note()}</p>
<h3>Shipping</h3>
<p>{$order->get_shipping_first_name()}</p>
<p>{$order->get_shipping_first_name() }  {$order->get_shipping_last_name()}</p>
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
                    $item->get_meta('barcode', true) &&
                    //  has_term('e-cards', 'product_cat',$item->get_product_id()) &&
                    $meta->value !== 'Ready to scan'
                ) {
                    $html2pdf->writeHTML('<p><barcode dimension="1D" type="EAN13" value="' . $meta->value . '" label="label" style="width:30mm; height:6mm; color: #000; font-size: 4mm"></barcode></p>');
                    $html2pdf->writeHTML('<p class="test">' . $meta->value . '</p>');
                }
            }
        }
    }

    $html2pdf->output('document_name.pdf', 'D');


$url = 'https://' . $_SERVER["HTTP_HOST"] . '/wp-content/uploads/pdfs/'.$_POST["product_id"].'.pdf';
echo $url;



}
?>
