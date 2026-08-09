<?php
/**
 * Creates / updates the Elementor landing page.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin tool to install the default landing structure.
 */
class Landing_Installer {

	const PAGE_OPTION = 'kbfacade_landing_page_id';

	/**
	 * @var Landing_Installer|null
	 */
	private static $instance = null;

	/**
	 * @return Landing_Installer
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
		add_action( 'admin_post_kbfacade_install_landing', array( $this, 'handle_install' ) );
	}

	/**
	 * @return void
	 */
	public function register_menu() {
		add_management_page(
			__( 'Install КБФасад Landing', 'kbfacade-elementor' ),
			__( 'КБФасад Landing', 'kbfacade-elementor' ),
			'manage_options',
			'kbfacade-landing',
			array( $this, 'render_page' )
		);
	}

	/**
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$page_id = (int) get_option( self::PAGE_OPTION );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Install КБФасад Landing', 'kbfacade-elementor' ); ?></h1>
			<p><?php esc_html_e( 'Creates or refreshes a WordPress page with all Elementor sections from the technical specification. Content remains fully editable in Elementor.', 'kbfacade-elementor' ); ?></p>
			<?php if ( $page_id && get_post( $page_id ) ) : ?>
				<p>
					<a href="<?php echo esc_url( get_permalink( $page_id ) ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'View landing page', 'kbfacade-elementor' ); ?>
					</a>
					|
					<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $page_id . '&action=elementor' ) ); ?>">
						<?php esc_html_e( 'Edit with Elementor', 'kbfacade-elementor' ); ?>
					</a>
				</p>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="kbfacade_install_landing" />
				<?php wp_nonce_field( 'kbfacade_install_landing' ); ?>
				<?php submit_button( __( 'Install / refresh landing page', 'kbfacade-elementor' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * @return void
	 */
	public function handle_install() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'kbfacade-elementor' ) );
		}
		check_admin_referer( 'kbfacade_install_landing' );

		if ( ! did_action( 'elementor/loaded' ) ) {
			wp_die( esc_html__( 'Activate Elementor before installing the landing page.', 'kbfacade-elementor' ) );
		}

		$page_id = $this->install_page();
		wp_safe_redirect( admin_url( 'tools.php?page=kbfacade-landing&installed=' . (int) $page_id ) );
		exit;
	}

	/**
	 * Create or update the landing page and Elementor data.
	 *
	 * @return int Page ID.
	 */
	public function install_page() {
		$page_id = (int) get_option( self::PAGE_OPTION );
		$postarr = array(
			'post_title'   => 'КБФасад',
			'post_name'    => 'home',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		);

		if ( $page_id && get_post( $page_id ) ) {
			$postarr['ID'] = $page_id;
			$page_id       = wp_update_post( $postarr, true );
		} else {
			$page_id = wp_insert_post( $postarr, true );
		}

		if ( is_wp_error( $page_id ) ) {
			wp_die( esc_html( $page_id->get_error_message() ) );
		}

		$document = $this->build_elementor_document();
		update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $page_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '4.2.2' );
		update_post_meta( $page_id, '_wp_page_template', 'templates/elementor-full-width.php' );
		update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $document ) ) );
		update_post_meta( $page_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );

		update_option( self::PAGE_OPTION, $page_id );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_id );

		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		$template_path = KBFACADE_ELEMENTOR_PATH . 'templates/landing.json';
		if ( is_writable( dirname( $template_path ) ) || ! file_exists( $template_path ) ) {
			// Keep an importable snapshot for Elementor > Templates > Import.
			file_put_contents(
				$template_path,
				wp_json_encode(
					array(
						'version'     => KBFACADE_ELEMENTOR_VERSION,
						'title'       => 'КБФасад Landing',
						'type'        => 'page',
						'content'     => $document,
						'page_settings' => array( 'hide_title' => 'yes' ),
					),
					JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
				)
			);
		}

		return (int) $page_id;
	}

	/**
	 * Build stacked Elementor sections with КБФасад widgets.
	 *
	 * @return array
	 */
	private function build_elementor_document() {
		$widgets = array(
			array( 'kbf-header', array() ),
			array( 'kbf-hero', array() ),
			array( 'kbf-about', array() ),
			array( 'kbf-catalog', array() ),
			array( 'kbf-gallery', array() ),
			array(
				'kbf-benefits',
				array(
					'title' => 'КБФасад — для девелоперов:',
					'lead'  => 'Сокращение сроков строительства и бюджета проекта + снижение рисков + увеличение полезной площади здания',
					'anchor' => '',
				),
			),
			array(
				'kbf-benefits',
				array(
					'title' => 'КБФасад — для строителей:',
					'lead'  => 'Минимум операций на объекте + удобный монтаж + быстрый переход к чистовой отделке',
					'preset' => 'builders',
				),
			),
			array(
				'kbf-slider',
				array(
					'title'  => 'Варианты креплений:',
					'preset' => 'fastenings',
					'anchor' => '',
				),
			),
			array( 'kbf-cta', array() ),
			array( 'kbf-services', array( 'anchor' => 'services' ) ),
			array(
				'kbf-slider',
				array(
					'title'       => 'Наши объекты:',
					'subtitle'    => 'Нам есть, чем гордиться!',
					'preset'      => 'objects',
					'anchor'      => 'objects',
					'overlay_mode'=> 'title_description',
				),
			),
			array( 'kbf-form', array() ),
			array( 'kbf-contacts', array( 'anchor' => 'contacts' ) ),
			array( 'kbf-footer', array() ),
		);

		$document = array();
		foreach ( $widgets as $item ) {
			$document[] = $this->wrap_widget( $item[0], $item[1] );
		}
		return $document;
	}

	/**
	 * Wrap a widget into a full-width section/column.
	 *
	 * @param string $widget_type Widget name.
	 * @param array  $settings    Widget settings overrides.
	 * @return array
	 */
	private function wrap_widget( $widget_type, $settings = array() ) {
		return array(
			'id'       => kbfacade_element_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'     => 'full_width',
				'gap'        => 'no',
				'padding'    => array(
					'unit'     => 'px',
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => true,
				),
			),
			'elements' => array(
				array(
					'id'       => kbfacade_element_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 100,
						'_inline_size' => null,
					),
					'elements' => array(
						array(
							'id'         => kbfacade_element_id(),
							'elType'     => 'widget',
							'widgetType' => $widget_type,
							'settings'   => $settings,
							'elements'   => array(),
						),
					),
				),
			),
			'isInner'  => false,
		);
	}
}
