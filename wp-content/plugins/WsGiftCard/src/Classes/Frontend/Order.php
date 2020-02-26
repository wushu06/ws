<?php
namespace WS\Classes\Frontend;

use WS\Classes\AbstractSettings;

/**
 * Class Order
 * @package WS\Frontend
 */
class Order extends AbstractSettings
{
    /**
     * Order constructor.
     */
    public function __construct()
    {

       add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'assignPostToOrderItem'), 10, 4 );

    }

    /**
     * @param $item
     * @param $cart_item_key
     * @param $values
     * @param $order
     */
    function assignPostToOrderItem($item, $cart_item_key, $values, $order ) {

        $eGifts = $this->getEgiftPosts($item->get_quantity());
        for ($i=1; $i <= $item->get_quantity(); $i++){
            if( array_key_exists($i - 1, $eGifts)) {
                $item->add_meta_data('barcode', $eGifts[$i - 1]['post_title'], false);
                $this->setEgiftStatus($eGifts[$i - 1]['ID']);
            }else{
                $this->logger('There are not enough barcode for order number '.$order->get_order_number());
            }

        }

    }

    /**
     * @param $limit
     * @param bool $decode
     * @return array|mixed|object|null
     */
    public function getEgiftPosts($limit, $decode = true)
    {
        global $wpdb;
        $result = $wpdb->get_results(
            "SELECT ID, post_title
                    FROM $wpdb->posts
                    WHERE post_type = 'e-gift'
                    AND post_status = 'publish'
                    ORDER BY ID
                    DESC LIMIT {$limit}"
        );

        if($decode){
            return json_decode(json_encode($result), true);
        }
        return $result;

    }

    /**
     * @param $post_id
     * @param string $status
     */
    public function setEgiftStatus($post_id, $status = 'draft')
    {
        $post = array( 'ID' => $post_id, 'post_status' => $status );
        wp_update_post($post);
    }
}