<?php
/**
 * This file contains a function which checks if the WooCommerce plugin is installed.
 *
 * @since 1.0.0
 *
 * @package    MP Core
 * @subpackage Functions
 *
 * @copyright  Copyright (c) 2016, Mint Plugins
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @author     Philip Johnston
 */
 
/**
* Check to make sure the WooCommerce Plugin is installed.
*
* @since    1.0.0
* @link     http://mintplugins.com/doc/plugin-checker-class/
* @return   array $plugins An array of plugins to be installed. This is passed in through the mp_core_check_plugins filter.
* @return   array $plugins An array of plugins to be installed. This is passed to the mp_core_check_plugins filter. (see link).
*/
if (!function_exists('woocommerce_plugin_check')){
	function woocommerce_plugin_check( $plugins ) {
		
		$add_plugins = array(
			array(
				'plugin_name' => 'WooCommerce',
				'plugin_message' => __('You require the WooCommerce plugin. Install it here.', 'mp_stacks_downloadgrid'),
				'plugin_filename' => 'woocommerce.php',
				'plugin_download_link' => '',
				'plugin_info_link' => 'http://woocommerce.com',
				'plugin_group_install' => true,
				'plugin_required' => true,
				'plugin_wp_repo' => true,
			)
		);
		
		return array_merge( $plugins, $add_plugins );
	}
}
add_filter( 'mp_core_check_plugins', 'woocommerce_plugin_check' );