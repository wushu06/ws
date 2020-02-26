<?php
/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

?>

<?php
$g_option = get_option("ws_settings");
$enable = !empty($g_option) && array_key_exists("enable",$g_option)  ? $g_option["enable"] : '';
if($enable) :
    if( has_term('e-cards', 'product_cat',$product->get_id())) { ?>

        <div class="instr-global" style="line-height:16px;font-size:14px!important; margin: 20px 0px;">
            <?=get_field('egift_product_notice','option')?>
        </div>

        <div class="giftee" >
            <div class="giftee-wrapper" id="mainGifteeWrapper">
                <h4>eGift #<span class="giftee-numb">1</span></h4>
                <p>In order to send the ecard directly to your chosen recipient, please provide their email address with a gift message if required.</p><br/>
                <label for="">To</label><br/>
                <input type="email" name="giftee-email2" class="giftee-email" value="" placeholder="Recipient email" />
                <label for="">Message</label><br/>
                <textarea name="giftee-msg2" class="giftee-msg"  id="" cols="30" rows="10"  placeholder="Recipient message"></textarea>

            </div>
            <div class="repeater-wrapper">
            </div>

            <input type="hidden" name="giftee-email" class="giftee-email-array" value=""/>
            <input type="hidden" name="giftee-msg" class="giftee-msg-array" value="" placeholder="Recipient message" />
        </div>

        <div class="giftee-instr">
            <p style="line-height:16px;font-size:14px!important;">
                <br/><strong>Please see details below for how an eGift Card works</strong>
                <br/><br/>

eGift cards work just like regular cards however, an eGift card is emailed rather than posted, to either yourself or to the recipient of your choosing.<br/><br/>
To send an eGift card to a recipient please choose the value and quantity you wish to send, complete the details in the box above and then click ‘Add to Basket’.<br/><br/>
If you do not wish to send the eGift to a recipient, simply choose a value and quantity, then click ‘Add to Basket’.<br/><br/>
If you choose to send an eGift to a recipient, you will receive a confirmation email advising the eGift has been sent.<br/><br/>
Once you have clicked ‘Add to Basket’, you will have the option to ‘View Basket’.

            </p>
        </div>


        <?php

    } else { ?>

        <div class="instr-global" style="line-height:16px;font-size:14px!important; margin: 20px 0px;">
            <?=get_field('delivery_note','option')?>
        </div>
      
    <?php }
endif;
?>

<div class="woocommerce-variation-add-to-cart variations_button" style="margin-top: 30px;">
    <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
    <p>Quantity</p>
    <?php
    do_action( 'woocommerce_before_add_to_cart_quantity' );

    woocommerce_quantity_input( array(
        'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
        'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
        'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
    ) );

    do_action( 'woocommerce_after_add_to_cart_quantity' );
    ?>

    <?php if(isset($_GET['variation']) && isset($_GET['attribute_pa_price']) && !empty($_GET['variation'])) : ?>
        <button data-url="<?= get_site_url() ?>/basket/" type="submit" class="single_update_cart_button btn btn-primary" >Update basket</button>
        <?php else: ?>
        <button type="submit" class="single_add_to_cart_button btn btn-primary"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

    <?php endif; ?>




<!--    <a href="#" class="single_add_to_cart_button btn btn-primary" data-title="<?php /*echo $product->get_title() */?>"><?php /*echo esc_html( $product->single_add_to_cart_text() ); */?></a>
-->
    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>


    <!--<label for="box-wrapped"> Wrappe it</label>
    <input type="checkbox" name="box-wrapped" class="" value="1" />-->
    <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
    <input type="hidden" id="singleID" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
    <input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>






<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js"></script>

<script>
    jQuery(document).ready(function($){


    return;
        var $pid = $('#singleID').val(),
            $qty = $('.qty').val(),
            $design,
            $price = 0;

        $('#pa_price').on('change', function () {

        });

        $('#pa_price, .qty').change(function(){

            $qty = $('.qty').val();
            $price = $('#pa_price').val();

            $('.single_add_to_cart_button.btn').attr('href', "/basket/?add-to-cart="+$pid+"&quantity="+$qty+"&attribute_pa_price="+$price);
        });

        $('.single_add_to_cart_button.btn').on('click', function (e) {
            e.preventDefault();
            gifteeEmail();
            return
            var check = true;
            var title = $(this).attr('data-title');
            if($price === '' || $price === undefined || $price === null) {
                e.preventDefault();
                $('.note').append('<div>Please select a price</div>');
                tweenCall();
                check = false;
            }


            if(check) {
                let path = window.location.origin + $('.single_add_to_cart_button.btn').attr('href');
                $('.single_add_to_cart_button.btn').addClass('disable-btn');
                $('.note').append('<div class="three col">\n' +
                    '                <div class="cutom-loader" id="loader-2">\n' +
                    '                    <span></span>\n' +
                    '                    <span></span>\n' +
                    '                    <span></span>\n' +
                    '                </div>\n' +
                    '            </div>');
                TweenMax.to('.note', 1, {
                    opacity: 1,
                    yPercent: -50,
                    ease: Power4.easeInOut
                });


                var xmlhttp = new XMLHttpRequest();
                xmlhttp.addEventListener("error", XHRErrorHandler);
                xmlhttp.onprogress = function () {
                    console.log('LOADING', xmlhttp.status);

                };

                xmlhttp.onreadystatechange=function() {
                    if (xmlhttp.readyState==3) {
                        TweenMax.to('.note', 0.3, {
                            opacity: 0,
                            yPercent: 0,
                            ease: Power4.easeInOut
                        });
                    }
                    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                        var response = xmlhttp.responseText; //if you need to do something with the returned value

                        $('.note').empty().append('<div>'+title+' Has been added to you basket</div>');
                        $('.single_add_to_cart_button.btn').removeClass('disable-btn').text('VIEW BASKET').attr('href', '/basket');
                        // $('.view-basket').show();
                        window.location.replace("http://staging.waterstonesgiftcards.com/basket/");
                        tweenCall();
                    }

                }

                function XHRErrorHandler(event) {
                    console.log("Error");
                }

                xmlhttp.open("GET",path,true);
                xmlhttp.send();
            }




        });



        function tweenCall() {
            TweenMax.to('.note', 1, {
                opacity: 1,
                yPercent: -50,
                ease: Power4.easeInOut
            });
            setTimeout(function(){
                TweenMax.to('.note', 1, {
                    opacity: 0,
                    yPercent: 0,
                    ease: Power4.easeInOut
                });

            }, 2000);
            setTimeout(function(){$('.note').empty();}, 2500);
        }





    });
</script>