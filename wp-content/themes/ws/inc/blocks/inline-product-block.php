<?php
global $woocommerce;
$cart_total = floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_cart_total() ) );
$product_id = theme('product_id');
$_product = wc_get_product($product_id);


$group_id = 7891; // ID of parent product
$product = wc_get_product( $group_id );
$children = $product->get_children();


?>

<div class="block-inline-product">
    <div class="container">
        <h3>1. Choose your gift card design</h3>
        <div class="designs-slide">
        <?php
        $designs = get_the_terms( $product_id , 'pa_design');
        foreach ($children as $child){
            $id = $child;
            $name = get_the_title($child);
            $img_src = get_the_post_thumbnail_url($child);
        ?>
            <li class="" rel="<?php echo $id ; ?>">
                <label for="<?php echo $name; ?>">
                <input type="radio" class="radio-designs" name="design" data-id="<?php echo  $id ; ?>" value="<?php echo $name; ?>" id="<?php echo $name; ?>">
                <img src="<?php echo $img_src; ?>" style="max-width: 200px;" /></label>
            </li>
        <?php
            }
        ?>
        </div>

        <h3>2. Choose amount and quantity</h3>
        <select class="selectbox disable-select" name="price" id="price">
            <option value="" disabled selected>Select an amount</option>
        </select>
        <?php
        foreach ($children as $child){
          echo ' <select class="selectbox" name="price" id="price'.$child.'" style="display: none;"> ';
          echo '  <option value="" disabled selected>Select an amount</option>';
            $variations =  wc_get_product( $child );
            $var_children = $variations->get_available_variations();
            foreach ($var_children as $var_child) {
                $html_price = $var_child['price_html'];
                $price = $var_child['display_price'];
                $in_stock = $var_child['is_in_stock'];
                if($in_stock):
            ?>
            <option value="<?php echo $price; ?>"><?php echo $html_price; ?></option>

        <?php
                    endif;

            }
        echo '  </select>';
        } ?>

        <div class="spinner">
            <input type="number" id="quantity" class="input-text qty text" step="1" min="1" max="100" name="quantity" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric" aria-labelledby="" disabled>
            <img src="<?php echo get_template_directory_uri(); ?>/img/icons/left-arrow.png" alt="" class="spinner-helper up">
            <img src="<?php echo get_template_directory_uri(); ?>/img/icons/left-arrow.png" alt="" class="spinner-helper down">
        </div>
        <div class="price-buy">
            <p class="total">£10.00</p>
            <a href="<?php echo site_url() ?>" id="home-buy" class="" value="<?php echo $product_id; ?>">Add to Basket</a>
            <a  class="view-basket" href="<?php echo site_url() ?>/basket"  class="" >View Basket</a>

        </div>
       <!-- <select name="delivery-options" id="delivery-options">
            <option value="" selected>Your Delivery options...</option>
        <?php
/*            $rates = $woocommerce->shipping->get_packages()[0]['rates'];

            foreach($rates as $rate){
                //var_dump($rate);
                echo '<option>'.($rate->label).' £'.($rate->cost).'</option>';
            }
        */?>
        </select>-->
        <div class="free-delivery-check">
            <?php if($cart_total >= 20): ?>
                <p><b>You are eligible for free shipping.</b> Orders over £250 are eligible for Waterstones <a href="<?php echo site_url() ?>/corporate-and-education-gift-cards/">corporate gift card</a> account.</p>
            <?php else: ?>
                <p><b>Spend £<?php echo 20-$cart_total; ?> to qualify for free UK delivery.</b> Orders over £250 are eligible for Waterstones <a href="#">corporate gift card</a> account.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
