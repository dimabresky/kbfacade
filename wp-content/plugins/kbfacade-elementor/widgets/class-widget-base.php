<?php
/**
 * Shared Elementor widget base.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor\Widgets;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Common category / script helpers.
 */
abstract class Widget_Base_Common extends Widget_Base {

	/**
	 * @return array
	 */
	public function get_categories() {
		return array( 'kbfacade' );
	}

	/**
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'kbfacade-elementor' );
	}

	/**
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'kbfacade-elementor' );
	}

	/**
	 * Add a media control with optional fallback description.
	 *
	 * @param string $id    Control ID.
	 * @param string $label Label.
	 * @return void
	 */
	protected function add_media_control( $id, $label ) {
		$this->add_control(
			$id,
			array(
				'label' => $label,
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);
	}
}
