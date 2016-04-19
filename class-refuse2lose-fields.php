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
		 * Allow users to record that they beat someone outside the ladder?
		 *
		 * @since  1.0.0
		 * @var boolean
		 */
		private $allow_others_beat = true;

		/**
		 * Construct
		 *
		 * @since  1.0.0
		 */
		function __construct( $members ) {
			$this->members = $members; // Store the members.

			// Change if we allow users to beat non-members.
			$this->can_beat_non_members = apply_filters( 'refuse2lose/can_beat_non_members', true );

			// Add fields to admin.
			add_action( 'cmb2_init', array( $this, 'cmb2' ) );
		}

		/**
		 * The Fields.
		 *
		 * @since  1.0.0
		 * @return array Fields.
		 */
		private function fields() {
			$none = array( '' => __( 'None', 'refuse2lose' ), );

			// Required.
			$required = array( 'required' => 'required' );

			// Can someone beat a non member?
			$non_member_option = $this->can_beat_non_members

				// Allow someone else.
				? array( 'non-member' => apply_filters( 'refuse2lose_beat_non_member_text', __( 'A Non-member', 'refuse2lose' ) ), )

				// Don't allow someone else.
				: array();

			// The fields.
			return apply_filters( 'refuse2lose_fields', array(
				// Who are you?
				'_who_are_you' => array(
					'name'            => __( 'Who Are You', 'cmb2' ),
					'desc'            => __( 'Who is the member that won?', 'cmb2' ),
					'id'              => '_who_are_you',
					'type'            => 'select',

					// Choose a member.
					'options'         => empty( $this->members) ? $none : $this->members ,

					// Sanitization.
					'sanitization_cb' => array( $this, 'basic_sanitize' ),

					// Attributes
					'attributes'      => $required,
				),

				// Who did you beat?
				'_who_did_you_beat' => array(
					'name'            => __( 'Who lost?', 'cmb2' ),
					'desc'            => $this->can_beat_non_members ? __( 'Choose a member or select if you beat a non-member.', 'cmb2' ) : __( 'Choose a member you beat.', 'cmb2' ),
					'id'              => '_who_did_you_beat',
					'type'            => 'radio',

					// Choose a member.
					'options'         => array_merge( $this->members, $non_member_option ),

					// Sanitization.
					'sanitization_cb' => array( $this, 'basic_sanitize' ),

					// Attributes
					'attributes'      => $required,
				),
			) );
		}

		/**
		 * Basic sanitization.
		 *
		 * @since  1.0.0
		 * @param  string $field The field value.
		 * @return string        The field sanitized.
		 */
		public function basic_sanitize( $field ) {
			return esc_html( $field );
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
