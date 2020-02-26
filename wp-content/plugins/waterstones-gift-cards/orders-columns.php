<?php

/**
 * Adds 'Profit' column header to 'Orders' page immediately after 'Total' column.
 *
 * @param string[] $columns
 * @return string[] $new_columns
 */
add_filter( 'manage_edit-shop_order_columns', 'bbloomer_add_new_order_admin_list_column' );

function bbloomer_add_new_order_admin_list_column( $columns ) {
    $columns['special_delivery'] = 'Special delivery';
    return $columns;
}

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
add_action( 'pre_get_posts', 'manage_wp_posts_be_qe_pre_get_posts', 1 );
function manage_wp_posts_be_qe_pre_get_posts( $query ) {

    /**
     * We only want our code to run in the main WP query
     * AND if an orderby query variable is designated.
     */
    if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {

        switch( $orderby ) {

            // If we're ordering by 'program_id'
            case 'special_delivery':

                // set our query's meta_key, which is used for custom fields
                $query->set( 'meta_key', 'special_delivery' );

                /**
                 * Tell the query to order by our custom field/meta_key's
                 * value
                 *
                 * If your meta value are numbers, change 'meta_value'
                 * to 'meta_value_num'.
                 */
                $query->set( 'orderby', 'meta_value' );

                break;

        }

    }

}

/*
 * add e cards column
 */

add_filter( 'manage_edit-shop_order_columns', 'tbb_ecards_column' );

function tbb_ecards_column( $columns ) {
    $columns['e_cards'] = 'Cards type';
    return $columns;
}

add_action( 'manage_shop_order_posts_custom_column', 'tbb_ecards_column_content' );

function tbb_ecards_column_content( $column ) {

    global $post;
    $check = true;
    $mixed = false;
    if ( 'e_cards' === $column ) {

        $order = wc_get_order($post->ID);


        foreach ($order->get_items() as $item_id => $item) {

            if (!has_term('e-cards', 'product_cat', $item->get_product_id())) {
                $check = false;
            }
            if (has_term('e-cards', 'product_cat', $item->get_product_id())) {
                $mixed = true;
            }

        }
        if($check){
            echo '<span style="color: red;">E-cards</span>' ;

        }else{
            if($mixed){
                echo '<span>Mixed</span>' ;
            }else{
                echo '<span>No eCards</span>' ;
            }

        }
    }
}