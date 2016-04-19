<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Refuse2Lose_Shortcodes' ) ) {
	/**
	 * Shortcodes.
	 *
	 * @since  1.0.0
	 */
	class Refuse2Lose_Shortcodes {
		/**
		 * Construct
		 *
		 * @since  1.0.0
		 */
		function __construct() {
			add_shortcode( 'refuse2lose_add_record', array( $this, 'add_record' ) ); // Add record shortcode.
			add_action( 'template_redirect', array( $this, 'prepare_new_record' ) ); // Make a new record.
		}

		/**
		 * Create a post for a new record, and reload the script with the ID of that post.
		 *
		 * @since  1.0.0
		 */
		public function prepare_new_record() {
			global $post;

			// Only if this thing has our shortcode.
			if ( ! has_shortcode( $post->post_content, 'refuse2lose_add_record' ) ) {
				return;
			}

			// If we're loading the page for the first time.
			if ( ! isset( $_GET['record_id'] ) ) {

				// Insert an empty record to save this to.
				$post_id = wp_insert_post( array(
					'post_type'   => 'refuse2lose',
					'post_title'  => date( "l, F j, Y, g:i a" , time() ),

					// Starts out as a trash post so if the user doesn't submit, it's in the trash.
					'post_status' => 'trash',
				) );

				// Tell the script the id of that post.
				wp_redirect( "?record_id={$post_id}" );
			}
		}

		/**
		 * Add a record.
		 *
		 * @since  1.0.0
		 */
		public function add_record() {
			$post_id = isset( $_GET['record_id'] ) ? absint( $_GET['record_id'] ) : false;
			$post = get_post( $post_id );

			// See if we can find who won.
			$_who_are_you = isset( $_POST['_who_are_you' ] ) ? $_POST['_who_are_you'] : false;
			if ( $_who_are_you ) {
				$user = get_userdata( $_who_are_you );
				$_who_are_you = $user->display_name;
			}

			// If no post id or the post does not exist.
			if ( ! $post ) { ?>
				<p><?php _e( "Sorry, something went wrong. <a href='?try-again'>Click here</a> and let's try again.", 'refuse2lose' ); ?></p>
			<?php return; }

			// They saved the record.
			if ( isset( $_POST['submit-cmb'] ) && apply_filters( 'refuse2lose_show_notices', true ) ) :
				// Change the post status, we're saving it.
				wp_update_post( array(
					'ID'          => $post_id,
					'post_status' => 'publish',
				) ); ?>
					<p class="notice">
						<?php _e( 'The record has been saved. <a href="?new-record">Click here</a> to create a new one.', 'refuse2lose' ); ?>
					</p>
			<?php endif;

			// Otherwise, show the form.
			ob_start(); ?>
				<h2 class="record-title"><?php echo apply_filters( 'refuse2lose_record_title', get_the_title( $post_id ) ); ?></h2>
				<?php cmb2_metabox_form( '_refuse2lose_fields', $post_id ); ?>
			<?php return ob_get_clean();
		}
	} // Refuse2Lose_Shortcodes
}
