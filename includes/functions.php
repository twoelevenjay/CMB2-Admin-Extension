<?php
/**
 * File: functions.php
 *
 * Functions for the CMB2 Admin Extension plugin.
 *
 * This file contains functions that provide global access to the main instance
 * of the CMB2_Meta_Box class and other utility functions.
 *
 * @package CMB2_Admin_Extension
 */

/**
 * Retrieve the main instance of the CMB2_Meta_Box class.
 *
 * This function ensures that only one instance of the CMB2_Meta_Box class exists
 * and provides global access to that instance.
 *
 * This is the main instance used to initialize and manage custom meta boxes.
 *
 * @since 1.0.0
 *
 * @return CMB2_Meta_Box The main instance of the CMB2_Meta_Box class.
 */

if ( ! function_exists( 'cmb2ae_metabox' ) ) {
	/**
	 * Main instance of CMB2_Meta_Box.
	 *
	 * @since  0.1.0
	 * @return object Main instance of the CMB2_Meta_Box class.
	 */
	function cmb2ae_metabox() {
		return CMB2_Meta_Box::get_instance();
	}
}

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
