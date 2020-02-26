<?php
namespace WS\Classes\Backend;

use WS\Classes\AbstractSettings;

/**
 * Class Extend
 * @package WS\Backend
 */
class Extend extends AbstractSettings
{

    /**
     * Extend constructor.
     */
    public function __construct()
    {
        add_action( 'woocommerce_admin_order_items_after_line_items', array($this, 'emailSelectBoxHtml'), 10, 1 );
        add_action('wp_ajax_emailAjaxRequest', array($this,'emailAjaxRequestHandler'));
        add_action('wp_ajax_nopriv_emailAjaxRequest', array($this,'emailAjaxRequestHandler'));

    }

    /**
     * @param $order_id
     */
    function emailSelectBoxHtml($order_id){
        $order = wc_get_order($order_id);
        ?>
        <div style="padding: 10px;">
            <label for="">Re-Send email</label>
            <select name="" id="egiftEmails" data-url="<?= admin_url('admin-ajax.php') ?>">
                <option value="">Select email</option>
                <?php
                foreach ($order->get_items() as $item_id => $item) {

                    $meta_value = $item->get_meta('barcode', true);
                    if ($item->get_meta('barcode', true)) {
                        foreach ($item->get_formatted_meta_data() as $meta_id => $meta) {
                            if ($meta->value !== '') {?>
                                <option value="<?= $meta->value ?>"><?= $meta->value ?></option>
                            <?php }
                        }
                    }

                }
                ?>

            </select>
            <button id="send">Send</button>
            <span id="loader"></span>
            <input type="hidden" value="<?= $order_id ?>">
        </div>
        <?php
    }

    /**
     *
     */
    function emailAjaxRequestHandler()
    {
        $email =  $_POST['email'];
        $orderId =  $_POST['orderId'];
        //$this->logger($email);
        $order = wc_get_order((int)$orderId);
        if(!$order->get_id()){
            $this->logger('Failed to send email to '.$email.' no order id!');
            return 'No order id';
        }
        $printData = [];
        if($order->get_items()) {
            foreach ($order->get_items() as $item) {
                foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                    if ($meta->key == 'email' && $meta->value == $email) {
                        $item_product_data_array =   $item->get_data();
                        $printData = array(
                                'name'  => $item->get_data()['name'],
                                'email' => $email,
                                'image' => get_field('pdf_background', $item->get_product_id())['url'],
                                'price' => wc_get_product( $item_product_data_array["variation_id"] )->get_price()
                        );
                    }
                }
            }
        }

    }

}