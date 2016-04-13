<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Refuse2Lose_CPT' ) ) {
	/**
	 * Refuse2Loose CPT.
	 *
	 * @since  1.0.0
	 */
	class Refuse2Lose_CPT {
		/**
		 * Construct
		 *
		 * @since  1.0.0
		 */
		function __construct() {
			add_action( 'init', array( $this, 'cpt' ) );
		}

		/**
		 * CPT for keeping track of records.
		 *
		 * @since 1.0.0
		 */
		public function cpt() {
			register_post_type( 'refuse2lose', array(
				'labels'             => array(
					'name'               => _x( 'Records', 'post type general name', 'refuse2lose' ),
					'singular_name'      => _x( 'Record', 'post type singular name', 'refuse2lose' ),
					'menu_name'          => apply_filters( 'refuse2lose_menu_name', _x( 'Refuse2Lose', 'admin menu', 'refuse2lose' ) ),
					'name_admin_bar'     => _x( 'Record', 'add new on admin bar', 'refuse2lose' ),
					'add_new'            => _x( 'Add New', 'Record', 'refuse2lose' ),
					'add_new_item'       => __( 'Add New Record', 'refuse2lose' ),
					'new_item'           => __( 'New Record', 'refuse2lose' ),
					'edit_item'          => __( 'Edit Record', 'refuse2lose' ),
					'view_item'          => __( 'View Record', 'refuse2lose' ),
					'all_items'          => __( 'All Records', 'refuse2lose' ),
					'search_items'       => __( 'Search Records', 'refuse2lose' ),
					'parent_item_colon'  => __( 'Parent Records:', 'refuse2lose' ),
					'not_found'          => __( 'No records found.', 'refuse2lose' ),
					'not_found_in_trash' => __( 'No records found in Trash.', 'refuse2lose' ),
				),
				'description'        => __( 'Logged Records.', 'refuse2lose' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'capability_type'    => 'page',
				'rewrite'            => array(
					'slug' => 'list',
				),
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => false,
				'menu_icon'          => 'dashicons-list-view',
				'supports'           => array(
					'title', // We don't really want this, but sure.
				),
			) );

			// Flush!
			$code         = md5( file_get_contents( __FILE__ ) );
			$flush_option = get_option( 'flush_refuse2lose_cpt' );
			if ( $flush_option != $code ) {
				// We have a code change, flush the rewrite rules!
				flush_rewrite_rules();
				update_option( 'flush_refuse2lose_cpt', $code );
			}

			// Simplify the UI.
			new Refuse2Lose_Simple_CPT_UI( array(
				'post_type'            => 'refuse2lose',
				'remove_slugs'         => true, // Remove edit slug UI.
				'remove_known_plugins' => true, // Remove some known plugins.
				'remove_yoast_metabox' => true, // Remove Yoast Metabox
			) );
		}
	} // Refuse2Lose_CPT
}
