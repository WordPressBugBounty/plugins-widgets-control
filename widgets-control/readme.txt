=== Widgets Control ===
Contributors: itthinx, proaktion
Donate link: https://www.itthinx.com/shop/widgets-control-pro/
Tags: widget, sidebar, context, visibility, theme, widgets, appearance, conditional, control, customize, display, hide, logic, placement, restrict, restrict content, shortcode, show, view
Requires at least: 4.6
Tested up to: 5.7
Requires PHP: 5.6.0
Stable tag: 2.5.0
License: GPLv3

A Widget toolbox that adds visibility management and helps to control where widgets, sidebars and content are shown efficiently.

== Description ==

_Widgets Control_ is a toolbox that features visibility management for all widgets, sidebars, sections of content and content blocks.
It allows to __show widgets and sidebars based on conditions__ - you can choose to show them only on certain pages or exclude them from being displayed.

__Sections__ of content can also be restricted by using this plugin's `[widgets_control]` shortcode.

You can also define new WYSIWYG __Content Blocks__ that can be used in widgets, sidebars and with shortcodes.

For each widget and sidebar, you can decide where it should be displayed:

- show it on all pages
- show it on some pages
- show it on all except some pages

... you can target small, medium or large screens for mobile, tablet and desktop users.

To include or exclude pages, the plugin allows you to indicate page ids, titles or slugs and tokens that identify the front page, categories, tags, etc.

In addition to page ids, titles and slugs, these tokens can be used to determine where a widget should or should not be displayed:

<code>[home] [front] [single] [page] [category] ...</code>

On sites using [WPML](https://wpml.org), widgets can be shown conditionally based on the language viewed.

The `[widgets_control]` shortcode is used to embed content and show it conditionally similar to the visibility options used for widgets and sidebars.
For example, `[widgets_control conditions="{archive}"]This text is shown only when the content is displayed on an archive page.[/widgets_control]`.

_Widgets Control_ also provides __flexible WYSIWYG Content Blocks__ and a proper widget that can be used to place them in sidebars,
the `[widgets_control_content]` shortcode to embed freely created blocks anywhere on your pages and
API functions that allow to include these blocks in PHP templates of your theme.
For even more flexible control, use [Widgets Control Pro](https://www.itthinx.com/shop/widgets-control-pro/) which provides freely definable additional sidebars.

Use display conditions to show or hide content on devices with small, medium or large displays, useful to adapt the display to __mobile, tablet and desktop__ viewers.

See the [documentation](http://docs.itthinx.com/document/widgets-control/) for more details.

_Widgets Control_ works with virtually any widget. It is compatible with lots of plugins, among these it has been tested with:

- [Groups](https://wordpress.org/plugins/groups/)
- [Affiliates](https://wordpress.org/plugins/affiliates/)
- [Decent Comments](https://wordpress.org/plugins/decent-comments/)
- [WooCommerce](https://wordpress.org/plugins/woocommerce/)
- [WooCommerce Product Search](https://www.itthinx.com/shop/woocommerce-product-search/)
- [Search Live](https://wordpress.org/plugins/search-live/)
- [Documentation](https://wordpress.org/plugins/documentation/)
- [Events Manager](https://wordpress.org/plugins/events-manager/)
- [BuddyPress](https://buddypress.org)
- [bbPress](https://wordpress.org/plugins/bbpress/)
- [Ninja Forms](https://wordpress.org/plugins/ninja-forms)
- [Gravity Forms](https://gravityforms.com/)
- [Jetpack](https://wordpress.org/plugins/jetpack/)
- [WPML](https://wpml.org)
- [NextGEN Gallery](https://wordpress.org/plugins/nextgen-gallery/)
- [Image Widget](https://wordpress.org/plugins/image-widget/)
- [MailChimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/)
- [The Events Calendar](https://wordpress.org/plugins/the-events-calendar/)
- [MailPoet Newsletters](https://wordpress.org/plugins/wysija-newsletters)
- [Elementor](https://wordpress.org/plugins/elementor/)

### Widgets Control Pro ###

Our [Widgets Control Pro](https://www.itthinx.com/shop/widgets-control-pro/) provides additional features:

- Conditions based on the viewed __post type__. For example, show a widget only on posts with `[type:post]` or only on product pages with `[type:product]`
- Show or hide widgets on full __page hierarchies__, where conditions are based on a parent page and all its child pages: `some-page/*`
- Show or hide widgets based on user __roles__. For example, show a widget to subscribers and customers only: `[role:subscriber,customer]`
- Show or hide widgets based on a user's __group membership__ with [Groups](https://wordpress.org/plugins/groups/). For example, show a widget only to registered users with `[group:Registered]` or show a widget only to users in a Premium group using `[group:Premium]`
- Show or hide widgets for archive pages of a specific post type. For example, `[archive:product]` can be used to show widgets for the WooCommerce shop page and product archives only.
- Allows to specify exclusions. For example, to show a widget only on pages, but exclude it from being shown on one or more specific pages.
- Provides additional __sidebar features__ that allow to define any number of __custom sidebars__, flexible placement based on common locations, including above and below content, the comment form, menus and other sidebars, the `[widgets_control_sidebar]` shortcode to embed them in content on your pages and API functions to use them in your theme's templates.

### Feedback ###

Feedback is welcome!

If you need help, have problems, want to leave feedback or want to provide constructive criticism, please do so here at the [Widgets Control](https://www.itthinx.com/plugins/widgets-control/) plugin page.

Please try to solve problems there before you rate this plugin or say it doesn't work. There goes a _lot_ of work into providing you with quality plugins!

Please help with your feedback and we're also grateful if you help spread the word about this plugin.

**Thanks!**

#### Twitter ####

Follow [@itthinx](https://twitter.com/itthinx) on Twitter for updates on this and other plugins.


== Installation ==

= Dashboard =

Log in as an administrator and go to <strong>Plugins > Add New</strong>.

Type <em>Widgets Control</em> in the search field and click <em>Search Plugins</em>, locate the <em>Widgets Control<em> plugin by <em>itthinx</em> and install it by clicking <em>Install Now</em>.
Now <em>activate</em> the plugin to have the widget placement features available.

= FTP =

You can install the plugin via FTP, see [Manual Plugin Installation](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Screenshots ==

1. Widget visibility example I.
2. Widget visibility example II.
3. Widget visibility example III.
4. Sidebar visibility example.
5. Shortcode usage examples to restrict sections of content.

== Frequently Asked Questions ==

= Where is the documentation for this plugin? =

You can find the documentation on the [Widgets Control](http://docs.itthinx.com/document/widgets-control/) documentation pages.

== Changelog ==

See the full [changelog](https://plugins.svn.wordpress.org/widgets-control/trunk/changelog.txt) for details.

== Upgrade Notice ==

This release has been tested with the latest version of WordPress.
