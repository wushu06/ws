<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
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

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class(); ?>>
    <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'full' );?>
    <a href="<?php echo get_permalink($product->get_id()); ?>"><img src="<?php echo $image[0]; ?>" alt=""></a>
    <h4><?php echo $product->get_title(); ?></h4>
    <a href="<?php echo get_permalink($product->get_id()); ?>" class="buy-now">Buy Now</a>
</li>
