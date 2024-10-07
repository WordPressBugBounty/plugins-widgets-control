<?php
/**
 * class-widgets-control-admin-content.php
 *
 * Copyright (c) www.itthinx.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package widgets-control
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a convenience button to posts to insert content blocks.
 */
class Widgets_Control_Admin_Content {

	/**
	 * Hook on admin_head.
	 */
	public static function init() {
		add_action( 'admin_head', array( __CLASS__, 'admin_head' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
	}

	/**
	 * Registers our hooks on media_buttons and admin_footer for any post type except our content post type.
	 */
	public static function admin_head() {
		if ( self::show_button() ) {
			add_action( 'media_buttons', array( __CLASS__, 'media_buttons' ) );
			add_action( 'admin_footer', array( __CLASS__, 'admin_footer' ) );
		}
	}

	/**
	 * Adds the Pro upgrade invite meta box to our content type add/edit admin screens.
	 *
	 * @param string $post_type
	 * @param WP_Post $post
	 */
	public static function add_meta_boxes( $post_type, $post = null ) {
		if ( $post_type === Widgets_Control_Content::get_post_type() ) {
			add_meta_box(
				'wctrl-pro',
				'Widgets Control Pro',
				array( __CLASS__, 'add_meta_box' ),
				null,
				'side',
				'low'
			);
		}
	}

	/**
	 * Renders the content of our Pro upgrade invite meta box.
	 *
	 * @param WP_Post $post
	 * @param array $box
	 */
	public static function add_meta_box( $post = null, $box = null ) {
		echo '<p>';
		echo __( 'Get <a href="http://www.itthinx.com/shop/widgets-control-pro/"><strong>Widgets Control Pro</strong></a> which supports flexible custom sidebars and conditions based on page hierarchies, post types, roles and <a href="http://wordpress.org/plugins/groups/">groups</a>.', 'widgets-control' );
		echo '</p>';
	}

	/**
	 * Calls add_thickbox().
	 */
	public static function admin_enqueue_scripts() {
		if ( self::show_button() ) {
			add_thickbox();
		}
	}

	/**
	 * Whether to show our button for the current screen.
	 *
	 * @return boolean
	 */
	private static function show_button() {
		global $current_screen, $pagenow;
		return
			is_admin() &&
			isset( $current_screen ) &&
			!empty( $current_screen->post_type ) &&
			!in_array( $current_screen->post_type, self::exclude_button_from_post_types() ) &&
			isset( $pagenow ) &&
			( $pagenow == 'post.php' || $pagenow == 'post-new.php' );
	}

	/**
	 * Whether we are on a content block add or edit admin page.
	 * @return boolean
	 */
	private static function is_content_edit() {
		global $current_screen, $pagenow;
		return
			is_admin() &&
			isset( $current_screen ) &&
			!empty( $current_screen->post_type ) &&
			( $current_screen->post_type == Widgets_Control_Content::get_post_type() ) &&
			isset( $pagenow ) &&
			( $pagenow == 'post.php' || $pagenow == 'post-new.php' );
	}

	/**
	 * Returns an array of post types where the button to add a content block should not be shown.
	 * @return mixed[]|mixed
	 */
	private static function exclude_button_from_post_types() {
		$post_types = array( Widgets_Control_Content::get_post_type() );
		if ( class_exists( 'Widgets_Control_Sidebar' ) ) {
			$post_types[] = Widgets_Control_Sidebar::get_post_type();
		}
		$_post_types = apply_filters( 'widgets_control_exclude_content_button_from_post_types', $post_types );
		if ( is_array( $_post_types ) ) {
			$post_types = $_post_types;
		}
		return $post_types;
	}

	/**
	 * Renders the insert button.
	 */
	public static function media_buttons() {
		$output = sprintf(
			'<a class="button thickbox wctrl-insert-content" title="%s" href="%s"><span class="wctrl-icon"></span>',
			__( 'Click to choose and insert an existing content block.', 'widgets-control' ),
			esc_url( '#TB_inline?width=320&height=320&inlineId=wctrl-insert-content' )
		);
		$output .= __( 'Add Content Block', 'widgets-control' );
		$output .= '</a>';
		echo $output;
	}

	/**
	 * Renders the form to add a content block.
	 */
	public static function admin_footer() {

		$posts = get_posts( array(
			'numberposts'      => -1,
			'post_type'        => Widgets_Control_Content::get_post_type(),
			'suppress_filters' => true,
			'order'            => 'ASC',
			'orderby'          => 'title'
		) );

		$output = '<script type="text/javascript">';
		$output .= 'function widgetsControlInsertContentShortcode() {';
		$output .= 'var post_id = jQuery("#content-post-id").val(), show_title = jQuery("#content-show-title").is(":checked"), show_post_thumbnail = jQuery("#content-show-post-thumbnail").is(":checked"), extra="";';
		$output .= sprintf( 'if ( post_id === "" ) { alert("%s"); return; }', __( 'Please choose a content block.', 'widgets-control' ) );
		$output .= 'if ( show_title ) { extra += " show_title=\"yes\" "; }';
		$output .= 'if ( show_post_thumbnail ) { extra += " show_post_thumbnail=\"yes\" "; }';
		$output .= 'window.send_to_editor("[widgets_control_content id=\""+post_id+"\""+extra+"]");';
		$output .= '}';
		$output .= '</script>';
		$output .= '<div id="wctrl-insert-content" style="display: none;">';

		// post ID
		$post_id = !empty( $_REQUEST['content_post_id'] ) ? intval( $_REQUEST['content_post_id'] ) : null;
		$output .= '<p>';
		$output .= sprintf( '<label title="%s" >', __( 'Content Block', 'widgets-control' ) );
		$output .= __( 'Choose the content to display', 'widgets-control' );
		$output .= '<select class="widefat" id="content-post-id" name="content_post_id">';
		$output .= sprintf(
			'<option value="" %s>%s</option>',
			empty( $post_id ) ? ' selected="selected" ' : '',
			__( 'None', 'widgets-control' )
		);
		foreach ( $posts as $post ) {
			$output .= sprintf(
				'<option value="%d" %s>%s [#%d]</option>',
				intval( $post->ID ),
				$post_id == $post->ID ? ' selected="selected" ' : '',
				esc_attr( get_the_title( $post->ID ) ),
				intval( $post->ID )
			);
		}
		$output .= '</select>';
		$output .= '</label>';
		$output .= '</p>';

		// show_title
		$show_title = !empty( $_REQUEST['content_show_title'] );
		$output .= '<p>';
		$output .= sprintf( '<label title="%s">', __( 'Whether to show the title of the Content Block.', 'widgets-control' ) );
		$output .= '<input id="content-show-title" type="checkbox" ' . $show_title . ' value="1" name="content_show_title" />';
		$output .= __( 'Show the Content Block\'s title', 'widgets-control' );
		$output .= '</label>';
		$output .= '</p>';

		// show_post_thumbnail
		$show_post_thumbnail = !empty( $_REQUEST['content_show_post_thumbnail'] );
		$output .= '<p>';
		$output .= sprintf( '<label title="%s">', __( 'Whether to show the featured image of the Content Block.', 'widgets-control' ) );
		$output .= '<input id="content-show-post-thumbnail" type="checkbox" ' . $show_post_thumbnail . ' value="1" name="content_show_post_thumbnail" />';
		$output .= __( 'Show the Content Block\'s featured image', 'widgets-control' );
		$output .= '</label>';
		$output .= '</p>';

		// submit
		$output .= '<p>';
		$output .= sprintf(
			'<input type="button" class="button button-primary" value="%s" onclick="widgetsControlInsertContentShortcode();"',
			esc_attr( __( 'Insert', 'widgets-control' ) )
		);
		$output .= '</p>';

		$output .= '<p>';
		$output .= __( 'Get <a href="http://www.itthinx.com/shop/widgets-control-pro/"><strong>Widgets Control Pro</strong></a> which supports flexible custom sidebars and conditions based on page hierarchies, post types, roles and <a href="http://wordpress.org/plugins/groups/">groups</a>.', 'widgets-control' );
		$output .= '</p>';

		$output .= '</div>';
		echo $output;
	}
}
Widgets_Control_Admin_Content::init();
