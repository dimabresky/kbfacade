<?php
/**
 * Hero banner widget.
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
 * Hero with static banner image and editable fact icons.
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
		return 'eicon-image';
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
			'title_line_1',
			array(
				'label'   => esc_html__( 'Title line 1', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Искусство',
			)
		);
		$this->add_control(
			'title_line_2',
			array(
				'label'   => esc_html__( 'Title line 2', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'преображения',
			)
		);
		$this->add_control(
			'title_line_3',
			array(
				'label'   => esc_html__( 'Title line 3', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Вашего объекта',
			)
		);

		$this->add_control(
			'banner_image',
			array(
				'label' => esc_html__( 'Banner image', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'banner_link',
			array(
				'label' => esc_html__( 'Banner click URL', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::URL,
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
			'value',
			array(
				'label'   => esc_html__( 'Value', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '350+',
			)
		);
		$facts->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Label', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => "реализованных\nпроектов",
			)
		);
		$facts->add_control(
			'text',
			array(
				'label'       => esc_html__( 'Text (legacy)', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'Устаревшее поле; используйте Value и Label.', 'kbfacade-elementor' ),
			)
		);
		$this->add_control(
			'facts',
			array(
				'label'       => esc_html__( 'Facts', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $facts->get_controls(),
				'default'     => array(
					array(
						'value' => '350+',
						'label' => "реализованных\nпроектов",
					),
					array(
						'value' => '28',
						'label' => "инженеров-\nпроектировщиков\nв штате",
					),
				),
				'title_field' => '{{{ value }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Normalize fact repeater row to value + multiline label.
	 *
	 * @param array $fact Raw repeater row.
	 * @return array{value:string,label:string}
	 */
	private function normalize_fact( array $fact ) {
		$value = isset( $fact['value'] ) ? trim( (string) $fact['value'] ) : '';
		$label = isset( $fact['label'] ) ? trim( (string) $fact['label'] ) : '';

		if ( $value || $label ) {
			return array(
				'value' => $value,
				'label' => $label,
			);
		}

		$text = isset( $fact['text'] ) ? trim( (string) $fact['text'] ) : '';
		if ( '' === $text ) {
			return array(
				'value' => '',
				'label' => '',
			);
		}

		if ( preg_match( '/^(\S+)\s+(.*)$/us', $text, $matches ) ) {
			return array(
				'value' => trim( $matches[1] ),
				'label' => trim( $matches[2] ),
			);
		}

		return array(
			'value' => $text,
			'label' => '',
		);
	}

	/**
	 * Resolve banner image and link from current or legacy slide settings.
	 *
	 * @param array $settings Widget settings.
	 * @return array{image:array|string,link:array}
	 */
	private function resolve_banner( array $settings ) {
		$image = ! empty( $settings['banner_image'] ) ? $settings['banner_image'] : array();
		$link  = ! empty( $settings['banner_link'] ) ? (array) $settings['banner_link'] : array();

		if ( empty( $image['url'] ) && ! empty( $settings['slides'][0]['image'] ) ) {
			$image = $settings['slides'][0]['image'];
		}
		if ( empty( $link['url'] ) && ! empty( $settings['slides'][0]['link'] ) ) {
			$link = (array) $settings['slides'][0]['link'];
		}

		return array(
			'image' => $image,
			'link'  => $link,
		);
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s             = $this->get_settings_for_display();
		$banner        = $this->resolve_banner( $s );
		$fallback      = kbfacade_asset_relative( 'assets/images/hero/hero-1.jpg' );
		$icon_fallback = array(
			kbfacade_theme_asset_relative( 'images/icons/badges.svg' ),
			kbfacade_theme_asset_relative( 'images/icons/engineer.svg' ),
		);

		// Backward compatibility: pages saved with a single `title` control.
		$legacy_title = isset( $s['title'] ) ? trim( (string) $s['title'] ) : '';
		$line_1       = isset( $s['title_line_1'] ) ? (string) $s['title_line_1'] : '';
		$line_2       = isset( $s['title_line_2'] ) ? (string) $s['title_line_2'] : '';
		$line_3       = isset( $s['title_line_3'] ) ? (string) $s['title_line_3'] : '';
		$composed     = trim( preg_replace( '/\s+/u', ' ', $line_1 . ' ' . $line_2 . ' ' . $line_3 ) );
		$legacy_flat  = trim( preg_replace( '/\s+/u', ' ', $legacy_title ) );
		if ( $legacy_title && $legacy_flat && $legacy_flat !== $composed ) {
			$parts = preg_split( '/\r\n|\r|\n/', $legacy_title );
			$parts = array_values( array_filter( array_map( 'trim', (array) $parts ), 'strlen' ) );
			if ( count( $parts ) >= 3 ) {
				$line_1 = $parts[0];
				$line_2 = $parts[1];
				$line_3 = implode( ' ', array_slice( $parts, 2 ) );
			} elseif ( 2 === count( $parts ) ) {
				$line_1 = $parts[0];
				$line_2 = $parts[1];
				$line_3 = '';
			} else {
				$line_1 = $legacy_title;
				$line_2 = '';
				$line_3 = '';
			}
		}

		$href = ! empty( $banner['link']['url'] ) ? $banner['link']['url'] : '';
		?>
		<section class="kbf-hero">
			<div class="kbf-container">
				<div class="kbf-hero__top">
					<h1 class="kbf-hero__title">
						<?php if ( $line_1 ) : ?><span class="kbf-hero__title-line"><?php echo esc_html( $line_1 ); ?></span><?php endif; ?>
						<?php if ( $line_2 ) : ?><span class="kbf-hero__title-line kbf-hero__title-line--muted"><?php echo esc_html( $line_2 ); ?></span><?php endif; ?>
					</h1>
					<div class="kbf-hero__facts">
						<?php foreach ( array_values( (array) $s['facts'] ) as $index => $fact ) : ?>
							<?php
							$fact_data = $this->normalize_fact( (array) $fact );
							$lines     = preg_split( '/\r\n|\r|\n/', $fact_data['label'] );
							$lines     = array_values( array_filter( array_map( 'trim', (array) $lines ), 'strlen' ) );
							?>
							<div class="kbf-hero__fact">
								<?php
								kbfacade_render_image(
									$fact['icon'],
									isset( $icon_fallback[ $index ] ) ? $icon_fallback[ $index ] : $icon_fallback[0],
									'',
									'kbf-hero__fact-icon'
								);
								?>
								<div class="kbf-hero__fact-text">
									<?php if ( $fact_data['value'] ) : ?>
										<span class="kbf-hero__fact-value"><?php echo esc_html( $fact_data['value'] ); ?></span>
									<?php endif; ?>
									<?php if ( ! empty( $lines ) ) : ?>
										<span class="kbf-hero__fact-label">
											<?php
											foreach ( $lines as $line_index => $line ) {
												if ( $line_index > 0 ) {
													echo '<br>';
												}
												echo esc_html( $line );
											}
											?>
										</span>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php if ( $line_3 ) : ?>
						<div class="kbf-hero__title-row">
							<span class="kbf-hero__title-line kbf-hero__title-line--sub"><?php echo esc_html( $line_3 ); ?></span>
							<span class="kbf-hero__title-line-rule" aria-hidden="true"></span>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="kbf-hero__media">
				<figure class="kbf-hero__banner">
					<?php if ( $href ) : ?>
						<a href="<?php echo esc_url( $href ); ?>" <?php echo ! empty( $banner['link']['is_external'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
					<?php endif; ?>
					<?php
					kbfacade_render_image(
						$banner['image'],
						$fallback,
						'',
						'',
						'eager'
					);
					?>
					<?php if ( $href ) : ?>
						</a>
					<?php endif; ?>
				</figure>
			</div>
		</section>
		<?php
	}
}
