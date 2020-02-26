
<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $woocommerce;
$cart_total = floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_cart_total() ) );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?> - <?php bloginfo( 'description' ); ?>">
    <meta name=”format-detection” content=”telephone=no”>
    <meta name=”format-detection” content=”date=no”>
    <meta name=”format-detection” content=”address=no”>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link href="https://fonts.googleapis.com/css?family=Libre+Baskerville:400,400i,700|Source+Sans+Pro:400,600,600i,700i" rel="stylesheet">
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <?php wp_head(); ?>
</head>



<body <?php body_class('eupopup eupopup-top'); ?>>
	<header class="main">
		<div class="container">
			<div class="row top-bar hide-for-desktop">
				<div class="col-12 col-md-4">
					<a href="https://www.waterstones.com/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/img/pin_black.png" width="10px"> Back to waterstones.com</a>
				</div>
				<div class="col-12 col-md-4 ml-auto right">
                    <a href="<?php echo site_url(); ?>/account/"><img src="<?php echo get_template_directory_uri(); ?>/img/icons/account.png" width="13px"><?php echo !is_user_logged_in() ? 'Sign in/Register' : 'My account' ?> </a>
				</div>
			</div>
			<div class="row">

				<div class="col-12">
                    <a class="title-a" href="<?php echo site_url() ?>"> <h1>Waterstones Gift Cards</h1></a>
				</div>

			</div>
		</div>

	</header>
	<nav class="top-nav">
		<div class="container">
			<div class="row">
				<div class="col-8 col-md-10 col-lg-10" >
					<?php wp_nav_menu(
					array(
						'theme_location'  => 'primary',
						'container_class' => 'desk-menu',
						'container_id'    => '',
						'menu_class'      => '',
						'fallback_cb'     => '',
						'menu_id'         => 'main-menu',
						'depth'           => 2
						)
					); ?>
                    <div id="nav-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
				</div>

                <div class="col-2 col-lg-2 sign-m ml-auto right hide-for-mobile login-icon" >
                    <a class="login-img" href="<?php echo site_url(); ?>/account/"><img src="<?php echo get_template_directory_uri(); ?>/img/icons/account.png"></a>
                </div>
				<div class="col-2 col-md-2 col-lg-2 basket d-md-block ">
					<a href="#" class="toggleNav">
                        <?php if(WC()->cart->get_cart_contents_count() == 0 ) {
                            $display_one ='block';
                            $display_two ='none';
                        }else{
                            $display_one ='none ';
                            $display_two ='block';
                        }   ?>
                           <img class="empty-basket" style="margin: auto; display: <?php echo $display_one; ?>" src="<?php echo get_template_directory_uri(); ?>/img/icons/basket-w.png" width="20">

                            <img class="full-basket" style="margin: auto; display: <?php echo $display_two ?>" src="<?php echo get_template_directory_uri(); ?>/img/icons/empty-basket.png" width="20">
                            <span class="basket-qt" style=" display: <?php echo $display_two ?>"><?php echo WC()->cart->get_cart_contents_count()  ; ?></span>


                    </a>
					<a class="sign-text toggleNav" href="#" style="color:#000; font-weight: 100">Basket</a>
					<div class="cart-dropdown">
						<div class="free-delivery-check">
							<?php  if($cart_total >= 20): ?>
                                <span style="width: 45px;float: left;"><img src="<?php echo get_template_directory_uri(); ?>/img/Delivery_icon_truck.png" alt="" width="35"></span>
								<p><b>Your order qualifies for free UK delivery.</b></p>
							<?php else: ?>
								<p><b>Spend £<?php echo 20-$cart_total; ?> to qualify for free UK delivery.</b>.</p>
							<?php endif; ?>
						</div>
						<?php woocommerce_mini_cart(); ?>
                        
					</div>
				</div>
			</div>
		</div>
	</nav>
    <?php if(is_front_page()): ?>
    <!--<div class="ribbon-banner plus-terms-ribbon">
        <a href="<?php /*echo site_url(); */?>/shop">
            <img src="https://cdn.waterstones.com/images/00116812-273x417.gif" alt="">
            <img src="https://cdn.waterstones.com/images/00116813-2268x417.gif" alt="Your new stamp reward card is here! Waterstones plus TELL ME MORE">
            <img src="https://cdn.waterstones.com/images/00116814-273x417.gif" alt=""></a>
    </div>-->
<?php endif; ?>

    <style>
        .mm-menu.mm-offcanvas {
            position: absolute;
        }
        .mm-menu.mm-offcanvas {
            z-index: 999;
            top: 115px;
            background: #fff;
            transform: translateY(12px);
        }
        .mm-listview>li:not(.mm-divider):after {
            left: 0;
            background-color: #ccc;
        }
        .mm-menu .mm-listview>li a:not(.mm-next) {
            font-size: 16px;
            padding: 20px;
        }
        .mm-panel.mm-hasnavbar .mm-navbar {
            display: none;
        }
        .mm-panels>.mm-panel.mm-hasnavbar {
            padding-top: 0;
        }
    </style>
    <nav id="menu">
        <?php wp_nav_menu(
            array(
                'theme_location'  => 'primary',
                'container_class' => '',
                'container_id'    => '',
                'menu_class'      => '',
                'fallback_cb'     => '',
                'menu_id'         => 'main-menu',
                'depth'           => 2
            )
        ); ?>
    </nav>

    <script>
        jQuery(document).ready(function( $ ) {
            $("#menu").mmenu({

            });
            $('#nav-icon').click(function(){
                $('#menu').toggle(500,"swing");
                $(this).toggleClass('open');
                $('.mm-menu ').toggleClass('mm-opened');
                $('body').toggleClass('remove-scroll');
            });


        });
    </script>
