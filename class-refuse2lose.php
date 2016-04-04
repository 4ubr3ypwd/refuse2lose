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
	$file   = "class-{$prefix}.php";

	if ( __FILE__ != $file ) {
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
			$this->vendor(); // Libraries and cool things.

			// Features.
			new Refuse2Lose_CPT(); // CPT.
		}

		/**
		 * Load cool libraries.
		 *
		 * @since 1.0.0
		 */
		private function vendor() {
			foreach ( array(
				'vendor/cmb2/init.php', // CMB2.
			) as $include ) {
				require_once( $include );
			}
		}
	} // Refuse2Lose

	new Refuse2Lose();
} // Class exists
