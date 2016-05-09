<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Refuse2Lose_Profile_Tennis' ) ) {
	/**
	 * Tennis Profile (default)
	 *
	 * @since  1.0.0
	 */
	class Refuse2Lose_Profile_Tennis {
		/**
		 * Construct
		 *
		 * @since  1.0.0
		 */
		function __construct( $chosen_profile ) {
			add_filter( 'refuse2lose_profiles', array( $this, 'add_profile' ) );

			if ( 'tennis' == $chosen_profile ) {
				add_filter( 'refuse2lose_fields', array( $this, 'refuse2lose_fields' ) );
			}
		}

		/**
		 * Fields for Tennis.
		 *
		 * @since  1.0.0
		 * @param  array $fields The current fields.
		 * @return array         Fields for Tennis.
		 */
		public function refuse2lose_fields( $fields ) {
			$template = $fields['_who_did_you_beat']; // Use this to get the attributes, santization, etc.

			$fields['_singles_doubles'] = array_merge( $template, array(
				'name'            => __( 'Singles or Doubles?', 'refuse2lose' ),
				'desc'            => __( 'Did you play singles or doubles? Singles awards double points.', 'refuse2lose' ),
				'id'              => '_singles_doubles',
				'type'            => 'select',

				// The different match types.
				'options'         => apply_filters( 'refuse2lose_tennis_match_types', array(
					'singles'       => __( 'Singles', 'refuse2lose' ),
					'doubles'       => __( 'Doubles', 'refuse2lose' ),
				) ),

				'required'    => 'required', // Required.

				// Assign points for values of this field.
				'points'      => apply_filters( 'refuse2lose_tennis_points', array(
					'singles' => 2, // 2 Points for this value.
					'doubles'  => 1, // 1 Point for this value.
				) ),
			) );

			// Go ahead, and even modify our tennis profile.
			return apply_filters( 'refuse2lose_profile_tennis_fields', $fields );
		}

		/**
		 * Adds the Tennis Profile to the settings page.
		 *
		 * @since 1.0.0
		 * @param array $profiles Current profile options.
		 */
		public function add_profile( $profiles ) {
			return array_merge( $profiles, array(
				'tennis' => __( 'Tennis', 'refuse2lose' ),
			) );
		}
	} // Refuse2Lose_Profile_Tennis
}
