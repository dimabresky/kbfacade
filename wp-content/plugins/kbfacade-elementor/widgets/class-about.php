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
				'default' => "Придумываем.\nПроектируем.\nМонтируем.",
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Text', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => '<p>Наша компания создана на базе предприятия-лидера по импорту строительно-отделочных материалов в Республику Беларусь - компании ООО “ОП НИИ ПКД”. КБФасад существует уже более 10 лет. И за это время реализовал более 350 проектов самой различной сложности. И да, мы с уверенностью можем заявить, что вентилируемые фасады для зданий - это наш конек.</p><p>Вентфасады сегодня – это функциональная необходимость, а также это оптимальный способ улучшить тепло- и звукоизоляционные свойства наружных стен здания. Помимо этого использование вентилируемых фасадов является отличным решением в борьбе с конденсацией.</p>',
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
		$logo_fallback = kbfacade_theme_asset_relative( 'images/logo/kbfacade-grey.png' );
		?>
		<section class="kbf-about" id="about">
			<div class="kbf-container kbf-about__grid">
				<div class="kbf-about__content">
					<h2><?php echo nl2br( esc_html( $s['title'] ) ); ?></h2>
					<div class="kbf-about__text">
						<?php echo wp_kses_post( $s['text'] ); ?>
					</div>
				</div>
				<div class="kbf-about__logo">
					<?php kbfacade_render_image( $s['logo'], $logo_fallback, __( 'КБФасад', 'kbfacade-elementor' ) ); ?>
				</div>
			</div>
		</section>
		<?php
	}
}
