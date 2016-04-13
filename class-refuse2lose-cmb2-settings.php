<?php
/**
 * CMB2 Theme Options
 * @version 1.0.0
 */
class Refuse2Lose_CMB2_Settings {

	/**
 	 * Option key, and option page slug
 	 *
 	 * @since  1.0.0
 	 *
 	 * @var string
 	 */
	private $key = 'refuse2lose_setting';

	/**
 	 * Options page metabox id
 	 *
 	 * @since  1.0.0
 	 *
 	 * @var string
 	 */
	private $metabox_id = 'refuse2lose_metabox';

	/**
	 * Options Page title
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Set our title
		$this->title = __( 'Settings', 'refuse2lose' );

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
	}

	/**
	 * Register our setting to WP
	 *
	 * @since  1.0.0
	 */
	public function admin_init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 *
	 * @since 1.0.0
	 */
	public function add_options_page() {
		$this->options_page = add_submenu_page( 'edit.php?post_type=refuse2lose', $this->title, $this->title, 'manage_options', 'settings', array( $this, 'admin_page_display' ) );

		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup.
	 *
	 * Mostly handled by CMB2
	 *
	 * @since  1.0.0
	 */
	public function admin_page_display() {
		?>
			<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
				<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

				<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
			</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 *
	 * @since  1.0.0
	 */
	function add_options_page_metabox() {
		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		// Setting fields.
		new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) )->add_field( array(
			'name'    => __( 'Sport Profile', 'refuse2lose' ),
			'desc'    => __( 'Choose a sport (mostly changes how you log a score)', 'refuse2lose' ),
			'id'      => 'refuse2lose_profile',
			'type'    => 'select',

			// The profiles.
			'options' => apply_filters( 'refuse2lose_profiles', array_merge( array(
				'' => __( 'None', 'refuse2lose' ),
			), array() ) ),
		) );
	}

	/**
	 * Get the profile.
	 *
	 * @since  1.0.0
	 * @return string The profile setting.
	 */
	public function get_profile() {
		$settings = get_option( $this->key );
		if ( isset( $settings[ 'refuse2lose_profile' ] ) ) {
			return $settings[ 'refuse2lose_profile' ];
		}
	}

	/**
	 * Register settings notices for display.
	 *
	 * @since  1.0.0
	 *
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 *
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'refuse2lose' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve.
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}
}
