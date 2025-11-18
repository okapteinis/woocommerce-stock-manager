<?php
/**
 * Validator utility class for Stock Manager
 *
 * @package   woocommerce-stock-manager/admin/includes/
 * @version   3.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WSM_Validator class for input validation and sanitization
 *
 * Provides centralized validation methods to ensure data integrity and security.
 *
 * @since 3.4.1
 */
class WSM_Validator {

	/**
	 * Sanitize and validate product ID
	 *
	 * @param mixed $product_id The product ID to validate.
	 *
	 * @return int Sanitized product ID (0 if invalid).
	 */
	public static function sanitize_product_id( $product_id ) {
		return absint( $product_id );
	}

	/**
	 * Sanitize product SKU
	 *
	 * @param string $sku The SKU to sanitize.
	 *
	 * @return string Sanitized SKU.
	 */
	public static function sanitize_sku( $sku ) {
		return sanitize_text_field( $sku );
	}

	/**
	 * Validate and sanitize stock quantity
	 *
	 * @param mixed $qty The stock quantity to validate.
	 *
	 * @return int Sanitized stock quantity (minimum 0).
	 */
	public static function validate_stock_quantity( $qty ) {
		$qty = intval( $qty );
		return max( 0, $qty );
	}

	/**
	 * Validate stock status
	 *
	 * @param string $status The stock status to validate.
	 *
	 * @return string Valid stock status or 'outofstock' if invalid.
	 */
	public static function validate_stock_status( $status ) {
		$allowed_statuses = array( 'instock', 'outofstock', 'onbackorder' );

		$status = sanitize_text_field( strtolower( $status ) );

		if ( in_array( $status, $allowed_statuses, true ) ) {
			return $status;
		}

		return 'outofstock'; // Default fallback.
	}

	/**
	 * Validate manage stock setting
	 *
	 * @param mixed $manage_stock The manage stock value to validate.
	 *
	 * @return string|bool Valid manage stock value ('yes'|'no'|true|false).
	 */
	public static function validate_manage_stock( $manage_stock ) {
		if ( is_bool( $manage_stock ) ) {
			return $manage_stock;
		}

		$manage_stock = sanitize_text_field( strtolower( $manage_stock ) );

		if ( 'yes' === $manage_stock || '1' === $manage_stock || 'true' === $manage_stock ) {
			return 'yes';
		}

		return 'no';
	}

	/**
	 * Validate backorders setting
	 *
	 * @param string $backorders The backorders value to validate.
	 *
	 * @return string Valid backorders value (yes|no|notify).
	 */
	public static function validate_backorders( $backorders ) {
		$allowed_values = array( 'yes', 'no', 'notify' );

		$backorders = sanitize_text_field( strtolower( $backorders ) );

		if ( in_array( $backorders, $allowed_values, true ) ) {
			return $backorders;
		}

		return 'no'; // Default fallback.
	}

	/**
	 * Validate price
	 *
	 * @param mixed $price The price to validate.
	 *
	 * @return float Sanitized price (minimum 0).
	 */
	public static function validate_price( $price ) {
		$price = floatval( str_replace( ',', '.', $price ) );
		return max( 0, $price );
	}

	/**
	 * Validate weight
	 *
	 * @param mixed $weight The weight to validate.
	 *
	 * @return float Sanitized weight (minimum 0).
	 */
	public static function validate_weight( $weight ) {
		$weight = floatval( str_replace( ',', '.', $weight ) );
		return max( 0, $weight );
	}

	/**
	 * Validate tax status
	 *
	 * @param string $tax_status The tax status to validate.
	 *
	 * @return string Valid tax status (taxable|shipping|none).
	 */
	public static function validate_tax_status( $tax_status ) {
		$allowed_statuses = array( 'taxable', 'shipping', 'none' );

		$tax_status = sanitize_text_field( strtolower( $tax_status ) );

		if ( in_array( $tax_status, $allowed_statuses, true ) ) {
			return $tax_status;
		}

		return 'taxable'; // Default fallback.
	}

	/**
	 * Validate tax class
	 *
	 * @param string $tax_class The tax class to validate.
	 *
	 * @return string Sanitized tax class.
	 */
	public static function validate_tax_class( $tax_class ) {
		// Tax classes can be custom, so we just sanitize.
		return sanitize_title( $tax_class );
	}

	/**
	 * Validate email address
	 *
	 * @param string $email The email to validate.
	 *
	 * @return string|bool Sanitized email or false if invalid.
	 */
	public static function validate_email( $email ) {
		$email = sanitize_email( $email );

		if ( is_email( $email ) ) {
			return $email;
		}

		return false;
	}

	/**
	 * Validate and sanitize URL
	 *
	 * @param string $url The URL to validate.
	 *
	 * @return string Sanitized URL.
	 */
	public static function sanitize_url( $url ) {
		return esc_url_raw( $url );
	}

	/**
	 * Validate nonce
	 *
	 * @param string $nonce  The nonce value.
	 * @param string $action The nonce action name.
	 *
	 * @return bool True if nonce is valid, false otherwise.
	 */
	public static function validate_nonce( $nonce, $action ) {
		if ( empty( $nonce ) ) {
			return false;
		}

		return (bool) wp_verify_nonce( $nonce, $action );
	}

	/**
	 * Validate file upload
	 *
	 * @param array  $file           The $_FILES array element.
	 * @param array  $allowed_types  Allowed MIME types. Default: CSV only.
	 * @param int    $max_size_mb    Maximum file size in MB. Default: 5.
	 *
	 * @return true|WP_Error True if valid, WP_Error if validation fails.
	 */
	public static function validate_file_upload( $file, $allowed_types = array( 'text/csv' ), $max_size_mb = 5 ) {

		// Check if file exists.
		if ( empty( $file ) || ! isset( $file['tmp_name'] ) ) {
			return new WP_Error( 'no_file', __( 'No file uploaded.', 'woocommerce-stock-manager' ) );
		}

		// Check for upload errors.
		if ( ! empty( $file['error'] ) && UPLOAD_ERR_OK !== $file['error'] ) {
			return new WP_Error( 'upload_error', __( 'File upload failed.', 'woocommerce-stock-manager' ) );
		}

		// Validate file size.
		$max_size_bytes = $max_size_mb * 1024 * 1024;
		if ( $file['size'] > $max_size_bytes ) {
			return new WP_Error(
				'file_too_large',
				sprintf(
					/* translators: %d: Maximum file size in MB */
					__( 'File size exceeds maximum limit of %d MB.', 'woocommerce-stock-manager' ),
					$max_size_mb
				)
			);
		}

		// Validate file type.
		$filetype = wp_check_filetype( $file['name'], $allowed_types );

		if ( ! in_array( $filetype['type'], $allowed_types, true ) ) {
			return new WP_Error( 'invalid_file_type', __( 'Invalid file type.', 'woocommerce-stock-manager' ) );
		}

		return true;
	}

	/**
	 * Sanitize array of IDs
	 *
	 * @param array $ids Array of IDs to sanitize.
	 *
	 * @return array Sanitized array of IDs.
	 */
	public static function sanitize_id_array( $ids ) {
		if ( ! is_array( $ids ) ) {
			return array();
		}

		return array_map( 'absint', $ids );
	}

	/**
	 * Sanitize boolean value
	 *
	 * @param mixed $value The value to sanitize as boolean.
	 *
	 * @return bool Sanitized boolean value.
	 */
	public static function sanitize_boolean( $value ) {
		return (bool) filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

}//end class
