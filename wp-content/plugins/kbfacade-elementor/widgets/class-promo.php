<?php
/**
 * Insulation recommendation promo block (BELTEP).
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Text + CTA link section before the catalog.
 */
class Promo extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-promo';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Promo', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-info-box';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'Promo', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Рекомендованная теплоизоляция для наших проектов',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Text', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Энергоэффективность вентилируемого фасада напрямую зависят от качества теплоизоляционного слоя. В своих технических решениях мы закладываем и рекомендуем к использованию негорючую минеральную вату от производителя БЕЛТЕП. Данный материал имеет оптимальную плотность для НВФ, защищен от эмиссии волокон и обладает всей необходимой сертификацией для коммерческого строительства.',
			)
		);
		$this->add_control(
			'button_label',
			array(
				'label'   => esc_html__( 'Button', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Перейти в каталог теплоизоляции',
			)
		);
		$this->add_control(
			'button_url',
			array(
				'label'       => esc_html__( 'Button URL', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'url'         => 'https://xn--90aiaxvq.xn--p1acf/',
					'is_external' => 'on',
					'nofollow'    => '',
				),
				'placeholder' => 'https://',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s   = $this->get_settings_for_display();
		$url = ! empty( $s['button_url']['url'] ) ? $s['button_url']['url'] : 'https://xn--90aiaxvq.xn--p1acf/';

		$is_external = ! empty( $s['button_url']['is_external'] );
		$rel         = array();
		if ( $is_external ) {
			$rel[] = 'noopener';
			$rel[] = 'noreferrer';
		}
		if ( ! empty( $s['button_url']['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}
		?>
		<section class="kbf-promo">
			<div class="kbf-container">
				<div class="kbf-section-head">
					<h2><?php echo esc_html( $s['title'] ); ?></h2>
					<p class="kbf-section-lead"><?php echo esc_html( $s['text'] ); ?></p>
				</div>
				<a
					href="<?php echo esc_url( $url ); ?>"
					class="kbf-btn kbf-btn--orange"
					<?php if ( $is_external ) : ?>
						target="_blank"
					<?php endif; ?>
					<?php if ( ! empty( $rel ) ) : ?>
						rel="<?php echo esc_attr( implode( ' ', $rel ) ); ?>"
					<?php endif; ?>
				>
					<?php echo esc_html( $s['button_label'] ); ?>
				</a>
			</div>
		</section>
		<?php
	}
}
