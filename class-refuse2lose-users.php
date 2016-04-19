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
			$users = array(); // No users at beginning.

			// Add users of these roles.
			foreach ( array(
				// 'administrator',
				'refuse2lose',
			) as $role ) {
				$this->push_display_names_for_role( $role, $users );
			}

			if ( is_array( $users ) ) {
				return $users;
			}

			return array(); // No users.
		}

		/**
		 * Add display name for users for certain role.
		 *
		 * @since  1.0.0
		 * @param  string $role   The role name.
		 * @param  array &$users  The users array.
		 * @return array          The users array with users for that role.
		 */
		private function push_display_names_for_role( $role, &$users ) {
			$_users = get_users( array(
				'role'   => $role,
			) );

			foreach ( $_users as $user ) {
				$users[ $user->data->ID ] = $user->display_name;
			}

			return $users;
		}
	} // Refuse2Lose_Users
}
