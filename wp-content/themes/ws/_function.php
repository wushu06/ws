<?php
/**
 * Understrap functions and definitions
 *
 * @package understrap
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


/*
 * extra field for woo orders
 */

/**
 * Adds 'Profit' column header to 'Orders' page immediately after 'Total' column.
 *
 * @param string[] $columns
 * @return string[] $new_columns
 */


function bcw_cpt_columns($columns) {
    $columns["special_delivery"] = "special_delivery";
    return $columns;
}
add_filter('manage_edit-shop_order_columns', 'bcw_cpt_columns');
add_filter('manage_edit-shop_order_sortable_columns', 'bcw_cpt_columns');




//add_filter('manage_edit-shop_order_sortable_columns', 'bcw_cpt_columns');


/*function bcw_sort_metabox($vars) {
    if(array_key_exists('orderby', $vars)) {
        if('special_delivery' == $vars['orderby']) {
            $vars['post_type'] = 'shop_order';
            $vars['orderby'] = 'title';

        }
    }
    return $vars;
}
add_filter('request', 'bcw_sort_metabox');*/







add_action( 'manage_shop_order_posts_custom_column', 'bbloomer_add_new_order_admin_list_column_content' );
function bbloomer_add_new_order_admin_list_column_content( $column ) {

    global $post;

    if ( 'special_delivery' === $column ) {

        $order = wc_get_order( $post->ID );
        $items = $order->get_items();

        global  $wpdb;

        $results = $wpdb->get_results( "SELECT order_item_name FROM  wp_woocommerce_order_items WHERE order_id = {$post->ID}", OBJECT );
        foreach ($results as $res){

            echo $res->order_item_name == 'Postage, Packing and Special Delivery (RMND)' ? '<span style="color: red;">Special delivery</span>' : '';

        }

    }
}

/*
add_filter( "manage_edit-shop_order_sortable_columns", 'MY_COLUMNS_SORT_FUNCTION' );
function MY_COLUMNS_SORT_FUNCTION( $columns )
{
    if(array_key_exists('orderby', $vars)) {
        if('special_delivery' == $vars['orderby']) {
            $vars['orderby'] = 'meta_value_num';
            $vars['meta_key'] = 'cost';
        }
    }
    return $vars;
}
*/
/*
add_action('pre_get_posts', 'custom_zipcode_orderby');
function custom_zipcode_orderby( $query ) {
    if ( !is_admin() ){ return; }

    $orderby = $query->get( 'orderby');
    if ('special_delivery' == $orderby){
        $query->set('meta_key','cost');
        $query->set('orderby','meta_value_num');
    }
}
*/






/*
 * Send barcode image by email
 */

add_action( 'woocommerce_email_after_order_table', 'send_img', 20, 4 );

function send_img($order, $sent_to_admin, $plain_text, $email){
    if ( $email->id == 'customer_completed_order' ) {

        require_once(ABSPATH . '/wp-content/themes/ws/pdf/fpdf.php');
        require_once(ABSPATH . '/wp-content/themes/ws/pdf/vendor/autoload.php');

        foreach( $order->get_items() as $item_id => $item ){

            // Get the common data in an array:
            $item_product_data_array = $item->get_data();
            // $item_product_data_array['name'];

            // Get the special meta data in an array:
            $item_product_meta_data_array = $item->get_meta_data();

            $meta_value = $item->get_meta( 'code', true );
            if( $meta_value ){
                require_once(ABSPATH . '/wp-content/themes/ws/pdf/vendor/autoload.php');
                $barcode = new \Com\Tecnick\Barcode\Barcode();
                $barcode = new \Com\Tecnick\Barcode\Barcode();
                $bobj = $barcode->getBarcodeObj('C128C', "{$meta_value}", 450, 70, 'black', array(0, 0, 0, 0));
                $imageData = $bobj->getPngData();
                $image1 = '<img alt="Embedded Image" src="data:image/png;base64,'.base64_encode($imageData).'" width="200"/>';

                $url = get_template_directory_uri() . '/barcode.php?size=50&text='.$meta_value;
                $thumb = get_the_post_thumbnail_url($item_product_data_array['id']);
                $message  = '<table style="width:100%; border-style: dotted;">';
                //  $message .= '<tr><td><img src="'.$thumb.'" width="200" /></td>';
                $message .= '<tr><td>'.$image1.'</td>';
                $message .= "<td><table><tr><td>";
                $message .= $item_product_data_array['name'].'</td></tr><tr><td>';
                $message .= "<img src='" . $url . "'></td></tr></table>";
                $message .= '</td></tr></table>';
                echo $message;
            }

        }

    }
}



