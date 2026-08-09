<?php
/**
 * Plugin settings (form recipient).
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings page for consultation form delivery.
 */
class Settings {

	const OPTION_KEY = 'kbfacade_form_recipient';

	/**
	 * Singleton.
	 *
	 * @var Settings|null
	 */
	private static $instance = null;

	/**
	 * @return Settings
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
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Recipient email with admin fallback.
	 *
	 * @return string
	 */
	public static function get_recipient() {
		$email = get_option( self::OPTION_KEY, '' );
		if ( ! is_email( $email ) ) {
			$email = get_option( 'admin_email' );
		}
		return (string) $email;
	}

	/**
	 * @return void
	 */
	public function register_menu() {
		add_options_page(
			__( 'КБФасад Forms', 'kbfacade-elementor' ),
			__( 'КБФасад Forms', 'kbfacade-elementor' ),
			'manage_options',
			'kbfacade-forms',
			array( $this, 'render_page' )
		);
	}

	/**
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'kbfacade_forms',
			self::OPTION_KEY,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_email',
				'default'           => get_option( 'admin_email' ),
			)
		);
	}

	/**
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'КБФасад Forms', 'kbfacade-elementor' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'kbfacade_forms' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="kbfacade_form_recipient"><?php esc_html_e( 'Recipient email', 'kbfacade-elementor' ); ?></label>
						</th>
						<td>
							<input
								type="email"
								class="regular-text"
								id="kbfacade_form_recipient"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>"
								value="<?php echo esc_attr( self::get_recipient() ); ?>"
								required
							/>
							<p class="description"><?php esc_html_e( 'Consultation requests are delivered with wp_mail() to this address.', 'kbfacade-elementor' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
