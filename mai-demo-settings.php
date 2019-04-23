<?php

/**
 * Plugin Name:     Mai Demo Settings
 * Plugin URI:      https://maitheme.com/
 * Description:     Mai Demo Settings plugin
 *
 * Version:         0.1.4
 *
 * GitHub URI:      https://github.com/maithemewp/mai-demo-settings/
 *
 * Author:          MaiTheme.com
 * Author URI:      https://maitheme.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main MaiBC_Plugin Class.
 *
 * @since 0.1.0
 */
final class MaiBC_Plugin {

	/**
	 * @var MaiBC_Plugin The one true MaiBC_Plugin
	 * @since 0.1.0
	 */
	private static $instance;

	/**
	 * Main MaiBC_Plugin Instance.
	 *
	 * Insures that only one instance of MaiBC_Plugin exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   0.1.0
	 * @static  var array $instance
	 * @uses    MaiBC_Plugin::setup_constants() Setup the constants needed.
	 * @uses    MaiBC_Plugin::includes() Include the required files.
	 * @uses    MaiBC_Plugin::setup() Activate, deactivate, etc.
	 * @see     MaiBC_Plugin()
	 * @return  object | MaiBC_Plugin The one true MaiBC_Plugin
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new MaiBC_Plugin;
			// Methods
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'textdomain' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'textdomain' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'MAI_BOXED_CONTAINERS_VERSION' ) ) {
			define( 'MAI_BOXED_CONTAINERS_VERSION', '0.1.4' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'MAI_BOXED_CONTAINERS_PLUGIN_DIR' ) ) {
			define( 'MAI_BOXED_CONTAINERS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'MAI_BOXED_CONTAINERS_PLUGIN_URL' ) ) {
			define( 'MAI_BOXED_CONTAINERS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'MAI_BOXED_CONTAINERS_PLUGIN_FILE' ) ) {
			define( 'MAI_BOXED_CONTAINERS_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Base Name.
		if ( ! defined( 'MAI_BOXED_CONTAINERS_BASENAME' ) ) {
			define( 'MAI_BOXED_CONTAINERS_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
		}

	}

	/**
	 * Includes.
	 *
	 * @since   0.1.0
	 *
	 * @return  void
	 */
	public function includes() {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	public function hooks() {

		// Updater.
		add_action( 'admin_init', array( $this, 'updater' ) );

		// Maybe deactivate. Run after Mai Theme.
		add_action( 'plugins_loaded', array( $this, 'maybe_deactivate' ), 20 );

		add_action( 'wp_enqueue_scripts',       array( $this, 'enqueue_scripts' ) );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'shortcodes_in_nav' ), 10, 2 );
		add_shortcode( 'cog_icon',              array( $this, 'cog_icon_shortcode' ) );
	}

	/**
	 * Setup the updater.
	 *
	 * composer require yahnis-elsts/plugin-update-checker
	 *
	 * @since   0.1.0
	 * @uses    https://github.com/YahnisElsts/plugin-update-checker/
	 * @return  void
	 */
	public function updater() {
		if ( ! class_exists( 'Puc_v4_Factory' ) ) {
			return;
		}
		$updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/maithemewp/mai-demo-settings/', __FILE__, 'mai-demo-settings' );
	}

	/**
	 * Maybe deactivate the plugin if conditions aren't met.
	 *
	 * @since   0.1.0
	 *
	 * @return  void
	 */
	public function maybe_deactivate() {
		// Bail if no Mai Theme.
		if ( class_exists( 'Mai_Theme_Engine' ) ) {
			return;
		}
		// Deactivate.
		add_action( 'admin_init', function() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		});
		// Notice.
		add_action( 'admin_notices', function() {
			printf( '<div class="notice notice-warning"><p>%s</p></div>', __( 'Mai Demo Settings requires Mai Theme. As a result, Mai Demo Settings has been deactivated.', 'mai-demo-settings' ) );
			// Remove "Plugin activated" notice.
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		});
	}

	/**
	 * Enqueue Javascript files.
	 *
	 * @since   0.1.0
	 *
	 * @return  void
	 */
	function enqueue_scripts() {
		$suffix = $this->get_suffix();
		wp_enqueue_style( 'mai-demo-settings', MAI_BOXED_CONTAINERS_PLUGIN_URL . "assets/css/mai-demo-settings{$suffix}.css", array(), MAI_BOXED_CONTAINERS_VERSION );
		wp_enqueue_script( 'mai-demo-settings', MAI_BOXED_CONTAINERS_PLUGIN_URL . "assets/js/mai-demo-settings{$suffix}.js", array( 'jquery' ), MAI_BOXED_CONTAINERS_VERSION,  true );
		wp_localize_script( 'mai-demo-settings', 'maidsVars', array(
			'boxed'   => genesis_get_option( 'boxed_elements' ),
			'choices' => $this->boxed_choices(),
			'html'    => $this->settings_html(),
		) );
	}

	/**
	 * Get settings HTML.
	 *
	 * @since   0.1.0
	 *
	 * @return  string|HTML
	 */
	function settings_html() {

		$disabled = array();

		$sections = is_page_template( 'sections.php' );

		if ( is_singular( array( 'page', 'post' ) ) ) {
			$layout = genesis_site_layout();
			// If layout does not contains sidebar.
			if ( false  === strpos ( $layout, 'sidebar' ) ) {
				$disabled[] = 'sidebar';
				$disabled[] = 'sidebar_widgets';
			}
		}

		if ( ! is_singular( array( 'page', 'post' ) ) || $sections ) {
			$disabled[] = 'entry_singular';
		}

		if ( ! ( is_home() || is_category() || is_tag() || is_search() ) ) {
			$disabled[] = 'entry_archive';
		}

		if ( $sections ) {
			$disabled[] = 'content';
		}

		$html  = '';
		$boxed = genesis_get_option( 'boxed_elements' );
		$html .= '<div class="maids-settings" style="display:none;">';
			$html .= '<div class="maids-settings-inner">';

			$html .= '<div class="maids-heading">Boxed Settings Demo</div>';
				$html .= '<p>Change boxed container settings (resets after page refresh).</p>';

				foreach( $this->boxed_choices() as $key => $name ) {

					if ( in_array( $key, $disabled ) ) {
						continue;
					}

					$checked = in_array( $key, $boxed ) ? ' checked' : '';
					$html .= sprintf( '<label><input type="checkbox" name="maids-%s"%s>%s</label>', $key, $checked, $name );
				}

			$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get boxed choices.
	 *
	 * @since   0.1.0
	 *
	 * @return  array  The choices.
	 */
	function boxed_choices() {
		return array(
			'site_container'       => __( 'Site Container (fixed width)', 'mai-theme-engine' ),
			'content_sidebar_wrap' => __( 'Content Sidebar Wrap', 'mai-theme-engine' ),
			'content'              => __( 'Main Content', 'mai-theme-engine' ),
			'entry_singular'       => __( 'Single Posts/Entries', 'mai-theme-engine' ),
			'entry_archive'        => __( 'Archive Posts/Entries', 'mai-theme-engine' ),
			'sidebar'              => __( 'Primary Sidebar', 'mai-theme-engine' ),
			// 'sidebar_alt'          => __( 'Secondary Sidebar', 'mai-theme-engine' ),
			'sidebar_widgets'      => __( 'Primary Sidebar Widgets', 'mai-theme-engine' ),
			// 'sidebar_alt_widgets'  => __( 'Secondary Sidebar Widget', 'mai-theme-engine' ),
			// 'author_box'           => __( 'After Entry Author Box', 'mai-theme-engine' ),
			// 'after_entry_widgets'  => __( 'After Entry Widgets', 'mai-theme-engine' ),
			// 'adjacent_entry_nav'   => __( 'Previous/Next Entry Navigation', 'mai-theme-engine' ),
			// 'comment_wrap'         => __( 'Comments Wrap', 'mai-theme-engine' ),
			// 'comment'              => __( 'Comments', 'mai-theme-engine' ),
			// 'comment_respond'      => __( 'Comment Submission Form', 'mai-theme-engine' ),
			// 'pings'                => __( 'Pings and Trackbacks', 'mai-theme-engine' ),
		);
	}

	/**
	 * Allow shortcodes in nav menu items.
	 *
	 * @since   0.1.0
	 *
	 * @return  void
	 */
	function shortcodes_in_nav( $item_output, $item ) {
		$item_output = do_shortcode( $item_output );
		return $item_output;
	}

	/**
	 * Add shortcode for cog icon.
	 *
	 * @since   0.1.0
	 *
	 * @return  string|HTML
	 */
	function cog_icon_shortcode( $atts ) {
		return $this->get_cog_icon();
	}

	/**
	 * Get the cog icon.
	 *
	 * @since   0.1.0
	 *
	 * @return  string|HTML
	 */
	function get_cog_icon() {
		return '<svg style="opacity:0;" xmlns="http://www.w3.org/2000/svg" class="maids-icon" fill="currentColor" width="32px" height="32px" viewBox="0 0 64 64" x="0px" y="0px"><title>settings,option,menu,preference,gear</title><g><path d="M32,21.45A10.55,10.55,0,1,0,42.53,32,10.57,10.57,0,0,0,32,21.45Zm0,19.11A8.55,8.55,0,1,1,40.53,32,8.56,8.56,0,0,1,32,40.55Z"/><path d="M32,17.2A14.8,14.8,0,1,0,46.77,32,14.82,14.82,0,0,0,32,17.2Zm0,27.6A12.8,12.8,0,1,1,44.77,32,12.81,12.81,0,0,1,32,44.8Z"/><path d="M57.87,25.48H55.35a24.11,24.11,0,0,0-2.23-5.39L54.9,18.3a3.1,3.1,0,0,0,0-4.38L50.06,9.07a3.1,3.1,0,0,0-4.38,0l-1.79,1.79A24.08,24.08,0,0,0,38.5,8.63V6.1A3.1,3.1,0,0,0,35.4,3H28.55a3.1,3.1,0,0,0-3.1,3.1V8.63a24.11,24.11,0,0,0-5.39,2.23L18.27,9.07a3.1,3.1,0,0,0-4.38,0L9,13.92A3.1,3.1,0,0,0,9,18.3l1.79,1.79A24.09,24.09,0,0,0,8.6,25.48H6.07A3.1,3.1,0,0,0,3,28.57v6.85a3.1,3.1,0,0,0,3.1,3.1H8.6a24.09,24.09,0,0,0,2.23,5.39L9,45.7a3.1,3.1,0,0,0,0,4.38l4.84,4.84a3.1,3.1,0,0,0,4.38,0l1.79-1.79a24.11,24.11,0,0,0,5.39,2.23V57.9a3.1,3.1,0,0,0,3.1,3.1H35.4a3.1,3.1,0,0,0,3.1-3.1V55.38a24.08,24.08,0,0,0,5.39-2.23l1.79,1.79a3.1,3.1,0,0,0,4.38,0l4.84-4.84a3.1,3.1,0,0,0,0-4.38l-1.79-1.79a24.11,24.11,0,0,0,2.23-5.39h2.53a3.1,3.1,0,0,0,3.1-3.1V28.57A3.1,3.1,0,0,0,57.87,25.48ZM59,35.43a1.1,1.1,0,0,1-1.1,1.1h-3.3a1,1,0,0,0-1,.76A22.07,22.07,0,0,1,51,43.55a1,1,0,0,0,.15,1.23l2.33,2.33a1.1,1.1,0,0,1,0,1.55l-4.84,4.84a1.1,1.1,0,0,1-1.55,0l-2.33-2.33A1,1,0,0,0,43.53,51a22.11,22.11,0,0,1-6.27,2.6,1,1,0,0,0-.76,1v3.3A1.1,1.1,0,0,1,35.4,59H28.55a1.1,1.1,0,0,1-1.1-1.1V54.6a1,1,0,0,0-.76-1A22.12,22.12,0,0,1,20.42,51a1,1,0,0,0-1.23.15l-2.33,2.33a1.1,1.1,0,0,1-1.55,0l-4.84-4.84a1.1,1.1,0,0,1,0-1.55l2.33-2.33a1,1,0,0,0,.15-1.23,22.12,22.12,0,0,1-2.6-6.27,1,1,0,0,0-1-.76H6.07A1.1,1.1,0,0,1,5,35.43V28.57a1.1,1.1,0,0,1,1.1-1.1h3.3a1,1,0,0,0,1-.76,22.12,22.12,0,0,1,2.6-6.27,1,1,0,0,0-.15-1.23l-2.33-2.33a1.1,1.1,0,0,1,0-1.55l4.84-4.84a1.1,1.1,0,0,1,1.55,0l2.33,2.33a1,1,0,0,0,1.23.15,22.12,22.12,0,0,1,6.27-2.6,1,1,0,0,0,.76-1V6.1A1.1,1.1,0,0,1,28.55,5H35.4a1.1,1.1,0,0,1,1.1,1.1V9.4a1,1,0,0,0,.76,1A22.11,22.11,0,0,1,43.53,13a1,1,0,0,0,1.23-.15l2.33-2.33a1.1,1.1,0,0,1,1.55,0l4.84,4.84a1.1,1.1,0,0,1,0,1.55l-2.33,2.33A1,1,0,0,0,51,20.45a22.07,22.07,0,0,1,2.6,6.27,1,1,0,0,0,1,.76h3.3a1.1,1.1,0,0,1,1.1,1.1Z"/></g></svg>';
	}

	/**
	 * Get the script/style suffix.
	 *
	 * @since   0.1.0
	 *
	 * @return  string
	 */
	function get_suffix() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		return $debug ? '' : '.min';
	}

}

/**
 * The main function for that returns MaiBC_Plugin
 *
 * The main function responsible for returning the one true MaiBC_Plugin
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = MaiBC_Plugin(); ?>
 *
 * @since 0.1.0
 *
 * @return object|MaiBC_Plugin The one true MaiBC_Plugin Instance.
 */
function MaiBC_Plugin() {
	return MaiBC_Plugin::instance();
}

// Get MaiBC_Plugin Running.
MaiBC_Plugin();
