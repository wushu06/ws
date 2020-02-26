<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}
?>
<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">

	<td class="woocommerce-table__product-name product-name">
		<?php


        $terms = get_the_terms(  $item->get_product_id(), 'product_cat' );


			$is_visible        = $product && $product->is_visible();
			$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

			echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible );
			echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item->get_quantity() ) . '</strong>', $item );

			/*do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );


			wc_display_item_meta( $item );

			do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );*/
             $item_product_data_array = $item->get_data();
                $pdf_bg = get_field('pdf_background', $item->get_product_id())['url'];
                $price = 0;
                $i = 0;
                $barcode ='';
            foreach ( $item->get_formatted_meta_data('_', true) as $meta_id => $meta ) {
     
                if(
                        $item->get_meta( 'barcode', true ) &&
                        has_term('e-cards', 'product_cat',$item->get_product_id()) &&
                        $meta->value !== 'Ready to scan' &&
                        $pdf_bg 
                ) {
                    if ($meta->key == 'pa_price') {
                        $price = $meta->value;
                    }
                     if ($meta->key == 'barcode' && $meta->value != 'Ready to scan') {
                        $barcode = $meta->value;


                    }

                    if ($meta->key == 'pin' && $meta->value != '3 digit pin') {
                        
                        ?>
                        <form
                            action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                            method="POST"
                        >
                            <input type="hidden" name="order" value="<?php echo $order->get_id() ?>">
                            <input type="hidden" name="thumb" value="<?php echo $pdf_bg ?>">
                            <input type="hidden" name="name" value="<?php echo $item->get_name() ?>">
                            <input type="hidden" name="price" value="<?php echo $price ?>">
                            <input type="hidden" name="pin" value="<?php echo   $meta->value ?>">
                            <input type="hidden" name="code" value="<?php echo   @encrypt_decrypt('decrypt',$barcode) ?>">
                            <input type="hidden" name="number" value="<?php echo   $i ?>">
                            <input type="hidden" name="action" value="contact_form">
                            <input type="submit" id="nomA" name="name1" value="Download gift card" class="button download_gift_card">


                    <?php    $i++; }
                        if($meta->key == 'giftee-email'){
                            echo '<div class="order-email"><strong>Recipient:</strong>  '.$meta->value.'</div>';

                        }
                        if($meta->key == 'giftee-msg'){
                            echo '<div class="order-msg"><strong>Message:</strong>  '.$meta->value.'</div>';

                        }
                    }

                echo '</form>';
            } ?>



	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php echo $order->get_formatted_line_subtotal( $item ); ?>
	</td>

</tr>

<?php if ( $show_purchase_note && $purchase_note ) : ?>

<tr class="woocommerce-table__product-purchase-note product-purchase-note">

	<td colspan="2"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>

</tr>
<?php


    ?>
<?php endif; ?>

