<?php
/**
 * Hero media slider widget.
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
 * Hero with media slider and editable fact icons.
 */
class Hero extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-hero';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Hero', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slider-push';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'Hero', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Искусство преображения вашего объекта',
			)
		);

		$slides = new Repeater();
		$slides->add_control(
			'media_type',
			array(
				'label'   => esc_html__( 'Media type', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image' => 'Image',
					'video' => 'Video',
				),
			)
		);
		$slides->add_control(
			'image',
			array(
				'label'     => esc_html__( 'Image', 'kbfacade-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'media_type' => 'image' ),
			)
		);
		$slides->add_control(
			'video_url',
			array(
				'label'       => esc_html__( 'Video URL (mp4)', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'condition'   => array( 'media_type' => 'video' ),
				'label_block' => true,
			)
		);
		$slides->add_control(
			'link',
			array(
				'label' => esc_html__( 'Click URL', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => esc_html__( 'Slides', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $slides->get_controls(),
				'default'     => array(
					array( 'media_type' => 'image' ),
					array( 'media_type' => 'image' ),
				),
				'title_field' => '{{{ media_type }}}',
			)
		);

		$facts = new Repeater();
		$facts->add_control(
			'icon',
			array(
				'label' => esc_html__( 'Icon', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$facts->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Text', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '350+ реализованных проектов',
			)
		);
		$this->add_control(
			'facts',
			array(
				'label'       => esc_html__( 'Facts', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $facts->get_controls(),
				'default'     => array(
					array( 'text' => '350+ реализованных проектов' ),
					array( 'text' => '28 инженеров-проектировщиков в штате' ),
				),
				'title_field' => '{{{ text }}}',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'kbfacade-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'speed',
			array(
				'label'   => esc_html__( 'Autoplay interval (ms)', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6000,
				'min'     => 0,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$fallback = kbfacade_asset_url( 'assets/images/hero/hero-1.jpg' );
		$icon_fallback = array(
			kbfacade_theme_asset_url( 'images/icons/badges.svg' ),
			kbfacade_theme_asset_url( 'images/icons/engineer.svg' ),
		);
		?>
		<section class="kbf-hero" data-kbf-slider data-autoplay="<?php echo esc_attr( $s['autoplay'] ); ?>" data-speed="<?php echo esc_attr( (string) $s['speed'] ); ?>">
			<div class="kbf-container kbf-hero__top">
				<h1 class="kbf-hero__title"><?php echo esc_html( $s['title'] ); ?></h1>
				<div class="kbf-hero__facts">
					<?php foreach ( array_values( (array) $s['facts'] ) as $index => $fact ) : ?>
						<div class="kbf-hero__fact">
							<?php
							kbfacade_render_image(
								$fact['icon'],
								isset( $icon_fallback[ $index ] ) ? $icon_fallback[ $index ] : $icon_fallback[0],
								'',
								'kbf-hero__fact-icon'
							);
							?>
							<p><?php echo esc_html( $fact['text'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="kbf-hero__media" data-kbf-slider-track>
				<?php foreach ( (array) $s['slides'] as $i => $slide ) : ?>
					<?php
					$href = ! empty( $slide['link']['url'] ) ? $slide['link']['url'] : '';
					$url  = ! empty( $slide['image']['url'] ) ? $slide['image']['url'] : $fallback;
					?>
					<figure class="kbf-hero__slide<?php echo 0 === $i ? ' is-active' : ''; ?>" data-kbf-slide>
						<?php if ( $href ) : ?>
							<a href="<?php echo esc_url( $href ); ?>" <?php echo ! empty( $slide['link']['is_external'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
						<?php endif; ?>
						<?php if ( 'video' === $slide['media_type'] && ! empty( $slide['video_url'] ) ) : ?>
							<video src="<?php echo esc_url( $slide['video_url'] ); ?>" muted playsinline loop></video>
						<?php else : ?>
							<img src="<?php echo esc_url( $url ); ?>" alt="" loading="<?php echo 0 === $i ? 'eager' : 'lazy'; ?>" />
						<?php endif; ?>
						<?php if ( $href ) : ?>
							</a>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>

			<div class="kbf-hero__controls">
				<button type="button" class="kbf-slider__btn" data-kbf-prev aria-label="<?php esc_attr_e( 'Предыдущий слайд', 'kbfacade-elementor' ); ?>">‹</button>
				<button type="button" class="kbf-slider__btn" data-kbf-next aria-label="<?php esc_attr_e( 'Следующий слайд', 'kbfacade-elementor' ); ?>">›</button>
			</div>
		</section>
		<?php
	}
}
