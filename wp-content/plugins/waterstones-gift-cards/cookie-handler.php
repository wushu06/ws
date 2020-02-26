<?php

add_action('woocommerce_add_to_cart', 'custome_add_to_cart');
function custome_add_to_cart() {
    $host = parse_url(get_option('siteurl'), PHP_URL_HOST);

    /*if(isset($_POST['giftee-email']) && !empty($_POST['giftee-email'])){

        $giftee_array =  array('email'=>$_POST['giftee-email'], 'message'=> $_POST['giftee-msg'] ? $_POST['giftee-msg'] : '' ) ;
        if(!isset($_COOKIE['gift'])) {
            $info[$_POST['variation_id']] = $giftee_array ;
            setcookie('gift', json_encode($info), strtotime('+1 month'), '/', $host);
        }
        if(isset($_COOKIE['gift'])){
            $old =  json_decode( stripslashes($_COOKIE['gift']));
            $old_ar = json_decode(json_encode($old),true);
            if (!array_key_exists($_POST['variation_id'], $old_ar)) {
                $old_ar[$_POST['variation_id']] = $giftee_array;
                setcookie('gift', json_encode($old_ar), strtotime('+1 month'), '/', $host);
            }


        }

    }*/
   /* if(isset($_POST['box-wrapped']) && $_POST['box-wrapped'] == '1'){
        if(!isset($_COOKIE['gift-wrapped'])) {
            $info[] = $_POST['variation_id'];
            setcookie('gift-wrapped', json_encode($info), strtotime('+1 month'), '/', $host);
        }
        if(isset($_COOKIE['gift-wrapped'])){
            $old =  json_decode( stripslashes($_COOKIE['gift-wrapped']));
            $old_ar = json_decode(json_encode($old),true);
            if (!in_array($_POST['variation_id'], $old_ar)) {
                $old_ar[] = $_POST['variation_id'];
                setcookie('gift-wrapped', json_encode($old_ar), strtotime('+1 month'), '/', $host);
            }


        }

    }*/
}


/*add_action( 'woocommerce_process_shop_order_meta', 'woocommerce_process_shop_order', 10, 2 );
function woocommerce_process_shop_order (  $post_id, $post) {
    // my code here
    $path = parse_url(get_option('siteurl'), PHP_URL_PATH);
    $host = parse_url(get_option('siteurl'), PHP_URL_HOST);
    $info = array('id'=> 13650, 'email'=> 'nour@thebiggerboat.co.uk');
   // setcookie( 'giftee', json_encode($info), strtotime('+1 month'),  $path, $host);
    if(!isset($_COOKIE['giftee'])) {
        $order = wc_get_order($post_id);

        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $_COOKIE['giftee']['id'];
            if ($item->get_product_id() == $product_id) {
                wc_add_order_item_meta($item_id, 'giftee_info',  json_decode($_COOKIE['giftee'], true));
            }
        }
    }

}*/

//add_action('init', 'cookie_set');
function cookie_set(){
 $host = parse_url(get_option('siteurl'), PHP_URL_HOST);


setcookie('gift-wrapped', '', -1, '/', $host);
unset($_COOKIE['gift-wrapped']);

setcookie('gift', '', -1, '/', $host);
unset($_COOKIE['gift']);

}
// define the woocommerce_cart_item_removed callback
function action_woocommerce_cart_item_removed( $cart_item_key, $instance ) {
    $host = parse_url(get_option('siteurl'), PHP_URL_HOST);
    $v = [];
    foreach ($instance->cart_contents as $key=>$content) {
        array_push($v, $content['variation_id']);
    }

    if(isset($_COOKIE['gift'])){
        $old =  json_decode( stripslashes($_COOKIE['gift']));
        $old_ar = json_decode(json_encode($old),true);
        foreach ($old_ar as $key=>$value){
            if (!in_array($key, $v)) {
                unset($old_ar[$key]);
            }
        }
        setcookie('gift', json_encode($old_ar), strtotime('+1 month'), '/', $host);


      //  wp_mail('nour@thebiggerboat.co.uk', 'cart', print_r($old_ar, true));
    }


    if(isset($_COOKIE['gift-wrapped'])){
        $gold =  json_decode( stripslashes($_COOKIE['gift-wrapped']));
        $gold_ar = json_decode(json_encode($gold),true);
        foreach ($gold_ar as $key=>$value){
            if (!in_array($value, $v)) {
                unset($gold_ar[$key]);
            }
        }

        setcookie('gift-wrapped', json_encode($gold_ar), strtotime('+1 month'), '/', $host);
    }


};

// add the action
add_action( 'woocommerce_cart_item_removed', 'action_woocommerce_cart_item_removed', 10, 2 );

/*add_action('init', 'cookie_set');
function cookie_set(){
    $path = parse_url(get_option('siteurl'), PHP_URL_PATH);
    $host = parse_url(get_option('siteurl'), PHP_URL_HOST);

    if(!isset($_COOKIE['giftee']) || empty($_COOKIE['giftee'])) {
        $info[123456] = 'nour@thebiggerboat.co.uk' ;
        setcookie('giftee', json_encode($info), strtotime('+1 month'), $path, $host);
    }
    $old =  json_decode( stripslashes($_COOKIE['giftee']));
    $old_ar = json_decode(json_encode($old),true);
    echo $old_ar[123456] ;
    echo '<pre>';
    var_dump( $old_ar);
    echo '</pre>';
    die();


}*/