add_filter( 'wc_order_is_editable', 'wc_make_processing_orders_editable', 10, 2 );
function wc_make_processing_orders_editable( $is_editable, $order ) {
    if (
        $order->get_status() == 'scanned'
        || $order->get_status() == 'processing'
        || $order->get_status() == 'avs-failed'
        || $order->get_status() == 'avs-passed'
    ) {
        $is_editable = true;
    }

    return $is_editable;
}

/*
 * add envolope to order & seprate orders
 */


function aaptc_add_product_to_cart( $item_key, $product_id ,$quantity, $variation_id, $variation, $cart_item_data) {

    WC()->cart->add_to_cart( 13593, 2 );

    $product_cats_ids 	= wc_get_product_term_ids( $product_id, 'product_cat' );
    if ( ! is_admin()  ) {
        // 1- add envelope
        $free_product_id = 13593;  // Product Id of the free product which will get added to cart
        $found 		= false;


        //check if product already in cart
        if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
            foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                $_product = $values['data'];
                if ( $_product->get_id() == $free_product_id )
                    $found = true;

            }
            // if product not found, add it
            if ( ! $found ){
                for ( $i = 1; $i <= $quantity; $i++ ) {
                    WC()->cart->add_to_cart( $free_product_id );
                }

            }

        } else {
            // if no products in cart, add it
            //   WC()->cart->add_to_cart( $free_product_id );
        }

        // 2- Seprate order
        /* if ( $quantity > 1 ) {

             // Keep the product but set its quantity to 1
             WC()->cart->set_quantity( $item_key, 1 );

             // Run a loop 1 less than the total quantity
             for ( $i = 1; $i <= $quantity -1; $i++ ) {
                 /**
                  * Set a unique key.
                  * This is what actually forces the product into its own cart line item
                  */
        $cart_item_data['unique_key'] = md5( microtime() . rand() . "Hi Mom!" );

        // Add the product as a new line item with the same variations that were passed
        /*  WC()->cart->add_to_cart( $product_id, 1, $variation_id, $variation, $cart_item_data );
      }

  }*/
    }
}
//add_action( 'woocommerce_add_to_cart', 'aaptc_add_product_to_cart', 10, 2 );

/*
 * add envlope to cart
 * 1- add envelope if not found
 * 2- update qty if found
 */
// -1

//add_action( 'woocommerce_add_to_cart', 'mai_split_multiple_quantity_products_to_separate_cart_items', 20, 6 );
function mai_split_multiple_quantity_products_to_separate_cart_items( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
    foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
        $_product = $values['data'];
        if ( $_product->get_id() == 13593 ){
            $found = true;
        }
    }
    if ( ! $found ){
        WC()->cart->add_to_cart( 13593 );
    }
    /*  if ( $quantity > 1 ) {

          // Keep the product but set its quantity to 1
          WC()->cart->set_quantity( $cart_item_key, 1 );

          // Run a loop 1 less than the total quantity
          for ( $i = 1; $i <= $quantity -1; $i++ ) {

              $cart_item_data['unique_key'] = uniqid();

              // Add the product as a new line item with the same variations that were passed
              WC()->cart->add_to_cart( $product_id, 1, $variation_id, $variation, $cart_item_data );
          }

      }*/

}
// -2
//add_action('woocommerce_before_calculate_totals', 'change_cart_item_quantities', 20, 1 );
function change_cart_item_quantities ( $cart ) {
    /*if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;*/

    /*if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        return;*/

    // HERE below define your specific products IDs
    $specific_ids = array(13593);
    $new_qty = 1; // New quantity
    $t =  WC()->cart->get_cart_contents_count() ;
    // Checking cart items
    foreach( $cart->get_cart() as $cart_item_key => $cart_item ) {
        $product_id = $cart_item['data']->get_id();
        // Check for specific product IDs and change quantity
        if( in_array( $product_id, $specific_ids )  ){
            $cart->set_quantity( $cart_item_key, $t - $cart_item['quantity'] ); // Change quantity
        }
    }
}

/*
 * split cart items to separate lines
 */

function bbloomer_split_product_individual_cart_items( $cart_item_data, $product_id ){

    $unique_cart_item_key = uniqid();
    $cart_item_data['unique_key'] = $unique_cart_item_key;



    return $cart_item_data;
}

//add_filter( 'woocommerce_add_cart_item_data', 'bbloomer_split_product_individual_cart_items' , 10, 4 );
