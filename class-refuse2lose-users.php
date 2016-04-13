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
			add_action( 'init', array( $this, 'role' ) );
		}

		/**
		 * Add the Refuse2Lose User Role.
		 *
		 * @since  1.0.0
		 */
		public function role() {
			if ( ! get_role( 'refuse2lose' ) ) {
				add_role( 'refuse2lose', 'Refuse2Lose' );
			}
		}

		/**
		 * The members.
		 *
		 * @since  1.0.0
		 * @return array The list of members.
		 */
		public function get_members_list() {
			$refuse2lose_users = get_users( array(
				'role'   => 'refuse2lose',
			) );

			foreach ( $refuse2lose_users as $user ) {
				$users[] = $user->display_name;
			}

			if ( isset( $users ) ) {
				return $users;
			}

			return array(); // No users.
		}
	} // Refuse2Lose_Users
}
