<?php

namespace EWA\RvnewsImportExport;

defined( 'ABSPATH' ) || exit;

/**
 * Class Product
 * @package EWA\RvnewsImportExport
 */

class Product {
	protected static $instance = null;

	public function __construct() {}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_products_by_supplier( $supplier_id = null ) {
		if ( $supplier_id == null ) {
			throw new \Exception( 'Invalid supplier ID' );
		}

		$posts = get_posts(
			array(
				'post_type'      => 'product',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'meta_query'     => array(
					array(
						'key'     => 'supplier',
						'value'   => $supplier_id,
						'compare' => '=',
						'type'    => 'numeric',
					),
				),
			)
		);

		return $posts;
	}

	public function get_categories() {
		$taxonomies = get_terms(
			array(
				'taxonomy'   => 'product_category',
				'hide_empty' => false,
			)
		);

		$all_terms = array();
		foreach ( $taxonomies as $key => $term ) {
			$all_terms[ $term->slug ] = $term->name;
		}

		return $all_terms;
	}
}
