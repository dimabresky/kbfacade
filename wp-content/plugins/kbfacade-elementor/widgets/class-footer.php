<?php
/**
 * Footer widget.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use KBFacadeElementor\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dark site footer. Contacts come from global Settings.
 */
class Footer extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-footer';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Footer', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-footer';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'Footer', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'slogan',
			array(
				'label'   => esc_html__( 'Top slogan', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Закажи свой проект у лидера!',
			)
		);
		$this->add_media_control( 'logo', esc_html__( 'Logo', 'kbfacade-elementor' ) );
		$this->add_media_control( 'side_image', esc_html__( 'Side image', 'kbfacade-elementor' ) );

		$nav = new Repeater();
		$nav->add_control(
			'label',
			array(
				'label' => esc_html__( 'Label', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$nav->add_control(
			'anchor',
			array(
				'label' => esc_html__( 'Anchor / URL', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'nav_items',
			array(
				'label'       => esc_html__( 'Navigation', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $nav->get_controls(),
				'default'     => array(
					array( 'label' => 'Главная', 'anchor' => '#' ),
					array( 'label' => 'Каталог', 'anchor' => '#catalog' ),
					array( 'label' => 'О нас', 'anchor' => '#about' ),
					array( 'label' => 'Услуги', 'anchor' => '#services' ),
					array( 'label' => 'Объекты', 'anchor' => '#objects' ),
					array( 'label' => 'Контакты', 'anchor' => '#contacts' ),
				),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->add_control(
			'cta_label',
			array(
				'label'   => esc_html__( 'CTA label', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Консультация',
			)
		);
		$this->add_control(
			'contacts_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Телефоны, email и соцсети берутся из Settings → КБФасад.', 'kbfacade-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$logo_fallback = kbfacade_theme_asset_relative( 'images/logo/kbfacade-white.png' );
		$side_fallback = kbfacade_asset_relative( 'assets/images/cta/engineer.png' );
		$phones = Settings::get_phones();
		$email  = Settings::get_email();
		$social = Settings::get_social();
		?>
		<footer class="kbf-footer">
			<div class="kbf-footer__bar"><?php echo esc_html( $s['slogan'] ); ?></div>
			<div class="kbf-container kbf-footer__inner">
				<div class="kbf-footer__media">
					<?php kbfacade_render_image( $s['side_image'], $side_fallback ); ?>
				</div>
				<div class="kbf-footer__center">
					<?php kbfacade_render_image( $s['logo'], $logo_fallback, __( 'КБФасад', 'kbfacade-elementor' ), 'kbf-footer__logo' ); ?>
					<nav class="kbf-footer__nav" aria-label="<?php esc_attr_e( 'Навигация в подвале', 'kbfacade-elementor' ); ?>">
						<?php foreach ( (array) $s['nav_items'] as $item ) : ?>
							<a href="<?php echo esc_url( $item['anchor'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
						<?php endforeach; ?>
					</nav>
				</div>
				<div class="kbf-footer__contacts">
					<?php foreach ( $phones as $phone ) : ?>
						<a href="tel:<?php echo esc_attr( kbfacade_tel_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					<?php endforeach; ?>
					<?php if ( $email ) : ?>
						<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
					<?php endif; ?>
					<?php if ( ! empty( $social ) ) : ?>
						<div class="kbf-footer__social">
							<?php foreach ( $social as $item ) : ?>
								<?php if ( empty( $item['url'] ) ) { continue; } ?>
								<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $item['label'] ? $item['label'] : $item['url'] ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<button type="button" class="kbf-btn kbf-btn--orange" data-kbf-open-modal>
						<?php echo esc_html( $s['cta_label'] ); ?>
					</button>
				</div>
			</div>
		</footer>
		<?php
	}
}
