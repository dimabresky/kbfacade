<?php
/**
 * Main plugin bootstrap.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers widgets, assets, settings and REST routes.
 */
class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get singleton.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Wire hooks.
	 *
	 * @return void
	 */
	public function init() {
		Settings::instance()->init();
		Rest_Forms::instance()->init();
		Assets::instance()->init();
		Landing_Installer::instance()->init();
		require_once KBFACADE_ELEMENTOR_PATH . 'includes/class-modal.php';
		Modal::instance()->init();

		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'admin_notices', array( $this, 'maybe_missing_elementor_notice' ) );
	}

	/**
	 * Register Elementor category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 * @return void
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'kbfacade',
			array(
				'title' => esc_html__( 'КБФасад', 'kbfacade-elementor' ),
				'icon'  => 'eicon-navigator',
			)
		);
	}

	/**
	 * Register custom widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		require_once KBFACADE_ELEMENTOR_PATH . 'widgets/class-widget-base.php';

		$files = array(
			'class-header.php'   => 'KBFacadeElementor\\Widgets\\Header',
			'class-hero.php'     => 'KBFacadeElementor\\Widgets\\Hero',
			'class-about.php'    => 'KBFacadeElementor\\Widgets\\About',
			'class-catalog.php'  => 'KBFacadeElementor\\Widgets\\Catalog',
			'class-gallery.php'  => 'KBFacadeElementor\\Widgets\\Gallery',
			'class-benefits.php' => 'KBFacadeElementor\\Widgets\\Benefits',
			'class-slider.php'   => 'KBFacadeElementor\\Widgets\\Slider',
			'class-cta.php'      => 'KBFacadeElementor\\Widgets\\Cta',
			'class-services.php' => 'KBFacadeElementor\\Widgets\\Services',
			'class-form.php'     => 'KBFacadeElementor\\Widgets\\Form',
			'class-contacts.php' => 'KBFacadeElementor\\Widgets\\Contacts',
			'class-footer.php'   => 'KBFacadeElementor\\Widgets\\Footer',
		);

		foreach ( $files as $file => $class ) {
			require_once KBFACADE_ELEMENTOR_PATH . 'widgets/' . $file;
			$widgets_manager->register( new $class() );
		}
	}

	/**
	 * Admin notice when Elementor is missing.
	 *
	 * @return void
	 */
	public function maybe_missing_elementor_notice() {
		if ( did_action( 'elementor/loaded' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		echo '<div class="notice notice-warning"><p>';
		echo esc_html__( 'КБФасад Elementor требует активный плагин Elementor.', 'kbfacade-elementor' );
		echo '</p></div>';
	}
}
