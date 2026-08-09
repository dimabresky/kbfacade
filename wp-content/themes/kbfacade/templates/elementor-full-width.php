<?php
/**
 * Template Name: Elementor Full Width
 * Template Post Type: page
 *
 * Full-width template for Elementor pages (no theme chrome around content).
 *
 * @package KBFacade
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
}

get_footer();
