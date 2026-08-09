<?php
/**
 * Frontend assets.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and enqueues plugin CSS/JS for Elementor frontend/editor.
 */
class Assets {

	/**
	 * @var Assets|null
	 */
	private static $instance = null;

	/**
	 * @return Assets
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return void
	 */
	public function init() {
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ) );
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_styles' ) );
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * @return void
	 */
	public function register_styles() {
		wp_register_style(
			'kbfacade-elementor',
			KBFACADE_ELEMENTOR_URL . 'assets/css/frontend.css',
			array(),
			KBFACADE_ELEMENTOR_VERSION
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'kbfacade-elementor' );
	}

	/**
	 * @return void
	 */
	public function register_scripts() {
		wp_register_script(
			'kbfacade-elementor',
			KBFACADE_ELEMENTOR_URL . 'assets/js/frontend.js',
			array(),
			KBFACADE_ELEMENTOR_VERSION,
			true
		);

		wp_localize_script(
			'kbfacade-elementor',
			'kbfacadeElementor',
			array(
				'restUrl' => esc_url_raw( rest_url( 'kbfacade/v1/consultation' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'i18n'    => array(
					'sending' => __( 'Отправка…', 'kbfacade-elementor' ),
					'error'   => __( 'Ошибка отправки. Попробуйте ещё раз.', 'kbfacade-elementor' ),
				),
			)
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'kbfacade-elementor' );
	}
}
