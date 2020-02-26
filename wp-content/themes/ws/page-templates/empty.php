<?php
/**
 * Template Name: Empty Page Template - Buildable Page
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

while ( have_posts() ) : the_post();
	get_page_structure( 'page_structure' );
	get_template_part( 'loop-templates/content', 'empty' );
endwhile;

get_footer();
