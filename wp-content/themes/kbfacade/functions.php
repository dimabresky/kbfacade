<?php
/**
 * КБФасад theme bootstrap.
 *
 * @package KBFacade
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KBFACADE_THEME_VERSION', '1.0.0' );

/**
 * Theme supports and menus.
 *
 * @return void
 */
function kbfacade_setup() {
	load_theme_textdomain( 'kbfacade', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 220,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Elementor compatibility.
	add_theme_support( 'align-wide' );
	add_theme_support( 'elementor' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'kbfacade' ),
			'footer'  => __( 'Footer Menu', 'kbfacade' ),
		)
	);
}
add_action( 'after_setup_theme', 'kbfacade_setup' );

/**
 * Register Elementor theme locations when Theme Builder is available.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager Theme manager.
 * @return void
 */
function kbfacade_register_elementor_locations( $elementor_theme_manager ) {
	if ( method_exists( $elementor_theme_manager, 'register_all_core_locations' ) ) {
		$elementor_theme_manager->register_all_core_locations();
	}
}
add_action( 'elementor/theme/register_locations', 'kbfacade_register_elementor_locations' );

/**
 * Enqueue theme styles.
 *
 * @return void
 */
function kbfacade_enqueue_assets() {
	$theme_uri = get_template_directory_uri();

	wp_enqueue_style(
		'kbfacade-fonts',
		$theme_uri . '/assets/css/fonts.css',
		array(),
		KBFACADE_THEME_VERSION
	);

	wp_enqueue_style(
		'kbfacade-main',
		$theme_uri . '/assets/css/main.css',
		array( 'kbfacade-fonts' ),
		KBFACADE_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'kbfacade_enqueue_assets' );

/**
 * Content width for embeds and Elementor.
 *
 * @return void
 */
function kbfacade_content_width() {
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'kbfacade_content_width', 0 );
