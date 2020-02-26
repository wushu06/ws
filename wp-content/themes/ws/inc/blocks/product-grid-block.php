<?php
$title = theme('title');
global $product;
?>

<div class="block-product-grid">
    <div class="container">
        <h3><?php echo $title; ?></h3>

        <?php if(have_rows('products')): ?>
                <div class="slider">
                    <?php while(have_rows('products')): the_row(); @$id = theme('product_id'); ?>
                        <div class="slide">
                            <?php echo do_shortcode('[product id="'.@$id.'" columns="1"]'); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
        <?php endif; ?>
    </div>
</div>