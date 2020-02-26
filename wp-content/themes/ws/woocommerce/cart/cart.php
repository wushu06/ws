<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>
  	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;<?php esc_html_e( 'Remove', 'understrap' ); ?></th>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name"><?php esc_html_e( 'Product', 'understrap' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Price', 'understrap' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'understrap' ); ?></th>
                <?php
                $check = false;
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                    if( has_term('e-cards', 'product_cat',  $cart_item['product_id'])) {
                        $check = true;

                    }
                }

                if($check) {
                    echo '	<th class="product-giftee">Recipient</th>';
                }?>
				<th class="product-subtotal"><?php esc_html_e( 'Total', 'understrap' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {




				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ).'&variation='.$cart_item["variation_id"].'&qty='.$cart_item['quantity'].'#mainGifteeWrapper'
                        : '', $cart_item,
                        $cart_item_key );
					$product_permalink2 = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item,
                        $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-remove">
							<?php

								// @codingStandardsIgnoreLine
								echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
									'<a data-var="'.$cart_item["variation_id"].'" href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
									esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
									__( 'Remove this item', 'understrap' ),
									esc_attr( $product_id ),
									esc_attr( $_product->get_sku() )
								), $cart_item_key );
							?>
						</td>

						<td class="product-thumbnail">
						<?php

						$thumbnail_org = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                        $thumbnail_csutom = '<img src="'.get_the_post_thumbnail_url( $_product->get_id() ).'" />';
                        $thumbnail = get_the_post_thumbnail_url( $_product->get_id() ) !='' ? $thumbnail_csutom : $thumbnail_org;
						if ( ! $product_permalink ) {
							echo wp_kses_post( $thumbnail );
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink2 ), wp_kses_post( $thumbnail ) );
						}
						?>
						</td>

						<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'understrap' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'understrap' ) . '</p>' ) );
						}
						?>
						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'understrap' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'understrap' ); ?>">
                            
						<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
						} else {
							$product_quantity = woocommerce_quantity_input( array(
								'input_name'   => "cart[{$cart_item_key}][qty]",
								'input_value'  => $cart_item['quantity'],
								'max_value'    => $_product->get_max_purchase_quantity(),
								'min_value'    => '0',
								'product_name' => $_product->get_name(),
							), $_product, false );

						}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.



                        if(isset($_COOKIE['gift-wrapped']) && !empty($_COOKIE['gift-wrapped'])) {

                            $old =  json_decode( stripslashes(urldecode ($_COOKIE['gift-wrapped'])));
                            $old_ar = json_decode(json_encode($old),true);
                            foreach ($old_ar as $value){
                                 echo $value == $cart_item["variation_id"] ? '<div>to be wrapped </div>' : '';
                            }
                        }

                            if (isset($_COOKIE['giftee']) && !empty($_COOKIE['giftee']) && $check) {

                                echo '<td class="giftee-td" >';
                                $old_ar = json_decode(stripslashes(urldecode ($_COOKIE['giftee'])));
                                ?>
                                <script>
                                    jQuery(document).ready(function($){
                                        console.log('yes');
                                        function readCookie(name) {
                                            var nameEQ = name + "=";
                                            var ca = document.cookie.split(';');
                                            for(var i=0;i < ca.length;i++) {
                                                var c = ca[i];
                                                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                                                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
                                            }
                                            return null;
                                        }

                                        console.log(decodeURIComponent(readCookie('giftee')));
                                    })
                                </script>
                                <?php

                                if($old_ar):
                                foreach ($old_ar as $cookie_arr) {
                                    foreach ($cookie_arr as $key => $value) {

                                        if ($cart_item["variation_id"] == $key && !empty($value)) {

                                            echo '<div class="giftee-cart-wrapper"> 
                                                        <span><a href="'.esc_url( $product_permalink ).'">Edit</a></span><br/>
                                                       <!--   <div class="giftee-edit">
                                                       <span class="g-edit">Edit</span>
                                                       <span class="g-save" style="display: none">save</span>                                                
                                                       </div> --> ';
                                            $i = 0;
                                            foreach ($value as $gift) {
                                                $i++;
                                                echo '<span><strong>Card</strong>: #'.$i.'</span><br>';
                                                echo '<span  class="giftee-email"><strong>To</strong>: ' . sanitize_email($gift->email) . '</span><br>';
                                                echo '<span  class="giftee-msg"><strong>Message</strong>:<br> ' . sanitize_text_field($gift->msg) . '</span><br>';
                                                echo '<div class="giftee-inp-wrapper" style="display: none"><input data-id="' . $key . '" type="email" class="giftee-inp-email" value="' . sanitize_email($gift->email) . '"><br>';
                                                echo '<textarea data-id="' . $key . '" name="" id="" cols="30" rows="10" class="giftee-inp-msg">' . sanitize_text_field($gift->msg) . '</textarea></div>';
                                            }
                                            echo '</div>';

                                        }

                                    }

                                }
                                else:
                                    wp_mail('joe@thebiggerboat.co.uk, lee@thebiggerboat.co.uk', 'Failed egift-card message', $_COOKIE['giftee']);

                                    setcookie('giftee', null, -1, '/');
                                    echo '<p style="color: red;">
                                            Please note there is an error with your order.<br/>
                                            The recipient email address and gift message have not been saved.<br/>
                                            Please remove the item from your basket and re-order the 
                                            product/s. Thank you.
                                            </p>';
                                    endif;
                                echo '</td>';

                            }
                        
                        ?>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Total', 'understrap' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<tr>
				<td colspan="6" class="actions">

					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon">
							<label for="coupon_code"><?php esc_html_e( 'Coupon:', 'understrap' ); ?></label> <input type="text" name="coupon_code" class="input-text form-control" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'understrap' ); ?>" /> <button type="submit" class="basket-btn" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'understrap' ); ?>"><?php esc_attr_e( 'Apply coupon', 'understrap' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>

					<button type="submit" class="basket-btn"  name="update_cart" value="<?php esc_attr_e( 'Update cart', 'understrap' ); ?>"><?php esc_html_e( 'Update cart', 'understrap' ); ?></button>

					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</td>
			</tr>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<div class="cart-collaterals">
	<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );
	?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
