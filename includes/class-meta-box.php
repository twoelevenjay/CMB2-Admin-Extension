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
		 * Current field array.
		 * Store the current field while adding user defined fields
		 *
		 * @var array
		 */
		private $field = array();

		/**
		 * Current field arguments array.
		 * Store the current field arguments while adding user defined fields
		 *
		 * @var array
		 */
		private $field_args = array();

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance;

		/**
		 * Initiate CMB2 Admin Extension object.
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
			if ( null === self::$instance ) {
				self::$instance = new self();
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

			if ( empty( $cmb2_settings ) || current_user_can( 'manage_options' ) ) {
				// No settings saved.
				return true;
			}

			$current_user  = wp_get_current_user();
			$allowed_users = isset( $cmb2_settings['_cmb2_user_multicheckbox'] ) ? $cmb2_settings['_cmb2_user_multicheckbox'] : array();

			return empty( $allowed_users ) || in_array( $current_user->ID, $allowed_users, true );
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
			if ( 'meta_box' === $screen->post_type ) {

				wp_register_style( 'cmb2_admin_styles', CMB2AE_URI . '/css/meta-box-fields.css', array(), '0.0.8' );
				wp_enqueue_style( 'cmb2_admin_styles' );
				wp_enqueue_script( 'cmb2_admin_scripts', CMB2AE_URI . '/js/meta-box-fields.js', array( 'jquery' ), '0.0.8', true );
			}
		}

		/**
		 * Check if the field type is repeatable.
		 *
		 * @since  0.0.6
		 * @param  string $field_type A CMB2 field type.
		 * @return boolean.
		 */
		public function is_repeatable( $field_type ) {

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
		 * Check if field types should have the options argument.
		 *
		 * @since  0.0.6
		 * @param  string $field_type A CMB2 field type.
		 * @return boolean.
		 */
		private function has_options( $field_type ) {

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
		 * Conditional to check if the field argument should be added..
		 *
		 * @since 1.1.4
		 * @param string $field_options String of options for fields liek select.
		 */
		private function add_option_arg( $field_options ) {
			$field_options = preg_split( "/\\r\\n|\\r|\\n/", $field_options );
			$options       = array();
			foreach ( $field_options as $option ) {
				$opt_arr = explode( ',', $option );
				if ( ! isset( $opt_arr[1] ) ) {
					$options[ $option ] = $option;
					continue;
				}
				$options[ $opt_arr[0] ] = $opt_arr[1];
			}
			$this->field_args['options'] = $options;
		}

		/**
		 * Conditional to check if the field argument should be added..
		 *
		 * @since 1.1.4
		 * @param array $arg_value A CMB2 field type.
		 */
		public function add_strpos_arg( $arg_value ) {

			if ( strpos( false !== $this->field['_cmb2_field_type_select'], $arg_value[0] ) && isset( $this->field[ $arg_value[2] ] ) && '' !== $this->field[ $arg_value[2] ] ) {

				if ( is_array( $arg_value[1] ) ) {
					$this->field_args[ $arg_value[1][0] ][ $arg_value[1][1] ] = $this->field[ $arg_value[2] ];
					return;
				}
				$this->field_args[ $arg_value[1] ] = $this->field[ $arg_value[2] ];
			}
		}

		/**
		 * Add the field argument.
		 *
		 * @since 1.1.4
		 * @param string       $arg   Field definition.
		 * @param string|array $value A CMB2 field type.
		 */
		public function add_arg( $arg, $value ) {

			if ( $this->should_add_arg( $this->field, $arg, $value[1] ) ) {

				if ( is_array( $value[1] ) ) {

					$this->field_args[ $value[0] ][ $value[1][0] ] = $this->field[ $value[1][1] ];
					return;
				}
				$this->field_args[ $value[0] ] = $this->field[ $value[1] ];
			}
		}

		/**
		 * Conditional to check if the field argument should be added.
		 *
		 * @since  0.1.4
		 * @param  array  $field      Field definition.
		 * @param  string $field_type A CMB2 field type.
		 * @param  string $field_key  Field key to check.
		 * @return boolean.
		 */
		public static function should_add_arg( $field, $field_type, $field_key ) {

			return ( $field_type === $field['_cmb2_field_type_select'] && ( ! empty( $field[ $field_key ] ) && '' !== $field[ $field_key ] ) );
		}

		/**
		 * Add the current field to the metabox.
		 *
		 * @since  0.2.0
		 * @param array  $field Field definition.
		 * @param object $metabox Metabox object.
		 * @param mixed  $group_field_id If is reaptable group this is the ID, and false otherwise.
		 */
		public function add_field( $field, $metabox, $group_field_id ) {

			$field = wp_parse_args(
				$field,
				array(
					'_cmb2_name_text'           => null,
					'_cmb2_decription_textarea' => null,
					'_cmb2_field_type_select'   => null,
					'_cmb2_options_textarea'    => false,
					'_cmb2_repeatable_checkbox' => null,
					'_cmb2_none_checkbox'       => null,
				)
			);

			$this->field = $field;
			$field_id    = '_' . strtolower( str_replace( ' ', '_', $field['_cmb2_name_text'] ) );

			$this->field_args = array(
				'name' => $field['_cmb2_name_text'],
				'desc' => $field['_cmb2_decription_textarea'],
				'id'   => $field_id,
				'type' => $field['_cmb2_field_type_select'],
			);

			$field_options = $field['_cmb2_options_textarea'];
			if ( $field_options ) {
				$this->add_option_arg( $field_options );
			}
			$should_add_strpos = array(
				array( 'tax', 'taxonomy', '_cmb2_tax_options_radio_inline' ),
				array( 'tax', array( 'options', 'no_terms_text' ), '_cmb2_no_terms_text' ),
				array( 'multicheck', 'select_all_button', '_cmb2_select_all_checkbox' ),
			);
			foreach ( $should_add_strpos as $arg_value ) {
				$this->add_strpos_arg( $arg_value );
			}
			if ( 'on' === $field['_cmb2_repeatable_checkbox'] && $this->is_repeatable( $field['_cmb2_field_type_select'] ) ) {
				$this->field_args['repeatable'] = true;
			}
			if ( 'on' === $field['_cmb2_none_checkbox'] && $this->has_options( $field['_cmb2_field_type_select'] ) ) {
				$this->field_args['show_option_none'] = true;
			}
			$should_add = array(
				'text_url'                         => array( 'protocols', '_cmb2_protocols_checkbox' ),
				'text_money'                       => array( 'before_field', '_cmb2_currency_text' ),
				'text_time'                        => array( 'time_format', '_cmb2_time_format' ),
				'text_datetime_timestamp_timezone' => array( 'time_format', '_cmb2_time_format', 'timezone_meta_key', '_cmb2_time_zone_key_select' ),
				'text_datetime_timestamp'          => array( 'time_format', '_cmb2_time_format' ),
				'text_date'                        => array( 'date_format', '_cmb2_date_format' ),
				'text_date_timestamp'              => array( 'date_format', '_cmb2_date_format' ),
				'select_timezone'                  => array( 'timezone_meta_key', '_cmb2_time_zone_key_select' ),
			);
			foreach ( $should_add as $arg => $value ) {
				$this->add_arg( $arg, $value );
			}
			if ( $group_field_id ) {
				$metabox->add_group_field( $group_field_id, $this->field_args );
				return;
			}
			$metabox->add_field( $this->field_args );
		}

		/**
		 * Covert the checkbox value of 'on' / '' to boolean.
		 *
		 * @since  0.2.0
		 * @param string $meta Sting value of checkbox on.
		 */
		public function string_to_bool( $meta ) {

			return 'on' === $meta ? true : false;
		}

		/**
		 * Get the metabox post meta.
		 *
		 * @since  0.2.0
		 * @param object $user_meta_box Post object.
		 */
		public function get_meta_data( $user_meta_box ) {

			$prefix     = $this->prefix;
			$metabox_id = $user_meta_box->ID;

			$meta_data['title']          = get_the_title( $metabox_id );
			$meta_data['id']             = str_replace( '-', '_', $user_meta_box->post_name );
			$meta_data['post_type']      = cmbf( $metabox_id, $prefix . 'post_type_multicheckbox' );
			$meta_data['post_id_text']   = cmbf( $metabox_id, $prefix . 'post_id_text' );
			$meta_data['context']        = cmbf( $metabox_id, $prefix . 'context_radio' );
			$meta_data['priority']       = cmbf( $metabox_id, $prefix . 'priority_radio' );
			$meta_data['show_names']     = $this->string_to_bool( cmbf( $metabox_id, $prefix . 'show_names' ) );
			$meta_data['disable_styles'] = $this->string_to_bool( cmbf( $metabox_id, $prefix . 'disable_styles' ) );
			$meta_data['closed']         = $this->string_to_bool( cmbf( $metabox_id, $prefix . 'closed' ) );
			$meta_data['repeatable']     = $this->string_to_bool( cmbf( $metabox_id, $prefix . 'repeatable_group' ) );
			$meta_data['group_desc']     = cmbf( $metabox_id, $prefix . 'group_description' );
			$entry_name                  = cmbf( $metabox_id, $prefix . 'entry_name' );
			$meta_data['entry_name']     = $entry_name ? $entry_name : 'Entry';
			$meta_data['fields']         = cmbf( $metabox_id, $prefix . 'custom_field' );

			return $meta_data;
		}

		/**
		 * Loop through user defined meta_box and creates the custom meta boxes and fields.
		 *
		 * @since 0.0.1
		 */
		public function init_user_defined_meta_boxes_and_fields() {

			$args = array(
				'post_type'        => 'meta_box',
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
				'suppress_filters' => false,
			);

			$user_meta_boxes = new WP_Query( $args );

			foreach ( $user_meta_boxes->posts as $user_meta_box ) {

				$meta_data = $this->get_meta_data( $user_meta_box );
				/**
				 * Initiate the metabox.
				 */
				$new_cmb2_args = array(
					'id'           => $meta_data['id'],
					'title'        => $meta_data['title'],
					'object_types' => $meta_data['post_type'], // Post type.
					'context'      => $meta_data['context'],
					'priority'     => $meta_data['priority'],
					'show_names'   => $meta_data['show_names'],
					'cmb_styles'   => $meta_data['disable_styles'],
					'closed'       => $meta_data['closed'],
				);
				if ( '' !== $meta_data['post_id_text'] ) {

					$new_cmb2_args['show_on'] = array(
						'key'   => 'id',
						'value' => explode( ',', $meta_data['post_id_text'] ),
					);
				}
				${ 'cmb_' . $meta_data['id'] } = new_cmb2_box( $new_cmb2_args );
				$meta_box                      = ${ 'cmb_' . $meta_data['id'] };
				$group_field_id                = false;
				if ( $meta_data['repeatable'] ) {

					$group_field_id = ${ 'cmb_' . $meta_data['id'] }->add_field(
						array(
							'id'          => '_' . $meta_data['id'],
							'type'        => 'group',
							'description' => $meta_data['group_desc'],
							'options'     => array(
								'group_title'   => $meta_data['entry_name'] . __( ' {#}', 'cmb2' ),
								'add_button'    => __( 'Add Another ', 'cmb2' ) . $meta_data['entry_name'],
								'remove_button' => __( 'Remove ', 'cmb2' ) . $meta_data['entry_name'],
								'sortable'      => true,
							),
						)
					);
				}
				foreach ( $meta_data['fields'] as $field ) {

					$this->add_field( $field, $meta_box, $group_field_id );
				}
			}
		}
	}
}
