<?php
/**
 * class-widgets-plugin-admin-settings.php
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
 * This class handles the admin settings.
 */
class Widgets_Plugin_Admin_Settings {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_filter( 'plugin_action_links_'. plugin_basename( WIDGETS_PLUGIN_FILE ), array( __CLASS__, 'admin_settings_link' ) );
		self::maybe_save();
	}

	public static function admin_settings_link( $links ) {
		if ( current_user_can( 'manage_options' ) ) {
			$_links = array();
			$_links[] = '<a target="_blank" style="font-weight:bold;" href="https://www.itthinx.com/shop/widgets-control-pro/">' . __( 'Get Pro', 'widgets-control' ) . '</a>';
			$_links[] = '<a href="' . get_admin_url( null, 'plugins.php?page=widgets-plugin' ) . '">' . __( 'Settings', 'widgets-control' ) . '</a>';
			if ( Widgets_Control_Content::is_enabled() ) {
				$_links[] = '<a href="' . esc_url( add_query_arg( array( 'post_type' => Widgets_Control_Content::get_post_type() ), admin_url( 'edit.php' ) ) ) . '">' . __( 'Content Blocks', 'widgets-control' ) . '</a>';
			}
			$links = $_links + $links;
		}
		return $links;
	}

	public static function admin_menu() {
		add_plugins_page(
			__( 'Widgets Control', 'widgets-control' ),
			__( 'Widgets Control', 'widgets-control' ),
			'manage_options',
			'widgets-plugin',
			array( __CLASS__, 'settings' )
		);
	}

	public static function maybe_save() {
		global $pagenow;
		if ( is_admin() && isset( $pagenow ) && ( $pagenow == 'plugins.php' ) && isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'widgets-plugin' ) {
			if ( current_user_can( 'manage_options' ) ) {
				if ( isset( $_POST['action'] ) && ( $_POST['action'] == 'set' ) && wp_verify_nonce( $_POST['widgets-plugin-settings'], 'admin' ) ) {
					$enable_content = empty( $_POST['enable_content'] ) ? 'no' : 'yes';
					Widgets_Plugin_Options::update_option(
						WIDGETS_CONTROL_CONTENT_ENABLE,
						$enable_content
					);
					$auto_clear_cache = empty( $_POST['auto_clear_cache'] ) ? 'no' : 'yes';
					Widgets_Plugin_Options::update_option(
						WIDGETS_PLUGIN_AUTO_CLEAR_CACHE,
						$auto_clear_cache
					);
				}
			}
		}
	}

	public static function settings() {

		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Access denied.', 'widgets-control' ) );
		}

		$enable_content = Widgets_Plugin_Options::get_option(
			WIDGETS_CONTROL_CONTENT_ENABLE,
			WIDGETS_CONTROL_CONTENT_ENABLE_DEFAULT
		);

		$auto_clear_cache = Widgets_Plugin_Options::get_option(
			WIDGETS_PLUGIN_AUTO_CLEAR_CACHE,
			WIDGETS_PLUGIN_AUTO_CLEAR_CACHE_DEFAULT
		);

		echo '<h2>';
		echo __( 'Widgets Control Settings', 'widgets-control' );
		echo '</h2>';

		echo '<div style="margin-right:1em;">';
		echo '<form name="settings" method="post" action="">';
		echo '<div class="widgets-plugin">';

		echo '<p>';
		echo '<label>';
		echo sprintf( '<input type="checkbox" name="enable_content" %s />', $enable_content == 'yes' ? ' checked="checked" ' : '' );
		echo ' ';
		echo __( 'Enable Content Features', 'widgets-control' );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		echo sprintf(
			__( 'Enable the plugin\'s built-in <a href="%s">Content Blocks</a> and features related to it?', 'widgets-control' ),
			esc_url( add_query_arg( array( 'post_type' => Widgets_Control_Content::get_post_type() ), admin_url( 'edit.php' ) ) )
		);
		echo '</p>';
		echo '<p>';
		echo __( 'The plugin provides its own post type which is used to create versatile blocks of content.', 'widgets-control' );
		echo ' ';
		echo __( 'Content Blocks are used to create content with the WYSIWYG editor and display it using a shortcode or widget anywhere on posts, pages and other custom post types and in sidebars.', 'widgets-control' );
		echo '</p>';

		echo '<p>';
		echo '<label>';
		echo sprintf( '<input type="checkbox" name="auto_clear_cache" %s />', $auto_clear_cache == 'yes' ? ' checked="checked" ' : '' );
		echo ' ';
		echo __( 'Automatic Cache Clearing', 'widgets-control' );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		echo __( 'Automatically clear the cache when widgets are updated?', 'widgets-control' );
		echo '</p>';
		echo '<p>';
		echo __( 'This works with W3 Total Cache, WP Super Cache and WP Engine.', 'widgets-control' );
		echo ' ';
		echo __( 'This option is only provided as a convenience while you set up your site.', 'widgets-control' );
		echo ' ';
		echo __( 'It is recommended while you set up your widgets or when you work on changes.', 'widgets-control' );
		echo ' ';
		echo __( 'Note that you can also clear the cache manually even with this option disabled and we recommend to disable it once you have finished setting up your widgets.', 'widgets-control' );
		echo '</p>';
		wp_nonce_field( 'admin', 'widgets-plugin-settings', true, true );
		echo '<div class="buttons">';
		echo sprintf( '<input class="save button button-primary" type="submit" name="submit" value="%s" />', esc_attr( __( 'Save', 'widgets-control' ) ) );
		echo '<input type="hidden" name="action" value="set" />';
		echo '</div>';
		echo '</form>';

		echo '<p>';
		echo __( 'Get <a href="http://www.itthinx.com/shop/widgets-control-pro/"><strong>Widgets Control Pro</strong></a> which supports flexible custom sidebars and conditions based on page hierarchies, post types, roles and <a href="http://wordpress.org/plugins/groups/">groups</a>.', 'widgets-control' );
		echo '</p>';

		echo '</div>';
	}
}
add_action( 'init', array( 'Widgets_Plugin_Admin_Settings', 'init' ), 9 );
