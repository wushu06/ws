<?php

namespace WS\Classes\Backend;

/**
 * Class PostType
 * @package WS\Backend
 */
class PostType
{
    /**
     * PostType constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'custom_post_type'), 0);

    }

    /**
     * @return \WP_Post_Type|\WP_Error The registered post type object, or an error object.
     */
    public function custom_post_type()
    {


        $labels = [
            "name" => __("E-Gifts", "storefront"),
            "singular_name" => __("E-Gift", "storefront"),
        ];

        $args = [
            "label" => __("E-Gifts", "storefront"),
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => false,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "delete_with_user" => false,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => ["slug" => "e-gift", "with_front" => true],
            "query_var" => true,
            "supports" => ["title", "editor", "thumbnail"],
        ];

        register_post_type("e-gift", $args);
    }
}