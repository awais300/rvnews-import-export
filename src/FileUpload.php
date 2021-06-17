<?php

namespace EWA\RvnewsImportExport;

defined( 'ABSPATH' ) || exit;

/**
 * Class FileUpload
 * @package EWA\RvnewsImportExport
 */

class FileUpload {

	protected static $instance = null;
	protected $filename        = null;
	protected $allowed_mimes   = array();

	public function __construct() {
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function set_filename( $filename ) {
		$this->filename = $filename;
	}

	public function get_filename() {
		if ( ! empty( $this->filename ) ) {
			return $this->filename;
		} else {
			throw new \Exception( 'File name is mandatory' );
		}
	}


	public function set_allowed_mimes( $allowed_mimes = array() ) {
		if ( is_array( $allowed_mimes ) ) {
			$this->allowed_mimes = $allowed_mimes;
		} else {
			// e.g. array('csv' => 'text/csv').
			throw new \Exception( 'Mime should be passed as array' );
		}
	}

	public function get_allowed_mimes() {
		return $this->allowed_mimes;
	}

	public function upload() {
		$file          = isset( $_FILES ) ? $_FILES : array();
		$allowed_mimes = $this->get_allowed_mimes();
		$filename      = $this->get_filename();

		$uploaded_file = wp_handle_sideload(
			$file[ $filename ],
			array(
				'test_form' => false,
				'mimes'     => $allowed_mimes,
			   //'unique_filename_callback' => array( $this, 'rename_uploaded_file' ),
			)
		);

		return $uploaded_file;
	}

	public function rename_uploaded_file( $dir, $name, $ext ) {
		return rand() . $name . $ext;
	}
}
