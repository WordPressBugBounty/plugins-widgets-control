<?php
/**
 * class-widgets-plugin-cache.php
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
 * Widget update cache handler.
 */
class Widgets_Plugin_Cache {

	/**
	 * Flag used to avoid multiple clears.
	 * @var boolean
	 */
	private static $cleared = false;

	/**
	 * Sets up our stuff on admin_init.
	 */
	public static function init() {
		$auto_clear_cache = Widgets_Plugin_Options::get_option( WIDGETS_PLUGIN_AUTO_CLEAR_CACHE, WIDGETS_PLUGIN_AUTO_CLEAR_CACHE_DEFAULT );
		if ( $auto_clear_cache == 'yes' ) {
			add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		}
	}

	/**
	 * Adds actions to cover possible widget updates.
	 */
	public static function admin_init() {
		//add_action( 'wp_ajax_save_widget', array( __CLASS__, 'wp_ajax_save_widget' ) );
		//add_action( 'wp_ajax_widgets_order', array( __CLASS__, 'wp_ajax_widgets_order' ) );
		add_action( 'sidebar_admin_setup', array( __CLASS__, 'sidebar_admin_setup' ) );
		add_action( 'customize_save_after',  array( __CLASS__, 'customize_save_after' ) );
		add_action( 'updated_option', array( __CLASS__, 'updated_option' ), 10, 3 );
	}

	/**
	 * Update when widgets are updated (also when entering the widget admin
	 * but we skip that).
	 */
	public static function sidebar_admin_setup() {
		if (
			!empty( $_REQUEST ) &&
			is_array( $_REQUEST ) &&
			!empty( $_REQUEST['action'] ) &&
			( $_REQUEST['action'] == 'save-widget' )
		) {
			self::clear();
		}
	}

	/**
	 * Act on theme customization saved.
	 *
	 * @param WP_Customize_Manager $wp_customize_manager
	 */
	public static function customize_save_after( $wp_customize_manager ) {
		self::clear();
	}

	/**
	 * Covers widget ordering updates.
	 *
	 * @param string $option
	 * @param mixed $old_value
	 * @param mixed $value
	 */
	public static function updated_option( $option, $old_value, $value ) {
		if ( $option == 'sidebars_widgets' ) {
			self::clear();
		}
	}

	/**
	 * Clear the cache on supported caching systems.
	 */
	private static function clear() {
		if ( !self::$cleared ) {
			// W3TC
			if ( function_exists( 'w3tc_pgcache_flush' ) ) {
				w3tc_pgcache_flush();
			}
			// WP Super Cache
			if ( function_exists( 'wp_cache_clean_cache' ) ) {
				global $file_prefix;
				wp_cache_clean_cache( $file_prefix );
			}
			// WP Engine
			if ( class_exists( 'WpeCommon' ) ) {
				if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
					WpeCommon::purge_memcached();
				}
				if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
					WpeCommon::clear_maxcdn_cache();
				}
				if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
					WpeCommon::purge_varnish_cache();
				}
			}
			self::$cleared = true;
		}
	}
}
Widgets_Plugin_Cache::init();
