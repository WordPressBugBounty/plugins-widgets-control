<?php
/**
 * Plugin Name: Widgets Control
 * Plugin URI: https://www.itthinx.com/
 * Description: A Widget toolbox that adds visibility management and helps to control where widgets and sidebars are shown efficiently.
 * Version: 2.5.0
 * Author: itthinx
 * Author URI: https://www.itthinx.com
 * Donate-Link: https://www.itthinx.com/shop/widgets-control-pro/
 * Text Domain: widgets-control
 * Domain Path: /languages
 * License: GPLv3
 *
 * widgets-control.php
 *
 * Copyright (c) 2015 - 2020 "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is released under the GNU General Public License Version 3.
 * The following additional terms apply to all files as per section
 * "7. Additional Terms." See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * All legal, copyright and license notices and all author attributions
 * must be preserved in all files and user interfaces.
 *
 * Where modified versions of this material are allowed under the applicable
 * license, modified version must be marked as such and the origin of the
 * modified material must be clearly indicated, including the copyright
 * holder, the author and the date of modification and the origin of the
 * modified material.
 *
 * This material may not be used for publicity purposes and the use of
 * names of licensors and authors of this material for publicity purposes
 * is prohibited.
 *
 * The use of trade names, trademarks or service marks, licensor or author
 * names is prohibited unless granted in writing by their respective owners.
 *
 * Where modified versions of this material are allowed under the applicable
 * license, anyone who conveys this material (or modified versions of it) with
 * contractual assumptions of liability to the recipient, for any liability
 * that these contractual assumptions directly impose on those licensors and
 * authors, is required to fully indemnify the licensors and authors of this
 * material.
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

function widgets_plugin_set() {
	define( 'WIDGETS_PLUGIN_VERSION', '2.5.0' );
	define( 'WIDGETS_PLUGIN_NAME',     'widgets-control' );
	define( 'WIDGETS_PLUGIN_DOMAIN',   'widgets-control' );
	define( 'WIDGETS_PLUGIN_FILE',     __FILE__ );
	define( 'WIDGETS_PLUGIN_BASENAME', plugin_basename( WIDGETS_PLUGIN_FILE ) );
	define( 'WIDGETS_PLUGIN_DIR',      plugin_dir_path( __FILE__ ) );
	define( 'WIDGETS_PLUGIN_LIB',      WIDGETS_PLUGIN_DIR . 'lib' );
	define( 'WIDGETS_PLUGIN_URL',      plugins_url( 'widgets-control' ) );
}

/**
 * Widget Plugin main class.
 */
class Widgets_Plugin {

	/**
	 * Plugin setup.
	 */
	public static function init() {
		if ( !defined( 'WIDGETS_PLUGIN_VERSION' ) ) {
			widgets_plugin_set();
			add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ) );
			add_action( 'init', array( __CLASS__, 'wp_init' ) );
			add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
			require_once WIDGETS_PLUGIN_LIB . '/includes/constants.php';
			require_once WIDGETS_PLUGIN_LIB . '/includes/functions.php';
			require_once WIDGETS_PLUGIN_LIB . '/conditions.php';
			require_once WIDGETS_PLUGIN_LIB . '/class-widgets-control-conditions.php';
			require_once WIDGETS_PLUGIN_LIB . '/class-widgets-plugin-options.php';
			require_once WIDGETS_PLUGIN_LIB . '/widgets.php';
			if ( is_admin() ) {
				require_once WIDGETS_PLUGIN_LIB . '/admin/class-widgets-plugin-admin.php';
				require_once WIDGETS_PLUGIN_LIB . '/admin/class-widgets-plugin-admin-settings.php';
				require_once WIDGETS_PLUGIN_LIB . '/admin/class-widgets-control-admin-content.php';
			}
			require_once WIDGETS_PLUGIN_LIB . '/class-widgets-plugin-cache.php';
			require_once WIDGETS_PLUGIN_LIB . '/class-widgets-control-sidebars.php';
			require_once WIDGETS_PLUGIN_LIB . '/class-widgets-control-shortcodes.php';
			require_once WIDGETS_PLUGIN_LIB . '/class-widgets-control-content.php';
			require_once WIDGETS_PLUGIN_LIB . '/class-widgets-control-content-widget.php';
			require_once WIDGETS_PLUGIN_LIB . '/class-widgets-control-media.php';
		}
	}

	/**
	 * Loads compatibility resources.
	 */
	public static function plugins_loaded() {
		if ( class_exists( 'Groups_Post_Access' ) && method_exists( 'Groups_Post_Access', 'user_can_read_post' ) ) {
			require_once WIDGETS_PLUGIN_LIB . '/compat/class-widgets-control-groups.php';
		}
		if ( class_exists( 'Elementor\Plugin' ) ) {
			require_once WIDGETS_PLUGIN_LIB . '/compat/class-widgets-control-elementor.php';
		}
	}

	/**
	 * Hooked on the init action, loads translations.
	 */
	public static function wp_init() {
		load_plugin_textdomain( WIDGETS_PLUGIN_DOMAIN, null, 'widgets-control/languages' );
		// load the notice class
		if ( is_admin() ) {
			if ( current_user_can( 'activate_plugins' ) ) { // important: after init hook
				require_once WIDGETS_PLUGIN_LIB . '/admin/class-widgets-control-admin-notice.php';
			}
		}
	}

	/**
	 * Hooks into admin_init to register our CSS.
	 */
	public static function admin_init() {
		wp_register_style( 'widgets_control_dashboard', WIDGETS_PLUGIN_URL . '/css/dashboard.css', array(), WIDGETS_PLUGIN_VERSION );
	}

	/**
	 * Enqueues our dashboard CSS everywhere (for our content post type menu's icon).
	 */
	public static function admin_enqueue_scripts() {
		wp_enqueue_style( 'widgets_control_dashboard' );
	}
}
Widgets_Plugin::init();
