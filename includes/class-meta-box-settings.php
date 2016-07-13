<?php
/**
 * CMB2 Meta Box Settings.
 *
 * @since  0.1.0
 *
 * @category  WordPress_Plugin
 * @package   CMB2 Admin Extension
 * @author    twoelevenjay
 * @license   GPL-2.0+
 */

if ( ! class_exists( 'CMB2_Meta_Box_Settings' ) ) {

	/**
	 * Class CMB2_Meta_Box_Settings.
	 */
	class CMB2_Meta_Box_Settings {

		/**
		 * Field prefix.
		 *
		 * @var string
		 */
		private $prefix = '_cmb2_';

		/**
		 * Settings key, and option page slug.
		 *
		 * @var string
		 */
		private $settings_key = '_cmb2_settings';

		/**
		 * Settings page metabox id.
		 *
		 * @var string
		 */
		private $settings_metabox_id = '_cmb2_settings_metabox';

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

			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
			add_action( 'cmb2_init', array( $this, 'init_cmb2_settings_page' ) );
		}

		/**
		 * Set up the plugin settings page.
		 *
		 * @since  0.0.1
		 */
		public function add_settings_page() {

			if ( cmb2ae_metabox()->is_cmb2_allowed() ) {
				$this->settings_page = add_submenu_page( 'edit.php?post_type=meta_box', __( 'CMB2 Settings', 'cmb2-admin-extension' ), __( 'CMB2 Settings', 'cmb2-admin-extension' ), 'edit_posts', $this->settings_key, array( $this, 'settings_page' ) );
				add_action( "admin_print_styles-{$this->settings_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
			}

		}

		/**
		 * Register the setting.
		 */
		public function register_settings() {

			register_setting( $this->settings_key, $this->settings_key );

		}

		/**
		 * Plugin settings page call back.
		 *
		 * @since  0.0.1
		 */
		public function settings_page() {

			?>
			<div class="wrap cmb2-options-page <?php echo esc_attr( $this->settings_key ); ?>">
				<h2><?php echo esc_html__( 'CMB2 Settings', 'cmb2-admin-extension' ); ?></h2>
				<?php cmb2_metabox_form( $this->settings_metabox_id, $this->settings_key, array( 'disable_styles' => false ) ); ?>
			</div>
			<?php

		}

		/**
		 * Get users for the soptions on the ettings page.
		 *
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
		 * This function needs documentation.
		 *
		 * @todo Document.
		 */
		public function init_cmb2_settings_page() {

			$prefix = $this->prefix;

			$cmb_settings = new_cmb2_box( array(
				'id'      => $this->settings_metabox_id,
				'hookup'  => false,
				'show_on' => array(
					// These are important, don't remove.
					'key'   => 'options-page',
					'value' => array( $this->settings_key ),
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
	}

	$cmb2_meta_box_settings = new CMB2_Meta_Box_Settings();
}
