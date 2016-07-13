<?php
/**
 * CMB2 Meta Box
 *
 * @since  0.1.0
 *
 * @category  WordPress_Plugin
 * @package   CMB2 Admin Extension
 * @author    twoelevenjay
 * @license   GPL-2.0+
 */

if ( ! class_exists( 'CMB2_Meta_Box' ) ) {

	/**
	 * Class CMB2_Meta_Box.
	 */
	class CMB2_Meta_Box {

		/**
		 * Field prefix.
		 *
		 * @var string
		 */
		private $prefix = '_cmb2_';

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance;

		/**
		 * Initiate CMB2 Admin Extension object.
		 *
		 * @todo For now plugin will use one main object, will consider 3 seperate objects in the future.
		 * @todo Comment.
		 *
		 * @since 0.0.1
		 */
		public function __construct() {

			add_action( 'pre_current_active_plugins', array( $this, 'hide_cmb2_plugins' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'cmb2_init', array( $this, 'init_user_defined_meta_boxes_and_fields' ) );
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( self::$instance === null ) {
				self::$instance = new self;
			}

			return self::$instance;
		}


		/**
		 * Determine if current user has permission to CMB2 view plugins.
		 *
		 * @since  0.0.1
		 */
		public function is_cmb2_allowed() {

			$cmb2_settings = get_option( '_cmb2_settings' );

			if ( empty( $cmb2_settings ) ) {
				// No settings saved.
				return true;
			}

			$current_user  = wp_get_current_user();
			$allowed_users = isset( $cmb2_settings['_cmb2_user_multicheckbox'] ) ? $cmb2_settings['_cmb2_user_multicheckbox'] : array();

			if ( empty( $allowed_users ) || in_array( $current_user->ID, $allowed_users, true ) ) {

				return true;
			}
			return false;
		}

		/**
		 * Only show CMB2 plugins to users defined in settings.
		 *
		 * @since  0.0.1
		 */
		public function hide_cmb2_plugins() {

			global $wp_list_table;
			if ( ! $this->is_cmb2_allowed() ) {
				$to_hide = array( CMB2AE_CMB2_PLUGIN_FILE, 'cmb2-admin-extension/cmb2-admin-extension.php' );
				$plugins = $wp_list_table->items;
				foreach ( array_keys( $plugins ) as $key ) {
					if ( in_array( $key, $to_hide, true ) ) {
						unset( $wp_list_table->items[ $key ] );
					}
				}
			}
		}

		/**
		 * Enqueue CMB2 Admin Extension scripts and styles.
		 *
		 * @since  0.0.8
		 */
		public function enqueue_scripts() {

			$screen = get_current_screen();
			if ( $screen->post_type === 'meta_box' ) {

				wp_register_style( 'cmb2_admin_styles', CMB2AE_URI . '/css/meta-box-fields.css', false, '0.0.8' );
				wp_enqueue_style( 'cmb2_admin_styles' );
				wp_enqueue_script( 'cmb2_admin_scripts', CMB2AE_URI . '/js/meta-box-fields.js', true, array( 'jquery' ), '0.0.8' );
			}
		}

		/**
		 * Function is_repeatable().
		 *
		 * @todo Document properly.
		 * @since  0.0.6
		 *
		 * @param string $field_type A CMB2 field type.
		 */
		static function is_repeatable( $field_type ) {

			$repeatable_fields = array(
				'text',
				'text_small',
				'text_medium',
				'text_email',
				'text_url',
				'text_money',
				'textarea',
				'textarea_small',
				'textarea_code',
				'text_date',
				'text_time',
				'select_timezone',
				'text_date_timestamp',
				'text_datetime_timestamp',
				'text_datetime_timestamp_timezone',
				'colorpicker',
				'select',
				'multicheck',
				'multicheck_inline',
				'file',
				'file_list',
			);
			return in_array( $field_type, $repeatable_fields, true );
		}

		/**
		 * Function has_options().
		 *
		 * @todo Document properly.
		 * @since  0.0.6
		 *
		 * @param string $field_type A CMB2 field type.
		 */
		static function has_options( $field_type ) {

			$options_fields = array(
				'radio',
				'radio_inline',
				'taxonomy_radio',
				'taxonomy_radio_inline',
				'select',
				'taxonomy_select',
				'multicheck',
				'multicheck_inline',
				'taxonomy_multicheck',
				'taxonomy_multicheck_inline',
			);
			return in_array( $field_type, $options_fields, true );
		}

		/**
		 * Function afo().
		 *
		 * @todo Document properly.
		 * @since  0.0.6
		 *
		 * @param array  $field      Field definition.
		 * @param string $field_type A CMB2 field type.
		 */
		static function afo( $field, $field_type ) {

			return ( in_array( $field['_cmb2_field_type_select'], $field_type, true ) && ( ! empty( $field['_cmb2_add_upload_file_text'] ) && is_string( $field['_cmb2_add_upload_file_text'] ) ) );
		}

		/**
		 * Loop through user defined meta_box and creates the custom meta boxes and fields.
		 *
		 * @since  0.0.1
		 */
		public function init_user_defined_meta_boxes_and_fields() {

			$args = array(
				'post_type'        => 'meta_box',
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
				'suppress_filters' => false,
			);

			$prefix = $this->prefix;

			$user_meta_boxes = get_posts( $args );

			foreach ( $user_meta_boxes as $user_meta_box ) {

				$metabox_id = $user_meta_box->ID;

				$title          = get_the_title( $metabox_id );
				$id             = str_replace( '-', '_', $user_meta_box->post_name );
				$post_type      = cmbf( $metabox_id, $prefix . 'post_type_multicheckbox' );
				$context        = cmbf( $metabox_id, $prefix . 'context_radio' );
				$priority       = cmbf( $metabox_id, $prefix . 'priority_radio' );
				$show_names     = cmbf( $metabox_id, $prefix . 'show_names' ) === 'on' ? true : false;
				$disable_styles = cmbf( $metabox_id, $prefix . 'disable_styles' ) === 'on' ? true : false;
				$closed         = cmbf( $metabox_id, $prefix . 'closed' ) === 'on' ? true : false;
				$fields         = cmbf( $metabox_id, $prefix . 'custom_field' );

				/**
				 * Initiate the metabox.
				 */
				${ 'cmb_' . $id } = new_cmb2_box( array(
					'id'           => $id,
					'title'        => $title,
					'object_types' => $post_type, // Post type.
					'context'      => $context,
					'priority'     => $priority,
					'show_names'   => $show_names,
					'cmb_styles'   => $disable_styles,
					'closed'       => $closed,
				) );

				foreach ( $fields as $field ) {

					$field_id = '_' . strtolower( str_replace( ' ', '_', $field['_cmb2_name_text'] ) );

					$field_args = array(
						'name' => $field['_cmb2_name_text'],
						'desc' => $field['_cmb2_decription_textarea'],
						'id'   => $field_id,
						'type' => $field['_cmb2_field_type_select'],
					);

					$options = isset( $field['_cmb2_options_textarea'] ) ? $field['_cmb2_options_textarea'] : false;
					if ( $options ) {
						$options = explode( PHP_EOL, $options );
						foreach ( $options as $option ) {
							$opt_arr = explode( ',', $option );
							if ( ! isset( $opt_arr[1] ) ) {
								continue;
							}
							$field_options[ $opt_arr[0] ] = $opt_arr[1];
						}
						$field_args['options'] = $field_options;
					}
					if ( strpos( $field['_cmb2_field_type_select'], 'tax' ) !== false  && $field['_cmb2_tax_options_radio_inline'] !== '' ) {
						$field_args['taxonomy'] = $field['_cmb2_tax_options_radio_inline'];
					}
					if ( strpos( $field['_cmb2_field_type_select'], 'tax' ) !== false && isset( $field['_cmb2_no_terms_text'] ) && $field['_cmb2_no_terms_text'] !== '' ) {
						$field_args['options']['no_terms_text'] = $field['_cmb2_no_terms_text'];
					}
					if ( isset( $field['_cmb2_repeatable_checkbox'] ) && $field['_cmb2_repeatable_checkbox'] === 'on' && $this->is_repeatable( $field['_cmb2_field_type_select'] ) ) {
						$field_args['repeatable'] = true;
					}
					if ( $field['_cmb2_field_type_select'] === 'url' && isset( $field['_cmb2_protocols_checkbox'] ) && ! empty( $field['_cmb2_protocols_checkbox'] ) ) {
						$field_args['protocols'] = $field['_cmb2_protocols_checkbox'];
					}
					if ( $this->afo( $field, array( 'text_money' ), '_cmb2_currency_text' ) ) {
						$field_args['before_field'] = $field['_cmb2_currency_text'];
					}
					if ( $this->afo( $field, array( 'text_time' ), '_cmb2_time_format' ) ) {
						$field_args['time_format'] = $field['_cmb2_time_format'];
					}
					if ( $this->afo( $field, array( 'text_date', 'text_date_timestamp' ), '_cmb2_date_format' ) ) {
						$field_args['date_format'] = $field['_cmb2_date_format'];
					}
					if ( $this->afo( $field, array( 'text_date_timestamp' ), '_cmb2_time_zone_key_select' ) ) {
						$field_args['timezone_meta_key'] = $field['_cmb2_time_zone_key_select'];
					}
					if ( isset( $field['_cmb2_none_checkbox'] ) && $field['_cmb2_none_checkbox'] === 'on' && $this->has_options( $field['_cmb2_field_type_select'] ) ) {
						$field_args['show_option_none'] = true;
					}
					if ( strpos( $field['_cmb2_field_type_select'], 'multicheck' ) !== false  && isset( $field['_cmb2_select_all_checkbox'] ) && $field['_cmb2_select_all_checkbox'] === 'on' ) {
						$field_args['select_all_button'] = false;
					}
					if ( $this->afo( $field, array( 'file' ), '_cmb2_add_upload_file_text' ) ) {
						$field_args['options']['add_upload_file_text'] = $field['_cmb2_add_upload_file_text'];
					}
					${ 'cmb_' . $id }->add_field( $field_args );

				}
			}
		}
	}
}

if ( ! function_exists( 'cmb2ae_metabox' ) ) {
	/**
	 * Main instance of CMB2_Meta_Box.
	 *
	 * @since  0.1.0
	 */
	function cmb2ae_metabox() {
		return CMB2_Meta_Box::get_instance();
	}
}
