<?php

namespace EWA\RvnewsImportExport;

defined( 'ABSPATH' ) || exit;

/**
 * Class Company
 * @package EWA\RvnewsImportExport
 */

class Company {
	protected static $instance = null;
	protected $taxonomy        = 'company_type';

	public function __construct() {}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_suppliers() {
		$posts = get_posts(
			array(
				'post_type'      => 'company',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'tax_query'      => array(
					array(
						'taxonomy' => $this->taxonomy,
						'field'    => 'slug',
						'terms'    => array( 'supplier' ),
					),
				),
			)
		);

		$supplier_list = array();
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $key => $post ) {
				$supplier_list[ $post->ID ] = $post->post_title;
			}
		}

		return $supplier_list;
	}

	public function get_distributors() {
		$posts = get_posts(
			array(
				'post_type'      => 'company',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'tax_query'      => array(
					array(
						'taxonomy' => $this->taxonomy,
						'field'    => 'slug',
						'terms'    => array( 'rv-distributor' ),
					),
				),
			)
		);

		$dist_list = array();
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $key => $post ) {
				$dist_list[ $post->ID ] = $post->post_title;
			}
		}

		return $dist_list;
	}
}
