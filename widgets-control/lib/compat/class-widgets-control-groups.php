<?php
/**
 * class-widgets-control-groups.php
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
 * @since 2.1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Used to check access restrictions set on a content block.
 */
class Widgets_Control_Groups {

	/**
	 * Adds a filter to decide whether to render output or not of a content block based on Groups' access restrictions.
	 */
	public static function init() {
		add_filter( 'widgets_control_content_render_output', array( __CLASS__, 'widgets_control_content_render_output' ), 10, 2 );
	}

	/**
	 * Returns false when the current user cannot read the post due to Groups access restrictions set.
	 *
	 * @param boolean $render whether to render the post
	 * @param int $post_id
	 *
	 * @return boolean
	 */
	public static function widgets_control_content_render_output( $render, $post_id ) {
		$user_can_read_post = true;
		if ( class_exists( 'Groups_Post_Access' ) && method_exists( 'Groups_Post_Access', 'user_can_read_post' ) ) {
			$user_can_read_post = Groups_Post_Access::user_can_read_post( $post_id, get_current_user_id() );
		}
		return $user_can_read_post;
	}
}
Widgets_Control_Groups::init();
