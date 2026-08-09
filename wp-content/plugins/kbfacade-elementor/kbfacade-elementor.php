<?php
/**
 * Plugin Name: КБФасад Elementor
 * Description: Elementor widgets, forms, popup and landing installer for the КБФасад site.
 * Plugin URI:  https://github.com/dimabresky/kbfacade
 * Version:     1.0.0
 * Author:      КБФасад
 * Text Domain: kbfacade-elementor
 * Requires at least: 6.8
 * Requires PHP: 7.4
 * Requires Plugins: elementor
 * Elementor tested up to: 4.2.2
 *
 * @package KBFacadeElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KBFACADE_ELEMENTOR_VERSION', '1.0.0' );
define( 'KBFACADE_ELEMENTOR_FILE', __FILE__ );
define( 'KBFACADE_ELEMENTOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'KBFACADE_ELEMENTOR_URL', plugin_dir_url( __FILE__ ) );

require_once KBFACADE_ELEMENTOR_PATH . 'includes/helpers.php';
require_once KBFACADE_ELEMENTOR_PATH . 'includes/class-settings.php';
require_once KBFACADE_ELEMENTOR_PATH . 'includes/class-rest-forms.php';
require_once KBFACADE_ELEMENTOR_PATH . 'includes/class-assets.php';
require_once KBFACADE_ELEMENTOR_PATH . 'includes/class-landing-installer.php';
require_once KBFACADE_ELEMENTOR_PATH . 'includes/class-plugin.php';

/**
 * Bootstrap plugin after plugins are loaded.
 *
 * @return void
 */
function kbfacade_elementor_init() {
	\KBFacadeElementor\Plugin::instance()->init();
}
add_action( 'plugins_loaded', 'kbfacade_elementor_init' );
