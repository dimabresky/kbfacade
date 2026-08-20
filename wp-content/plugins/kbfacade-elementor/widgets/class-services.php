<?php
/**
 * Services grid widget.
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
 * Eight-card services section.
 */
class Services extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-services';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Services', 'kbfacade-elementor' );
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
				'label' => esc_html__( 'Services', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'anchor',
			array(
				'label'   => esc_html__( 'Section ID', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'services',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Наши услуги:',
			)
		);
		$this->add_control(
			'lead',
			array(
				'label'   => esc_html__( 'Lead', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Наши клиенты могут спать спокойно и не переживать, что завтра придется ехать на стройку и самостоятельно контролировать каждый этап монтажа. Мы сделаем ВСЕ ПОД КЛЮЧ!',
			)
		);

		$items = new Repeater();
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
				'label' => esc_html__( 'Description', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Services', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $items->get_controls(),
				'default'     => array(
					array(
						'title' => 'Проектирование',
						'text'  => 'Разрабатываем полный комплект проектной документации для устройства вентилируемого фасада с учетом архитектурных, конструктивных и нормативных требований',
					),
					array(
						'title' => 'Разработка концепции фасадного решения',
						'text'  => 'Предлагаем оптимальную концепцию фасада с учетом назначения здания, бюджета, архитектурного стиля и требований к эксплуатационным характеристикам',
					),
					array(
						'title' => 'Дизайн-проект (3d-визуализация и подбор материала)',
						'text'  => 'Создаем реалистичные 3D-визуализации будущего фасада, подбираем материалы, цвета и фактуры, чтобы вы могли увидеть итоговый результат еще до начала строительства. Разрабатываем детализированные чертежи КМД',
					),
					array(
						'title' => 'Техническая съемка и замер объекта',
						'text'  => 'Выполняем профессиональные обмеры здания и фиксируем все геометрические особенности объекта для последующего точного проектирования и монтажа',
					),
					array(
						'title' => 'Испытание на вырыв анкера',
						'text'  => 'Проводим испытания несущего основания для определения надежности крепления фасадной системы и выбора оптимального типа крепежных элементов',
					),
					array(
						'title' => 'Шеф-монтаж (авторский надзор)',
						'text'  => 'Контролируем соблюдение проектных решений на всех этапах монтажа, консультируем подрядчиков и обеспечиваем высокое качество выполнения работ',
					),
					array(
						'title' => 'Монтаж вентфасада',
						'text'  => 'Выполняем профессиональный монтаж вентилируемых фасадов любой сложности с соблюдением технологии, строительных норм и согласованных сроков реализации проекта',
					),
					array(
						'title' => 'Комплексная поставка',
						'text'  => 'Обеспечиваем полный комплект материалов и комплектующих для устройства вентилируемого фасада — от подсистемы и крепежа до облицовочных материалов и доборных элементов',
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
		$s  = $this->get_settings_for_display();
		$id = ! empty( $s['anchor'] ) ? $s['anchor'] : 'services';
		?>
		<section class="kbf-services" id="<?php echo esc_attr( $id ); ?>">
			<div class="kbf-container">
				<div class="kbf-section-head">
					<h2><?php echo esc_html( $s['title'] ); ?></h2>
					<p class="kbf-section-lead"><?php echo esc_html( $s['lead'] ); ?></p>
				</div>
				<div class="kbf-services__grid">
					<?php foreach ( (array) $s['items'] as $item ) : ?>
						<article class="kbf-card kbf-services__item">
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
