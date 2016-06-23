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
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_box_slugdiv' ) );
		add_action( 'admin_head', array( $this, 'hide_edit_slug_bar' ) );
		add_action( 'pre_current_active_plugins', array( $this, 'hide_cmb2_plugins' ) );

		add_action( 'cmb2_init', array( $this, 'init_meta_box_settings' ) );
		add_action( 'cmb2_init', array( $this, 'init_custom_field_settings' ) );
		add_action( 'cmb2_init', array( $this, 'init_cmb2_settings_page' ) );
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
	public function add_settings_page() {

		if ( $this->is_cmb2_allowed() ) {

			$this->settings_page = add_submenu_page( 'edit.php?post_type=meta_box', __( 'CMB2 Settings', 'cmb2-admin-extension' ), __( 'CMB2 Settings', 'cmb2-admin-extension' ), 'edit_posts', $this->settings_key, array( $this, 'settings_page' ) );

			add_action( "admin_print_styles-{$this->settings_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );

		}

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

	public function register_settings() {

		register_setting( $this->settings_key, $this->settings_key );

	}

	/**
	 * Plugin settings page call back
	 * @since  0.0.1
	 */
	public function settings_page() {

		?>
		<div class="wrap cmb2-options-page <?php echo $this->settings_key; ?>">
			<h2><?php echo $this->settings_title; ?></h2>
			<?php cmb2_metabox_form( $this->settings_metabox_id, $this->settings_key, array( 'cmb_styles' => false ) ); ?>
		</div>
		<?php

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
	function hide_cmb2_plugins() {

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
	 * Get users for the soptions on the ettings page
	 * @since  0.0.6
	 */
	public function user_options() {

		$users = get_users();
		$user_options = array();
		foreach ( $users as $user ) {

			if ( user_can( $user, 'update_plugins' ) || user_can( $user, 'install_plugins' ) || user_can( $user, 'delete_plugins' ) || user_can( $user, 'edit_theme_options' ) ) {
				$user_options[ $user->ID ] = $user->display_name;
			}

		}
		return $user_options;

	}

	/**
	 * Add show/hide options callback
	 * @since  0.0.1
	 */
	public function show_hide_options() {

		// TODO make options field only show if a relavant field type is slected.

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

	public function init_cmb2_settings_page() {

		$prefix = $this->prefix;

		$cmb_settings = new_cmb2_box( array(
			'id'      => $this->settings_metabox_id,
			'hookup'  => false,
			'show_on' => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->settings_key, )
			),
		) );

		$cmb_settings->add_field( array(
			'name'    => __( 'Users', 'cmb2-admin-extension' ),
			'desc'    => __( 'Check the users to grant access to this plugin and the CMB2 plugin. Leave unchecked to grant access to all users.', 'cmb2-admin-extension' ),
			'id'      => $prefix . 'user_multicheckbox',
			'type'    => 'multicheck',
			'options' => $this->user_options(),
			'inline'  => true,
		) );

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
			'desc'    => __( 'This addiotional conrtols for positioning of the meta box. Advanced displays after Normal. Side places the meta box in the right sidebar.', 'cmb2-admin-extension' ),
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
			'id'   => $prefix . 'cmb_styles',
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
				'taxonomy_multicheck_inline'       => 'taxonomy_multicheck_inline: Taxonomy Multiple Checkboxes Inline',
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
			'name' => __( 'Options', 'cmb2-admin-extension' ),
			//'desc' => __( 'Your field type requires manual options. Please add one option per line. Type value then name seprated by a comma.<br>Example:<br>sml,Small<br>med,Medium<br>lrg,Large', 'cmb2-admin-extension' ),
			'desc' => __( 'If your field type requires manual options, please add one option per line. Type value then name seprated by a comma.<br>Example:<br>sml,Small<br>med,Medium<br>lrg,Large', 'cmb2-admin-extension' ),
			'id'   => $prefix . 'options_textarea',
			'type' => 'textarea_small',
			'after'=> $this->show_hide_options(),
		) );

		$tax_options = $this->tax_options();
		reset($tax_options);
		$default_tax_options = key($tax_options);
		$cmb_group->add_group_field( $group_field_id, array(
			'name'    => 'Taxonomy Options',
			'id'      => $prefix . 'tax_options_radio_inline',
			'type'    => 'radio_inline',
			'options' => $this->tax_options(),
			'default' => $default_tax_options,
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

			$title      = get_the_title( $ID );
			$id         = str_replace( '-', '_', $user_meta_box->post_name );
			$post_type  = $this->cmbf( $ID, $prefix . 'post_type_multicheckbox' );
			$context    = $this->cmbf( $ID, $prefix . 'context_radio' );
			$priority   = $this->cmbf( $ID, $prefix . 'priority_radio' );
			$show_names = $this->cmbf( $ID, $prefix . 'show_names' );
			$cmb_styles = $this->cmbf( $ID, $prefix . 'cmb_styles' );
			$closed     = $this->cmbf( $ID, $prefix . 'cmb_styles' );
			$fields     = $this->cmbf( $ID, $prefix . 'custom_field' );

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
				if ( isset( $field['_cmb2_repeatable_checkbox'] ) && $field['_cmb2_repeatable_checkbox'] == 'on' ) {
					$repeatable = true;
				}else{
					$repeatable = false;
				}

				$field_args = array(
					'name'       => $field['_cmb2_name_text'],
					'desc'       => $field['_cmb2_decription_textarea'],
					'id'         => $field_id ,
					'type'       => $field['_cmb2_field_type_select'],
					'repeatable' => $repeatable,
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

				${ 'cmb_'.$id }->add_field( $field_args );

			}

		}
	}
}
