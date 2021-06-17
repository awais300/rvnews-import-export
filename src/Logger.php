<?php

namespace EWA\RvnewsImportExport;

defined( 'ABSPATH' ) || exit;

/**
 * Class Logger
 * @package EWA\RvnewsImportExport
 */

class Logger {
	protected static $instance = null;

	public function __construct() {}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function log( $mix, $log_file = null ) {
		if ( $log_file == null ) {
			$daily_log = date( 'Y-m-d' );
			$file_name = "import_export_{$daily_log}.log";
		} else {
			$file_name = $log_file;
		}

		$data  = '[' . date( 'Y-m-d H:i:s' ) . ']' . PHP_EOL;
		$data .= print_r( $mix, true );
		file_put_contents( RVNEWS_CUST_PLUGIN_DIR_PATH . '/logs/' . $file_name, $data . PHP_EOL, FILE_APPEND | LOCK_EX );
	}
}
