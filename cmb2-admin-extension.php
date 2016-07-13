<?php
/**
 * CMB2 Admin Extension - a WordPress plugin.
 *
 * @category     WordPress_Plugin
 * @package      CMB2-Admin-Extension
 * @author       twoelevenjay
 * @license      GPL-2.0+
 * @link         http://211j.com
 *
 * @wordpress-plugin
 * Plugin Name:  CMB2 Admin Extension
 * Plugin URI:   https://github.com/twoelevenjay/CMB2-Admin-Extension
 * Description:  CMB2 Admin Extension add a user interface for admins to create CMB2 meta boxes from the WordPress admin.
 * Author:       twoelevenjay
 * Author URI:   http://211j.com
 * Contributors:
 * Version:      0.1.3
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
 * Silence is golden; exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Define plugin constant.
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
	const VERSION = '0.1.3';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * Initiate CMB2 Admin Extension.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {

		$this->check_for_cmb2();

		add_action( 'init', array( $this, 'load_textdomain' ), 9 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Check for the CMB2 plugin.
	 *
	 * @since 0.0.1
	 */
	private function check_for_cmb2() {

		if ( defined( 'CMB2_LOADED' ) && CMB2_LOADED !== false ) {

			require_once dirname( __FILE__ ) . '/includes/class-meta-box.php';
			require_once dirname( __FILE__ ) . '/includes/class-meta-box-post-type.php';
			require_once dirname( __FILE__ ) . '/includes/class-meta-box-settings.php';
			cmb2ae_metabox();
			return;
		} elseif ( file_exists( WP_PLUGIN_DIR . '/' . CMB2AE_CMB2_PLUGIN_FILE ) ) {

			add_action( 'admin_notices', array( $this, 'cmb2_not_activated' ) );
			return;
		}
		add_action( 'admin_notices', array( $this, 'missing_cmb2' ) );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain() {

		$lang_path = plugin_basename( dirname( __FILE__ ) ) . '/languages';
		$loaded    = load_muplugin_textdomain( 'cmb2-admin-extension', $lang_path );
		if ( strpos( __FILE__, basename( WPMU_PLUGIN_DIR ) ) === false ) {
			$loaded = load_plugin_textdomain( 'cmb2-admin-extension', false, $lang_path );
		}

		if ( ! $loaded ) {
			$loaded = load_theme_textdomain( 'cmb2-admin-extension', get_stylesheet_directory() . '/languages' );
		}

		if ( ! $loaded ) {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'cmb2-admin-extension' );
			$mofile = dirname( __FILE__ ) . '/languages/cmb2-admin-extension-' . $locale . '.mo';
			load_textdomain( 'cmb2-admin-extension', $mofile );
		}
	}

	/**
	 * Add an error notice if the CMB2 plugin is missing.
	 *
	 * @return void
	 */
	public function missing_cmb2() {

		?>
			<div class="error">
				<p><?php printf( esc_html__( 'CMB2 Admin Extension depends on the last version of %s the CMB2 plugin %s to work!', 'cmb2-admin-extension' ), '<a href="https://wordpress.org/plugins/cmb2/">', '</a>' ); ?></p>
			</div>
		<?php

	}

	/**
	 * Add an error notice if the CMB2 plugin isn't activated.
	 *
	 * @return void
	 */
	public function cmb2_not_activated() {

		?>
			<div class="error">
				<p><?php printf( esc_html__( 'The CMB2 plugin is installed but has not been activated. Please %s activate %s it to use the CMB2 Admin Extension', 'cmb2-admin-extension' ), '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">', '</a>' ); ?></p>
			</div>
		<?php

	}
}

add_action( 'plugins_loaded', array( 'CMB2_Admin_Extension_Class', 'get_instance' ), 20 );

if ( ! function_exists( 'cmbf' ) ) {

	/**
	 * This function needs documentation.
	 *
	 * @todo
	 *
	 * @param int    $id    Post ID.
	 * @param string $field The meta key to retrieve.
	 */
	function cmbf( $id, $field ) {

		return get_post_meta( $id, $field, true );
	}
}
