<?php

namespace EWA\RvnewsImportExport;

defined( 'ABSPATH' ) || exit;

/**
 * Class Helper
 * @package EWA\RvnewsImportExport
 */

class Helper {

	/**
	 * Associative array push
	 * @param  Array $array
	 * @param  String $key
	 * @param  Mix $value
	 * @return Array
	 */
	public static function array_push_assoc( $array, $key, $value ) {
		$array[ $key ] = $value;
		return $array;
	}

	/**
	 * Multi-select menu
	 *
	 * @param   string
	 * @param   array
	 * @param   mixed
	 * @param   mixed
	 * @return  string
	 */
	public static function form_multiselect( $name = '', $options = array(), $selected = array(), $extra = '' ) {
		$extra = self::_attributes_to_string( $extra );
		if ( stripos( $extra, 'multiple' ) === false ) {
			$extra .= ' multiple="multiple"';
		}

		return self::form_dropdown( $name, $options, $selected, $extra );
	}

	/**
	 * Simplre dropdown
	 * @param  string $data
	 * @param  array  $options
	 * @param  array  $selected
	 * @param  string $extra
	 * @return string
	 */
	public static function form_dropdown( $data = '', $options = array(), $selected = array(), $extra = '' ) {
		$defaults = array();

		if ( is_array( $data ) ) {
			if ( isset( $data['selected'] ) ) {
				$selected = $data['selected'];
				unset( $data['selected'] ); // select tags don't have a selected attribute
			}

			if ( isset( $data['options'] ) ) {
				$options = $data['options'];
				unset( $data['options'] ); // select tags don't use an options attribute
			}
		} else {
			$defaults = array( 'name' => $data );
		}

		is_array( $selected ) or $selected = array( $selected );
		is_array( $options ) or $options   = array( $options );

		// If no selected state was submitted we will attempt to set it automatically
		if ( empty( $selected ) ) {
			if ( is_array( $data ) ) {
				if ( isset( $data['name'], $_POST[ $data['name'] ] ) ) {
					$selected = array( $_POST[ $data['name'] ] );
				}
			} elseif ( isset( $_POST[ $data ] ) ) {
				$selected = array( $_POST[ $data ] );
			}
		}

		$extra = self::_attributes_to_string( $extra );

		$multiple = ( count( $selected ) > 1 && stripos( $extra, 'multiple' ) === false ) ? ' multiple="multiple"' : '';

		$form = '<select ' . rtrim( self::_parse_form_attributes( $data, $defaults ) ) . $extra . $multiple . ">\n";

		foreach ( $options as $key => $val ) {
			$key = (string) $key;

			if ( is_array( $val ) ) {
				if ( empty( $val ) ) {
					continue;
				}

				$form .= '<optgroup label="' . $key . "\">\n";

				foreach ( $val as $optgroup_key => $optgroup_val ) {
					$sel   = in_array( $optgroup_key, $selected ) ? ' selected="selected"' : '';
					$form .= '<option value="' . esc_html( $optgroup_key ) . '"' . $sel . '>'
					. (string) $optgroup_val . "</option>\n";
				}

				$form .= "</optgroup>\n";
			} else {
				$form .= '<option value="' . esc_html( $key ) . '"'
				. ( in_array( $key, $selected ) ? ' selected="selected"' : '' ) . '>'
				. (string) $val . "</option>\n";
			}
		}

		return $form . "</select>\n";
	}

	/**
	 * Attributes To String
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @param   mixed
	 * @return  string
	 */
	public static function _attributes_to_string( $attributes ) {
		if ( empty( $attributes ) ) {
			return '';
		}

		if ( is_object( $attributes ) ) {
			$attributes = (array) $attributes;
		}

		if ( is_array( $attributes ) ) {
			$atts = '';

			foreach ( $attributes as $key => $val ) {
				$atts .= ' ' . $key . '="' . $val . '"';
			}

			return $atts;
		}

		if ( is_string( $attributes ) ) {
			return ' ' . $attributes;
		}

		return false;
	}

	/**
	 * Parse form attribures
	 * @param  Array $attributes
	 * @param  Array $default
	 * @return String
	 */
	public static function _parse_form_attributes( $attributes, $default ) {
		if ( is_array( $attributes ) ) {
			foreach ( $default as $key => $val ) {
				if ( isset( $attributes[ $key ] ) ) {
					$default[ $key ] = $attributes[ $key ];
					unset( $attributes[ $key ] );
				}
			}

			if ( count( $attributes ) > 0 ) {
				$default = array_merge( $default, $attributes );
			}
		}

		$att = '';

		foreach ( $default as $key => $val ) {
			if ( $key === 'value' ) {
				$val = html_escape( $val );
			} elseif ( $key === 'name' && ! strlen( $default['name'] ) ) {
				continue;
			}

			$att .= $key . '="' . $val . '" ';
		}

		return $att;
	}

	/**
	 * Form label
	 * @param  Stirng $name
	 * @return Stirng
	 */
	public static function form_label( $name ) {
		$lable_for = strtolower( str_replace( ' ', '_', $name ) );
		return '<label for="' . $lable_for . '">' . $name . '</label>';
	}

	/**
	 * force a file to download
	 * @param  String $filepath
	 * @return void
	 */
	public static function force_download( $filepath ) {
		if ( file_exists( $filepath ) ) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . basename( $filepath ) . '"' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate' );
			header( 'Pragma: public' );
			header( 'Content-Length: ' . filesize( $filepath ) );
			flush(); // Flush system output buffer
			header( 'Cache-Control: no-cache, must-revalidate' );
			header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
			readfile( $filepath );
			die();
		} else {
			http_response_code( 404 );
			die();
		}
	}
}
