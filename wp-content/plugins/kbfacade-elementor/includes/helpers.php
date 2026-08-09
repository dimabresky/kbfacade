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
 * Convert an absolute same-site URL to a root-relative path.
 *
 * @param string $url Image or asset URL.
 * @return string
 */
function kbfacade_relative_url( $url ) {
	$url = (string) $url;
	if ( '' === $url ) {
		return '';
	}

	if ( '/' === $url[0] && ( ! isset( $url[1] ) || '/' !== $url[1] ) ) {
		return $url;
	}

	if ( function_exists( 'wp_make_link_relative' ) ) {
		$relative = wp_make_link_relative( $url );
		if ( is_string( $relative ) && '' !== $relative ) {
			return $relative;
		}
	}

	$parsed = wp_parse_url( $url );
	if ( empty( $parsed['path'] ) ) {
		return $url;
	}

	$relative = $parsed['path'];
	if ( ! empty( $parsed['query'] ) ) {
		$relative .= '?' . $parsed['query'];
	}

	return $relative;
}

/**
 * Root-relative URL to a plugin asset.
 *
 * @param string $relative Relative path under the plugin root.
 * @return string
 */
function kbfacade_asset_relative( $relative ) {
	return kbfacade_relative_url( kbfacade_asset_url( $relative ) );
}

/**
 * Root-relative theme asset URL with plugin fallback.
 *
 * @param string $relative Relative path under the theme assets folder.
 * @return string
 */
function kbfacade_theme_asset_relative( $relative ) {
	return kbfacade_relative_url( kbfacade_theme_asset_url( $relative ) );
}

/**
 * Map a public URL to a local filesystem path when possible.
 *
 * @param string $url Asset URL.
 * @return string
 */
function kbfacade_url_to_path( $url ) {
	$relative = kbfacade_relative_url( $url );
	if ( '' === $relative ) {
		return '';
	}

	$plugin_prefix = kbfacade_relative_url( KBFACADE_ELEMENTOR_URL );
	if ( $plugin_prefix && 0 === strpos( $relative, $plugin_prefix ) ) {
		return KBFACADE_ELEMENTOR_PATH . ltrim( substr( $relative, strlen( $plugin_prefix ) ), '/' );
	}

	$theme_prefix = kbfacade_relative_url( get_template_directory_uri() );
	if ( $theme_prefix && 0 === strpos( $relative, $theme_prefix ) ) {
		return get_template_directory() . '/' . ltrim( substr( $relative, strlen( $theme_prefix ) ), '/' );
	}

	$upload_dir     = wp_upload_dir();
	$uploads_prefix = ! empty( $upload_dir['baseurl'] ) ? kbfacade_relative_url( $upload_dir['baseurl'] ) : '';
	if ( $uploads_prefix && 0 === strpos( $relative, $uploads_prefix ) ) {
		return $upload_dir['basedir'] . substr( $relative, strlen( $uploads_prefix ) );
	}

	return '';
}

/**
 * Map a local filesystem path back to a root-relative public URL.
 *
 * @param string $path Local filesystem path.
 * @return string
 */
function kbfacade_path_to_relative_url( $path ) {
	$path = wp_normalize_path( $path );
	if ( '' === $path ) {
		return '';
	}

	$plugin_root = wp_normalize_path( KBFACADE_ELEMENTOR_PATH );
	if ( 0 === strpos( $path, $plugin_root ) ) {
		return kbfacade_asset_relative( ltrim( substr( $path, strlen( $plugin_root ) ), '/' ) );
	}

	$theme_root = wp_normalize_path( get_template_directory() );
	if ( 0 === strpos( $path, $theme_root ) ) {
		$theme_relative = ltrim( substr( $path, strlen( $theme_root ) ), '/' );
		return kbfacade_relative_url( trailingslashit( get_template_directory_uri() ) . $theme_relative );
	}

	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['basedir'] ) ) {
		$uploads_root = wp_normalize_path( $upload_dir['basedir'] );
		if ( 0 === strpos( $path, $uploads_root ) ) {
			return kbfacade_relative_url( $upload_dir['baseurl'] . substr( $path, strlen( $uploads_root ) ) );
		}
	}

	return '';
}

