<?php
/**
 * class-widgets-control-content-widget.php
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
 * Content widget.
 */
class Widgets_Control_Content_Widget extends WP_Widget {

	/**
	 * @var string cache id
	 */
	private static $cache_id = 'widgets_control_content_widget';

	/**
	 * @var string cache flag
	 */
	private static $cache_flag = 'widget';

	/**
	 * Some default values.
	 * @var array
	 */
	private static $defaults = array(
		'show_title'          => false,
		'show_post_thumbnail' => false,
		'filter_the_title'    => true,
		'filter_the_content'  => true,
		'suppress_filters'    => false
	);

	/**
	 * Initialize.
	 */
	public static function init() {
// 		if ( !has_action( 'wp_print_styles', array( __CLASS__, '_wp_print_styles' ) ) ) {
// 			add_action( 'wp_print_styles', array( __CLASS__, '_wp_print_styles' ) );
// 		}
// 		if ( !has_action( 'comment_post', array( __CLASS__, 'cache_delete' ) ) ) {
// 			add_action( 'comment_post', array( __CLASS__, 'cache_delete' ) );
// 		}
// 		if ( !has_action( 'transition_comment_status', array( __CLASS__, 'cache_delete' ) ) ) {
// 			add_action( 'transition_comment_status', array( __CLASS__, 'cache_delete' ) );
// 		}

		if ( Widgets_Control_Content::is_enabled() ) {
			add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
		}
	}

	/**
	 * Registers the widget.
	 */
	public static function widgets_init() {
		register_widget( 'Widgets_Control_Content_Widget' );
	}

	/**
	 * Creates a content widget.
	 */
	public function __construct() {
		parent::__construct(
			'widgets_control_content_block',
			__( 'Content Block', 'widgets-control' ),
			array( 'description' => __( 'Used to display Content Blocks with Widgets Control.', 'widgets-control' ) )
		);
	}

	/**
	 * Clears cached widget.
	 */
	public static function cache_delete() {
		if ( function_exists( 'wp_cache_delete' ) ) {
			wp_cache_delete( self::$cache_id, self::$cache_flag );
		}
	}

	/**
	 * Enqueue styles if at least one widget is used (not used currently).
	 */
	public static function _wp_print_styles() {
		global $wp_registered_widgets;
		foreach ( $wp_registered_widgets as $widget ) {
			if ( $widget['name'] == 'Widgets Control Content' ) {
				//wp_enqueue_style( 'widgets-control-content-widget', WIDGETS_PLUGIN_URL . '/css/content-widget.css', array(), WIDGETS_PLUGIN_VERSION );
				break;
			}
		}
	}

	/**
	 * Widget output
	 *
	 * @see WP_Widget::widget()
	 * @link http://codex.wordpress.org/Class_Reference/WP_Object_Cache
	 */
	public function widget( $args, $instance ) {
		$cache = wp_cache_get( self::$cache_id, self::$cache_flag );
		if ( ! is_array( $cache ) ) {
			$cache = array();
		}
		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		extract( $args );

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );

		$widget_id = $args['widget_id'];

		// output
		$output = '';
		$output .= $before_widget;
		if ( !empty( $title ) ) {
			$output .= $before_title . $title . $after_title;
		}
		$output .= self::render( $instance );
		$output .= $after_widget;
		echo $output;

