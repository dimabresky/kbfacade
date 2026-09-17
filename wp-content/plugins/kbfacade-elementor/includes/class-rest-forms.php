<?php
/**
 * REST endpoint for consultation forms.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles public form submissions.
 */
class Rest_Forms {

	const SUCCESS_MESSAGE = 'Спасибо! Ваш запрос доставлен и мы уже начинаем Вам звонить';

	/**
	 * @var Rest_Forms|null
	 */
	private static $instance = null;

	/**
	 * @return Rest_Forms
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
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'kbfacade/v1',
			'/consultation',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_submission' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Process form payload.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function handle_submission( $request ) {
		$honeypot = trim( (string) $request->get_param( 'website' ) );
		if ( '' !== $honeypot ) {
			return rest_ensure_response(
				array(
					'success' => true,
					'message' => self::SUCCESS_MESSAGE,
				)
			);
		}

		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
		$rate_key = 'kbf_form_' . md5( $ip );
		if ( get_transient( $rate_key ) ) {
			return new \WP_Error(
				'kbfacade_rate_limited',
				__( 'Пожалуйста, подождите немного перед повторной отправкой.', 'kbfacade-elementor' ),
				array( 'status' => 429 )
			);
		}

		$form_type = sanitize_key( (string) $request->get_param( 'form_type' ) );
		if ( ! in_array( $form_type, array( 'modal', 'inline' ), true ) ) {
			$form_type = 'modal';
		}

		$consent = rest_sanitize_boolean( $request->get_param( 'consent' ) );
		if ( ! $consent ) {
			return new \WP_Error(
				'kbfacade_consent_required',
				__( 'Необходимо согласие на обработку персональных данных.', 'kbfacade-elementor' ),
				array( 'status' => 400 )
			);
		}

		$phone = sanitize_text_field( (string) $request->get_param( 'phone' ) );
		if ( '' === $phone ) {
			return new \WP_Error(
				'kbfacade_phone_required',
				__( 'Укажите контактный номер.', 'kbfacade-elementor' ),
				array( 'status' => 400 )
			);
		}

		$company = sanitize_text_field( (string) $request->get_param( 'company' ) );
		$name    = sanitize_text_field( (string) $request->get_param( 'name' ) );
		$email   = sanitize_email( (string) $request->get_param( 'email' ) );

		if ( 'modal' === $form_type && $email && ! is_email( $email ) ) {
			return new \WP_Error(
				'kbfacade_email_invalid',
				__( 'Укажите корректный email.', 'kbfacade-elementor' ),
				array( 'status' => 400 )
			);
		}

		$recipient = Settings::get_recipient();
		$subject   = sprintf(
			/* translators: %s: site name */
			__( '[%s] Заявка на консультацию', 'kbfacade-elementor' ),
			wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
		);

		$lines = array(
			'Тип формы: ' . $form_type,
			'Организация: ' . ( $company ? $company : '—' ),
			'Имя: ' . ( $name ? $name : '—' ),
			'Телефон: ' . $phone,
			'Email: ' . ( $email ? $email : '—' ),
			'Страница: ' . esc_url_raw( (string) $request->get_param( 'page_url' ) ),
			'IP: ' . $ip,
		);

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		if ( $email && is_email( $email ) ) {
			$headers[] = 'Reply-To: ' . $email;
		}

		$sent = wp_mail( $recipient, $subject, implode( "\n", $lines ), $headers );
		if ( ! $sent ) {
			return new \WP_Error(
				'kbfacade_mail_failed',
				__( 'Не удалось отправить заявку. Попробуйте позже или позвоните нам.', 'kbfacade-elementor' ),
				array( 'status' => 500 )
			);
		}

		set_transient( $rate_key, 1, 60 );

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => self::SUCCESS_MESSAGE,
			)
		);
	}
}
