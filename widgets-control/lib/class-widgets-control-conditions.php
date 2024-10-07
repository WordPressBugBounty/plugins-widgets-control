<?php
/**
 * class-widgets-control-conditions.php
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
 * @since 1.4.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Condition Engine
 */
class Widgets_Control_Conditions {

	/**
	 * Find out if the current page is $page. If $page is empty we consider that we ask for the current page which will always be true.
	 * Note that is_page() is of no use for us as it will only consider the condition to be met if
	 * $wp_query->is_page ...
	 *
	 * @param mixed $page Page ID, title, slug, or array of those
	 * @return true if $page is empty or $page is the ID, title, slug, ... of the current page
	 */
	public static function is_page( $page = '' ) {
		global $post;
		$result = false;
		if ( empty( $page ) ) {
			$result = true;
		} else if ( !empty( $post ) ) {
			if (
				( is_single() || is_page() ) &&
				(
					is_numeric( $page ) && ( $post->ID == $page ) ||
					( $post->post_title == $page )
				)
			) {
				$result = true;
			} else {
				$permalink = get_permalink( $post->ID );
				$prefix = home_url();
				$slug = substr( $permalink, strlen( $prefix ) );
				$slug = ltrim( rtrim( $slug, '/' ), '/' );
				$page = ltrim( rtrim( $page, '/' ), '/' );
				if ( $slug == $page ) {
					$result = true;
				}
			}
		}
		return $result;
	}

	/**
	 * Determines TRUE if the current page matches. Used to show or not sidebars and widgets on a page.
	 * @param string $condition evaluation condition
	 * @param string $pages pages expression
	 */
	public static function evaluate_display_condition( $condition, $pages ) {
		global $post;
		$show = false;
		if ( empty( $condition ) || !$condition || ( $condition == WIDGETS_PLUGIN_SHOW_ON_ALL_PAGES ) ) {
			$show = true;
		}
		if ( ! $show ) {
			$pages = explode( "\n", $pages ); // must use "
			if ( is_array( $pages ) ) {
				$pages = array_map( 'trim', $pages );
				// filter out sizes
				$pages = preg_replace( '/\[(large|medium|small).*\]/', '', $pages, -1, $count_sizes );
				$pages = array_filter( $pages, 'strlen' );
				// if nothing but sizes were given we already have a match
				if ( count( $pages ) === 0 && $count_sizes > 0 ) {
					$show = true;
				}
			}
		}
		if ( ! $show ) {
			$matches = array_map( array( __CLASS__, 'match' ), $pages );
			$matches[] = false;
			$result = in_array( true, $matches, true );
			switch ( $condition ) {
				case WIDGETS_PLUGIN_SHOW_ON_THESE_PAGES :
					$show = $result;
					break;
				case WIDGETS_PLUGIN_SHOW_NOT_ON_THESE_PAGES :
					$show = ! $result;
					break;
			}
		}
		return $show;
	}

	/**
	 * Check if the current page is a match.
	 *
	 * @param string $page token, title, ID, ...
	 * @return boolean returns true if the current page matches
	 */
	public static function match( $page ) {
		$page = trim( $page );
		$match = false;
		// token?
		if ( ( strpos( $page, '[' ) === 0 ) && ( strrpos( $page, ']' ) === strlen( $page ) - 1 ) ) {
			// strip tokenizers
			$page = trim ( substr( $page, 1, strlen( $page ) - 2 ) );
			// decompose
			$page_params = explode( ':', $page );
			$token = isset( $page_params[0] ) ? trim( $page_params[0] ) : null;
			$value = isset( $page_params[1] ) ? trim( $page_params[1] ) : null;
			$value2 = isset( $page_params[2] ) ? trim( $page_params[2] ) : null;
			switch ( $token ) {
				case 'home' :
					$match = is_home();
					break;
				case 'front' :
					$match = is_front_page();
					break;
				case 'single' :
					$match = is_single();
					break;
				case 'page' :
					$match = is_page();
					break;
				case 'category' :
					if ( ! empty( $value ) ) {
						$match = is_category( $value );
					} else {
						$match = is_category();
					}
					break;
				case 'has_term' :
					if ( !empty( $value ) ) {
						if ( !empty( $value2 ) ) {
							$match = has_term( $value, $value2 );
						} else {
							$match = has_term( $value, 'category' ) || has_term( $value, 'post_tag' );
						}
					}
					break;
				case 'tag' :
					if ( ! empty ( $value ) ) {
						$match = is_tag( $value );
					} else {
						$match = is_tag();
					}
					break;
				case 'tax' :
					if ( ! empty( $value ) ) {
						if ( empty( $value2 ) ) {
							switch( $value ) {
								case 'category' :
									$match = is_category();
									break;
								case 'tag' :
									$match = is_tag();
									break;
								default :
									$match = is_tax( $value );
							}
						} else {
							switch( $value ) {
								case 'category' :
									$match = is_category( $value2 );
									break;
								case 'tag' :
									$match = is_tag( $value2 );
									break;
								default :
									$match = is_tax( $value, $value2 );
							}
						}
					} else {
						$match = is_tax() || is_category() || is_tag();
					}
					break;
				case 'author' :
					if ( ! empty( $value ) ) {
						$match = is_author( $value );
					} else {
						$match = is_author();
					}
					break;
				case 'archive' :
					$match = is_archive();
					break;
				case 'search' :
					$match = is_search();
					break;
				case '404' :
					$match = is_404();
					break;
				case 'language' :
					if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
						$languages = array_map( 'trim', explode( ',', $value ) );
						if ( in_array( ICL_LANGUAGE_CODE, $languages ) ) {
							$match = true;
						}
					}
					break;
			}
		} else {
			$page = trim( $page );
			$match = self::is_page( $page );
		}
		return $match;
	}

}