/**
 * Return a root-relative webp URL when a sibling .webp file exists.
 *
 * @param string $url Image URL.
 * @return string
 */
function kbfacade_webp_src_for_url( $url ) {
	if ( preg_match( '/\.svg$/i', (string) $url ) ) {
		return '';
	}

	$path = kbfacade_url_to_path( $url );
	if ( ! $path ) {
		return '';
	}

	$webp_path = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $path );
	if ( $webp_path === $path || ! file_exists( $webp_path ) ) {
		return '';
	}

	return kbfacade_path_to_relative_url( $webp_path );
}

/**
 * Pick the first existing SVG fallback from relative asset paths.
 *
 * @param string[] $paths Relative asset paths (theme or plugin).
 * @param string   $default Default fallback relative URL.
 * @return string
 */
function kbfacade_prefer_svg_fallback( array $paths, $default ) {
	foreach ( $paths as $path ) {
		$theme_path = get_template_directory() . '/assets/' . ltrim( $path, '/' );
		if ( file_exists( $theme_path ) && preg_match( '/\.svg$/i', $path ) ) {
			return kbfacade_theme_asset_relative( $path );
		}

		$plugin_path = KBFACADE_ELEMENTOR_PATH . ltrim( $path, '/' );
		if ( file_exists( $plugin_path ) && preg_match( '/\.svg$/i', $path ) ) {
			return kbfacade_asset_relative( $path );
		}
	}

	return $default;
}

/**
 * Root-relative fallback for the CTA/footer worker image.
 *
 * @return string
 */
function kbfacade_worker_fallback_relative() {
	$base = 'assets/images/cta/worker';
	if ( file_exists( KBFACADE_ELEMENTOR_PATH . $base . '.webp' ) ) {
		return kbfacade_asset_relative( $base . '.webp' );
	}
	if ( file_exists( KBFACADE_ELEMENTOR_PATH . $base . '.png' ) ) {
		return kbfacade_asset_relative( $base . '.png' );
	}

	return kbfacade_asset_relative( $base . '.jpg' );
}

/**
 * Output an image (or picture) tag for a resolved URL.
 *
 * @param string $url     Image URL.
 * @param string $alt     Alt text.
 * @param string $class   CSS class.
 * @param string $loading loading attribute.
 * @return void
 */
function kbfacade_render_image_url( $url, $alt = '', $class = '', $loading = 'lazy' ) {
	$url = kbfacade_relative_url( $url );
	if ( ! $url ) {
		return;
	}

	$webp = kbfacade_webp_src_for_url( $url );
	if ( $webp && ! preg_match( '/\.webp$/i', $url ) ) {
		printf(
			'<picture><source type="image/webp" srcset="%1$s"><img src="%2$s" alt="%3$s" class="%4$s" loading="%5$s" decoding="async" /></picture>',
			esc_url( $webp ),
			esc_url( $url ),
			esc_attr( $alt ),
			esc_attr( $class ),
			esc_attr( $loading )
		);
		return;
	}

	printf(
		'<img src="%1$s" alt="%2$s" class="%3$s" loading="%4$s" decoding="async" />',
		esc_url( $url ),
		esc_attr( $alt ),
		esc_attr( $class ),
		esc_attr( $loading )
	);
}

/**
 * Render an Elementor media control or fall back to a URL.
 *
 * @param array|string $image    Elementor media control value or URL.
 * @param string       $fallback Fallback image URL.
 * @param string       $alt      Alt text.
 * @param string       $class    CSS class.
 * @param string       $loading  loading attribute.
 * @return void
 */
function kbfacade_render_image( $image, $fallback = '', $alt = '', $class = '', $loading = 'lazy' ) {
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

	kbfacade_render_image_url( $url, $alt, $class, $loading );
}

/**
 * Generate a short Elementor-like unique id.
 *
 * @return string
 */
function kbfacade_element_id() {
	return substr( md5( uniqid( (string) wp_rand(), true ) ), 0, 7 );
}

/**
 * Normalize a phone string for tel: links.
 *
 * @param string $phone Display phone.
 * @return string
 */
function kbfacade_tel_href( $phone ) {
	return preg_replace( '/[^\d+]/', '', (string) $phone );
}
