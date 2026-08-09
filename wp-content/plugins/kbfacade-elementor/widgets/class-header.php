<?php
/**
 * Header widget.
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
 * Sticky-style header with burger and consultation CTA.
 * Phones/email always come from global Settings.
 */
class Header extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-header';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Header', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-header';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'Header', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_media_control( 'logo', esc_html__( 'Logo', 'kbfacade-elementor' ) );
		$this->add_control(
			'logo_link',
			array(
				'label'   => esc_html__( 'Logo link', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Label', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Каталог',
			)
		);
		$repeater->add_control(
			'anchor',
			array(
				'label'   => esc_html__( 'Anchor / URL', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '#catalog',
			)
		);

		$this->add_control(
			'nav_items',
			array(
				'label'       => esc_html__( 'Navigation', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'label' => 'Каталог', 'anchor' => '#catalog' ),
					array( 'label' => 'Объекты', 'anchor' => '#objects' ),
					array( 'label' => 'Услуги', 'anchor' => '#services' ),
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
				'default' => 'Заказать консультацию',
			)
		);
		$this->add_control(
			'contacts_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Телефоны и email берутся из Settings → КБФасад.', 'kbfacade-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s         = $this->get_settings_for_display();
		$logo_url  = ! empty( $s['logo']['url'] ) ? $s['logo']['url'] : kbfacade_theme_asset_url( 'images/logo/kbfacade-grey.png' );
		$logo_href = ! empty( $s['logo_link']['url'] ) ? $s['logo_link']['url'] : home_url( '/' );
		$phones    = Settings::get_phones();
		$email     = Settings::get_email();
		?>
		<header class="kbf-header" data-kbf-header>
			<div class="kbf-container kbf-header__inner">
				<a class="kbf-header__logo" href="<?php echo esc_url( $logo_href ); ?>">
					<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php esc_attr_e( 'КБФасад', 'kbfacade-elementor' ); ?>" />
				</a>

				<nav class="kbf-header__nav" data-kbf-nav aria-label="<?php esc_attr_e( 'Основная навигация', 'kbfacade-elementor' ); ?>">
					<?php foreach ( (array) $s['nav_items'] as $item ) : ?>
						<a href="<?php echo esc_url( $item['anchor'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
					<?php endforeach; ?>
				</nav>

				<div class="kbf-header__contacts">
					<div class="kbf-header__phones">
						<?php foreach ( $phones as $phone ) : ?>
							<a href="tel:<?php echo esc_attr( kbfacade_tel_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
						<?php endforeach; ?>
					</div>
					<?php if ( $email ) : ?>
						<a class="kbf-header__email" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
					<?php endif; ?>
					<button type="button" class="kbf-btn kbf-btn--dark kbf-header__cta" data-kbf-open-modal>
						<?php echo esc_html( $s['cta_label'] ); ?>
					</button>
				</div>

				<button type="button" class="kbf-header__burger" data-kbf-burger aria-expanded="false" aria-controls="kbf-mobile-panel">
					<span class="screen-reader-text"><?php esc_html_e( 'Меню', 'kbfacade-elementor' ); ?></span>
					<span></span><span></span><span></span>
				</button>
			</div>

			<div id="kbf-mobile-panel" class="kbf-header__mobile" data-kbf-mobile hidden>
				<nav class="kbf-header__mobile-nav" aria-label="<?php esc_attr_e( 'Мобильная навигация', 'kbfacade-elementor' ); ?>">
					<?php foreach ( (array) $s['nav_items'] as $item ) : ?>
						<a href="<?php echo esc_url( $item['anchor'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
					<?php endforeach; ?>
				</nav>
				<div class="kbf-header__mobile-contacts">
					<?php foreach ( $phones as $phone ) : ?>
						<a href="tel:<?php echo esc_attr( kbfacade_tel_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					<?php endforeach; ?>
					<?php if ( $email ) : ?>
						<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
					<?php endif; ?>
					<button type="button" class="kbf-btn kbf-btn--orange" data-kbf-open-modal>
						<?php echo esc_html( $s['cta_label'] ); ?>
					</button>
				</div>
			</div>
		</header>
		<?php
	}
}
