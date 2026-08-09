<?php
/**
 * Header widget.
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
 * Sticky-style header with burger, phones and consultation CTA.
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

		$phones = new Repeater();
		$phones->add_control(
			'phone',
			array(
				'label'   => esc_html__( 'Phone', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '+375 44 777-96-96',
			)
		);
		$this->add_control(
			'phones',
			array(
				'label'       => esc_html__( 'Phones', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $phones->get_controls(),
				'default'     => array(
					array( 'phone' => '+375 44 777-96-96' ),
					array( 'phone' => '+375 17 294-96-96' ),
				),
				'title_field' => '{{{ phone }}}',
			)
		);

		$this->add_control(
			'email',
			array(
				'label'   => esc_html__( 'Email', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'sales@pkdfasad.by',
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

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s         = $this->get_settings_for_display();
		$logo_url  = ! empty( $s['logo']['url'] ) ? $s['logo']['url'] : kbfacade_theme_asset_url( 'images/logo/kbfacade-grey.png' );
		$logo_href = ! empty( $s['logo_link']['url'] ) ? $s['logo_link']['url'] : home_url( '/' );
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
						<?php foreach ( (array) $s['phones'] as $item ) : ?>
							<?php $tel = preg_replace( '/[^\d+]/', '', $item['phone'] ); ?>
							<a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $item['phone'] ); ?></a>
						<?php endforeach; ?>
					</div>
					<?php if ( ! empty( $s['email'] ) ) : ?>
						<a class="kbf-header__email" href="mailto:<?php echo esc_attr( $s['email'] ); ?>"><?php echo esc_html( $s['email'] ); ?></a>
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
					<?php foreach ( (array) $s['phones'] as $item ) : ?>
						<?php $tel = preg_replace( '/[^\d+]/', '', $item['phone'] ); ?>
						<a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $item['phone'] ); ?></a>
					<?php endforeach; ?>
					<button type="button" class="kbf-btn kbf-btn--orange" data-kbf-open-modal>
						<?php echo esc_html( $s['cta_label'] ); ?>
					</button>
				</div>
			</div>
		</header>

		<div class="kbf-modal" data-kbf-modal hidden>
			<div class="kbf-modal__backdrop" data-kbf-close-modal></div>
			<div class="kbf-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="kbf-modal-title">
				<button type="button" class="kbf-modal__close" data-kbf-close-modal aria-label="<?php esc_attr_e( 'Закрыть', 'kbfacade-elementor' ); ?>">×</button>
				<h2 id="kbf-modal-title"><?php esc_html_e( 'Заказать консультацию', 'kbfacade-elementor' ); ?></h2>
				<form class="kbf-form kbf-form--modal" data-kbf-form data-form-type="modal" novalidate>
					<label>
						<span><?php esc_html_e( 'Наименование организации', 'kbfacade-elementor' ); ?></span>
						<input type="text" name="company" autocomplete="organization" />
					</label>
					<label>
						<span><?php esc_html_e( 'Имя', 'kbfacade-elementor' ); ?> *</span>
						<input type="text" name="name" required autocomplete="name" />
					</label>
					<label>
						<span><?php esc_html_e( 'Контактный номер', 'kbfacade-elementor' ); ?> *</span>
						<input type="tel" name="phone" required autocomplete="tel" />
					</label>
					<label>
						<span><?php esc_html_e( 'Ваш email', 'kbfacade-elementor' ); ?></span>
						<input type="email" name="email" autocomplete="email" />
					</label>
					<label class="kbf-form__consent">
						<input type="checkbox" name="consent" value="1" required />
						<span><?php esc_html_e( 'Согласен(на) на обработку персональных данных', 'kbfacade-elementor' ); ?></span>
					</label>
					<input type="text" name="website" class="kbf-form__hp" tabindex="-1" autocomplete="off" aria-hidden="true" />
					<button type="submit" class="kbf-btn kbf-btn--orange"><?php esc_html_e( 'Отправить', 'kbfacade-elementor' ); ?></button>
					<p class="kbf-form__message" data-kbf-form-message role="status" aria-live="polite"></p>
				</form>
			</div>
		</div>
		<?php
	}
}
