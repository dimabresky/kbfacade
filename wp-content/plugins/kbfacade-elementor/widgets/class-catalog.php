<?php
/**
 * Catalog widget with expandable cards.
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
 * Offer / catalog section.
 */
class Catalog extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-catalog';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Catalog', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'Catalog', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Мы предлагаем:',
			)
		);
		$this->add_control(
			'lead',
			array(
				'label'   => esc_html__( 'Lead', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Конструкция легко адаптируется под архитектурные и дизайнерские задачи + большой выбор материалов + выгодная экономика',
			)
		);
		$this->add_control(
			'more_label',
			array(
				'label'   => esc_html__( 'More button', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '— ПОДРОБНЕЕ',
			)
		);
		$this->add_control(
			'initial_count',
			array(
				'label'   => esc_html__( 'Visible cards initially', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 1,
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
				'label'   => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Фиброцементные плиты',
			)
		);
		$items->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Description', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Практичный и выразительный материал для современных фасадов с широкой палитрой фактур.',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Items', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $items->get_controls(),
				'default'     => array(
					array(
						'title' => 'Фиброцементные плиты',
						'text'  => 'Долговечная облицовка с высокой устойчивостью к влаге, УФ и перепадам температур.',
					),
					array(
						'title' => 'Декоративный камень',
						'text'  => 'Натуральный и искусственный камень для статусных архитектурных решений.',
					),
					array(
						'title' => 'Керамогранит',
						'text'  => 'Точная геометрия, богатый выбор форматов и надёжные системы крепления.',
					),
					array(
						'title' => 'Металлическая плита',
						'text'  => 'Лёгкие металлические панели для динамичных и технологичных фасадов.',
					),
					array(
						'title' => 'Линеарные панели',
						'text'  => 'Горизонтальный и вертикальный ритм, быстрый монтаж и аккуратные стыки.',
					),
					array(
						'title' => 'Фасадные ламели',
						'text'  => 'Солнцезащита и выразительная пластика для общественных и коммерческих объектов.',
					),
					array(
						'title' => 'Фальцевые панели',
						'text'  => 'Современная металлическая эстетика с высокой герметичностью облицовки.',
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s             = $this->get_settings_for_display();
		$initial       = max( 1, (int) $s['initial_count'] );
		$icon_fallback = kbfacade_theme_asset_url( 'images/icons/best-price.svg' );
		?>
		<section class="kbf-catalog" id="catalog" data-kbf-catalog data-initial="<?php echo esc_attr( (string) $initial ); ?>">
			<div class="kbf-container">
				<div class="kbf-section-head">
					<h2><?php echo esc_html( $s['title'] ); ?></h2>
					<p class="kbf-section-lead"><?php echo esc_html( $s['lead'] ); ?></p>
				</div>
				<div class="kbf-catalog__grid">
					<?php foreach ( array_values( (array) $s['items'] ) as $index => $item ) : ?>
						<article class="kbf-catalog__item<?php echo $index >= $initial ? ' is-collapsed' : ''; ?>" <?php echo $index >= $initial ? 'hidden' : ''; ?>>
							<?php kbfacade_render_image( $item['icon'], $icon_fallback, '', 'kbf-catalog__icon' ); ?>
							<h3><?php echo esc_html( $item['title'] ); ?></h3>
							<p><?php echo esc_html( $item['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
				<?php if ( count( (array) $s['items'] ) > $initial ) : ?>
					<button type="button" class="kbf-catalog__more" data-kbf-catalog-more>
						<?php echo esc_html( $s['more_label'] ); ?>
					</button>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
