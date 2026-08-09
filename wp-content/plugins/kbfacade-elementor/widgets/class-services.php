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
						'text'  => 'Рабочая документация вентфасада с расчётами и узлами крепления.',
					),
					array(
						'title' => 'Разработка концепции фасадного решения',
						'text'  => 'Подбор системы и материалов под архитектуру и бюджет объекта.',
					),
					array(
						'title' => 'Дизайн-проект (3D-визуализации и подбор материала)',
						'text'  => 'Наглядные визуализации и варианты облицовки до старта работ.',
					),
					array(
						'title' => 'Техническая съемка и замер объекта',
						'text'  => 'Точные исходные данные для проектирования и комплектации.',
					),
					array(
						'title' => 'Испытание на вырыв анкера',
						'text'  => 'Проверка несущей способности основания на объекте.',
					),
					array(
						'title' => 'Шеф-монтаж (авторский надзор)',
						'text'  => 'Контроль качества монтажа и соблюдения проектных решений.',
					),
					array(
						'title' => 'Монтаж вентфасада',
						'text'  => 'Полный цикл монтажных работ силами собственной бригады.',
					),
					array(
						'title' => 'Комплексная поставка',
						'text'  => 'Подсистема, утеплитель, облицовка и крепёж в одном контуре ответственности.',
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
		$s    = $this->get_settings_for_display();
		$icon = kbfacade_theme_asset_url( 'images/icons/engineer.svg' );
		$id   = ! empty( $s['anchor'] ) ? $s['anchor'] : 'services';
		?>
		<section class="kbf-services" id="<?php echo esc_attr( $id ); ?>">
			<div class="kbf-container">
				<div class="kbf-section-head">
					<h2><?php echo esc_html( $s['title'] ); ?></h2>
					<p class="kbf-section-lead"><?php echo esc_html( $s['lead'] ); ?></p>
				</div>
				<div class="kbf-services__grid">
					<?php foreach ( (array) $s['items'] as $item ) : ?>
						<article class="kbf-services__item">
							<?php kbfacade_render_image( $item['icon'], $icon, '', 'kbf-services__icon' ); ?>
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
