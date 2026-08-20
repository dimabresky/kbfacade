<?php
/**
 * Contacts widget.
 *
 * @package KBFacadeElementor
 */

namespace KBFacadeElementor\Widgets;

use Elementor\Controls_Manager;
use KBFacadeElementor\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Address / phones / email contacts block from global settings.
 */
class Contacts extends Widget_Base_Common {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'kbf-contacts';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'КБФасад Contacts', 'kbfacade-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-google-maps';
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'Contacts', 'kbfacade-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'anchor',
			array(
				'label'   => esc_html__( 'Section ID', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'contacts',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Наши контакты',
			)
		);
		$this->add_control(
			'contacts_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Адрес, телефоны, email, карта и соцсети берутся из Settings → КБФасад.', 'kbfacade-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s       = $this->get_settings_for_display();
		$id      = ! empty( $s['anchor'] ) ? $s['anchor'] : 'contacts';
		$address = Settings::get_address();
		$phones  = Settings::get_phones();
		$email   = Settings::get_email();
		$map     = Settings::get_map_embed();
		$social  = Settings::get_social();
		?>
		<section class="kbf-contacts" id="<?php echo esc_attr( $id ); ?>">
			<div class="kbf-container kbf-contacts__row">
				<h2 class="kbf-contacts__title"><?php echo esc_html( $s['title'] ); ?></h2>
				<div class="kbf-contacts__details">
					<?php if ( $address ) : ?>
						<p class="kbf-contacts__address"><?php echo esc_html( $address ); ?></p>
					<?php endif; ?>
					<div class="kbf-contacts__phones">
						<?php foreach ( $phones as $phone ) : ?>
							<a href="tel:<?php echo esc_attr( kbfacade_tel_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
						<?php endforeach; ?>
					</div>
					<?php if ( $email ) : ?>
						<a class="kbf-contacts__email" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
					<?php endif; ?>
					<?php if ( ! empty( $social ) ) : ?>
						<div class="kbf-contacts__social">
							<?php foreach ( $social as $item ) : ?>
								<?php if ( empty( $item['url'] ) ) { continue; } ?>
								<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $item['label'] ? $item['label'] : $item['url'] ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( $map ) : ?>
				<div class="kbf-container kbf-contacts__map">
					<?php echo $map; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized iframe allowlist. ?>
				</div>
			<?php endif; ?>
		</section>
		<?php
	}
}
