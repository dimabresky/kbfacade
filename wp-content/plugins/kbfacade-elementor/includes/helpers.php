<?php
/**
 * Shared helpers.
 *
 * @package KBFacadeElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Absolute URL to a plugin asset.
 *
 * @param string $relative Relative path under the plugin root.
 * @return string
 */
function kbfacade_asset_url( $relative ) {
	return trailingslashit( KBFACADE_ELEMENTOR_URL ) . ltrim( $relative, '/' );
}

/**
 * Theme asset URL helper with plugin fallback for logos/icons.
 *
 * @param string $relative Relative path under the theme assets folder.
 * @return string
 */
function kbfacade_theme_asset_url( $relative ) {
	$theme_path = get_template_directory() . '/assets/' . ltrim( $relative, '/' );
	if ( file_exists( $theme_path ) ) {
		return get_template_directory_uri() . '/assets/' . ltrim( $relative, '/' );
	}

	return kbfacade_asset_url( $relative );
}

/**
 * Render an Elementor media control or fall back to a URL.
 *
 * @param array  $image    Elementor media control value.
 * @param string $fallback Fallback image URL.
 * @param string $alt      Alt text.
 * @param string $class    CSS class.
 * @return void
 */
function kbfacade_render_image( $image, $fallback = '', $alt = '', $class = '' ) {
	$url = '';
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		$url = $image['url'];
		if ( empty( $alt ) && ! empty( $image['alt'] ) ) {
			$alt = $image['alt'];
		}
	} elseif ( is_string( $image ) && $image ) {
		$url = $image;
	}

	if ( ! $url && $fallback ) {
		$url = $fallback;
	}

	if ( ! $url ) {
		return;
	}

	printf(
		'<img src="%1$s" alt="%2$s" class="%3$s" loading="lazy" decoding="async" />',
		esc_url( $url ),
		esc_attr( $alt ),
		esc_attr( $class )
	);
}

/**
 * Generate a short Elementor-like unique id.
 *
 * @return string
 */
function kbfacade_element_id() {
	return substr( md5( uniqid( (string) wp_rand(), true ) ), 0, 7 );
}
