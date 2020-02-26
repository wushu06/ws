<?php
/**
 * ws functions and definitions
 *
 * @package waterstones E gift cards
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$understrap_includes = array(
    '/acf/config.php',
    '/helpers/tbb.php',
    '/theme-settings.php',                  // Initialize theme default settings.
    '/setup.php',                           // Theme setup and custom theme supports.
    '/widgets.php',                         // Register widget area.
    '/enqueue.php',                         // Enqueue scripts and styles.
    '/template-tags.php',                   // Custom template tags for this theme.
    '/pagination.php',                      // Custom pagination for this theme.
    '/hooks.php',                           // Custom hooks.
    '/extras.php',                          // Custom functions that act independently of the theme templates.
    '/customizer.php',                      // Customizer additions.
    '/custom-comments.php',                 // Custom Comments file.
    '/jetpack.php',                         // Load Jetpack compatibility file.
    '/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker.
    '/woocommerce.php',                     // Load WooCommerce functions.
    '/editor.php',                          // Load Editor functions.
   // '/e-gift-card.php',                     // E-gift card functions.

);

foreach ( $understrap_includes as $file ) {
    $filepath = locate_template( '/inc' . $file );
    if ( ! $filepath ) {
        trigger_error( sprintf( 'Error locating /inc%s for inclusion', $file ), E_USER_ERROR );
    }
    require_once $filepath;
}

function register_my_menu() {
    register_nav_menu('footer',__( 'Footer' ));
}
add_action( 'init', 'register_my_menu' );

/* Show pagination on the top of shop page */
add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 10 );

/* Remove pagination on the bottom of shop page */
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

/* Remove Company Name from checkout in Woocommerce */
add_filter( 'woocommerce_checkout_fields' , 'alter_woocommerce_checkout_fields' );

function alter_woocommerce_checkout_fields( $fields ) {

    unset($fields['billing']['billing_company']);
    unset($fields['shipping']['shipping_company']);

    return $fields;
}

add_filter('next_posts_link_attributes', 'posts_link_attributes_1');
add_filter('previous_posts_link_attributes', 'posts_link_attributes_2');

function posts_link_attributes_1() {
    return 'class="next-link"';
}
function posts_link_attributes_2() {
    return 'class="prev-link"';
}

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

/**
 * This hook runs at the end of the default add to cart processes, when you try to add an item to cart.
 * If trying to add more than item to the cart, separate them into individual cart items
 *
 * Taken from here if you need to change anything:
 * https://stackoverflow.com/questions/32485152/woocommerce-treat-cart-items-separate-if-quantity-is-more-than-1
 * @author  Mike Hemberger <mike@bizbudding.com>
 *
 * @return  void
 */




add_theme_support( 'post-thumbnails' );
add_image_size( 'custom-size', 2010  , 1053, true );

//add_action('init', 'orderC');
function orderC()
{
    $order = new WC_Order(837);
    $order->update_status('pending'); // order note is optional, if you want to  add a note to order

    $orderID = 837; $custom_provider='test'; $number=837; $date=date('Y-m-d') ;

    $v = array();
    $v[] = array(
        "tracking_provider"=> ''  ,
        "custom_tracking_provider"=> $custom_provider  ,
        "tracking_number" => $number,
        "tracking_id" => 'function tracking id'

    );

    update_post_meta(  837, '_wc_shipment_tracking_items', $v );

}

add_filter( 'gravityflow_workflow_detail_display_field', 'sh_gravityflow_workflow_detail_display_field', 10, 5 );
function sh_gravityflow_workflow_detail_display_field( $display, $field, $form, $entry, $current_step ) {
    if ( $form['id'] == 1 ) {
        if ( in_array( $field->id, array( 42, 23, 3, 25, 22, 4, 26, 70, 60, 61, 72, 62, 63, 73, 64, 65, 24, 7, 28, 21, 29, 14, 30, 20, 13, 66, 67, 68, 69, 10, 19, 32, 58, 59, 71, 44, 46, 48, 75 ) ) ) {
            $display = false;
        } else {
            $display = true;
        }
    }
    return $display;
}

