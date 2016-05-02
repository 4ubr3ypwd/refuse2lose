<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoloads our includes.
 *
 * @since  NEXT
 * @param  string $class The class found.
 */
function refuse2lose_autoload( $class ) {
	$prefix = strtolower( str_replace( '_', '-', $class ) );
	$dir    = trailingslashit( dirname( __FILE__ ) );
	$file   = "{$dir}/class-{$prefix}.php";

	if ( basename( __FILE__ ) != $file && file_exists( $file ) ) {
		require_once( $file ); // Include the class.
	}
}

spl_autoload_register( 'refuse2lose_autoload' );

if ( ! class_exists( 'Refuse2Lose' ) ) {
	/**
	 * Refuse2Lose.
	 *
	 * @since  1.0.0
	 */
	class Refuse2Lose {
		/**
		 * Construct
		 *
		 * @since  1.0.0
		 */
		function __construct() {
			$this->_require(); // Libraries and cool things.

			// Features.
			$cpt        = new Refuse2Lose_CPT(); // CPT.
			$users      = new Refuse2Lose_Users(); // Users.
			$settings   = new Refuse2Lose_CMB2_Settings(); // Settings.
			$tennis     = new Refuse2Lose_Profile_Tennis( $settings->get_profile() ); // Tennis profile.
			$fields     = new Refuse2Lose_Fields( $users->get_members_list() ); // All the fields (cmb2).
			$shortcodes = new Refuse2Lose_Shortcodes( $users->get_members_list(), $fields->fields() );
		}

		/**
		 * Load cool libraries.
		 *
		 * @since 1.0.0
		 */
		private function _require() {
			foreach ( array(
				'vendor/cmb2/init.php', // CMB2.
				'class-refuse2lose-simple-cpt-ui.php', // Simple CPT UI.
			) as $include ) {
				require_once( $include );
			}
		}
	} // Refuse2Lose

	new Refuse2Lose();
} // Class exists
