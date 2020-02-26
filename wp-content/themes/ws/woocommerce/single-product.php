<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


get_header( 'shop' );
$cart_total = floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_cart_total() ) );
if ( ! is_object( $product)) $product = wc_get_product( get_the_ID() );

?>

<div class="container single-product-wrapper">
	<div class="row">
		<div class="col-12">
			<div class="crumbtrail">
				<p>This gift can be found in:</p>
					<?php woocommerce_breadcrumb(array(
						'delimiter' => ' > '
					)); ?>
				<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );?>
			</div>
		</div>
	</div>
	<div class="product-main">
		<div class="row">
			<div class="col-lg-5 col-12">
				<img src="<?php  echo $image[0]; ?>" data-id="<?php echo $product->get_id(); ?>" class="featured-product">
			</div>
			<div class="col-lg-7 col-12">
				<h1 class="single_title"><?php echo $product->get_title(); ?></h1>
                <div style="margin-top: 20px; ">
                    <p><?php echo get_the_content(); ?></p>
                </div>

				<div class="summary-wrapper">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php wc_get_template_part( 'content', 'single-product' ); ?>
					<?php endwhile; // end of the loop. ?>
				</div>
       
                <?php if(!has_term('e-cards', 'product_cat',get_the_ID())): ?>
				<div class="free-delivery-check">


                  <p><b>Spend over £20 to get FREE delivery (excluding E-Gift purchases)</b></p>



				</div>
                    
                <?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php get_page_structure( 'page_structure', 'options' ); ?>

<?php get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
