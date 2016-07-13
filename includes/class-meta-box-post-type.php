<?php
/**
 * CMB2 Meta Box Post Type.
 *
 * @since  0.0.1
 *
 * @category  WordPress_Plugin
 * @package   CMB2 Admin Extension
 * @author    twoelevenjay
 * @license   GPL-2.0+
 */

if ( ! class_exists( 'CMB2_Meta_Box_Post_Type' ) ) {

	/**
	 * Class CMB2_Meta_Box_Post_Type.
	 */
	class CMB2_Meta_Box_Post_Type {

		/**
		 * Field prefix.
		 *
		 * @var string
		 */
		private $prefix = '_cmb2_';

		/**
		 * Settings Page hook.
		 *
		 * @var string
		 */
		protected $settings_page = '';

		/**
		 * Initiate CMB2 Admin Extension object.
		 *
		 * @todo For now plugin will use one main object, will consider 3 seperate objects in the future.
		 * @todo Comment.
		 *
		 * @since 0.0.1
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'init_post_type' ) );
			add_action( 'add_meta_boxes', array( $this, 'remove_meta_box_slugdiv' ) );
			add_action( 'admin_head', array( $this, 'hide_edit_slug_bar' ) );
			add_action( 'cmb2_init', array( $this, 'init_custom_field_settings' ) );
			add_action( 'cmb2_init', array( $this, 'init_meta_box_settings' ) );
			add_filter( 'cmb2_row_classes', array( $this, 'show_hide_classes' ), 10, 2 );
		}

		/**
		 * Create the Meta Box post type.
		 *
		 * @since  0.0.1
		 */
		public function init_post_type() {

			$labels = array(
				'name'                => _x( 'Meta Boxes', 'Post Type General Name', 'cmb2-admin-extension' ),
				'singular_name'       => _x( 'Meta Box', 'Post Type Singular Name', 'cmb2-admin-extension' ),
				'menu_name'           => __( 'CMB2', 'cmb2-admin-extension' ),
				'name_admin_bar'      => __( 'Meta Box', 'cmb2-admin-extension' ),
				'parent_item_colon'   => __( 'Meta Box:', 'cmb2-admin-extension' ),
				'all_items'           => __( 'All Meta Boxes', 'cmb2-admin-extension' ),
				'add_new_item'        => __( 'Add New Meta Box', 'cmb2-admin-extension' ),
				'add_new'             => __( 'Add New Meta Box', 'cmb2-admin-extension' ),
				'new_item'            => __( 'New Meta Box', 'cmb2-admin-extension' ),
				'edit_item'           => __( 'Edit Meta Box', 'cmb2-admin-extension' ),
				'update_item'         => __( 'Update Meta Box', 'cmb2-admin-extension' ),
				'view_item'           => __( 'View Meta Box', 'cmb2-admin-extension' ),
				'search_items'        => __( 'Search Meta Box', 'cmb2-admin-extension' ),
				'not_found'           => __( 'Not found', 'cmb2-admin-extension' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'cmb2-admin-extension' ),
			);
			$args = array(
				'label'               => __( 'meta_box', 'cmb2-admin-extension' ),
				'description'         => __( 'Create custom meta boxes and fields', 'cmb2-admin-extension' ),
				'labels'              => $labels,
				'supports'            => array(),
				'hierarchical'        => false,
				'rewrite'             => true,
				'supports'            => array( 'title' ),
				'public'              => true,
				'menu_position'       => 100,
				'menu_icon'           => 'dashicons-feedback',
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			);

			$args['show_ui']      = false;
			$args['show_in_menu'] = false;

			if ( $this->is_cmb2_allowed() ) {

				$args['show_ui']      = true;
				$args['show_in_menu'] = true;

			}
			register_post_type( 'meta_box', $args );
		}

		/**
		 * Set up the plugin settings page.
		 *
		 * @since  0.0.1
		 */
		public function remove_meta_box_slugdiv() {

			remove_meta_box( 'slugdiv', 'page', 'normal' );

		}

		/**
		 * Set up the plugin settings page.
		 *
		 * @since  0.0.1
		 */
		public function hide_edit_slug_bar() {

			global $post;

			if ( isset( $post->post_type ) && $post->post_type === 'meta_box' ) {

				echo '<style type="text/css"> #edit-slug-box, #minor-publishing { display: none; }</style>';

			}

		}


		/**
		 * Determine if current user has permission to CMB2 view plugins.
		 *
		 * @since  0.0.1
		 */
		private function is_cmb2_allowed() {

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
		 * Pass each item in an array through strpos().
		 *
		 * @since  0.0.8
		 * @todo Improve parameter documentation.
		 *
		 * @param string $field_id      Field ID.
		 * @param array  $field_classes CSS Classes.
		 * @param string $classes       CSS Classes to add.
		 */
		public function conditionally_add_class( $field_id, $field_classes, $classes ) {

			foreach ( $field_classes as $field => $class ) {
				if ( strpos( $field_id, $field ) !== false ) {
					return $classes . ' ' . $class;
				}
			}
			return $classes;

		}

		/**
		 * Add show/hide options callback.
		 *
		 * @since  0.0.8
		 * @todo Improve parameter documentation.
		 *
		 * @param array  $classes CSS Classes.
		 * @param object $field   CMB2 Field object.
		 */
		public function show_hide_classes( $classes, $field ) {

			$screen = get_current_screen();
			if ( $screen->post_type === 'meta_box' ) {
				$field_classes = array(
					'repeatable_checkbox'      => 'cmb_hide_field  text text_small text_medium text_email text_url text_money textarea textarea_small textarea_code text_date text_timeselect_timezone text_date_timestamp text_datetime_timestamp text_datetime_timestamp_timezone colorpicker select multicheck multicheck_inline',
					'protocols_checkbox'       => 'cmb_hide_field text_url',
					'currency_text'            => 'cmb_hide_field text_money',
					'date_format'              => 'cmb_hide_field text_date text_date_timestamp',
					'time_format'              => 'cmb_hide_field text_time text_datetime_timestamp text_datetime_timestamp_timezone',
					'time_zone_key_select'     => 'cmb_hide_field ',
					'options_textarea'         => 'cmb_hide_field radio radio_inline select multicheck multicheck_inline',
					'tax_options_radio_inline' => 'cmb_hide_field taxonomy_radio taxonomy_radio_inline taxonomy_select taxonomy_multicheck taxonomy_multicheck_inline',
					'no_terms_text'            => 'cmb_hide_field taxonomy_radio taxonomy_radio_inline taxonomy_select taxonomy_multicheck taxonomy_multicheck_inline',
					'none_checkbox'            => 'cmb_hide_field radio radio_inline select',
					'select_all_checkbox'      => 'cmb_hide_field multicheck multicheck_inline taxonomy_multicheck taxonomy_multicheck_inline',
					'add_upload_file_text'     => 'cmb_hide_field file',
					'default_value_text'       => 'default_value',
				);
				$classes = $this->conditionally_add_class( $field->args['id'], $field_classes, $classes );
			}
			return $classes;

		}

		/**
		 * Get users for the options on the settings page.
		 *
		 * @since  0.0.6
		 */
		public function tax_options() {

			$taxonomies  = get_taxonomies( array( 'public' => true ), 'objects' );
			$tax_options = array();
			foreach ( $taxonomies as $taxonomy ) {

				$tax_options[ $taxonomy->name ] = $taxonomy->labels->name;

			}
			return $tax_options;

		}

		/**
		 * Add custom meta box to the Meta Box post type.
		 *
		 * @since  0.0.1
		 */
		public function init_meta_box_settings() {

			// Start with an underscore to hide fields from custom fields list.
			$prefix            = $this->prefix;
			$post_type_objects = get_post_types( '', 'object' );
			$post_types        = array();

			foreach ( $post_type_objects as $post_type_object ) {
				if ( $post_type_object->show_ui && $post_type_object->name !== 'meta_box' ) {
					$post_types[ $post_type_object->name ] = $post_type_object->label;
				}
			}

			/**
			 * Initiate the metabox.
			 */
			$cmb = new_cmb2_box( array(
				'id'            => 'metabox_settings',
				'title'         => __( 'Metabox Settings', 'cmb2-admin-extension' ),
				'object_types'  => array( 'meta_box' ), // Post type.
				'context'       => 'side',
				'priority'      => 'low',
				'show_names'    => true,
			) );

			$cmb->add_field( array(
				'name'    => __( 'Post Types', 'cmb2-admin-extension' ),
				'desc'    => __( 'Check the post types that you want to add this meta box to.', 'cmb2-admin-extension' ),
				'id'      => $prefix . 'post_type_multicheckbox',
				'type'    => 'multicheck',
				'options' => $post_types,
				'inline'  => true,
			) );

			$cmb->add_field( array(
				'name'    => __( 'Priority', 'cmb2-admin-extension' ),
				'desc'    => __( 'This is to control what order your meta box appears in.', 'cmb2-admin-extension' ),
				'id'      => $prefix . 'priority_radio',
				'type'    => 'radio',
				'default' => 'high',
				'options' => array(
					'high'    => __( 'High', 'cmb2-admin-extension' ),
					'core'    => __( 'Core', 'cmb2-admin-extension' ),
					'default' => __( 'Default', 'cmb2-admin-extension' ),
					'low'     => __( 'Low', 'cmb2-admin-extension' ),
				),
				'inline'  => true,
			) );

			$cmb->add_field( array(
				'name'    => __( 'Context', 'cmb2-admin-extension' ),
				'desc'    => __( 'This additional controls for positioning of the meta box. Advanced displays after Normal. Side places the meta box in the right sidebar.', 'cmb2-admin-extension' ),
				'id'      => $prefix . 'context_radio',
				'type'    => 'radio',
				'default' => 'advanced',
				'options' => array(
					'advanced' => __( 'Advanced', 'cmb2-admin-extension' ),
					'normal'   => __( 'Normal', 'cmb2-admin-extension' ),
					'side'     => __( 'Side', 'cmb2-admin-extension' ),
				),
				'inline'  => true,
			) );

			$cmb->add_field( array(
				'name'    => __( 'Show Names', 'cmb2-admin-extension' ),
				'desc'    => __( 'Show field names on the left', 'cmb2-admin-extension' ),
				'id'      => $prefix . 'show_names',
				'type'    => 'checkbox',
				'default' => 'on',
			) );

			$cmb->add_field( array(
				'name' => __( 'Disable CMB2 Styles', 'cmb2-admin-extension' ),
				'desc' => __( 'Check to disable the CMB stylesheet', 'cmb2-admin-extension' ),
				'id'   => $prefix . 'disable_styles',
				'type' => 'checkbox',
			) );

			$cmb->add_field( array(
				'name' => __( 'Closed by Default', 'cmb2-admin-extension' ),
				'desc' => __( 'Check to keep the metabox closed by default', 'cmb2-admin-extension' ),
				'id'   => $prefix . 'closed',
				'type' => 'checkbox',
			) );

		}

		/**
		 * Add custom fields to the Meta Box post type
		 *
		 * @since  0.0.1
		 */
		public function init_custom_field_settings() {

			$prefix = $this->prefix;
			$cmb_group = new_cmb2_box( array(
				'id'           => $prefix . 'custom_fields',
				'title'        => __( 'Custom Field Settings', 'cmb2-admin-extension' ),
				'object_types' => array( 'meta_box' ),
			) );

			$group_field_id = $cmb_group->add_field( array(
				'id'          => $prefix . 'custom_field',
				'type'        => 'group',
				'description' => __( 'Add the custom fields that you want to display with in this meta box.', 'cmb2-admin-extension' ),
				'options'     => array(
					'group_title'   => __( 'Field {#}', 'cmb2-admin-extension' ), // {#} gets replaced by row number.
					'add_button'    => __( 'Add Another Field', 'cmb2-admin-extension' ),
					'remove_button' => __( 'Remove Field', 'cmb2-admin-extension' ),
					'sortable'      => true, // Beta.
				),
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name'       => __( 'Name', 'cmb2-admin-extension' ),
				'desc'       => __( 'Add a field name.', 'cmb2-admin-extension' ),
				'id'         => $prefix . 'name_text',
				'type'       => 'text',
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name' => __( 'Description', 'cmb2-admin-extension' ),
				'desc' => __( 'Add a field description.', 'cmb2-admin-extension' ),
				'id'   => $prefix . 'decription_textarea',
				'type' => 'textarea_small',
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name'             => __( 'Field Type', 'cmb2-admin-extension' ),
				'desc'             => __( 'Pick what type of field to display.', 'cmb2-admin-extension' ) . '</br>' . __( 'For a full list of fields visit <a href="https://github.com/WebDevStudios/CMB2/wiki/Field-Types">the documentation</a>.', 'cmb2-admin-extension' ) . '</br>* ' . __( 'Not available as a repeatable field.', 'cmb2-admin-extension' ) . '</br>† ' . __( 'Use file_list for repeatable.', 'cmb2-admin-extension' ),
				'id'               => $prefix . 'field_type_select',
				'attributes'       => array(
					'class' => 'cmb2_select field_type_select',
				),
				'type'             => 'select',
				'show_option_none' => false,
				'options'          => array(
					'title'                            => 'title: ' . __( 'An arbitrary title field', 'cmb2-admin-extension' ) . ' *',
					'text'                             => 'text: ' . __( 'Text', 'cmb2-admin-extension' ),
					'text_small'                       => 'text_small: ' . __( 'Text Small', 'cmb2-admin-extension' ),
					'text_medium'                      => 'text_medium: ' . __( 'Text Medium', 'cmb2-admin-extension' ),
					'text_email'                       => 'text_email: ' . __( 'Email', 'cmb2-admin-extension' ),
					'text_url'                         => 'text_url: ' . __( 'URL', 'cmb2-admin-extension' ),
					'text_money'                       => 'text_money: ' . __( 'Money', 'cmb2-admin-extension' ),
					'textarea'                         => 'textarea: ' . __( 'Text Area', 'cmb2-admin-extension' ),
					'textarea_small'                   => 'textarea_small: ' . __( 'Text Area Small', 'cmb2-admin-extension' ),
					'textarea_code'                    => 'textarea_code: ' . __( 'Text Area Code', 'cmb2-admin-extension' ),
					'text_date'                        => 'text_date: ' . __( 'Date Picker', 'cmb2-admin-extension' ),
					'text_time'                        => 'text_time: ' . __( 'Time picker', 'cmb2-admin-extension' ),
					'select_timezone'                  => 'select_timezone: ' . __( 'Time zone dropdown', 'cmb2-admin-extension' ),
					'text_date_timestamp'              => 'text_date_timestamp: ' . __( 'Date Picker (UNIX timestamp)', 'cmb2-admin-extension' ),
					'text_datetime_timestamp'          => 'text_datetime_timestamp: ' . __( 'Text Date/Time Picker Combo (UNIX timestamp)', 'cmb2-admin-extension' ),
					'text_datetime_timestamp_timezone' => 'text_datetime_timestamp_timezone: ' . __( 'Text Date/Time Picker/Time zone Combo (serialized DateTime object)', 'cmb2-admin-extension' ),
					'colorpicker'                      => 'colorpicker: ' . __( 'Color picker', 'cmb2-admin-extension' ),
					'radio'                            => 'radio: ' . __( 'Radio Buttons', 'cmb2-admin-extension' ) . ' *',
					'radio_inline'                     => 'radio_inline: ' . __( 'Radio Buttons Inline', 'cmb2-admin-extension' ) . ' *',
					'taxonomy_radio'                   => 'taxonomy_radio: ' . __( 'Taxonomy Radio Buttons', 'cmb2-admin-extension' ) . ' *',
					'taxonomy_radio_inline'            => 'taxonomy_radio_inline: ' . __( 'Taxonomy Radio Buttons Inline', 'cmb2-admin-extension' ) . ' *',
					'select'                           => 'select: ' . __( 'Select', 'cmb2-admin-extension' ),
					'taxonomy_select'                  => 'taxonomy_select: ' . __( 'Taxonomy Select', 'cmb2-admin-extension' ) . ' *',
					'checkbox'                         => 'checkbox: ' . __( 'Checkbox', 'cmb2-admin-extension' ) . ' *',
					'multicheck'                       => 'multicheck: ' . __( 'Multiple Checkboxes', 'cmb2-admin-extension' ),
					'multicheck_inline'                => 'multicheck_inline: ' . __( 'Multiple Checkboxes Inline', 'cmb2-admin-extension' ),
					'taxonomy_multicheck'              => 'taxonomy_multicheck: ' . __( 'Taxonomy Multiple Checkboxes', 'cmb2-admin-extension' ) . ' *',
					'taxonomy_multicheck_inline'       => 'taxonomy_multicheck_inline: ' . __( 'Taxonomy Multiple Checkboxes Inline', 'cmb2-admin-extension' ) . ' *',
					'wysiwyg'                          => 'wysiwyg: ' . __( '(TinyMCE)', 'cmb2-admin-extension' ) . ' *',
					'file'                             => 'file: ' . __( 'Image/File upload', 'cmb2-admin-extension' ) . ' *†',
					'file_list'                        => 'file_list: ' . __( 'Image/File list upload', 'cmb2-admin-extension' ),
					'oembed'                           => 'oembed: ' . __( 'Converts oembed urls (instagram, twitter, youtube, etc. oEmbed in the Codex)', 'cmb2-admin-extension' ),
					// 'group'                            => 'group: ' . __( 'Hybrid field that supports adding other fields as a repeatable group.', 'cmb2-admin-extension' ) . ' *',
				),
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name' => __( 'Repeatable', 'cmb2-admin-extension' ),
				'desc' => __( 'Check this box to make the field repeatable. Field types marked with a "*" are not repeatable.', 'cmb2-admin-extension' ),
				'id'   => $prefix . 'repeatable_checkbox',
				'type' => 'checkbox',
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name'    => __( 'Protocols', 'cmb2-admin-extension' ),
				'desc'    => __( 'Check the boxes for each allowed protocol. If you are unsure then do nothing and all protocols will be allowed.', 'cmb2-admin-extension' ),
				'id'      => $prefix . 'protocols_checkbox',
				'type'    => 'multicheck_inline',
				'options' => array(
					'http'   => 'http',
					'https'  => 'https',
					'ftp'    => 'ftp',
					'ftps'   => 'ftps',
					'mailto' => 'mailto',
					'news'   => 'news',
					'irc'    => 'irc',
					'gopher' => 'gopher',
					'nntp'   => 'nntp',
					'feed'   => 'feed',
					'telnet' => 'telnet',
				),
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name'    => __( 'Currency Symbol', 'cmb2-admin-extension' ),
				'desc'    => __( 'Replaces the default "$".', 'cmb2-admin-extension' ),
				'id'      => $prefix . 'currency_text',
				'type'    => 'text_small',
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name'    => __( 'Date Format', 'cmb2-admin-extension' ),
				'desc'    => __( 'Default:', 'cmb2-admin-extension' ) . ' "m/d/Y". ' . __( 'See <a target="_blank" href="http://php.net/manual/en/function.date.php">php.net/manual/en/function.date.php</a>.', 'cmb2-admin-extension' ),
				'id'      => $prefix . 'date_format',
				'type'    => 'text_small',
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name'    => __( 'Time Format', 'cmb2-admin-extension' ),
				'desc'    => __( 'Default:', 'cmb2-admin-extension' ) . ' "h:i A". ' . __( 'See <a target="_blank" href="http://php.net/manual/en/function.date.php">php.net/manual/en/function.date.php</a>.', 'cmb2-admin-extension' ),
				'id'      => $prefix . 'time_format',
				'type'    => 'text_small',
			) );

			/*
			@todo Make this field generate options from predefined time zone fields. Maybe both from previously saved fields and ones just created via javascript.

			$cmb_group->add_group_field( $group_field_id, array(
				'name'             =>  __( 'Time Zone', 'cmb2-admin-extension' ),
				'desc'             =>  __( 'Select a time zone field to make this field honor.', 'cmb2-admin-extension' ),
				'id'               => $prefix . 'time_zone_key_select',
				'type'             => 'select',
				'options'          => array(),
			) );
			*/

			$cmb_group->add_group_field( $group_field_id, array(
				'name' => __( 'Options', 'cmb2-admin-extension' ),
				// 'desc' => __( 'Your field type requires manual options. Please add one option per line. Type value then name seprated by a comma.<br>Example:<br>sml,Small<br>med,Medium<br>lrg,Large', 'cmb2-admin-extension' ),
				'desc' => __( 'If your field type requires manual options, please add one option per line. Type value then name seprated by a comma.<br>Example:<br>sml,Small<br>med,Medium<br>lrg,Large', 'cmb2-admin-extension' ),
				'id'   => $prefix . 'options_textarea',
				'type' => 'textarea_small',
			) );

			$tax_options = $this->tax_options();
			reset( $tax_options );
			$default_tax_options = key( $tax_options );
			$cmb_group->add_group_field( $group_field_id, array(
				'name'    => __( 'Taxonomy Options', 'cmb2-admin-extension' ),
				'id'      => $prefix . 'tax_options_radio_inline',
				'type'    => 'radio_inline',
				'options' => $this->tax_options(),
				'default' => $default_tax_options,
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name'    => __( 'No Terms Text', 'cmb2-admin-extension' ),
				'desc'    => __( 'Enter text to change the text that is shown when no terms are found.', 'cmb2-admin-extension' ) . '</br>' . __( 'Default:', 'cmb2-admin-extension' ) . ' "' . __( 'No terms', 'cmb2-admin-extension' ) . '".',
				'id'      => $prefix . 'no_terms_text',
				'type'    => 'text_small',
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name' => __( 'Include a "none" option', 'cmb2-admin-extension' ),
				'desc' => __( 'Check this box to include a "none" option with this field.', 'cmb2-admin-extension' ),
				'id'   => $prefix . 'none_checkbox',
				'type' => 'checkbox',
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name' => __( 'Disable select all', 'cmb2-admin-extension' ),
				'desc' => __( 'Check this box to disable the select all button for this field.', 'cmb2-admin-extension' ),
				'id'   => $prefix . 'select_all_checkbox',
				'type' => 'checkbox',
			) );

			$cmb_group->add_group_field( $group_field_id, array(
				'name'    => __( 'Button Text', 'cmb2-admin-extension' ),
				'desc'    => __( 'Enter text to change the upload button text.', 'cmb2-admin-extension' ) . '</br>' . __( 'Default:', 'cmb2-admin-extension' ) . ' "' . __( 'Add or Upload File', 'cmb2-admin-extension' ) . '".',
				'id'      => $prefix . 'add_upload_file_text',
				'type'    => 'text_small',
			) );

			/*
			$cmb_group->add_group_field( $group_field_id, array(
				'name' => __( 'Default Value', 'cmb2-admin-extension' ),
				'desc' => __( 'Enter a value to use as a default for this field. If you want a checkbox to be checked enter "on". Leave blank for no default value.', 'cmb2-admin-extension' ),
				'id'   => $prefix . 'default_value_text',
				'type' => 'text',
			) );
			*/
		}
	}

	$cmb2_meta_box_post_type = new CMB2_Meta_Box_Post_Type();
}
