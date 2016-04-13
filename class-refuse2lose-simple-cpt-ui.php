<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Refuse2Lose_Simple_CPT_UI' ) ) :
	/**
	 * Simplifies a CPT UI.
	 *
	 * Create a new instance of this and you can
	 * make your CPT Publishing options
	 * less cluttered.
	 *
	 * @since  1.0.0
	 * @package Appirio
	 */
	class Refuse2Lose_Simple_CPT_UI {
		/**
		 * Arguments.
		 *
		 * @since  1.0.0
		 *
		 * @see  self::__construct()
		 * @var  array
		 */
		public $args = array();

		/**
		 * Construct.
		 *
		 * @since  1.0.0
		 *
		 * @param array $args Arguments {
		 *    @type string $post_type             The post type to apply this to.
		 *                                        Required. Default: false.
		 *
		 *    @type boolean $publish_2_save       Whether to rename Publish to Save
		 *                                        on the screen.
		 *                                        Default: true.
		 *
		 *    @type boolean $hide_actions         Whether or not to hide publish actions
		 *                                        like Password, date, etc.
		 *                                        Default: true.
		 *
		 *    @type boolean $remove_quick_edit    Whether to remove quick-edit
		 *                                        from bulk view.
		 *                                        Default: true.
		 *
		 *    @type boolean $remove_slugs         Remove the ability to edit slugs.
		 *                                        Default: false.
		 *
		 *    @type boolean $remove_known_plugins Remove some other known plugin
		 *                                        publish UI.
		 *                                        Default: false.
		 *
		 *    @type boolean	$remove_yoast_metabox Remove Yoast Metabox.
		 *                                        Default: false.
		 * }
		 */
		function __construct( $args = array() ) {
			$this->args = wp_parse_args( $args, array(
				'post_type'            => false,
				'publish_2_save'       => true, // Customize screen text.
				'hide_actions'         => true, // Hide publishing actions/options.
				'remove_quick_edit'    => true, // Remove quickedit.
				'remove_slugs'         => false, // Remove edit slug UI.
				'remove_known_plugins' => false, // Remove some known plugins.
				'remove_yoast_metabox' => false, // Remove Yoast Metabox
			) );

			if ( ! $this->args['post_type'] ) {
				return; // Don't do anything without a post type.
			}

			// Make our screen nice.
			if ( $this->args['publish_2_save'] )
				add_filter( "gettext", array( $this, 'publish_2_save' ), 20, 3 );

			if ( $this->args['hide_actions'] ) {
				add_action( "admin_head-post.php", array( $this, 'hide_actions' ) );
				add_action( "admin_head-post-new.php", array( $this, 'hide_actions' ) );
			}

			if ( $this->args['remove_yoast_metabox'] )
				add_action( 'admin_head', array( $this, 'remove_yoast_metabox' ) );

			if ( $this->args['remove_known_plugins'] )
				add_action( 'admin_head', array( $this, 'remove_known_plugins' ) );

			if ( $this->args['remove_slugs'] )
				add_action( 'admin_head', array( $this, 'remove_edit_slug_ui' ) );

			if ( $this->args['remove_quick_edit'] )
				add_filter( "post_row_actions", array( $this, 'remove_quick_edit' ), 10, 2 );
		}

		public function remove_known_plugins() {
			if( $this->args['post_type'] == get_post_type() ) : ?>
				<style>
					#major-publishing-actions #wpseo-score,
					#major-publishing-actions #traffic_light { display: none; }
				</style>
			<?php endif;
		}

		/**
		 * Remove Yoast Metabox
		 *
		 * @since  1.0.0
		 */
		public function remove_yoast_metabox() {
			if( $this->args['post_type'] == get_post_type() ) : ?>
				<style>
					.postbox-container #wpseo_meta { display: none; }
				</style>
			<?php endif;
		}

		/**
		 * Hides the slug edit UI.
		 *
		 * @since  1.0.0
		 */
		public function remove_edit_slug_ui() {
			if( $this->args['post_type'] == get_post_type() ) : ?>
				<style>
					#edit-slug-box { display: none; }
				</style>
			<?php endif;
		}

		/**
		 * Hides the publishing options since they aren't relevant here.
		 *
		 * @since  1.0.0
		 */
		public function hide_actions() {
			if( $this->args['post_type'] == get_post_type() ){
				echo '
					<!-- Hides the publishing options -->
					<style type="text/css">
						#misc-publishing-actions,
						#minor-publishing-actions {
							display:none;
						}
					</style>
				';
			}
		}

		/**
		 * Removes the Quick Edit from the bulk list options.
		 *
		 * @since  1.0.0
		 *
		 * @param  array $actions Default Actions
		 *
		 * @return array          Actions with any inline actions removed.
		 */
		public function remove_quick_edit( $actions ) {
			global $current_screen;

			if ( ! $current_screen ) {
				return;
			}

			// Only on this CPT
			if( $current_screen->post_type != $this->args['post_type'] ) {
				return $actions;
			}

			// Remove any inline actions (Quick Edit).
			unset( $actions['inline hide-if-no-js'] );

			return $actions;
		}

		/**
		 * Changes default text to text that makes more sense for this plugin.
		 *
		 * @since  1.0.0
		 *
		 * @param  string $translated_text The un-translated text.
		 * @param  string $text            The original translated text.
		 * @param  string $domain          The text domain.
		 *
		 * @return string                  Modified text.
		 */
		public function publish_2_save( $translated_text, $text, $domain ) {
			if ( $this->args['post_type'] == get_post_type() ) {
				switch ( $translated_text ) {
					case 'Publish' :
						$translated_text = __( 'Save', $domain );
						break;
					case 'Published' :
						$translated_text = __( 'Saved', $domain );
						break;
				}
			}
			return $translated_text;
		}
	}
endif;