/*
** GravityForms - Disable Autocomplete
*/
add_filter( 'gform_form_tag', 'gform_form_tag_autocomplete', 11, 2 );
function gform_form_tag_autocomplete( $form_tag, $form )
{
    if ( is_admin() ) return $form_tag;
    if ( GFFormsModel::is_html5_enabled() )
    {
        $form_tag = str_replace( '>', ' autocomplete="off">', $form_tag );
    }
    return $form_tag;
}
add_filter( 'gform_field_content_#_#', 'gform_form_input_autocomplete', 11, 5 );
function gform_form_input_autocomplete( $input, $field, $value, $lead_id, $form_id )
{
    if ( is_admin() ) return $input;
    if ( GFFormsModel::is_html5_enabled() )
    {
        $input = preg_replace( '/<(input|textarea)/', '<${1} autocomplete="off" ', $input );
    }
    return $input;
}

function fix_request_query_args_for_woocommerce( $query_args ) {
    if ( isset( $query_args['post_status'] ) && empty( $query_args['post_status'] ) ) {
        unset( $query_args['post_status'] );
    }
    return $query_args;
}
add_filter( 'request', 'fix_request_query_args_for_woocommerce', 1, 1 );


add_filter( 'gform_field_validation_1_14', 'custom_validation', 10, 4 );
function custom_validation( $result, $value, $form, $field ) {
    //change value for price field to just be numeric (strips off currency symbol, etc.) using Gravity Forms to_number function
    //the second parameter to to_number is the currency code, ie "USD", if not specified USD is used
    $number = GFCommon::to_number( $value, '' );

    if ( $result['is_valid'] && intval( $number ) < 250 ) {
        $result['is_valid'] = false;
        $result['message'] = 'To qualify for a corporate order your order must be over £250.';
    }
    return $result;
}


function remove_item_from_cart($cart) {
    $cart = WC()->instance()->cart;
    $id = $_POST['product_id'];
    $cart_id = $cart->generate_cart_id($id);
    $cart_item_id = $cart->find_product_in_cart($cart_id);
    global $woocommerce;
    foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {

        if($cart_item['product_id'] == $_POST['product_id'] ){

            $cart->set_quantity( $cart_item_key, $_POST['quantity'] );
           return true;

        }
    }

    die();
}

add_action('wp_ajax_remove_item_from_cart', 'remove_item_from_cart');
add_action('wp_ajax_nopriv_remove_item_from_cart', 'remove_item_from_cart');


add_filter( 'woocommerce_add_to_cart_fragments', 'header_add_to_cart_fragment', 30, 1 );
function header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;

    ob_start();

    ?>
    <a class="cart-customlocation" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>"><?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>
    <?php
    $fragments['a.cart-customlocation'] = ob_get_clean();

    return $fragments;
}

function sgh_show_town_state( $locales ) {
    $locales['SG']['city']['hidden'] = false;
    return $locales;
}
add_filter( 'woocommerce_get_country_locale', 'sgh_show_town_state' );


// Snippet from https://stackoverflow.com/questions/41964737/change-default-sorting-order-in-woocommerce-dashboard-screen
// Sort products in wp_list_table by column in ascending or descending order. */
function custom_product_order( $query ){

    global $typenow;

    if( is_admin() && $query->is_main_query() && $typenow == 'shop_order' ){

        /* Post Column: e.g. DATE */
        if($query->get('orderby') == 'date'){
            $query->set('orderby', 'date');
        }
        /* Post Order: ASC / DESC */
        if($query->get('order') == ''){
            $query->set('order', 'ASC');
        }

    }
}
add_action( 'parse_query', 'custom_product_order' );


