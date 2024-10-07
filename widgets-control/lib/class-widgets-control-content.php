<?php
/**
 * class-widgets-control-content.php
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
 * Defines the Content custom post type.
 */
class Widgets_Control_Content {

	/**
	 * Our post type's key.
	 * @var string
	 */
	const POST_TYPE = 'wctrl_content';

	/**
	 * Nonce.
	 * @var string
	 */
	const NONCE = 'wctrl-meta-box-nonce';

	/**
	 * Nonce action.
	 * @var string
	 */
	const ACTION = 'wctrl-action';

	/**
	 * Adds our init action.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_shortcode( 'widgets_control_content', array( __CLASS__, 'get_content' ) );
		add_filter( 'widgets_control_content_id', array( __CLASS__, 'widgets_control_content_id' ) );
	}

	/**
	 * Registers our content custom post type.
	 */
	public static function wp_init() {
		if ( !self::is_enabled() ) {
			return;
		}
		register_post_type(
			self::get_post_type(),
			array(
				'labels' => array(
					'name'               => _x( 'Content Blocks', 'Widgets Control Content Post Type', 'widgets-control' ),
					'singular_name'      => _x( 'Content Block', 'Widgets Control Content Post Type Singular Name', 'widgets-control' ),
					'plural_name'        => _x( 'Content Blocks', 'Widgets Control Content Post Type Plural Name', 'widgets-control' ),
					'all_items'          => __( 'All Content Blocks', 'widgets-control' ),
					'add_new'            => _x( 'Add Content Block', 'block', 'widgets-control' ),
					'add_new_item'       => __( 'Add New Content Block', 'widgets-control' ),
					'edit'               => __( 'Edit', 'widgets-control' ),
					'edit_item'          => __( 'Edit Content Block', 'widgets-control' ),
					'new_item'           => __( 'New Content Block', 'widgets-control' ),
					'not_found'          => __( 'No Content Block Found', 'widgets-control' ),
					'not_found_in_trash' => __( 'No Content Block found in Trash', 'widgets-control' ),
					'search_items'       => __( 'Search Content Blocks', 'widgets-control' ),
					'view'               => __( 'View', 'widgets-control' ),
					'view_item'          => __( 'View Content Block', 'widgets-control' )
				),
				'capability_type'     => self::POST_TYPE,
				'exclude_from_search' => true,
				'hierarchical'        => false,
				'map_meta_cap'        => true,
				// we use a CSS background icon or the icon image if the post type key is changed
				'menu_icon'           => Widgets_Control_Content::get_post_type() === Widgets_Control_Content::POST_TYPE ? '' : WIDGETS_PLUGIN_URL . '/images/wctrl_gear-20x20.png',
				'public'              => false,
				'publicly_queryable'  => false,
				'query_var'           => true,
				'rewrite'             => true,
				'show_ui'             => true,
				'supports'            => array(
					'author',
					'editor',
					'revisions',
					'title',
					'thumbnail'
				)
			)
		);
		self::create_capabilities();
	}

	/**
	 * Create and assign required capabilities to the 'administrator' role and those
	 * roles that have the capability 'edit_theme_options'.
	 */
	public static function create_capabilities() {

		global $wp_roles;

		$roles = array();
		if ( $administrator_role = $wp_roles->get_role( 'administrator' ) ) {
			$roles[] = $administrator_role;
		}
		foreach ( $wp_roles->role_objects as $role ) {
			if ( $role->has_cap( 'edit_theme_options' ) ) {
				$roles[] = $role;
			}
		}

		$capabilities = array(
			'assign_%ss',
			'edit_%ss',
			'edit_others_%ss',
			'publish_%ss',
			'read_private_%ss',
			'delete_%ss',
			'delete_private_%ss',
			'delete_published_%ss',
			'delete_others_%ss',
			'edit_private_%ss',
			'edit_published_%ss',
		);

		foreach ( $roles as $role ) {
			foreach ( $capabilities as $capability ) {
				$capability = sprintf( $capability, self::POST_TYPE );
				if ( !$role->has_cap( $capability ) ) {
					$role->add_cap( $capability );
				}
			}
		}
	}

