<?php
/**
 * widgets.php
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
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
* Returns the current widget settings for the theme in an array.
* @return array of current widget settings for the theme
*/
function widgets_plugin_get_widget_settings() {
	// have to get it always so that they are updated when changing widget settings in admin
	return Widgets_Plugin_Options::get_option( WIDGETS_PLUGIN_WIDGET_SETTINGS );
}

/**
 * Set/update widget settings.
 * No need to set the widget setting global here,
 * It's done once when the settings are retrieved.
 * @param array $settings for all widgets
 */
function widgets_plugin_set_widget_settings( $settings ) {
	Widgets_Plugin_Options::update_option( WIDGETS_PLUGIN_WIDGET_SETTINGS, $settings );
}

// Register our callback which takes care of wrapping modifications around widgets.
add_action( 'wp_head', 'widgets_plugin_widget_alter' );

/**
* Widget customization callback.
* Alters widgets to add our customized options.
*/
function widgets_plugin_widget_alter() {
	global $wp_registered_widgets;
	foreach ( $wp_registered_widgets as $id => $widget ) {
		if ( !isset( $wp_registered_widgets[$id]['original_callback'] ) ) {
			array_push( $wp_registered_widgets[$id]['params'], $id );
			$wp_registered_widgets[$id]['original_callback'] = $wp_registered_widgets[$id]['callback'];
			$wp_registered_widgets[$id]['callback'] = 'widgets_plugin_widget_alter_callback';
		}
	}
}

/**
 * Widget customization callback.
 * Wraps itself around the original callback and prints a widget.
 */
function widgets_plugin_widget_alter_callback() {
	global $wp_registered_widgets, $wp_reset_query_is_done;
	$params = func_get_args();
	$id = array_pop( $params );
	$original_callback = $wp_registered_widgets[$id]['original_callback'];
	$wp_registered_widgets[$id]['callback'] = $original_callback;

	$widget_settings = widgets_plugin_get_widget_settings();

	// don't bother with anything if this widget is not to be displayed on this page
	$condition = isset( $widget_settings['widgets'][$id]['display']['condition'] ) ? $widget_settings['widgets'][$id]['display']['condition'] : null;
	$pages = isset( $widget_settings['widgets'][$id]['display']['pages'] ) ? $widget_settings['widgets'][$id]['display']['pages'] : '';
	$show = Widgets_Control_Conditions::evaluate_display_condition( $condition, $pages );
	if ( ! $show ) {
		return;
	}

	ob_start();
	call_user_func_array( $original_callback, $params );
	$widget_content = ob_get_contents();
	ob_end_clean();

	echo apply_filters( 'widgets_plugin_widget_content', $widget_content, $id );
}
