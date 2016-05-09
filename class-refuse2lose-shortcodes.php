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
		 * The members.
		 *
		 * @since  1.0.0
		 * @var array
		 */
		private $members_list = array();

		/**
		 * The fields.
		 *
		 * @since  1.0.0
		 * @var array
		 */
		private $fields = array();

		/**
		 * Constructor.
		 *
		 * @since  1.0.0
		 */
		function __construct( $members_list, $fields ) {
			$this->members_list = $members_list; // Store the members.
			$this->fields       = $fields; // Store the fields we're using.

			// Adding a record.
			add_shortcode( 'refuse2lose_add_record', array( $this, 'add_record' ) ); // Add record shortcode.
			add_action( 'template_redirect', array( $this, 'prepare_new_record' ) ); // Make a new record.

			// Showing the ladder.
			add_shortcode( 'refuse2lose_ladder', array( $this, 'ladder' ) );
		}

		/**
		 * Get the rankings.
		 *
		 * @since  1.0.0
		 *
		 * @return array Rankings Data; The rankings and ties that will
		 *               be removed from array_flip() with the same points.
		 */
		private function get_rankings() {

			// Go through each user and get the total records.
			foreach ( $this->members_list as $user_id => $display_name ) {

				// Get the records for this user.
				$records = get_posts( array(
					'post_type'      => 'refuse2lose',
					'post_status'    => 'publish',
					'fields'         => 'ids',
					'posts_per_page' => 5000,

					// Only records where the user was selected as the person who won.
					'meta_query' => array(
						array(
							'key'   => '_who_are_you',
							'value' => $user_id,
						),
					),
				) );

				foreach ( $records as $record_id ) {
					foreach ( $this->fields as $field ) {
						if ( isset( $field['points'] ) ) {

							// For each field value as points.
							foreach ( $field['points'] as $value => $points ) {
								$option_value = get_post_meta( $record_id, $field['id'], true );

								if ( $option_value == $value ) {

									// Count the points.
									$rankings[ $user_id ][ $record_id ] = $points;
								}
							}
						}
					}
				} // foreach records

			} // foreach user

			if ( ! isset( $rankings ) ) {
				return array(); // No rankings.
			}

			// Add the points.
			foreach ( $rankings as $user_id => $record ) {
				foreach ( $record as $record_id => $points ) {

					// The user.
					$user = $this->members_list[ $user_id ];

					// Get the user's name.
					$users_nicename = sanitize_title_with_dashes( $user );

					/*
					 * The organizing key is the points and the user's name.
					 */
					$org_key = "{$points}-{$users_nicename}";

					// Add the points.
					$the_points[ $org_key ]['points']    = $the_points[ $org_key ] + $points;
					$the_points[ $org_key ]['user_id']   = $user_id;
				}
			}

			// Sort by points and users.
			ksort( $the_points, SORT_REGULAR );

			// Large points on top.
			$the_points = array_reverse( $the_points );

			// The ranking should be sorted by points.
			return $the_points;
		}

		/**
		 * A row of ladder data.
		 *
		 * @since  1.0.0
		 *
		 * @param  int $rank    The rank number.
		 * @param  int $user_id The user ID.
		 * @param  int $points  The points they got.
		 */
		private function the_row( $rank, $user_id, $points ) {
			$user = get_userdata( $user_id ); ?>
				<tr>
					<td><?php echo absint( $rank ); ?></td>
					<td><?php echo esc_html( $user->display_name ); ?></td>
					<td><?php echo absint( $points ); ?></td>

					<?php do_action( 'refuse2lose_ladder_columns' ); ?>
				</tr>
			<?php
		}

		public function ladder() {

			// Get the rankings.
			$data     = $this->get_rankings();

			$rankings = $data['points']; // Rankings w/ ties removed.
			$ties     = $data['ties']; // All the ties.

			// Starting point for rank.
			$rank = 1;

			ob_start(); ?>

				<table class="refuse2lose-ladder">
					<thead>
						<th><?php _e( 'Rank', 'refuse2lose' ); ?></th>
						<th><?php _e( 'Name', 'refuse2lose' ); ?></th>
						<th><?php _e( 'Points', 'refuse2lose' ); ?></th>

						<?php do_action( 'refuse2lose_ladder_headers' ); ?>
					</thead>
					<tbody>
						<?php foreach ( $rankings as $points => $user_id ) : ?>
							<!-- Normal Rank -->
							<?php $this->the_row( $rank, $user_id, $points ); ?>

							<!-- Ties -->
							<?php foreach ( $ties as $tied_user_id => $tied_points ) : ?>
								<?php if ( $points == $tied_points ) : // If they got the same points as this user. ?>
									<?php $this->the_row( $rank, $tied_user_id, $tied_points ); ?>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php $rank++; endforeach; ?>
					</tbody>
				</table>

			<?php return ob_get_clean();
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
