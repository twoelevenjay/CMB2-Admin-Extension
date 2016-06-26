<?php
/**
 * @category     WordPress_Plugin
 * @package      CMB2-Admin-Extension
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
 * Version:      0.0.8
 * Text Domain:  cmb2-admin-extension
 * Domain Path:  /languages
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
if ( ! defined( 'CMB2AE_CMB2_PLUGIN_FILE' ) ) {
	define( 'CMB2AE_CMB2_PLUGIN_FILE', 'cmb2/init.php' );
}

if ( ! defined( 'CMB2AE_URI' ) ) {
	define( 'CMB2AE_URI', plugins_url( '', __FILE__ ) );
}

if ( ! defined( 'CMB2AE_PATH' ) ) {
	define( 'CMB2AE_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * CMB2 Admin Extension main class.
 */
class CMB2_Admin_Extension_Class {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '0.0.8';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * Initiate CMB2 Admin Extension
	 * @since 0.0.1
	 */
	public function __construct() {

		// TODO comment
		$this->check_for_cmb2();
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );


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

		if ( defined( 'CMB2_LOADED' ) && CMB2_LOADED === true ) {

			require_once dirname( __FILE__ ) . '/includes/class-meta-box-post-type.php';
			$cbm2 = new CMB2_Meta_Box_Post_Type();

		} elseif ( file_exists( WP_PLUGIN_DIR . '/' . CMB2AE_CMB2_PLUGIN_FILE ) ) {

			$this->cmb2_admin_extension_cmb2_not_activated();

		} else{

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
				<p><?php printf( esc_html__( 'CMB2 Admin Extension depends on the last version of %s the CMB2 plugin %s to work!', 'cmb2-admin-extension' ), '<a href="https://wordpress.org/plugins/cmb2/">', '</a>' ); ?></p>
			</div>
		<?php

	}

	private function cmb2_admin_extension_cmb2_not_activated() {

		// TODO comment
		 ?>
			<div class="error">
				<p><?php printf( esc_html__( 'The CMB2 plugin is installed but has not been activated. Please %s activate %s it to use the CMB2 Admin Extension', 'cmb2-admin-extension' ), '<a href="'.admin_url('plugins.php').'">', '</a>' ); ?></p>
			</div>
		<?php

	}

	/**
	 * Load plugin textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain() {

		load_plugin_textdomain( 'cmb2-admin-extension', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	}

}

add_action( 'plugins_loaded', array( 'CMB2_Admin_Extension_Class', 'get_instance' ), 20 );

if ( ! function_exists( 'cmbf' ) ) {

	function cmbf( $ID, $field ) {

		return CMB2_Meta_Box_Post_Type::cmbf( $ID, $field );

	}
}