		$cache[$args['widget_id']] = $output;
		wp_cache_set( self::$cache_id, $cache, self::$cache_flag );
	}

	/**
	 * Save widget options
	 *
	 * @see WP_Widget::update()
	 */
	public function update( $new_instance, $old_instance ) {

		global $wpdb;

		$settings = $old_instance;

		// title
		$settings['title'] = strip_tags( $new_instance['title'] );

		// number
		$settings['post_id'] = '';
		$post_id = trim( $new_instance['post_id'] );
		if ( !empty( $post_id ) ) {
			$post_id = intval( $post_id );
			if ( $post_id > 0 ) {
				$settings['post_id'] = $post_id;
			}
		}

		$settings['show_title']          = !empty( $new_instance['show_title'] );
		$settings['show_post_thumbnail'] = !empty( $new_instance['show_post_thumbnail'] );
		$settings['filter_the_title']    = !empty( $new_instance['filter_the_title'] );
		$settings['filter_the_content']  = !empty( $new_instance['filter_the_content'] );
		$settings['suppress_filters']    = !empty( $new_instance['suppress_filters'] );

		$this->cache_delete();

		return $settings;
	}

	/**
	 * Output admin widget options form
	 *
	 * @see WP_Widget::form()
	 */
	public function form( $instance ) {

		extract( self::$defaults );

		// title
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'The widget title.', 'widgets-control' ) ) );
		echo __( 'Title', 'widgets-control' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . esc_attr( $title ) . '" />';
		echo '</label>';
		echo '</p>';

		$posts = get_posts( array(
			'numberposts'      => -1,
			'post_type'        => Widgets_Control_Content::get_post_type(),
			'suppress_filters' => true,
			'order'            => 'ASC',
			'orderby'          => 'title'
		) );

		// post ID
		$post_id = !empty( $instance['post_id'] ) ? intval( $instance['post_id'] ) : '';
		echo '<p>';
		echo sprintf( '<label title="%s" >', __( 'Content Block', 'widgets-control' ) );
		echo __( 'Choose the content to display', 'widgets-control' );
		printf(
			'<select class="widefat" id="%s" name="%s">',
			esc_attr( $this->get_field_id( 'post_id' ) ),
			esc_attr( $this->get_field_name( 'post_id' ) )
		);
		printf(
			'<option value="" %s>%s</option>',
			empty( $instance['post_id'] ) ? ' selected="selected" ' : '',
			__( 'None', 'widgets-control' )
		);
		foreach ( $posts as $post ) {
			printf(
				'<option value="%d" %s>%s [#%d]</option>',
				intval( $post->ID ),
				isset( $instance['post_id'] ) && $instance['post_id'] == $post->ID ? ' selected="selected" ' : '',
				esc_attr( get_the_title( $post->ID ) ),
				intval( $post->ID )
			);
		}
		echo '</select>';
		echo '</label>';
		echo '</p>';

		// show_title
		$checked = ( ( ( !isset( $instance['show_title'] ) && self::$defaults['show_title'] ) || ( isset( $instance['show_title'] ) && ( $instance['show_title'] === true ) ) ) ? 'checked="checked"' : '' );
		echo '<p>';
		echo sprintf( '<label title="%s">', __( 'Whether to show the title of the Content Block.', 'widgets-control' ) );
		echo '<input type="checkbox" ' . $checked . ' value="1" name="' . $this->get_field_name( 'show_title' ) . '" />';
		echo __( 'Show the Content Block\'s title', 'widgets-control' );
		echo '</label>';
		echo '</p>';

		// show_post_thumbnail
		$checked = ( ( ( !isset( $instance['show_post_thumbnail'] ) && self::$defaults['show_post_thumbnail'] ) || ( isset( $instance['show_post_thumbnail'] ) && ( $instance['show_post_thumbnail'] === true ) ) ) ? 'checked="checked"' : '' );
		echo '<p>';
		echo sprintf( '<label title="%s">', __( 'Whether to show the featured image of the Content Block.', 'widgets-control' ) );
		echo '<input type="checkbox" ' . $checked . ' value="1" name="' . $this->get_field_name( 'show_post_thumbnail' ) . '" />';
		echo __( 'Show the Content Block\'s featured image', 'widgets-control' );
		echo '</label>';
		echo '</p>';

		// filter_the_title
		$checked = ( ( ( !isset( $instance['filter_the_title'] ) && self::$defaults['filter_the_title'] ) || ( isset( $instance['filter_the_title'] ) && ( $instance['filter_the_title'] === true ) ) ) ? 'checked="checked"' : '' );
		echo '<p>';
		echo sprintf( '<label title="%s">', __( 'Whether to apply the the_title filter.', 'widgets-control' ) );
		echo '<input type="checkbox" ' . $checked . ' value="1" name="' . $this->get_field_name( 'filter_the_title' ) . '" />';
		echo __( 'Filter the title', 'widgets-control' );
		echo '</label>';
		echo '</p>';

		// filter_the_content
		$checked = ( ( ( !isset( $instance['filter_the_content'] ) && self::$defaults['filter_the_content'] ) || ( isset( $instance['filter_the_content'] ) && ( $instance['filter_the_content'] === true ) ) ) ? 'checked="checked"' : '' );
		echo '<p>';
		echo sprintf( '<label title="%s">', __( 'Whether to apply the the_content filter.', 'widgets-control' ) );
		echo '<input type="checkbox" ' . $checked . ' value="1" name="' . $this->get_field_name( 'filter_the_content' ) . '" />';
		echo __( 'Filter the content', 'widgets-control' );
		echo '</label>';
		echo '</p>';

		// suppress_filters
		$checked = ( ( ( !isset( $instance['suppress_filters'] ) && self::$defaults['suppress_filters'] ) || ( isset( $instance['suppress_filters'] ) && ( $instance['suppress_filters'] === true ) ) ) ? 'checked="checked"' : '' );
		echo '<p>';
		echo sprintf( '<label title="%s">', __( 'Whether to suppress filters when obtaining the content.', 'widgets-control' ) );
		echo '<input type="checkbox" ' . $checked . ' value="1" name="' . $this->get_field_name( 'suppress_filters' ) . '" />';
		echo __( 'Suppress filters', 'widgets-control' );
		echo '</label>';
		echo '</p>';
	}

	/**
	 * Renders the widget instance.
	 *
	 * @param array $instance
	 * @return string|mixed
	 */
	public static function render( $instance ) {
		$output = '';
		if ( !empty( $instance['post_id'] ) ) {
			$post_id = intval( $instance['post_id'] );
			$atts = array(
				'id'                  => !empty( $post_id ) ? $post_id : null,
				'show_title'          => isset( $instance['show_title'] ) ? $instance['show_title'] : self::$defaults['show_title'],
				'show_post_thumbnail' => isset( $instance['show_post_thumbnail'] ) ? $instance['show_post_thumbnail'] : self::$defaults['show_post_thumbnail'],
				'filter_the_title'    => isset( $instance['filter_the_title'] ) ? $instance['filter_the_title'] : self::$defaults['filter_the_title'],
				'filter_the_content'  => isset( $instance['filter_the_content'] ) ? $instance['filter_the_content'] : self::$defaults['filter_the_content'],
				'suppress_filters'    => isset( $instance['suppress_filters'] ) ? $instance['suppress_filters'] : self::$defaults['suppress_filters']
			);
			$output = Widgets_Control_Content::get_content( $atts );
		}
		return $output;
	}
}
Widgets_Control_Content_Widget::init();