	/**
	 * Adds our settings meta box to our sidebar content type.
	 *
	 * @param string $post_type
	 * @param object $post
	 */
	public static function add_meta_boxes( $post_type, $post = null ) {
		if ( !self::is_enabled() ) {
			return;
		}
		if ( $post_type === self::get_post_type() ) {
			add_meta_box(
				'wctrl-content-code',
				_x( 'Codes', 'Meta box title', 'widgets-control' ),
				array( __CLASS__, 'add_meta_box' ),
				null,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Renders the code meta box.
	 *
	 * @param object $post
	 * @param array $box
	 */
	public static function add_meta_box( $post = null, $box = null ) {
		$output = '';

		$post_id   = isset( $post->ID ) ? $post->ID : null;
		$post_type = isset( $post->post_type ) ? $post->post_type : null;

		if ( $post_type !== self::get_post_type() ) {
			return;
		}

		$output .= '<h3>';
		$output .= __( 'Shortcode', 'widgets-control' );
		$output .= '</h3>';
		$output .= '<p>';
		$output .= __( 'To display the block in content, embed this shortcode anywhere on a page:', 'widgets-control' );
		$output .= '</p>';
		$output .= '<p>';
		$output .= '<textarea style="width:100%;font-family:monospace;" class="widgets-control-content-shortcode-code" readonly="readonly" onmouseover="this.focus();" onfocus="this.select();">';
		$output .= sprintf( '[widgets_control_content id="%s"]', esc_attr( $post_id ) );
		$output .= '</textarea>';
		$output .= '</p>';
		$output .= '<p>';
		$output .= __( 'To include the title or the featured image, use the shortcode with these additional attributes:', 'widgets-control' );
		$output .= '<p>';
		$output .= '<textarea style="width:100%;font-family:monospace;" class="widgets-control-content-shortcode-code" readonly="readonly" onmouseover="this.focus();" onfocus="this.select();">';
		$output .= sprintf( '[widgets_control_content id="%s" show_title="yes" show_post_thumbnail="yes"]', esc_attr( $post_id ) );
		$output .= '</textarea>';
		$output .= '</p>';
		$output .= '<p>';
		$output .= __( 'You can also use this button to insert the shortcode for any content block:', 'widgets-control' );
		$output .= ' ';
		$output .= '<div style="vertical-align:middle;" class="button wctrl-insert-content"><span class="wctrl-icon"></span>Add Content Block</div>';
		$output .= ' ';
		$output .= __( 'You will find it conveniently placed above the editor when you edit your pages.', 'widgets-control' );
		$output .= '</p>';

		$output .= '<h3>';
		$output .= __( 'PHP', 'widgets-control' );
		$output .= '</h3>';
		$output .= '<p>';
		$output .= __( 'To display the content block in a PHP template, use this code:', 'widgets-control' );
		$output .= '</p>';
		$output .= '<p>';
		$output .= '<textarea style="width:100%;font-family:monospace;" class="widgets-control-content-php-code" readonly="readonly" onmouseover="this.focus();" onfocus="this.select();">';
		$output .= sprintf( '<&quest;php echo widgets_control_content( array( \'id\' => %d ) ); &quest;>', esc_attr( $post_id ) );
		$output .= '</textarea>';
		$output .= '</p>';

		$output .= wp_nonce_field( self::ACTION, self::NONCE, true, false );

		echo $output;
	}

	/**
	 * Returns the rendered content. Handler for the [widgets_control_content] shortcode.
	 *
	 * Supported attributes:
	 * - content : the ID or title of the requested content
	 * - id : the ID of the requested content
	 * - slug : the slug of the requested content
	 * - filter_the_content : default is true
	 * - filter_the_title : default is true
	 * - suppress_filters : default is false
	 * - show_title : default is false
	 * - show_content : default is true
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string|mixed
	 */
	public static function get_content( $atts, $content = '' ) {

		if ( !self::is_enabled() ) {
			return '';
		}

		$defaults = array(
			'content'             => null,
			'id'                  => null,
			'slug'                => null,
			'filter_the_content'  => true,
			'filter_the_title'    => true,
			'suppress_filters'    => false,
			'show_title'          => false,
			'show_post_thumbnail' => false,
			'show_content'        => true
		);

		$atts = shortcode_atts( $defaults, $atts );

		$post_id = null;
		$output  = '';

		foreach ( $atts as $key => $value ) {
			switch ( $key ) {
				case 'content' :
				case 'id' :
					if ( ( $value !== null ) && ( is_numeric( $value ) || is_string( $value ) ) ) {
						$value = trim( $value );
						if ( is_numeric( $value ) ) {
							$post_id = intval( $value );
						} else {
							$post = get_page_by_title( $value, OBJECT, self::get_post_type() );
							if ( $post !== null ) {
								$post_id = $post->ID;
							}
						}
					} else {
						$value = $defaults[$key];
					}
					break;
				case 'slug' :
					if ( ( $value !== null ) && is_string( $value ) ) {
						$value = trim( $value );
						$post = get_page_by_path( $value, OBJECT, self::get_post_type() );
						if ( $post !== null ) {
							$post_id = $post->ID;
						}
					}
					break;
				case 'filter_the_content' :
				case 'filter_the_title' :
				case 'suppress_filters' :
				case 'show_title' :
				case 'show_post_thumbnail' :
				case 'show_content' :
					if ( is_string( $value ) ) {
						$value = strtolower( trim( $value ) );
					}
					switch( $value ) {
						case 'yes' :
						case 'true' :
						case true :
							$value = true;
							break;
						case 'no' :
						case 'false' :
						case false :
							$value = false;
							break;
						default :
							$value = $defaults[$key];
					}
					break;
			}
			$atts[$key] = $value;
		}

		if ( $post_id !== null ) {
			$post_id = apply_filters( 'widgets_control_content_id', $post_id );
			if (
				apply_filters( 'widgets_control_content_render_output', true, $post_id ) &&
				( $post = get_post( intval( $post_id ) ) ) &&
				$post->post_type == self::get_post_type() &&
				$post->post_status == 'publish'
			) {
				$title = isset( $post->post_title ) ? $post->post_title : '';
				$id = isset( $post->ID ) ? $post->ID : 0;
				if ( $atts['filter_the_title'] ) {
					do_action( 'widgets_control_before_the_title_filter', $id, $post );
					$title = apply_filters( 'the_title', $title, $id );
					do_action( 'widgets_control_after_the_title_filter', $id, $post );
				}
				if ( $atts['filter_the_content'] ) {
					do_action( 'widgets_control_before_the_content_filter', $id, $post );
					$content = apply_filters( 'the_content', $post->post_content );
					do_action( 'widgets_control_after_the_content_filter', $id, $post );
				} else {
					$content = $post->content;
				}
				if ( $atts['show_title'] ) {
					$output .= apply_filters( 'widgets_control_content_before_title', '<div class="content-title"><h2>', $post_id, $title );
					$output .= $title;
					$output .= apply_filters( 'widgets_control_content_after_title', '</h2></div>', $post_id );
				}
				if ( $atts['show_post_thumbnail'] ) {
					$output .= apply_filters( 'widgets_control_content_before_post_thumbnail', '<div class="content-post-thumbnail">', $post_id, $title );
					$output .= get_the_post_thumbnail( $post_id );
					$output .= apply_filters( 'widgets_control_content_after_post_thumbnail', '</div>', $post_id );
				}
				if ( $atts['show_content'] ) {
					$output .= apply_filters( 'widgets_control_content_before_content', '<div class="content-content">', $post_id, $content );
					$output .= $content;
					$output .= apply_filters( 'widgets_control_content_after_content', '</div>', $post_id );
				}
				if ( strlen( $output ) > 0 ) {
					$prefix = apply_filters( 'widgets_control_content_before', sprintf( '<div class="widgets-control-content content-%d">', intval( $post_id ) ), $post_id, $output );
					$suffix = apply_filters( 'widgets_control_content_after', '</div>', $post_id, $output );
					$output = $prefix . $output . $suffix;
				}
			}
		}

		return $output;

	}

	/**
	 * Returns the key of our Content post type.
	 * @return mixed
	 */
	public static function get_post_type() {
		return apply_filters( 'widgets_control_content_post_type', self::POST_TYPE );
	}

	/**
	 * Returns true if the Content post type is enabled.
	 * @return boolean
	 */
	public static function is_enabled() {
		return 'yes' == Widgets_Plugin_Options::get_option(
			WIDGETS_CONTROL_CONTENT_ENABLE,
			WIDGETS_CONTROL_CONTENT_ENABLE_DEFAULT
		);
	}

	/**
	 * Modifies the post ID for the content block if required for WPML translation.
	 *
	 * @param int $post_id ID of the content block
	 * @return int the ID of the translated content block
	 */
	public static function widgets_control_content_id( $post_id ) {
		if ( function_exists( 'wpml_object_id_filter' ) ) {
			$post_id = wpml_object_id_filter( $post_id, self::get_post_type(), true );
		} else if ( function_exists( 'icl_object_id' ) ) {
			$post_id = icl_object_id( $post_id, self::get_post_type(), true );
		}
		return $post_id;
	}
}
Widgets_Control_Content::init();
