<?php
/**
 * functions.php
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
 * @since 2.0.1
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders a content block and returns corresponding HTML.
 *
 * You must provide the content block's ID, slug or title in the $atts array.
 *
 * content : (int | string) pass the post ID or the title to identify the content block that should be displayed
 * id : (int) pass the post ID of the content block to be displayed
 * slug : (string) pass the post slug of the content block to be displayed
 * filter_the_content : (boolean | string) whether the the_content filter should be applied
 * filter_the_title : (boolean | string) whether the the_title filter should be applied
 * suppress_filters : (boolean | string) whether the filters should be suppressed while obtaining the content block
 * show_title : (boolean | string) whether to display the title of the content block
 * show_post_thumbnail : (boolean | string) whether to display the featured image of the content block
 * show_content : (boolean | string) whether to display the content of the content block
 *
 * The (boolean | string) attributes allow these values: true false 'true' 'false' 'yes' 'no'.
 *
 * For example:
 *
 * <code>echo widgets_control_content( array( 'id' => 123 ) );</code>
 *
 * @param array $atts must provide at least 'id', 'content' or 'slug'
 *
 * @return string content HTML
 */
function widgets_control_content( $atts = null ) {
	$output = '';
	if ( isset( $atts['id'] ) || isset( $atts['content'] ) || isset( $atts['slug'] ) ) {
		$output = Widgets_Control_Content::get_content( $atts );
	}
	return $output;
}
