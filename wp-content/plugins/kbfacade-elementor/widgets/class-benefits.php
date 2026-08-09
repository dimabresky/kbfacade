<?php
/**
 * Benefits grid widget (developers / builders).
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Four-column benefits section.
 */
class Benefits extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-benefits';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Benefits', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-bullet-list';
	}

	/**
	 * Default items by preset.
	 *
	 * @param string $preset Preset key.
	 * @return array
	 */
	private function defaults_for_preset( $preset ) {
		if ( 'builders' === $preset ) {
			return array(
				array(
					'title' => 'Быстрый и технологичный монтаж',
					'text'  => 'Системные решения сокращают число операций и ускоряют сдачу фасада.',
				),
				array(
					'title' => 'Монтаж в любое время года',
					'text'  => 'Работы ведутся без «мокрых» процессов и жёсткой зависимости от сезона.',
				),
				array(
					'title' => 'Простой доступ к инженерным коммуникациям',
					'text'  => 'Вентзазор и съёмные элементы упрощают обслуживание сетей.',
				),
				array(
					'title' => 'Улучшение теплотехнических характеристик здания',
					'text'  => 'Непрерывный контур утеплителя повышает энергоэффективность объекта.',
				),
			);
		}

		return array(
			array(
				'title' => 'Снижение эксплуатационных расходов',
				'text'  => 'Энергоэффективная оболочка уменьшает затраты на отопление и обслуживание.',
			),
			array(
				'title' => 'Увеличение срока службы здания',
				'text'  => 'Фасад защищает несущие конструкции от влаги и перепадов температур.',
			),
			array(
				'title' => 'Повышение инвестиционной привлекательности',
				'text'  => 'Современный облик и предсказуемые сроки повышают ценность объекта.',
			),
			array(
				'title' => 'Минимальные затраты на обслуживание фасада',
				'text'  => 'Долговечные материалы и доступ к узлам снижают стоимость владения.',
			),
		);
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'Benefits', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'preset',
			array(
				'label'   => esc_html__( 'Preset', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'developers',
				'options' => array(
					'developers' => 'Developers',
					'builders'   => 'Builders',
				),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'КБФасад — для девелоперов:',
			)
		);
		$this->add_control(
			'lead',
			array(
				'label'   => esc_html__( 'Lead', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Сокращение сроков строительства и бюджета проекта + снижение рисков + увеличение полезной площади здания',
			)
		);

		$items = new Repeater();
		$items->add_control(
			'icon',
			array(
				'label' => esc_html__( 'Icon', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$items->add_control(
			'title',
			array(
				'label' => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$items->add_control(
			'text',
			array(
				'label' => esc_html__( 'Text', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Items', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $items->get_controls(),
				'default'     => $this->defaults_for_preset( 'developers' ),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s       = $this->get_settings_for_display();
		$items   = ! empty( $s['items'] ) ? $s['items'] : $this->defaults_for_preset( $s['preset'] );
		$icon    = kbfacade_theme_asset_url( 'images/icons/badges.svg' );

		// If installer passed builders preset without custom items, swap defaults.
		if ( 'builders' === $s['preset'] && 4 === count( $items ) && 'Снижение эксплуатационных расходов' === $items[0]['title'] ) {
			$items = $this->defaults_for_preset( 'builders' );
		}
		?>
		<section class="kbf-benefits">
			<div class="kbf-container">
				<div class="kbf-section-head">
					<h2><?php echo esc_html( $s['title'] ); ?></h2>
					<?php if ( ! empty( $s['lead'] ) ) : ?>
						<p class="kbf-section-lead"><?php echo esc_html( $s['lead'] ); ?></p>
					<?php endif; ?>
				</div>
				<div class="kbf-benefits__grid">
					<?php foreach ( (array) $items as $item ) : ?>
						<article class="kbf-benefits__item">
							<?php kbfacade_render_image( $item['icon'], $icon, '', 'kbf-benefits__icon' ); ?>
							<h3><?php echo esc_html( $item['title'] ); ?></h3>
							<p><?php echo esc_html( $item['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
