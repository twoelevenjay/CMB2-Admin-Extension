<?php
/**
 * @category     WordPress_Plugin
 * @package      CMB2
 * @author       twoelevenjay
 * @license      GPL-2.0+
 * @link         http://211j.com
 *
 * Plugin Name:  CMB2 Admin Extension
 * Plugin URI:   https://github.com/twoelevenjay/CMB2-Admin-Extension
 * Description:  CMB2 Admin Extension add a user interface for admins to create CMB2 meta boxes from the WordPress admin.
 * Author:       twoelevenjay
 * Author URI:   http://211j.com
 * Contributors: 
 *
 * Version:      0.0.2
 *
 * Text Domain:  cmb2-admin-extension
 * Domain Path:  languages
 *
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */
 
/**
 * Silence is golden; exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Define plugin constant
 */
define('CMB2_PLUGIN_FILE', 'cmb2/init.php');

/**
 * CMB2 Admin Extension main class.
 */
class CMB2_Admin_Extension_Class {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '0.0.1';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initiate CMB2 Admin Extension
	 * @since 0.0.1
	 */
	public function __construct() {
	
		// TODO comment
		$this->check_for_cmb2();
		
	}

	/**
	 * Return an instance of this class.
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Check for the CMB2 plugin
	 * @since 0.0.1
	 */
	private function check_for_cmb2() {
	
		global $plugins;

		if ( in_array( CMB2_PLUGIN_FILE, get_option('active_plugins' ) ) ) {
		
			require_once 'includes/class-meta-box-post-type.php';

		}elseif ( file_exists( ABSPATH . 'wp-content/plugins/cmb2/init.php' ) ) {

			$this->cmb2_admin_extension_cmb2_not_activated();

		}else{

			$this->cmb2_admin_extension_missing_cmb2();

		}
	}

	/**
	 * Add an error notices to the dashboard for if the CMB2 plugin is missing or not activated
	 *
	 * @return void
	 */
 
	private function cmb2_admin_extension_missing_cmb2() {

		?>
			<div class="error">
				<p><?php _e( 'CMB2 Admin Extension depends on the last version of <a href="https://wordpress.org/plugins/cmb2/">the CMB2 plugin</a> to work!', 'cmb2-admin-extension' ); ?></p>
			</div>
		<?php
	
	}

	private function cmb2_admin_extension_cmb2_not_activated() {

		// TODO comment
		$activate_url = $this->activate_cmb2_link(CMB2_PLUGIN_FILE);
		 ?>
			<div class="error">
				<p><?php _e( 'The CMB2 plugin is installed but has not been activated. Please '.$activate_url.' it to use the CMB2 Admin Extension', 'cmb2-admin-extension' ); ?></p>
			</div>
		<?php
	
	}

	private function activate_cmb2_link($plugin_file) {

		// TODO comment
		global $status, $page, $s, $totals;
	
		$context = $status;
	
		// TODO comment
		$activateUrl = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin') . '" class="edit">' . __('activate') . '</a>';

		return $activateUrl;
	
	}

}

add_action( 'plugins_loaded', array( 'CMB2_Admin_Extension_Class', 'get_instance' ), 20 );

function cmbf( $ID, $field ) {

	return CMB2_Meta_Box_Post_Type::cmbf( $ID, $field );
	
}