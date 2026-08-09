<?php
/**
 * Contacts widget.
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
 * Address / phones / email contacts block.
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
			'address',
			array(
				'label'   => esc_html__( 'Address', 'kbfacade-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '223060, Минский р-н, п/о Тростенец, ул. Молодежная, 2А',
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
			'map_embed',
			array(
				'label'       => esc_html__( 'Map embed HTML / iframe', 'kbfacade-elementor' ),
				'type'        => Controls_Manager::CODE,
				'language'    => 'html',
				'description' => esc_html__( 'Optional. Paste an iframe from your map provider.', 'kbfacade-elementor' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		$s  = $this->get_settings_for_display();
		$id = ! empty( $s['anchor'] ) ? $s['anchor'] : 'contacts';
		?>
		<section class="kbf-contacts" id="<?php echo esc_attr( $id ); ?>">
			<div class="kbf-container kbf-contacts__grid">
				<div class="kbf-contacts__info">
					<h2><?php echo esc_html( $s['title'] ); ?></h2>
					<p class="kbf-contacts__address"><?php echo esc_html( $s['address'] ); ?></p>
					<div class="kbf-contacts__phones">
						<?php foreach ( (array) $s['phones'] as $item ) : ?>
							<?php $tel = preg_replace( '/[^\d+]/', '', $item['phone'] ); ?>
							<a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $item['phone'] ); ?></a>
						<?php endforeach; ?>
					</div>
					<?php if ( ! empty( $s['email'] ) ) : ?>
						<a class="kbf-contacts__email" href="mailto:<?php echo esc_attr( $s['email'] ); ?>"><?php echo esc_html( $s['email'] ); ?></a>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $s['map_embed'] ) ) : ?>
					<div class="kbf-contacts__map">
						<?php echo wp_kses( $s['map_embed'], array( 'iframe' => array( 'src' => true, 'width' => true, 'height' => true, 'style' => true, 'allowfullscreen' => true, 'loading' => true, 'referrerpolicy' => true, 'frameborder' => true, 'title' => true ) ) ); ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
