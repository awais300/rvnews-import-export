<?php

namespace EWA\RvnewsImportExport;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customization
 * @package EWA\RvnewsImportExport
 */

class Customization {

	private $version = '1.0.0';

	/**
	 * Instance to call certain functions globally within the plugin
	 *
	 * @var _instance
	 */
	protected static $_instance = null;

	/**
	 * Construct the plugin.
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'load_plugin' ), 0 );

	}

	/**
	 * Main Customization instance
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Determine which plugin to load.
	 */
	public function load_plugin() {

		$this->define_constants();
		$this->init_hooks();
	}

	/**
	 * Define WC Constants.
	 */
	private function define_constants() {
		$upload_dir = wp_upload_dir();

		// Path related defines
		$this->define( 'RVNEWS_CUST_PLUGIN_FILE', RVNEWS_CUST_PLUGIN_FILE );
		$this->define( 'RVNEWS_CUST_PLUGIN_BASENAME', plugin_basename( RVNEWS_CUST_PLUGIN_FILE ) );
		$this->define( 'RVNEWS_CUST_PLUGIN_DIR_PATH', untrailingslashit( plugin_dir_path( RVNEWS_CUST_PLUGIN_FILE ) ) );
		$this->define( 'RVNEWS_CUST_PLUGIN_DIR_URL', untrailingslashit( plugins_url( '/', RVNEWS_CUST_PLUGIN_FILE ) ) );
	}

	/**
	 * Collection of hooks.
	 */
	public function init_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ), 1 );
	}

	/**
	 * Localisation
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'rvnews-import-export', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		new Helper();
		new Logger();
		new TemplateLoader();
		new Company();
		new Product();
		new ImportExport();
	}

	/**
	 * Enqueue all styles.
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();
		if ( $screen->id === 'product_page_product-import-export' ) {
			wp_enqueue_style( 'rvnews-import-export-backend', RVNEWS_CUST_PLUGIN_DIR_URL . '/assets/css/rvnews-import-export-backend.css', array(), null, 'all' );
		}
	}

	/**
	 * Enqueue all scripts.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( $screen->id === 'product_page_product-import-export' ) {
			wp_enqueue_script( 'rvnews-import-export-backend', RVNEWS_CUST_PLUGIN_DIR_URL . '/assets/js/rvnews-import-export-backend.js', array( 'jquery' ) );
		}

	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

}
