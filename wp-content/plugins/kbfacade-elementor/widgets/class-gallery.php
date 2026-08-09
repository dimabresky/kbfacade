<?php
/**
 * Clickable gallery with lightbox (landscape 1:1:2 row).
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
 * Photo showcase gallery.
 */
class Gallery extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-gallery';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Gallery', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-justified';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'Gallery', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
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
			'size',
			array(
				'label'   => esc_html__( 'Tile size', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'md',
				'options' => array(
					'sm' => '1 part',
					'md' => '1 part',
					'lg' => '2 parts',
				),
			)
		);
		$items->add_control(
			'caption',
			array(
				'label' => esc_html__( 'Caption', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Images', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $items->get_controls(),
				'default'     => array(
					array( 'size' => 'sm', 'caption' => 'Стеклянный фасад' ),
					array( 'size' => 'md', 'caption' => 'Цветные панели' ),
					array( 'size' => 'lg', 'caption' => 'Современный офис' ),
				),
				'title_field' => '{{{ caption }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s         = $this->get_settings_for_display();
		$fallbacks = array(
			kbfacade_asset_relative( 'assets/images/gallery/gallery-1.jpg' ),
			kbfacade_asset_relative( 'assets/images/gallery/gallery-2.jpg' ),
			kbfacade_asset_relative( 'assets/images/gallery/gallery-3.jpg' ),
		);
		?>
		<section class="kbf-gallery" data-kbf-gallery>
			<div class="kbf-container">
				<div class="kbf-gallery__grid">
					<?php foreach ( array_values( (array) $s['items'] ) as $index => $item ) : ?>
						<?php
						$fallback = isset( $fallbacks[ $index ] ) ? $fallbacks[ $index ] : $fallbacks[0];
						$size     = ! empty( $item['size'] ) ? $item['size'] : 'md';
						$lightbox = ! empty( $item['image']['url'] )
							? kbfacade_relative_url( $item['image']['url'] )
							: $fallback;
						?>
						<button
							type="button"
							class="kbf-gallery__item kbf-gallery__item--<?php echo esc_attr( $size ); ?>"
							data-kbf-lightbox-src="<?php echo esc_url( $lightbox ); ?>"
							aria-label="<?php echo esc_attr( $item['caption'] ? $item['caption'] : __( 'Открыть изображение', 'kbfacade-elementor' ) ); ?>"
						>
							<?php kbfacade_render_image( $item['image'], $fallback, $item['caption'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="kbf-lightbox" data-kbf-lightbox hidden>
				<button type="button" class="kbf-lightbox__close" data-kbf-lightbox-close aria-label="<?php esc_attr_e( 'Закрыть', 'kbfacade-elementor' ); ?>">×</button>
				<img src="" alt="" data-kbf-lightbox-image />
			</div>
		</section>
		<?php
	}
}
