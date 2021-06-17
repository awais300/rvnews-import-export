<?php

namespace EWA\RvnewsImportExport;

defined( 'ABSPATH' ) || exit;

use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Writer;

/**
 * Class ImportExport
 * @package EWA\RvnewsImportExport
 */

class ImportExport {

	protected $loader;
	protected $company;
	protected $product;
	protected $logger;

	protected const MAX_TERMS = 6;

	public function __construct() {
		$this->loader  = TemplateLoader::get_instance();
		$this->company = Company::get_instance();
		$this->product = Product::get_instance();
		$this->logger  = Logger::get_instance();

		add_action( 'admin_menu', array( $this, 'add_product_import_export_menu' ) );
		add_action( 'admin_notices', array( $this, 'import_export_admin_notices' ) );

		add_action( 'admin_init', array( $this, 'export' ) );
		add_action( 'admin_init', array( $this, 'import' ) );
		add_action( 'admin_init', array( $this, 'download_distributors' ) );
		add_action( 'admin_init', array( $this, 'download_categories' ) );
	}

	public function add_product_import_export_menu() {
		add_submenu_page(
			'edit.php?post_type=product',
			'Product Import Export',
			'Import/Export',
			'manage_options',
			'product-import-export',
			array( $this, 'import_export_display_page' )
		);
	}

	public function import_export_display_page() {
		$suppliers = $this->company->get_suppliers();
		asort( $suppliers );

		$this->loader->get_template(
			'admin/import-export.php',
			array(
				'suppliers' => $suppliers,
			),
			RVNEWS_CUST_PLUGIN_DIR_PATH . '/templates/',
			true
		);
	}

	public function import_export_admin_notices() {
		$screen = get_current_screen();
		if ( $screen->id === 'product_page_product-import-export' && isset( $_POST['submit'] ) ) {
			$this->loader->get_template(
				'admin/notices-import-export.php',
				array(),
				RVNEWS_CUST_PLUGIN_DIR_PATH . '/templates/',
				true
			);
		}
	}

	public function export() {
		ini_set( 'max_execution_time', 0 );
		ini_set( 'memory_limit', '512M' );

		if ( isset( $_POST['submit'] ) &&
			$_POST['submit'] == 'Export' &&
			isset( $_POST['suppliers'] ) &&
			! empty( $_POST['suppliers'] )
		) {
			$csv_data_rows = array();

			$products = $this->product->get_products_by_supplier( intval( $_POST['suppliers'] ) );
			if ( $products ) {
				$i = 0;
				foreach ( $products as $key => $product ) {
					$csv_data_rows[ $i ][] = $product->ID;
					$csv_data_rows[ $i ][] = $product->part_number;
					$csv_data_rows[ $i ][] = $product->brand_name;
					$csv_data_rows[ $i ][] = $product->short_description;
					$csv_data_rows[ $i ][] = $product->merchandising_materials;
					$csv_data_rows[ $i ][] = $product->post_content;

					$terms = get_the_terms( $product->ID, 'product_category' );

					for ( $j = 0; $j < self::MAX_TERMS; $j++ ) {
						if ( ! empty( $terms[ $j ] ) ) {
							 $csv_data_rows[ $i ][] = $terms[ $j ]->slug;
						} else {
							$csv_data_rows[ $i ][] = '';
						}
					}

					$distributors = get_field( 'distributors', $product->ID );
					if ( ! empty( $distributors ) ) {
						foreach ( $distributors as $key => $dist ) {
							$csv_data_rows[ $i ][] = get_post( $dist['company'] )->post_title;
							$csv_data_rows[ $i ][] = $dist['distributor_part_number'];
						}
					}

					$i++;
				}
			}

			//if ( ! empty( $csv_data_rows ) ) {
				$csv_path = RVNEWS_CUST_PLUGIN_DIR_PATH . '/assets/export/products.csv';
			try {
				$writer = Writer::createFromPath( $csv_path, 'w' );
				$writer->insertOne( $this->get_csv_header() );
				$writer->insertAll( new \ArrayIterator( $csv_data_rows ) );
			} catch ( CannotInsertRecord $e ) {
				$this->logger->log( 'CSV insert record exception:' );
				$this->logger->log( $e->getRecords() );
			} catch ( Exception | RuntimeException $e ) {
				$this->logger->log( 'CSV insert exception:' );
				$this->logger->log( $e->getMessage() );
			}

				// Download
				header( 'Content-Type: text/csv; charset=UTF-8' );
				header( 'Content-Description: File Transfer' );
				header( 'Content-Disposition: attachment; filename="products.csv"' );
			try {
				$reader = Reader::createFromPath( $csv_path );
				$reader->output();
			} catch ( Exception $e ) {
				$this->logger->log( 'CSV download exception:' );
				$this->logger->log( $e->getMessage() );
			}

				exit();
			//}
		}
	}

