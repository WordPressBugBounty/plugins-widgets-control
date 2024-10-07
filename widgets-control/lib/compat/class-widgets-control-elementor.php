<?php
/**
 * class-widgets-control-elementor.php
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
 * Disables Elementor's the_content filter as it applies it to content blocks when it should not.
 * When our Content Block is used*, Elementor's the_content filter would kick in and retrieve
 * the content for the current post instead of the current Content Block. Thus we disable its
 * the_content filter temporarily until we have the content for our block.
 * This happens during the call to Widgets_Control_Content::get_content() where we apply
 * the_content filter to the content of the content block.
 */
class Widgets_Control_Elementor {

	/**
	 * Adds actions before and after content block content is running the the_content filter.
	 */
	public static function init() {
		if ( class_exists( 'Elementor\Plugin' ) && method_exists( 'Elementor\Plugin', 'instance' ) ) {
			add_action( 'widgets_control_before_the_content_filter', array( __CLASS__, 'widgets_control_before_the_content_filter' ) );
			add_action( 'widgets_control_after_the_content_filter', array( __CLASS__, 'widgets_control_after_the_content_filter' ) );
		}
	}

	/**
	 * Disables Elementor's the_content filter.
	 */
	public static function widgets_control_before_the_content_filter() {
		$frontend = Elementor\Plugin::instance()->frontend;
		if ( is_object( $frontend ) && method_exists( $frontend, 'remove_content_filter' ) ) {
			$frontend->remove_content_filter();
		}
	}

	/**
	 * Enables Elementor's the_content filter.
	 */
	public static function widgets_control_after_the_content_filter() {
		$frontend = Elementor\Plugin::instance()->frontend;
		if ( is_object( $frontend ) && method_exists( $frontend, 'add_content_filter' ) ) {
			$frontend->add_content_filter();
		}
	}
}
Widgets_Control_Elementor::init();
