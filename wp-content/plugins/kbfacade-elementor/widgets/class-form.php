<?php
/**
 * Inline consultation form widget.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compact lead form with side image and underline fields.
 */
class Form extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-form';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Form', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'Form', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Обсудим ваш проект и подберём оптимальное фасадное решение',
			)
		);
		$this->add_control(
			'button_label',
			array(
				'label'   => esc_html__( 'Button', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Отправить заявку на консультацию',
			)
		);
		$this->add_media_control( 'image', esc_html__( 'Side image', 'kbfacade-elementor' ) );
		$this->add_control(
			'show_name',
			array(
				'label'        => esc_html__( 'Show name field', 'kbfacade-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s   = $this->get_settings_for_display();
		$url = ! empty( $s['image']['url'] ) ? $s['image']['url'] : kbfacade_asset_url( 'assets/images/form/form-side.jpg' );
		?>
		<section class="kbf-inline-form">
			<div class="kbf-container kbf-inline-form__grid">
				<div class="kbf-inline-form__media">
					<img src="<?php echo esc_url( $url ); ?>" alt="" loading="lazy" />
				</div>
				<div class="kbf-inline-form__content">
					<h2><?php echo esc_html( $s['title'] ); ?></h2>
					<form class="kbf-form kbf-form--underline" data-kbf-form data-form-type="inline" novalidate>
						<?php if ( 'yes' === $s['show_name'] ) : ?>
							<label>
								<span><?php esc_html_e( 'Имя', 'kbfacade-elementor' ); ?></span>
								<input type="text" name="name" autocomplete="name" />
							</label>
						<?php endif; ?>
						<label>
							<span><?php esc_html_e( 'ТЕЛЕФОН', 'kbfacade-elementor' ); ?> *</span>
							<input type="tel" name="phone" required autocomplete="tel" />
						</label>
						<label class="kbf-form__consent">
							<input type="checkbox" name="consent" value="1" required />
							<span><?php esc_html_e( 'Согласен(на) на обработку персональных данных', 'kbfacade-elementor' ); ?></span>
						</label>
						<input type="text" name="website" class="kbf-form__hp" tabindex="-1" autocomplete="off" aria-hidden="true" />
						<button type="submit" class="kbf-btn kbf-btn--orange"><?php echo esc_html( $s['button_label'] ); ?></button>
						<p class="kbf-form__message" data-kbf-form-message role="status" aria-live="polite"></p>
					</form>
				</div>
			</div>
		</section>
		<?php
	}
}