	public function import() {
		ini_set( 'max_execution_time', 0 );
		ini_set( 'memory_limit', '512M' );

		if ( isset( $_POST['submit'] ) && $_POST['submit'] == 'Import' ) {
			$uploaded = $this->upload_file();
			if ( $uploaded && ! isset( $uploaded['error'] ) ) {
				$csv_path = $uploaded['file'];
				try {
					$reader = Reader::createFromPath( $csv_path, 'r' );
					$reader->setHeaderOffset( 0 );
				} catch ( Exception $e ) {
					$this->logger->log( 'CSV read exception:' );
					$this->logger->log( $e->getMessage() );
				}

				foreach ( $reader as $key => $record ) {
					$this->insert_record( $record );
				}

				$this->logger->log( 'Import execution finished' );
			} elseif ( isset( $uploaded['error'] ) ) {
				$error = $uploaded['error'];
				$this->logger->log( 'File upload error:' );
				$this->logger->log( $error );
				//wp_die($error);
			}
		}
	}

	public function insert_record( $record ) {
		$post_id       = intval( $record["RV New's Internal Product ID"] );
		$dist_data     = $this->filter_distributors_data( $record );
		$category_data = $this->filter_categories_data( $record );

		$data = array(
			'ID'           => $post_id,
			'post_type'    => 'product',
			'post_status'  => 'publish',
			'post_content' => $record['Long Description'],
			'meta_input'   => array(
				'part_number'             => $record['Supplier Part Number'],
				'brand_name'              => $record['Brand Name'],
				'short_description'       => $record['Short Description'],
				'merchandising_materials' => $record['Merchandising Materials field'],
			),
		);

		if ( ! empty( $post_id ) ) {
			$post_id = wp_update_post( $data );
			if ( is_wp_error( $post_id ) ) {
				$errors = $post_id->get_error_messages();
				$this->logger->log( 'Update post errors: Post ID: ' . $post_id );
				$this->logger->log( $errors );
				$this->logger->log( $data );
				$this->logger->log( $dist_data );
			} else {
				// field_60a2b135c8030 - Distributors field.
				update_field( 'field_60a2b135c8030', $dist_data, $post_id );
				$this->logger->log( 'A post is updated with ID:' . $post_id );

				// Set terms
				if ( empty( $category_data ) ) {
					wp_set_object_terms( $post_id, null, 'product_category' );
				} else {
					wp_set_object_terms( $post_id, null, 'product_category' );
					wp_set_object_terms( $post_id, $category_data, 'product_category' );
				}

				$this->logger->log( 'Post ID: ' . $post_id . ' updated with categories' );
				$this->logger->log( $category_data );
			}
		} else {
			$post_id = wp_insert_post( $data );
			if ( is_wp_error( $post_id ) ) {
				$errors = $post_id->get_error_messages();
				$this->logger->log( 'Insert post errors:' );
				$this->logger->log( $errors );
				$this->logger->log( $data );
				$this->logger->log( $dist_data );
			} else {
				// field_60a2b135c8030 - Distributors field.
				update_field( 'field_60a2b135c8030', $dist_data, $post_id );
				$this->logger->log( 'A post is inserted with ID:' . $post_id );

				// Set terms
				if ( empty( $category_data ) ) {
					wp_set_object_terms( $post_id, null, 'product_category' );
				} else {
					wp_set_object_terms( $post_id, null, 'product_category' );
					wp_set_object_terms( $post_id, $category_data, 'product_category' );
				}

				$this->logger->log( 'Post ID: ' . $post_id . ' updated with categories' );
				$this->logger->log( $category_data );
			}
		}
	}

	public function filter_categories_data( $record ) {
		$category_data = array();
		foreach ( $record as $key => $value ) {
			if ( strpos( $key, 'Product Category' ) !== false ) {
				if ( ! empty( $value ) ) {
					$category_data[] = $value;
					$i++;
				}
			}
		}

		return $category_data;
	}

