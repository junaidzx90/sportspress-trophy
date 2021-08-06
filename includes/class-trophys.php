<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Trophy
 * @subpackage Trophy/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Trophy
 * @subpackage Trophy/includes
 * @author     Md Junayed <admin@easeare.com>
 */
class Trophy {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Trophy_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TROPY_VERSION' ) ) {
			$this->version = TROPY_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'trophys';

		$this->load_dependencies();
		$this->define_trophys_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Trophy_Loader. Orchestrates the hooks of the plugin.
	 * - Trophy_i18n. Defines internationalization functionality.
	 * - Trophy_Admin. Defines all hooks for the admin area.
	 * - Trophy_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-trophys-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'view/class-trophys-admin.php';
	
		$this->loader = new Trophy_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_trophys_hooks() {

		$plugin_admin = new Trophy_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$this->loader->add_action( 'wp_footer', $plugin_admin, 'custom_script' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_admin, 'public_enqueue_styles' );

		$this->loader->add_action( 'init', $plugin_admin, 'trophys_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, '_trophys_tables' );

		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'trophys_meta_boxes',99 );

		$this->loader->add_action( 'save_post_sp_team', $plugin_admin, 'save_tabs_meta_data',99,2 );
		$this->loader->add_action( 'save_post_sp_player', $plugin_admin, 'save_tabs_meta_data',99,2 );
		$this->loader->add_action( 'save_post_trophys', $plugin_admin, 'save_trophys_meta_data',99,2 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Trophy_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
