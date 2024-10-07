<?php
/**
 * class-widgets-control-media.php
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
 * Handles the conditions to show or hide depending on the device size,
 * using the @media queries defined in our widgets-control.css
 */
class Widgets_Control_Media {

	/**
	 * Adds the dynamic_sidebar_params filter and enqueues our CSS.
	 */
	public static function init() {
		add_filter( 'dynamic_sidebar_params', array( __CLASS__, 'dynamic_sidebar_params' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
	}

	/**
	 * Registers our widgets-control.css stylesheet.
	 */
	public static function wp_enqueue_scripts() {
		wp_register_style( 'widgets-control', WIDGETS_PLUGIN_URL . '/css/widgets-control.css', array(), WIDGETS_PLUGIN_VERSION );
	}

	/**
	 * Filters the widgets to add classes required for our @media queries as needed.
	 * @param array $params
	 * @return array
	 */
	public static function dynamic_sidebar_params( $params ) {
		$widget_params = isset( $params[0] ) ? $params[0] : null;
		$id            = isset( $params[2] ) ? $params[2] : null;
		if ( $id !== null && $widget_params !== null ) {
			$widget_settings = widgets_plugin_get_widget_settings();
			$suffix = null;
			$condition = isset( $widget_settings['widgets'][$id]['display']['condition'] ) ? $widget_settings['widgets'][$id]['display']['condition'] : null;
			switch ( $condition ) {
				case WIDGETS_PLUGIN_SHOW_ON_ALL_PAGES :
				case WIDGETS_PLUGIN_SHOW_ON_THESE_PAGES :
					$suffix = 'show';
					break;
				case WIDGETS_PLUGIN_SHOW_NOT_ON_THESE_PAGES :
					$suffix = 'hide';
					break;
			}
			if ( $suffix !== null ) {
				$pages = isset( $widget_settings['widgets'][$id]['display']['pages'] ) ? $widget_settings['widgets'][$id]['display']['pages'] : '';
				$pages = explode( "\n", $pages ); // must use "
				if ( is_array( $pages ) ) {
					$sizes = array();
					$pages = array_map( 'trim', $pages );
					foreach ( $pages as $page ) {
						if ( ( strpos( $page, '[' ) === 0 ) && ( strrpos( $page, ']' ) === strlen( $page ) - 1 ) ) {
							// strip tokenizers
							$page = trim ( substr( $page, 1, strlen( $page ) - 2 ) );
							// decompose
							$page_params = explode( ':', $page );
							$token = isset( $page_params[0] ) ? trim( $page_params[0] ) : null;
							$value = isset( $page_params[1] ) ? trim( $page_params[1] ) : null;
							$value2 = isset( $page_params[2] ) ? trim( $page_params[2] ) : null;
							switch( $token ) {
								case 'small' :
								case 'medium' :
								case 'large' :
									$sizes[] = $token;
									break;
							}
						}
					}
					$classes = array();
					foreach ( $sizes as $size ) {
						$classes[] = 'widgets-control-' . $size . '-' . $suffix;
					}
					if ( count( $classes )  > 0 ) {
						wp_enqueue_style( 'widgets-control' );
						$params[0]['before_widget'] = str_replace(
							'class="',
							' class="' . implode( ' ', $classes ). ' ',
							$params[0]['before_widget']
						);
					}
				}
			}
		}
		return $params;
	}

	/**
	 * Returns the classes for our @media queries based on the string containing tokens separated by "\n".
	 * <code>wp_enqueue_style( 'widgets-control' );</code> must be called to use it if the result returned is not empty.
	 *
	 * @param int $visibility WIDGETS_PLUGIN_SHOW_ON_ALL_PAGES, WIDGETS_PLUGIN_SHOW_ON_THESE_PAGES or WIDGETS_PLUGIN_SHOW_NOT_ON_THESE_PAGES
	 * @param string $tokens a string containing tokens separated by "\n"
	 * @return string CSS classes or empty string if none apply
	 */
	public static function get_class_from_tokens( $visibility, $tokens ) {
		$result = '';
		$suffix = '';
		switch ( $visibility ) {
			case WIDGETS_PLUGIN_SHOW_ON_ALL_PAGES :
			case WIDGETS_PLUGIN_SHOW_ON_THESE_PAGES :
				$suffix = 'show';
				break;
			case WIDGETS_PLUGIN_SHOW_NOT_ON_THESE_PAGES :
				$suffix = 'hide';
				break;
		}
		$pages = explode( "\n", $tokens ); // must use "
		if ( is_array( $pages ) ) {
			$sizes = array();
			$pages = array_map( 'trim', $pages );
			foreach ( $pages as $page ) {
				if ( ( strpos( $page, '[' ) === 0 ) && ( strrpos( $page, ']' ) === strlen( $page ) - 1 ) ) {
					// strip tokenizers
					$page = trim ( substr( $page, 1, strlen( $page ) - 2 ) );
					// decompose
					$page_params = explode( ':', $page );
					$token = isset( $page_params[0] ) ? trim( $page_params[0] ) : null;
					$value = isset( $page_params[1] ) ? trim( $page_params[1] ) : null;
					$value2 = isset( $page_params[2] ) ? trim( $page_params[2] ) : null;
					switch( $token ) {
						case 'small' :
						case 'medium' :
						case 'large' :
							$sizes[] = $token;
							break;
					}
				}
			}
			$classes = array();
			foreach ( $sizes as $size ) {
				$classes[] = 'widgets-control-' . $size . '-' . $suffix;
			}
			if ( count( $classes )  > 0 ) {
				$result = implode( ' ', $classes );
			}
		}
		return $result;
	}
}
Widgets_Control_Media::init();
