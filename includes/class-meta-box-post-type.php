<?php

/**
 * CMB2 Meta Box Post Type
 *
 * @since  0.0.1
 *
 * @category  WordPress_Plugin
 * @package   CMB2 Admin Extension
 * @author    twoelevenjay
 * @license   GPL-2.0+
 */
class CMB2_Meta_Box_Post_Type {

	/**
	 * Field prefix
	 * @var string
	 */
	private $prefix = '_cmb2_';

	/**
	 * Settings key, and option page slug
	 * @var string
	 */
	private $settings_key = '_cmb2_settings';
	/**
	 * Settings page metabox id
	 * @var string
	 */
	private $settings_metabox_id = '_cmb2_settings_metabox';
	/**
	 * Settings Page title
	 * @var string
	 */
	protected $settings_title = '';
	/**
	 * Settings Page hook
	 * @var string
	 */
	protected $settings_page = '';

	/**
	 * Initiate CMB2 Admin Extension object
	 * TODO for now plugin will use one main object, will consider 3 seperate objects in the future
	 * @since 0.0.1
	 */
	public function __construct() {

		// TODO comment

		$this->settings_title = __( 'CMB2 Settings', 'cmb2-admin-extension' );

		add_action( 'init', array( $this, 'init_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_box_slugdiv' ) );
		add_action( 'admin_head', array( $this, 'hide_edit_slug_bar' ) );
		add_action( 'pre_current_active_plugins', array( $this, 'hide_cmb2_plugins' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'cmb2_row_classes', array( $this, 'show_hide_classes' ), 10, 2 );

		add_action( 'cmb2_init', array( $this, 'init_meta_box_settings' ) );
		add_action( 'cmb2_init', array( $this, 'init_custom_field_settings' ) );
		add_action( 'cmb2_init', array( $this, 'init_user_defined_meta_boxes_and_fields' ) );

	}

	/**
	 * Create the Meta Box post type
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
			'supports'            => array( ),
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

		if ( $this->is_cmb2_allowed() ) {

			$args['show_ui']      = true;
			$args['show_in_menu'] = true;

		} else{

			$args['show_ui']      = false;
			$args['show_in_menu'] = false;

		}
		register_post_type( 'meta_box', $args );



	}

	/**
	 * Set up the plugin settings page
	 * @since  0.0.1
	 */
	public function remove_meta_box_slugdiv() {

		remove_meta_box( 'slugdiv', 'page', 'normal' );

	}

	/**
	 * Set up the plugin settings page
	 * @since  0.0.1
	 */
	public function hide_edit_slug_bar() {

		global $post;

		if ( isset( $post->post_type ) && $post->post_type === 'meta_box' ) {

			$hide_slugs = '<style type="text/css"> #edit-slug-box, #minor-publishing { display: none; }</style>';
			echo $hide_slugs;

		}

	}


	/**
	 * Determine if current user has permission to CMB2 view plugins
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

		if ( empty( $allowed_users ) || in_array( $current_user->ID, $allowed_users ) ) {

			return true;

		} else {

			return false;

		}
	}

	/**
	 * Only show CMB2 plugins to users defined in settings
	 * @since  0.0.1
	 */
	public function hide_cmb2_plugins() {

		global $wp_list_table;
		if ( ! $this->is_cmb2_allowed() ) {
			$to_hide = array( CMB2AE_CMB2_PLUGIN_FILE, 'cmb2-admin-extension/cmb2-admin-extension.php' );
			$plugins = $wp_list_table->items;
			foreach ( $plugins as $key => $val ) {
				if ( in_array( $key, $to_hide ) ) {
					unset( $wp_list_table->items[ $key ] );
				}
			}
		}

	}

	/**
	 * Enqueue CMB2 Admin Extension scripts and styles
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
	 * Pass each item in an array through strpos()
	 * @since  0.0.8
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
	 * Add show/hide options callback
	 * @since  0.0.8
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
	 * Get users for the soptions on the ettings page
	 * @since  0.0.6
	 */
	public function tax_options() {

		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		$tax_options = array();
		foreach ( $taxonomies as $taxonomy ) {

			$tax_options[ $taxonomy->name ] = $taxonomy->labels->name;

		}
		return $tax_options;

	}

	/**
	 * Add custom meta box to the Meta Box post type
	 * @since  0.0.1
	 */
	public function init_meta_box_settings() {

		// Start with an underscore to hide fields from custom fields list
		$prefix = $this->prefix;
		$post_type_objects = get_post_types( '', 'object' );
		$post_types = array();

		foreach ( $post_type_objects as $post_type_object ) {
			if ( $post_type_object->show_ui && $post_type_object->name !== 'meta_box' ) {
				$post_types[ $post_type_object->name ] = $post_type_object->label;
			}

		}

		/**
		* Initiate the metabox
		*/
		$cmb = new_cmb2_box( array(
			'id'            => 'metabox_settings',
			'title'         => __( 'Metabox Settings', 'cmb2-admin-extension' ),
			'object_types'  => array( 'meta_box', ), // Post type
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
	 * @since  0.0.1
	 */
	public function init_custom_field_settings() {

		// Start with an underscore to hide fields from custom fields list
		$prefix = $this->prefix;

		/**
		* Repeatable Field Groups
		*/
		$cmb_group = new_cmb2_box( array(
			'id'           => $prefix . 'custom_fields',
			'title'        => __( 'Custom Field Settings', 'cmb2-admin-extension' ),
			'object_types' => array( 'meta_box', ),
		) );

		// $group_field_id is the field id string, so in this case: $prefix . 'demo'
		$group_field_id = $cmb_group->add_field( array(
			'id'          => $prefix . 'custom_field',
			'type'        => 'group',
			'description' => __( 'Add the custom fields that you want to display with in this meta box.', 'cmb2-admin-extension' ),
			'options'     => array(
				'group_title'   => __( 'Field {#}', 'cmb2-admin-extension' ), // {#} gets replaced by row number
				'add_button'    => __( 'Add Another Field', 'cmb2-admin-extension' ),
				'remove_button' => __( 'Remove Field', 'cmb2-admin-extension' ),
				'sortable'      => true, // beta
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
			'name'             =>  __( 'Field Type', 'cmb2-admin-extension' ),
			'desc'             =>  __( 'Pick what type of field to display. For a full list of fields visit <a href="https://github.com/WebDevStudios/CMB2/wiki/Field-Types">the documentation</a>. * Not available as a repeatable field † Use file_list for repeatable', 'cmb2-admin-extension' ),
			'id'               => $prefix . 'field_type_select',
			'attributes'       => array(
				'class' => 'cmb2_select field_type_select'
			),
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => array(
				'title'                            => 'title: An arbitrary title field *',
				'text'                             => 'text: Text',
				'text_small'                       => 'text_small: Text Small',
				'text_medium'                      => 'text_medium: Text Medium',
				'text_email'                       => 'text_email: Email',
				'text_url'                         => 'text_url: URL',
				'text_money'                       => 'text_money: Money',
				'textarea'                         => 'textarea: Text Area',
				'textarea_small'                   => 'textarea_small: Text Area Small',
				'textarea_code'                    => 'textarea_code: Text Area Code',
				'text_date'                        => 'text_date: Date Picker',
				'text_time'                        => 'text_time: Time picker',
				'select_timezone'                  => 'select_timezone: Time zone dropdown',
				'text_date_timestamp'              => 'text_date_timestamp: Date Picker (UNIX timestamp)',
				'text_datetime_timestamp'          => 'text_datetime_timestamp: Text Date/Time Picker Combo (UNIX timestamp)',
				'text_datetime_timestamp_timezone' => 'text_datetime_timestamp_timezone: Text Date/Time Picker/Time zone Combo (serialized DateTime object)',
				'colorpicker'                      => 'colorpicker: Color picker',
				'radio'                            => 'radio: Radio Buttons*',
				'radio_inline'                     => 'radio_inline: Radio Buttons Inline*',
				'taxonomy_radio'                   => 'taxonomy_radio: Taxonomy Radio Buttons*',
				'taxonomy_radio_inline'            => 'taxonomy_radio_inline: Taxonomy Radio Buttons Inline*',
				'select'                           => 'select: Select',
				'taxonomy_select'                  => 'taxonomy_select: Taxonomy Select*',
				'checkbox'                         => 'checkbox: Checkbox*',
				'multicheck'                       => 'multicheck: Multiple Checkboxes',
				'multicheck_inline'                => 'multicheck_inline: Multiple Checkboxes Inline',
				'taxonomy_multicheck'              => 'taxonomy_multicheck: Taxonomy Multiple Checkboxes*',
				'taxonomy_multicheck_inline'       => 'taxonomy_multicheck_inline: Taxonomy Multiple Checkboxes Inline*',
				'wysiwyg'                          => 'wysiwyg: (TinyMCE) *',
				'file'                             => 'file: Image/File upload *†',
				'file_list'                        => 'file_list: Image/File list upload',
				'oembed'                           => 'oembed: Converts oembed urls (instagram, twitter, youtube, etc. oEmbed in the Codex)',
				'group'                            => 'group: Hybrid field that supports adding other fields as a repeatable group. *',
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
			'desc'    => __( 'Defaults to "m/d/Y". See <a target="_blank" href="http://php.net/manual/en/function.date.php">php.net/manual/en/function.date.php</a>.', 'cmb2-admin-extension' ),
			'id'      => $prefix . 'date_format',
			'type'    => 'text_small',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name'    => __( 'Time Format', 'cmb2-admin-extension' ),
			'desc'    => __( 'Defaults to "h:i A". See <a target="_blank" href="http://php.net/manual/en/function.date.php">php.net/manual/en/function.date.php</a>.', 'cmb2-admin-extension' ),
			'id'      => $prefix . 'time_format',
			'type'    => 'text_small',
		) );

		// TODO make this field generate optins from predefined time zone fields. Maybe both from previously saved fields and ones just created via javascript.
		//$cmb_group->add_group_field( $group_field_id, array(
		//	'name'             =>  __( 'Time Zone', 'cmb2-admin-extension' ),
		//	'desc'             =>  __( 'Select a time zone field to make this field honor.', 'cmb2-admin-extension' ),
		//	'id'               => $prefix . 'time_zone_key_select',
		//	'type'             => 'select',
		//	'options'          => array(),
		//) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => __( 'Options', 'cmb2-admin-extension' ),
			//'desc' => __( 'Your field type requires manual options. Please add one option per line. Type value then name seprated by a comma.<br>Example:<br>sml,Small<br>med,Medium<br>lrg,Large', 'cmb2-admin-extension' ),
			'desc' => __( 'If your field type requires manual options, please add one option per line. Type value then name seprated by a comma.<br>Example:<br>sml,Small<br>med,Medium<br>lrg,Large', 'cmb2-admin-extension' ),
			'id'   => $prefix . 'options_textarea',
			'type' => 'textarea_small',
		) );

		$tax_options = $this->tax_options();
		reset($tax_options);
		$default_tax_options = key($tax_options);
		$cmb_group->add_group_field( $group_field_id, array(
			'name'    => __( 'Taxonomy Options', 'cmb2-admin-extension' ),
			'id'      => $prefix . 'tax_options_radio_inline',
			'type'    => 'radio_inline',
			'options' => $this->tax_options(),
			'default' => $default_tax_options,
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name'    => __( 'No Terms Text', 'cmb2-admin-extension' ),
			'desc'    => __( 'Enter text to change the text that is shown when no terms are found. Default: "No terms".', 'cmb2-admin-extension' ),
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
			'desc'    => __( 'Enter text to change the upload button text. Default: "Add or Upload File".', 'cmb2-admin-extension' ),
			'id'      => $prefix . 'add_upload_file_text',
			'type'    => 'text_small',
		) );

		$cmb_group->add_group_field( $group_field_id, array(
			'name' => __( 'Default Value', 'cmb2-admin-extension' ),
			'desc' => __( 'Enter a value to use as a default for this field. If you want a checkbox to be checked enter "on". Leave blank for no default value.', 'cmb2-admin-extension' ),
			'id'   => $prefix . 'default_value_text',
			'type' => 'text',
		) );

	}


	/**
	 * cmbf() shortens the get_post_meta() function.
	 * @since  0.0.1
	 */
	static function cmbf( $ID, $field ) {

		return get_post_meta( $ID, $field, true );

	}

	/**
	 * is_repeatable() shortens the get_post_meta() function.
	 * @since  0.0.6
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
		return in_array( $field_type, $repeatable_fields );

	}

	/**
	 * is_repeatable() shortens the get_post_meta() function.
	 * @since  0.0.6
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
		return in_array( $field_type, $options_fields );

	}

	/**
	 * is_repeatable() shortens the get_post_meta() function.
	 * @since  0.0.6
	 */
	static function afo( $field, $field_type, $option_value ) {

		return in_array( $field['_cmb2_field_type_select'], $field_type ) && isset( $field['_cmb2_add_upload_file_text'] ) && $field['_cmb2_add_upload_file_text'] != '';

	}

	/**
	 * Loop through user defined meta_box and creates the custom meta boxes and fields.
	 * @since  0.0.1
	 */
	public function init_user_defined_meta_boxes_and_fields() {

		$args = array(
			'post_type'        => 'meta_box',
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
		);

		$prefix = $this->prefix;

		$user_meta_boxes = get_posts( $args );

		foreach ( $user_meta_boxes as $user_meta_box ) {

			$ID = $user_meta_box->ID;

			$title          = get_the_title( $ID );
			$id             = str_replace( '-', '_', $user_meta_box->post_name );
			$post_type      = $this->cmbf( $ID, $prefix . 'post_type_multicheckbox' );
			$context        = $this->cmbf( $ID, $prefix . 'context_radio' );
			$priority       = $this->cmbf( $ID, $prefix . 'priority_radio' );
			$show_names     = $this->cmbf( $ID, $prefix . 'show_names' );
			$disable_styles = $this->cmbf( $ID, $prefix . 'disable_styles' );
			$closed         = $this->cmbf( $ID, $prefix . 'closed' );
			$fields         = $this->cmbf( $ID, $prefix . 'custom_field' );

			/**
			 * Initiate the metabox
			 */
			${ 'cmb_' . $id } = new_cmb2_box( array(
				'id'            => $id,
				'title'         => $title,
				'object_types'  => $post_type, // Post type
				'context'       => $context,
				'priority'      => $priority,
			) );

			foreach ( $fields as $field ) {

				$field_id = '_' . strtolower( str_replace( ' ', '_', $field['_cmb2_name_text'] ) );

				$field_args = array(
					'name'       => $field['_cmb2_name_text'],
					'desc'       => $field['_cmb2_decription_textarea'],
					'id'         => $field_id ,
					'type'       => $field['_cmb2_field_type_select'],
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
				if ( strpos($field['_cmb2_field_type_select'], 'tax') !== false  && $field['_cmb2_tax_options_radio_inline'] != '' ) {
					$field_args['taxonomy'] = $field['_cmb2_tax_options_radio_inline'];
				}
				if ( strpos($field['_cmb2_field_type_select'], 'tax') !== false && isset( $field['_cmb2_no_terms_text'] ) && $field['_cmb2_no_terms_text'] != '' ) {
					$field_args['options']['no_terms_text'] = $field['_cmb2_no_terms_text'];
				}
				if ( isset( $field['_cmb2_repeatable_checkbox'] ) && $field['_cmb2_repeatable_checkbox'] == 'on' && $this->is_repeatable( $field['_cmb2_field_type_select'] ) ) {
					$field_args['repeatable'] = true;
				}
				if ( $field['_cmb2_field_type_select'] == 'url' && isset( $field['_cmb2_protocols_checkbox'] ) && !empty( $field['_cmb2_protocols_checkbox'] ) ) {
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
				if ( isset( $field['_cmb2_none_checkbox'] ) && $field['_cmb2_none_checkbox'] == 'on' && $this->has_options( $field['_cmb2_field_type_select'] ) ) {
					$field_args['show_option_none'] = true;
				}
				if ( strpos($field['_cmb2_field_type_select'], 'multicheck') !== false  && isset( $field['_cmb2_select_all_checkbox'] ) && $field['_cmb2_select_all_checkbox'] == 'on' ) {
					$field_args['select_all_button'] = false;
				}
				if ( $this->afo( $field, array( 'file' ), '_cmb2_add_upload_file_text' ) ) {
					$field_args['options']['add_upload_file_text'] = $field['_cmb2_add_upload_file_text'];
				}
				${ 'cmb_'.$id }->add_field( $field_args );

			}

		}
	}
}

$CMB2_Meta_Box_Post_Type = new CMB2_Meta_Box_Post_Type();
