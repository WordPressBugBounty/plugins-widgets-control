<?php
/**
 * class-widgets-control-admin-notice.php
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
 * Notices
 */
class Widgets_Control_Admin_Notice {

	/**
	 * Time mark.
	 *
	 * @var string
	 */
	const INIT_TIME = 'widgets-control-init-time';

	/**
	 * Used to store user meta and hide the notice asking to review.
	 *
	 * @var string
	 */
	const HIDE_REVIEW_NOTICE = 'widgets-control-hide-review-notice';

	/**
	 * The number of seconds in seven days, since init date to show the notice.
	 *
	 * @var int
	 */
	const SHOW_LAPSE = 604800;

	/**
	 * Adds actions.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__,'admin_init' ) );
	}

	/**
	 * Hooked on the admin_init action.
	 */
	public static function admin_init() {
		if ( current_user_can( 'activate_plugins' ) ) {
			$user_id = get_current_user_id();
			if ( !empty( $_GET[self::HIDE_REVIEW_NOTICE] ) ) {
				add_user_meta( $user_id, self::HIDE_REVIEW_NOTICE, true );
			}
			$hide_review_notice = get_user_meta( $user_id, self::HIDE_REVIEW_NOTICE, true );
			if ( empty( $hide_review_notice ) ) {
				$d = time() - self::get_init_time();
				if ( $d >= self::SHOW_LAPSE ) {
					add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
				}
			}
		}
	}

	/**
	 * Initializes if necessary and returns the init time.
	 */
	public static function get_init_time() {
		$init_time = get_site_option( self::INIT_TIME, null );
		if ( $init_time === null ) {
			$init_time = time();
			add_site_option( self::INIT_TIME, $init_time );
		}
		return $init_time;
	}

	/**
	 * Adds the admin notice.
	 */
	public static function admin_notices() {

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_url = add_query_arg( self::HIDE_REVIEW_NOTICE, true, $current_url );

		$output = '';

		$output .= '<style type="text/css">';
		$output .= 'div.widgets-control-rating {';
		$output .= sprintf( 'background: url(%s) #fff no-repeat 8px 8px;', WIDGETS_PLUGIN_URL . '/images/wctrl_gear.png' );
		$output .= 'padding-left: 76px ! important;';
		$output .= '}';
		$output .= '</style>';

		$output .= '<div class="updated widgets-control-rating">';
		$output .= '<p>';
		$output .= __( 'Many thanks for using <strong>Widgets Control</strong>!', 'widgets-control' );
		$output .= ' ';
		$output .= __( 'Could you please spare a minute and give it a review over at WordPress.org?', 'widgets-control' );
		$output .= '</p>';
		$output .= '<p>';
		$output .= sprintf(
			'<a class="button button-primary" href="%s" target="_blank">%s</a>',
			esc_url( 'http://wordpress.org/support/view/plugin-reviews/widgets-control?filter=5#postform' ),
			__( 'Yes, let\'s go!', 'widgets-control' )
		);
		$output .= ' ';
		$output .= sprintf(
			'<a style="margin:1em" href="%s">%s</a>',
			esc_url( $current_url ),
			esc_html( __( 'I\'ve already rated it.', 'widgets-control' ) )
		);
		$output .= '</p>';
		$output .= '<p>';
		$output .= sprintf(
			__( 'You can also follow <a href="%s">@itthinx</a> on Twitter or visit <a href="%s" target="_blank">itthinx.com</a> to check out other free and premium plugins we provide.', 'widgets-control' ),
			esc_url( 'https://twitter.com/itthinx' ),
			esc_url( 'https://www.itthinx.com' )
		);
		$output .= '</p>';
		$output .= '</div>';

		echo $output;
	}
}
Widgets_Control_Admin_Notice::init();
