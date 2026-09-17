<?php
/**
 * Global consultation modal markup.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders a single modal outside individual widgets.
 */
class Modal {

	/**
	 * @var Modal|null
	 */
	private static $instance = null;

	/**
	 * @return Modal
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return void
	 */
	public function init() {
		add_action( 'wp_footer', array( $this, 'render' ), 20 );
	}

	/**
	 * Print modal once on the frontend.
	 *
	 * @return void
	 */
	public function render() {
		if ( is_admin() ) {
			return;
		}
		?>
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
						<span class="kbf-form__label-strong"><?php esc_html_e( 'Контактный номер', 'kbfacade-elementor' ); ?> *</span>
						<input type="tel" name="phone" required autocomplete="tel" placeholder="<?php echo esc_attr( '+7 977 721 00 21' ); ?>" />
					</label>
					<label>
						<span class="kbf-form__label-strong"><?php esc_html_e( 'Ваш email', 'kbfacade-elementor' ); ?></span>
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
