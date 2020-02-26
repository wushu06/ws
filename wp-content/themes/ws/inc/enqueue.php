<?php
/**
 * Understrap enqueue scripts
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'understrap_scripts' ) ) {
	/**
	 * Load theme's JavaScript and CSS sources.
	 */
	function understrap_scripts() {
		// Get the theme data.
		$the_theme = wp_get_theme();
		$theme_version = $the_theme->get( 'Version' );

		$css_version = $theme_version . '.' . filemtime(get_template_directory() . '/css/theme.min.css');
		wp_enqueue_style( 'understrap-styles', get_stylesheet_directory_uri() . '/css/theme.min.css', array(), $css_version );
		wp_enqueue_style( 'slick-style', get_template_directory_uri() . '/js/plugins/slick-carousel/slick/slick-theme.css');
		wp_enqueue_style( 'select-style', get_template_directory_uri() . '/css/select2.min.css');
		wp_enqueue_style( 'eu-cookie.css', get_template_directory_uri() . '/css/eu-cookie.css');
		wp_enqueue_style( 'style.css', get_template_directory_uri() . '/style.css');

        wp_enqueue_style( 'mmenu-styles', get_stylesheet_directory_uri() . '/js/plugins/jquery.mmenu/h/jquery.mmenu.all.css', array(), $the_theme->get( 'Version' ) );
        wp_enqueue_script( 'jquery-mmenu', get_template_directory_uri() . '/js/plugins/jquery.mmenu/h/jquery.mmenu.all.js', array(), $the_theme->get( 'Version' ), true );

		wp_enqueue_script( 'jquery');
		wp_enqueue_script( 'app', get_template_directory_uri() . '/js/app.js',  array(), '1.1.4');


		wp_enqueue_script( 'slider', get_template_directory_uri() . '/js/slider.js');
		wp_enqueue_script( 'select', get_template_directory_uri() . '/js/plugins/select2.full.min.js');
		wp_enqueue_script( 'slick', get_template_directory_uri() . '/js/plugins/slick-carousel/slick/slick.min.js');
		wp_enqueue_script( 'infinite', get_template_directory_uri() . '/js/plugins/infinite-scroll.min.js');
		wp_enqueue_script( 'eu-cookie', get_template_directory_uri() . '/js/eu-cookie.js');
		wp_enqueue_script( 'idea-postcodes', get_template_directory_uri() . '/js/ideal-postcodes.js');

		$js_version = $theme_version . '.' . filemtime(get_template_directory() . '/js/theme.min.js');
		wp_enqueue_script( 'understrap-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $js_version, true );
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

        $translation_array = array( 'templateUrl' => get_stylesheet_directory_uri() );
        wp_localize_script( 'slider', 'path', $translation_array );

	}
} // endif function_exists( 'understrap_scripts' ).

add_action( 'wp_enqueue_scripts', 'understrap_scripts' );