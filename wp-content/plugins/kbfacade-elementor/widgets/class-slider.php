<?php
/**
 * Configurable media slider (fastenings / objects).
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
 * Horizontal slider with hover overlays.
 */
class Slider extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-slider';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Slider', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slider-album';
	}

	/**
	 * @param string $preset Preset key.
	 * @return array
	 */
	private function defaults_for_preset( $preset ) {
		if ( 'objects' === $preset ) {
			$items = array();
			for ( $i = 1; $i <= 5; $i++ ) {
				$items[] = array(
					'title'       => 'Объект ' . $i,
					'description' => 'Комплекс работ по проектированию и монтажу вентилируемого фасада.',
				);
			}
			return $items;
		}

		return array(
			array(
				'title'       => 'Крепление для плит из керамогранита на кляммерах (видимое крепление)',
				'description' => 'Надёжная видимая система для керамогранита с точной геометрией швов.',
			),
			array(
				'title'       => 'Крепление для плит из фиброцемента',
				'description' => 'Система для фиброцементных плит с учётом температурных расширений.',
			),
			array(
				'title'       => 'Крепление для металлических облицовок',
				'description' => 'Профили и крепёж для металлических и линеарных панелей.',
			),
			array(
				'title'       => 'Крепление для клинкерной плитки',
				'description' => 'Решение для клинкера с акцентом на долговечность и аккуратный внешний вид.',
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
				'label' => esc_html__( 'Slider', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'preset',
			array(
				'label'   => esc_html__( 'Preset', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fastenings',
				'options' => array(
					'fastenings' => 'Fastenings',
					'objects'    => 'Objects',
				),
			)
		);
		$this->add_control(
			'anchor',
			array(
				'label'   => esc_html__( 'Section ID', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Варианты креплений:',
			)
		);
		$this->add_control(
			'subtitle',
			array(
				'label' => esc_html__( 'Subtitle', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'overlay_mode',
			array(
				'label'   => esc_html__( 'Overlay mode', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'description',
				'options' => array(
					'description'       => 'Description',
					'title_description' => 'Title + description',
				),
			)
		);
		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'kbfacade-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'speed',
			array(
				'label'   => esc_html__( 'Autoplay interval (ms)', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				'min'     => 0,
			)
		);

		$items = new Repeater();
		$items->add_control(
			'image',
			array(
				'label' => esc_html__( 'Image', 'kbfacade-elementor' ),
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
			'description',
			array(
				'label' => esc_html__( 'Description', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Slides', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $items->get_controls(),
				'default'     => $this->defaults_for_preset( 'fastenings' ),
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
		$preset  = ! empty( $s['preset'] ) ? $s['preset'] : 'fastenings';
		$items   = ! empty( $s['items'] ) ? $s['items'] : $this->defaults_for_preset( $preset );
		// Installer may pass preset without items; control defaults stay on fastenings.
		if ( 'objects' === $preset && ! empty( $items[0]['title'] ) && false !== strpos( $items[0]['title'], 'Крепление' ) ) {
			$items = $this->defaults_for_preset( 'objects' );
		}
		// PSD has 5 unique objects; drop leftover slides that only used removed object-6..8 fallbacks.
		if ( 'objects' === $preset && count( $items ) > 5 ) {
			$has_custom_images = false;
			foreach ( $items as $item ) {
				if ( ! empty( $item['image']['url'] ) ) {
					$has_custom_images = true;
					break;
				}
			}
			if ( ! $has_custom_images ) {
				$items = array_slice( array_values( $items ), 0, 5 );
			}
		}
		$anchor      = ! empty( $s['anchor'] ) ? $s['anchor'] : ( 'objects' === $preset ? 'objects' : '' );
		$dir         = 'objects' === $preset ? 'objects/object-' : 'fastenings/fastening-';
		$asset_count = 'objects' === $preset ? 5 : 4;
		?>
		<section class="kbf-slider-section" <?php echo $anchor ? 'id="' . esc_attr( $anchor ) . '"' : ''; ?> data-kbf-carousel data-autoplay="<?php echo esc_attr( $s['autoplay'] ); ?>" data-speed="<?php echo esc_attr( (string) $s['speed'] ); ?>">
			<div class="kbf-container">
				<div class="kbf-section-head">
					<h2><?php echo esc_html( $s['title'] ); ?></h2>
					<?php if ( ! empty( $s['subtitle'] ) ) : ?>
						<p class="kbf-section-lead"><?php echo esc_html( $s['subtitle'] ); ?></p>
					<?php endif; ?>
				</div>
				<div class="kbf-carousel">
					<button type="button" class="kbf-slider__btn kbf-carousel__prev" data-kbf-carousel-prev aria-label="<?php esc_attr_e( 'Назад', 'kbfacade-elementor' ); ?>">‹</button>
					<div class="kbf-carousel__track" data-kbf-carousel-track tabindex="0">
						<?php foreach ( array_values( (array) $items ) as $index => $item ) : ?>
							<?php
							$asset_index = ( $index % $asset_count ) + 1;
							$fallback    = kbfacade_asset_url( 'assets/images/' . $dir . $asset_index . '.jpg' );
							$url         = ! empty( $item['image']['url'] ) ? $item['image']['url'] : $fallback;
							?>
							<article class="kbf-carousel__item" tabindex="0">
								<div class="kbf-carousel__media">
									<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy" />
									<div class="kbf-carousel__overlay">
										<?php if ( 'title_description' === $s['overlay_mode'] ) : ?>
											<strong><?php echo esc_html( $item['title'] ); ?></strong>
											<span><?php echo esc_html( $item['description'] ); ?></span>
										<?php else : ?>
											<span><?php echo esc_html( $item['description'] ? $item['description'] : $item['title'] ); ?></span>
										<?php endif; ?>
									</div>
								</div>
								<?php if ( 'title_description' !== $s['overlay_mode'] ) : ?>
									<p class="kbf-carousel__caption"><?php echo esc_html( $item['title'] ); ?></p>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
					<button type="button" class="kbf-slider__btn kbf-carousel__next" data-kbf-carousel-next aria-label="<?php esc_attr_e( 'Вперёд', 'kbfacade-elementor' ); ?>">›</button>
				</div>
			</div>
		</section>
		<?php
	}
}
