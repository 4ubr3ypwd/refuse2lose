<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Refuse2Lose_Fields' ) ) {
	/**
	 * Fields.
	 *
	 * @since  1.0.0
	 */
	class Refuse2Lose_Fields {
		/**
		 * CMB2.
		 *
		 * @since  1.0.0
		 * @var object.
		 */
		private $cmb2;

		/**
		 * Members.
		 *
		 * @since  1.0.0
		 * @var array
		 */
		private $members;

		/**
		 * Construct
		 *
		 * @since  1.0.0
		 */
		function __construct( $members ) {
			$this->members = $members;
			add_action( 'cmb2_admin_init', array( $this, 'cmb2' ) );
		}

		/**
		 * The Fields.
		 *
		 * @since  1.0.0
		 * @return array Fields.
		 */
		private function fields() {
			return array(
				array(
					'name'            => __( 'Who Are You', 'cmb2' ),
					'desc'            => __( 'Who is the member that won?', 'cmb2' ),
					'id'              => '_who_are_you',
					'type'            => 'select',

					// Choose a member.
					'options'         =>	$this->members,

					// Sanitization.
					'sanitization_cb' => array( $this, 'sanitize' ),
					// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
				),
			);
		}

		public function sanitize( $field ) {
			return $field;
		}

		/**
		 * CMB2 Fields.
		 *
		 * @since  1.0
		 */
		public function cmb2() {
			$this->cmb2 = new_cmb2_box( array(
				'id'            => '_refuse2lose_fields',
				'title'         => __( 'Details', 'refuse2lose' ),
				'object_types'  => array( 'refuse2lose', ), // Post type
			) );

			foreach ( $this->fields() as $field ) {
				$this->cmb2->add_field( $field );
			}
		}
	} // Refuse2Lose_Fields
}
