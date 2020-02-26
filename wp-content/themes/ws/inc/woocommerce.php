<?php
/**
 * Add WooCommerce support
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

add_action( 'after_setup_theme', 'understrap_woocommerce_support' );
if ( ! function_exists( 'understrap_woocommerce_support' ) ) {
    /**
     * Declares WooCommerce theme support.
     */
    function understrap_woocommerce_support() {
        add_theme_support( 'woocommerce' );

        // Add New Woocommerce 3.0.0 Product Gallery support
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-slider' );

        // hook in and customizer form fields.
        add_filter( 'woocommerce_form_field_args', 'understrap_wc_form_field_args', 10, 3 );
    }
}

/**
 * First unhook the WooCommerce wrappers
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

/**
 * Then hook in your own functions to display the wrappers your theme requires
 */
add_action('woocommerce_before_main_content', 'understrap_woocommerce_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'understrap_woocommerce_wrapper_end', 10);
if ( ! function_exists( 'understrap_woocommerce_wrapper_start' ) ) {
    function understrap_woocommerce_wrapper_start() {
        $container   = get_theme_mod( 'understrap_container_type' );
        echo '<div class="wrapper" id="woocommerce-wrapper">';
        echo '<div class="container" id="content" tabindex="-1">';
        echo '<div class="row">';
        echo '<main class="site-main" id="main">';
    }
}
if ( ! function_exists( 'understrap_woocommerce_wrapper_end' ) ) {
    function understrap_woocommerce_wrapper_end() {
        echo '</main><!-- #main -->';
        echo '</div><!-- .row -->';
        echo '</div><!-- Container end -->';
        echo '</div><!-- Wrapper end -->';
    }
}


/**
 * Filter hook function monkey patching form classes
 * Author: Adriano Monecchi http://stackoverflow.com/a/36724593/307826
 *
 * @param string $args Form attributes.
 * @param string $key Not in use.
 * @param null   $value Not in use.
 *
 * @return mixed
 */
if ( ! function_exists ( 'understrap_wc_form_field_args' ) ) {
    function understrap_wc_form_field_args( $args, $key, $value = null ) {
        // Start field type switch case.
        switch ( $args['type'] ) {
            /* Targets all select input type elements, except the country and state select input types */
            case 'select' :
                // Add a class to the field's html element wrapper - woocommerce
                // input types (fields) are often wrapped within a <p></p> tag.
                $args['class'][] = 'form-group';
                // Add a class to the form input itself.
                $args['input_class']       = array( 'form-control', 'input-lg' );
                $args['label_class']       = array( 'control-label' );
                $args['custom_attributes'] = array(
                    'data-plugin'      => 'select2',
                    'data-allow-clear' => 'true',
                    'aria-hidden'      => 'true',
                    // Add custom data attributes to the form input itself.
                );
                break;
            // By default WooCommerce will populate a select with the country names - $args
            // defined for this specific input type targets only the country select element.
            case 'country' :
                $args['class'][]     = 'form-group single-country';
                $args['label_class'] = array( 'control-label' );
                break;
            // By default WooCommerce will populate a select with state names - $args defined
            // for this specific input type targets only the country select element.
            case 'state' :
                // Add class to the field's html element wrapper.
                $args['class'][] = 'form-group';
                // add class to the form input itself.
                $args['input_class']       = array( '', 'input-lg' );
                $args['label_class']       = array( 'control-label' );
                $args['custom_attributes'] = array(
                    'data-plugin'      => 'select2',
                    'data-allow-clear' => 'true',
                    'aria-hidden'      => 'true',
                );
                break;
            case 'password' :
            case 'text' :
            case 'email' :
            case 'tel' :
            case 'number' :
                $args['class'][]     = 'form-group';
                $args['input_class'] = array( 'form-control', 'input-lg' );
                $args['label_class'] = array( 'control-label' );
                break;
            case 'textarea' :
                $args['input_class'] = array( 'form-control', 'input-lg' );
                $args['label_class'] = array( 'control-label' );
                break;
            case 'checkbox' :
                $args['label_class'] = array( 'custom-control custom-checkbox' );
                $args['input_class'] = array( 'custom-control-input', 'input-lg' );
                break;
            case 'radio' :
                $args['label_class'] = array( 'custom-control custom-radio' );
                $args['input_class'] = array( 'custom-control-input', 'input-lg' );
                break;
            default :
                $args['class'][]     = 'form-group';
                $args['input_class'] = array( 'form-control', 'input-lg' );
                $args['label_class'] = array( 'control-label' );
                break;
        } // end switch ($args).
        return $args;
    }
}

add_filter('gettext', 'translate_reply');
add_filter('ngettext', 'translate_reply');

function translate_reply($translated) {
    $translated = str_ireplace('Shipping', 'Delivery', $translated);
    return $translated;
}

add_filter( 'woocommerce_shipping_package_name' , 'woocommerce_replace_text_shipping_to_delivery', 10, 3);

function woocommerce_replace_text_shipping_to_delivery($package_name, $i, $package){
    return sprintf( _nx( 'Delivery', 'Delivery %d', ( $i + 1 ), 'shipping packages', 'put-here-you-domain-i18n' ), ( $i + 1 ) );
}
/*
add_action( 'wp_footer', 'add_js_to_wp_wcommerce');

function add_js_to_wp_wcommerce(){ ?>
    <script type="text/javascript">
        jQuery('.remove-product').click(function(e){
            e.preventDefault();
            var product_id = jQuery(this).attr("data-product_id");
            var quantity =  jQuery(this).attr("data-q");
            var price =  jQuery(this).attr("data-p");
            var newQ = jQuery('.basket-qt').text() - quantity;
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: jQuery(this).attr('data-ajax'),
                data: { action: "product_remove",
                    product_id: product_id
                },success: function(data){
                    console.log(newQ);
                   jQuery('#product'+product_id).hide();

                }
            });
            return false;
        });
    </script>
<?php }

add_action( 'wp_ajax_product_remove', 'product_remove' );
add_action( 'wp_ajax_nopriv_product_remove', 'product_remove' );
function product_remove() {
    global $woocommerce;
    $cart = $woocommerce->cart;
    foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item){
        if($cart_item['product_id'] == $_POST['product_id'] ){
            // Remove product in the cart using  cart_item_key.
            $cart->remove_cart_item($cart_item_key);
        }
    }
}*/

//add_action( 'woocommerce_order_status_processing', 'tbb_change_posted_date' );
//add_action( 'woocommerce_order_status_cancelled', 'tbb_change_posted_date' );
//add_action( 'woocommerce_order_status_on-hold', 'tbb_change_posted_date' );
//add_action( 'woocommerce_order_status_failed', 'tbb_change_posted_date' );

function tbb_change_posted_date( $order_id ) {
    //wp_mail( 'nour@thebiggerboat.co.uk', 'order', 'changed', array('Content-Type: text/html; charset=UTF-8') );
    $order = wc_get_order( $order_id );

    $args = array(
        'post_id' => $order_id,
        //wp_insert_post (called by wp_update_post) will set the date to "now" if `post_date` is empty, likewise with `post_date_gmt`
        'post_date' => '',
        'post_date_gmt' => '',
    );
    wp_update_post( $args );
}

// Display the payment gateway transwaction ID on email notifications
add_action('woocommerce_email_order_details', 'before_email_order_details_transaction_id', 5, 4 );
function before_email_order_details_transaction_id( $order, $sent_to_admin, $plain_text, $email ) {

/*    if( $order->get_transaction_id() && $sent_to_admin ) {
        echo '<br><strong>' . __('Order notes') . ':</strong> ' . $order->get_customer_note();
        $order_notes = get_private_order_notes( $order->get_id() );
        foreach($order_notes as $note){
            $note_id = $note['note_id'];
            $note_date = $note['note_date'];
            $note_author = $note['note_author'];
            $note_content = $note['note_content'];
            // Outputting each note content for the order
            echo '<p>'.$note_content.'</p>';
        }
    }*/
    if( 'new_order' == $email->id ){
        // WC3+ compatibility
        $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
        echo '<br><p><strong>Customer IP address:</strong> '. get_post_meta( $order_id, '_customer_ip_address', true ).'</p>';
    }
}

function get_private_order_notes( $order_id){
    global $wpdb;

    $table_perfixed = $wpdb->prefix . 'comments';
    $results = $wpdb->get_results("
        SELECT *
        FROM $table_perfixed
        WHERE  `comment_post_ID` = $order_id
        AND  `comment_type` LIKE  'order_note'
    ");

    foreach($results as $note){
        $order_note[]  = array(
            'note_id'      => $note->comment_ID,
            'note_date'    => $note->comment_date,
            'note_author'  => $note->comment_author,
            'note_content' => $note->comment_content,
        );
    }
    return $order_note;
}


