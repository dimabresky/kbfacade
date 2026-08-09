<?php
/**
 * Dark consultation CTA banner.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mid-page CTA.
 */
class Cta extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-cta';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад CTA', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'CTA', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'line_1',
			array(
				'label'   => esc_html__( 'Line 1', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Устали выбирать?!',
			)
		);
		$this->add_control(
			'line_2',
			array(
				'label'   => esc_html__( 'Line 2', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Проконсультируем бесплатно!',
			)
		);
		$this->add_control(
			'button_label',
			array(
				'label'   => esc_html__( 'Button', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Заказать!',
			)
		);
		$this->add_media_control( 'image', esc_html__( 'Side image', 'kbfacade-elementor' ) );

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s              = $this->get_settings_for_display();
		$image_fallback = kbfacade_asset_relative( 'assets/images/cta/engineer.png' );
		?>
		<section class="kbf-cta">
			<div class="kbf-container kbf-cta__inner">
				<div class="kbf-cta__copy">
					<p class="kbf-cta__line kbf-cta__line--accent"><?php echo esc_html( $s['line_1'] ); ?></p>
					<p class="kbf-cta__line"><?php echo esc_html( $s['line_2'] ); ?></p>
					<button type="button" class="kbf-btn kbf-btn--orange" data-kbf-open-modal>
						<?php echo esc_html( $s['button_label'] ); ?>
					</button>
				</div>
				<div class="kbf-cta__media">
					<?php kbfacade_render_image( $s['image'], $image_fallback ); ?>
				</div>
			</div>
		</section>
		<?php
	}
}