	public function filter_distributors_data( $record ) {
		$dist_data = array();
		$i         = 0;
		$j         = 0;
		foreach ( $record as $key => $value ) {
			if ( strpos( $key, 'Distributor Name' ) !== false ) {
				if ( ! empty( $value ) ) {
					$dist_data[ $i ]['company'] = get_page_by_title( $value, OBJECT, 'company' )->ID;
					$i++;
				}
			}

			if ( strpos( $key, 'Distributor Product ID' ) !== false ) {
				if ( ! empty( $value ) ) {
					$dist_data[ $j ]['distributor_part_number'] = $value;
					$j++;
				}
			}
		}

		return $dist_data;
	}

	public function upload_file() {
		if ( isset( $_POST['submit'] ) && $_POST['submit'] == 'Import' ) {
			$file = new FileUpload();
			$file->set_filename( 'csv' );
			$file->set_allowed_mimes( array( 'csv' => 'text/csv' ) );
			$uploaded = $file->upload();
			return $uploaded;
		}
	}

	public function download_distributors() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'product-import-export' ) {
			if ( isset( $_POST['submit'] ) && $_POST['submit'] == 'Download Distributors Names' ) {
				$dists = $this->company->get_distributors();
				asort( $dists );

				$filename = RVNEWS_CUST_PLUGIN_DIR_PATH . '/assets/downloads/distributors.txt';
				$file     = fopen( $filename, 'w' );
				if ( ! $file ) {
					wp_die( 'Could not open file' );
				}

				foreach ( $dists as $dist ) {
					fwrite( $file, $dist . PHP_EOL );
				}
				fclose( $file );
				Helper::force_download( $filename );
			}
		}
	}

	public function download_categories() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'product-import-export' ) {
			if ( isset( $_POST['submit'] ) && $_POST['submit'] == 'Download Category Names' ) {
				$categories = $this->product->get_categories();
				asort( $categories );

				$filename = RVNEWS_CUST_PLUGIN_DIR_PATH . '/assets/downloads/categories.txt';
				$file     = fopen( $filename, 'w' );
				if ( ! $file ) {
					wp_die( 'Could not open file' );
				}

				//fwrite($file, 'Category Name = category-slug' . PHP_EOL);
				foreach ( $categories as $slug => $category ) {
					fwrite( $file, $category . ' = ' . $slug . PHP_EOL );
				}
				fclose( $file );
				Helper::force_download( $filename );
			}
		}
	}

	public function get_csv_header() {
		return array(
			"RV New's Internal Product ID",
			'Supplier Part Number',

			'Brand Name',
			'Short Description',

			'Merchandising Materials field',
			'Long Description',

			'Product Category 1',
			'Product Category 2',

			'Product Category 3',
			'Product Category 4',

			'Product Category 5',
			'Product Category 6',

			'Distributor Name 1',
			'Distributor Product ID 1',

			'Distributor Name 2',
			'Distributor Product ID 2',

			'Distributor Name 3',
			'Distributor Product ID 3',

			'Distributor Name 4',
			'Distributor Product ID 4',

			'Distributor Name 5',
			'Distributor Product ID 5',

			'Distributor Name 6',
			'Distributor Product ID 6',

			'Distributor Name 7',
			'Distributor Product ID 7',

			'Distributor Name 8',
			'Distributor Product ID 8',

			'Distributor Name 9',
			'Distributor Product ID 9',

			'Distributor Name 10',
			'Distributor Product ID 10',

			'Distributor Name 11',
			'Distributor Product ID 11',

			'Distributor Name 12',
			'Distributor Product ID 12',

			'Distributor Name 13',
			'Distributor Product ID 13',

			'Distributor Name 14',
			'Distributor Product ID 14',

			'Distributor Name 15',
			'Distributor Product ID 15',

			'Distributor Name 16',
			'Distributor Product ID 16',

			'Distributor Name 17',
			'Distributor Product ID 17',

			'Distributor Name 18',
			'Distributor Product ID 18',

			'Distributor Name 19',
			'Distributor Product ID 19',

			'Distributor Name 20',
			'Distributor Product ID 20',

			'Distributor Name 21',
			'Distributor Product ID 21',

			'Distributor Name 22',
			'Distributor Product ID 22',

			'Distributor Name 23',
			'Distributor Product ID 23',

			'Distributor Name 24',
			'Distributor Product ID 24',

			'Distributor Name 25',
			'Distributor Product ID 25',

			'Distributor Name 26',
			'Distributor Product ID 26',

			'Distributor Name 27',
			'Distributor Product ID 27',

			'Distributor Name 28',
			'Distributor Product ID 28',

			'Distributor Name 29',
			'Distributor Product ID 29',

			'Distributor Name 30',
			'Distributor Product ID 30',
		);
	}
}
