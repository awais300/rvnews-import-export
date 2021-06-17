<?php

namespace EWA\RvnewsImportExport;

defined( 'ABSPATH' ) || exit;

/**
 * Class TemplateLoader
 * @package EWA\RvnewsImportExport
 */

class TemplateLoader {
	protected static $instance = null;

	public function __construct() {}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_template( $template_name, $args = array(), $template_path, $echo = false ) {
		$output = null;

		$template_path = $template_path . $template_name;

		if ( file_exists( $template_path ) ) {
			extract( $args );

			ob_start();
			include $template_path;
			$output = ob_get_clean();
		} else {
			throw new \Exception( 'Specified path does not exist', 1 );
		}

		if ( $echo ) {
			print $output;
		} else {
			return $output;
		}
	}
}
