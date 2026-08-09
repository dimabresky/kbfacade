<?php
/**
 * About / intro widget.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Company introduction block.
 */
class About extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-about';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад About', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-text-area';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'About', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Придумываем. Проектируем. Монтируем.',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Text', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => '<p>КБФасад — команда инженеров и монтажников, которая превращает идею вентилируемого фасада в надёжную, энергоэффективную и выразительную оболочку здания. Мы подбираем материалы, проектируем подсистему и выполняем монтаж под ключ.</p><p>Вентилируемый фасад защищает несущие конструкции, улучшает теплотехнические характеристики и позволяет реализовать смелые архитектурные решения без компромисса по срокам и бюджету.</p>',
			)
		);
		$this->add_media_control( 'logo', esc_html__( 'Side logo', 'kbfacade-elementor' ) );

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s        = $this->get_settings_for_display();
		$logo_url = ! empty( $s['logo']['url'] ) ? $s['logo']['url'] : kbfacade_theme_asset_url( 'images/logo/kbfacade-grey.png' );
		?>
		<section class="kbf-about" id="about">
			<div class="kbf-container kbf-about__grid">
				<div class="kbf-about__content">
					<h2><?php echo esc_html( $s['title'] ); ?></h2>
					<div class="kbf-about__text">
						<?php echo wp_kses_post( $s['text'] ); ?>
					</div>
				</div>
				<div class="kbf-about__logo">
					<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php esc_attr_e( 'КБФасад', 'kbfacade-elementor' ); ?>" />
				</div>
			</div>
		</section>
		<?php
	}
}
