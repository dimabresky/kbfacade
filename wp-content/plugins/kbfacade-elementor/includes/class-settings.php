<?php
/**
 * Global site settings (contacts, forms, social).
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WordPress Options API settings used by header/contacts/footer and forms.
 */
class Settings {

	const OPTION_KEY          = 'kbfacade_site_settings';
	const LEGACY_RECIPIENT_KEY = 'kbfacade_form_recipient';

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
	 * Wire admin hooks and migrate legacy options.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		// Migrate on frontend too so form mail keeps the legacy recipient before any admin visit.
		add_action( 'init', array( $this, 'maybe_migrate_legacy_option' ), 1 );
	}

	/**
	 * Default site contact values from the design / TZ.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults() {
		return array(
			'phones'         => array(
				'+375 44 777-96-96',
				'+375 17 294-96-96',
			),
			'email'          => 'sales@pkdfasad.by',
			'address'        => '223060, Минский р-н, п/о Тростенец, ул. Молодежная, 2А',
			'map_embed'      => '',
			'form_recipient' => '',
			'social'         => array(
				array(
					'label' => '',
					'url'   => '',
				),
			),
		);
	}

	/**
	 * Sanitized settings with defaults applied.
	 *
	 * @return array<string,mixed>
	 */
	public static function get_all() {
		$stored   = get_option( self::OPTION_KEY, array() );
		$defaults = self::defaults();
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		$merged = array_merge( $defaults, $stored );

		$phones = array();
		if ( ! empty( $merged['phones'] ) && is_array( $merged['phones'] ) ) {
			foreach ( $merged['phones'] as $phone ) {
				$phone = sanitize_text_field( (string) $phone );
				if ( '' !== $phone ) {
					$phones[] = $phone;
				}
			}
		}
		if ( empty( $phones ) ) {
			$phones = $defaults['phones'];
		}

		$social = array();
		if ( ! empty( $merged['social'] ) && is_array( $merged['social'] ) ) {
			foreach ( $merged['social'] as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}
				$label = sanitize_text_field( isset( $item['label'] ) ? (string) $item['label'] : '' );
				$url   = esc_url_raw( isset( $item['url'] ) ? (string) $item['url'] : '' );
				if ( '' === $label && '' === $url ) {
					continue;
				}
				$social[] = array(
					'label' => $label,
					'url'   => $url,
				);
			}
		}

		$email = sanitize_email( (string) $merged['email'] );
		if ( ! is_email( $email ) ) {
			$email = $defaults['email'];
		}

		$recipient = sanitize_email( (string) $merged['form_recipient'] );
		if ( ! is_email( $recipient ) ) {
			$recipient = '';
		}

		return array(
			'phones'         => $phones,
			'email'          => $email,
			'address'        => sanitize_textarea_field( (string) $merged['address'] ),
			'map_embed'      => self::sanitize_map_embed( (string) $merged['map_embed'] ),
			'form_recipient' => $recipient,
			'social'         => $social,
		);
	}

	/**
	 * Phone list for widgets.
	 *
	 * @return array<int,string>
	 */
	public static function get_phones() {
		$data = self::get_all();
		return $data['phones'];
	}

	/**
	 * Public contact email.
	 *
	 * @return string
	 */
	public static function get_email() {
		$data = self::get_all();
		return $data['email'];
	}

	/**
	 * Postal address.
	 *
	 * @return string
	 */
	public static function get_address() {
		$data = self::get_all();
		return $data['address'];
	}

	/**
	 * Optional map iframe HTML.
	 *
	 * @return string
	 */
	public static function get_map_embed() {
		$data = self::get_all();
		return $data['map_embed'];
	}

	/**
	 * Social links.
	 *
	 * @return array<int,array{label:string,url:string}>
	 */
	public static function get_social() {
		$data = self::get_all();
		return $data['social'];
	}

	/**
	 * Form delivery recipient with admin fallback.
	 *
	 * @return string
	 */
	public static function get_recipient() {
		$data = self::get_all();
		if ( is_email( $data['form_recipient'] ) ) {
			return $data['form_recipient'];
		}

		$legacy = get_option( self::LEGACY_RECIPIENT_KEY, '' );
		if ( is_email( (string) $legacy ) ) {
			return (string) $legacy;
		}

		if ( is_email( $data['email'] ) ) {
			return $data['email'];
		}
		return (string) get_option( 'admin_email' );
	}

	/**
	 * Allow only iframe embeds for maps.
	 *
	 * @param string $html Raw HTML.
	 * @return string
	 */
	public static function sanitize_map_embed( $html ) {
		$html = trim( (string) $html );
		if ( '' === $html ) {
			return '';
		}

		return wp_kses(
			$html,
			array(
				'iframe' => array(
					'src'             => true,
					'width'           => true,
					'height'          => true,
					'style'           => true,
					'allowfullscreen' => true,
					'loading'         => true,
					'referrerpolicy'  => true,
					'frameborder'     => true,
					'title'           => true,
					'allow'           => true,
				),
			)
		);
	}

	/**
	 * Sanitize settings payload from admin form.
	 *
	 * @param mixed $input Raw input.
	 * @return array<string,mixed>
	 */
	public static function sanitize( $input ) {
		$defaults = self::defaults();
		if ( ! is_array( $input ) ) {
			return $defaults;
		}

		$phones_raw = isset( $input['phones'] ) ? (string) $input['phones'] : '';
		$phones     = array();
		foreach ( preg_split( '/\r\n|\r|\n/', $phones_raw ) as $line ) {
			$line = sanitize_text_field( trim( $line ) );
			if ( '' !== $line ) {
				$phones[] = $line;
			}
		}
		if ( empty( $phones ) ) {
			$phones = $defaults['phones'];
		}

		$social = array();
		if ( ! empty( $input['social'] ) && is_array( $input['social'] ) ) {
			foreach ( $input['social'] as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}
				$label = sanitize_text_field( isset( $item['label'] ) ? (string) $item['label'] : '' );
				$url   = esc_url_raw( isset( $item['url'] ) ? (string) $item['url'] : '' );
				if ( '' === $label && '' === $url ) {
					continue;
				}
				$social[] = array(
					'label' => $label,
					'url'   => $url,
				);
			}
		}

		$email = sanitize_email( isset( $input['email'] ) ? (string) $input['email'] : '' );
		if ( ! is_email( $email ) ) {
			$email = $defaults['email'];
		}

		$recipient = sanitize_email( isset( $input['form_recipient'] ) ? (string) $input['form_recipient'] : '' );
		if ( ! is_email( $recipient ) ) {
			$recipient = '';
		}

		return array(
			'phones'         => $phones,
			'email'          => $email,
			'address'        => sanitize_textarea_field( isset( $input['address'] ) ? (string) $input['address'] : '' ),
			'map_embed'      => self::sanitize_map_embed( isset( $input['map_embed'] ) ? (string) $input['map_embed'] : '' ),
			'form_recipient' => $recipient,
			'social'         => $social,
		);
	}

	/**
	 * Migrate old single recipient option into the new array.
	 *
	 * @return void
	 */
	public function maybe_migrate_legacy_option() {
		$current = get_option( self::OPTION_KEY, null );
		if ( null !== $current && false !== $current ) {
			return;
		}

		$legacy = get_option( self::LEGACY_RECIPIENT_KEY, '' );
		$data   = self::defaults();
		if ( is_email( (string) $legacy ) ) {
			$data['form_recipient'] = (string) $legacy;
		}
		add_option( self::OPTION_KEY, $data );
	}

	/**
	 * @return void
	 */
	public function register_menu() {
		add_options_page(
			__( 'КБФасад', 'kbfacade-elementor' ),
			__( 'КБФасад', 'kbfacade-elementor' ),
			'manage_options',
			'kbfacade-settings',
			array( $this, 'render_page' )
		);
	}

	/**
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'kbfacade_site',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$data    = self::get_all();
		$phones  = implode( "\n", $data['phones'] );
		$social  = $data['social'];
		if ( empty( $social ) ) {
			$social = array(
				array(
					'label' => '',
					'url'   => '',
				),
			);
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'КБФасад — настройки сайта', 'kbfacade-elementor' ); ?></h1>
			<p><?php esc_html_e( 'Контактные данные используются в шапке, блоке контактов, подвале и формах. Редактирование в Elementor отключено — меняйте значения здесь.', 'kbfacade-elementor' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'kbfacade_site' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="kbfacade_phones"><?php esc_html_e( 'Телефоны', 'kbfacade-elementor' ); ?></label>
						</th>
						<td>
							<textarea
								class="large-text"
								rows="4"
								id="kbfacade_phones"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>[phones]"
							><?php echo esc_textarea( $phones ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Один номер на строку.', 'kbfacade-elementor' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="kbfacade_email"><?php esc_html_e( 'Публичный email', 'kbfacade-elementor' ); ?></label>
						</th>
						<td>
							<input
								type="email"
								class="regular-text"
								id="kbfacade_email"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>[email]"
								value="<?php echo esc_attr( $data['email'] ); ?>"
								required
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="kbfacade_address"><?php esc_html_e( 'Адрес', 'kbfacade-elementor' ); ?></label>
						</th>
						<td>
							<textarea
								class="large-text"
								rows="3"
								id="kbfacade_address"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>[address]"
							><?php echo esc_textarea( $data['address'] ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="kbfacade_map"><?php esc_html_e( 'Карта (iframe, опционально)', 'kbfacade-elementor' ); ?></label>
						</th>
						<td>
							<textarea
								class="large-text code"
								rows="5"
								id="kbfacade_map"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>[map_embed]"
							><?php echo esc_textarea( $data['map_embed'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'По умолчанию пусто — в макете карты нет. При необходимости вставьте iframe.', 'kbfacade-elementor' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="kbfacade_form_recipient"><?php esc_html_e( 'Email получателя заявок', 'kbfacade-elementor' ); ?></label>
						</th>
						<td>
							<input
								type="email"
								class="regular-text"
								id="kbfacade_form_recipient"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>[form_recipient]"
								value="<?php echo esc_attr( $data['form_recipient'] ); ?>"
							/>
							<p class="description"><?php esc_html_e( 'Если пусто — используется публичный email, затем admin_email.', 'kbfacade-elementor' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Соцсети', 'kbfacade-elementor' ); ?></th>
						<td>
							<?php foreach ( $social as $index => $item ) : ?>
								<p>
									<input
										type="text"
										class="regular-text"
										placeholder="<?php esc_attr_e( 'Название', 'kbfacade-elementor' ); ?>"
										name="<?php echo esc_attr( self::OPTION_KEY ); ?>[social][<?php echo esc_attr( (string) $index ); ?>][label]"
										value="<?php echo esc_attr( $item['label'] ); ?>"
									/>
									<input
										type="url"
										class="regular-text"
										placeholder="https://"
										name="<?php echo esc_attr( self::OPTION_KEY ); ?>[social][<?php echo esc_attr( (string) $index ); ?>][url]"
										value="<?php echo esc_attr( $item['url'] ); ?>"
									/>
								</p>
							<?php endforeach; ?>
							<p>
								<input
									type="text"
									class="regular-text"
									placeholder="<?php esc_attr_e( 'Название', 'kbfacade-elementor' ); ?>"
									name="<?php echo esc_attr( self::OPTION_KEY ); ?>[social][new][label]"
									value=""
								/>
								<input
									type="url"
									class="regular-text"
									placeholder="https://"
									name="<?php echo esc_attr( self::OPTION_KEY ); ?>[social][new][url]"
									value=""
								/>
							</p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
