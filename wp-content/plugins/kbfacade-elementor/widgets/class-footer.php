<?php
/**
 * Footer widget.
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
 * Dark site footer.
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

		$phones = new Repeater();
		$phones->add_control(
			'phone',
			array(
				'label' => esc_html__( 'Phone', 'kbfacade-elementor' ),
				'type'  => Controls_Manager::TEXT,
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
				'default' => 'Консультация',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s         = $this->get_settings_for_display();
		$logo      = ! empty( $s['logo']['url'] ) ? $s['logo']['url'] : kbfacade_theme_asset_url( 'images/logo/kbfacade-white.png' );
		$side      = ! empty( $s['side_image']['url'] ) ? $s['side_image']['url'] : kbfacade_asset_url( 'assets/images/cta/worker.jpg' );
		?>
		<footer class="kbf-footer">
			<div class="kbf-footer__bar"><?php echo esc_html( $s['slogan'] ); ?></div>
			<div class="kbf-container kbf-footer__inner">
				<div class="kbf-footer__media">
					<img src="<?php echo esc_url( $side ); ?>" alt="" loading="lazy" />
				</div>
				<div class="kbf-footer__center">
					<img class="kbf-footer__logo" src="<?php echo esc_url( $logo ); ?>" alt="<?php esc_attr_e( 'КБФасад', 'kbfacade-elementor' ); ?>" />
					<nav class="kbf-footer__nav" aria-label="<?php esc_attr_e( 'Навигация в подвале', 'kbfacade-elementor' ); ?>">
						<?php foreach ( (array) $s['nav_items'] as $item ) : ?>
							<a href="<?php echo esc_url( $item['anchor'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
						<?php endforeach; ?>
					</nav>
				</div>
				<div class="kbf-footer__contacts">
					<?php foreach ( (array) $s['phones'] as $item ) : ?>
						<?php $tel = preg_replace( '/[^\d+]/', '', $item['phone'] ); ?>
						<a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $item['phone'] ); ?></a>
					<?php endforeach; ?>
					<?php if ( ! empty( $s['email'] ) ) : ?>
						<a href="mailto:<?php echo esc_attr( $s['email'] ); ?>"><?php echo esc_html( $s['email'] ); ?></a>
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
