=== MP Stacks + WooGrid ===
Contributors: johnstonphilip
Donate link: http://mintplugins.com/
Tags: message bar, header
Requires at least: 3.5
Tested up to: 4.3
Stable tag: 1.0.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add-On Plugin for MP Stacks which shows a grid of Posts from WooCommerce in a Brick. Set the source of posts to a category or tag, set the number of posts per row, featured image size, title and excerpt colours and sizes, or show just images, or just text - or both!

== Description ==

Extremely simple to set up - allows you to show posts from WooCommerce on any page, at any time, anywhere on your website. Just put make a brick using “MP Stacks”, put the stack on a page, and set the brick’s Content-Type to be “WooGrid”.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the 'mp-stacks-woogrid’ folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Build Bricks under the “Stacks and Bricks” menu. 
4. Publish your bricks into a “Stack”.
5. Put Stacks on pages using the shortcode or the “Add Stack” button.

== Frequently Asked Questions ==

See full instructions at http://mintplugins.com/doc/mp-stacks

== Screenshots ==


== Changelog ==

= 1.0.0.4 = January 12, 2016
* Make WooGrid output its CSS WITH initial css. This fixes a bug where the woogrid is the second content-type in a brick and it cancels any css prior (1st Content Type).
* Added check for WooCommerce plugin.
* Added check to see if any product categories exist prior to outputting category chooser

= 1.0.0.3 = November 5, 2015
* Removed Font Awesome to, instead, use version from MP Stacks.
* Added support for "Load From Scratch" for Isotopes Filtering.

= 1.0.0.2 = September 24, 2015
* Products per row are now passed through the mp_stacks_grid_posts_per_row_percentage function.

= 1.0.0.1 = September 21, 2015
* Brick Metabox controls now load using ajax.
* Admin Meta Scripts now enqueued only when needed.
* Added support for "All" products for Woo product grid.
* Added jQuery namespaces for animations.
* Make WooGrid alignment "centered" by default.

= 1.0.0.0 = May 12, 2015
* Original release
