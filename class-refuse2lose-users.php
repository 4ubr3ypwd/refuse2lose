<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Refuse2Lose_Users' ) ) {
	/**
	 * Users.
	 *
	 * @since  1.0.0
	 */
	class Refuse2Lose_Users {
		/**
		 * Construct
		 *
		 * @since  1.0.0
		 */
		function __construct() {

		}

		public function get_members() {
			return array(
				'option1' => __( 'An Option', 'refuse2lose' ),
				'option3' => __( 'An Option', 'refuse2lose' ),
			);
		}
	} // Refuse2Lose_Users
}
