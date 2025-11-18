<?php
/**
 * Logger utility class for Stock Manager
 *
 * @package   woocommerce-stock-manager/admin/includes/
 * @version   3.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WSM_Logger class for error logging and debugging
 *
 * Provides centralized logging functionality with WooCommerce logger integration.
 *
 * @since 3.4.1
 */
class WSM_Logger {

	/**
	 * Log a message
	 *
	 * @param string $message The message to log.
	 * @param string $level   The log level (emergency|alert|critical|error|warning|notice|info|debug). Default: 'info'.
	 * @param array  $context Additional context data. Default: empty array.
	 *
	 * @return void
	 */
	public static function log( $message, $level = 'info', $context = array() ) {

		// Skip logging if WP_DEBUG is not enabled (unless it's an error or higher).
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			if ( ! in_array( $level, array( 'emergency', 'alert', 'critical', 'error' ), true ) ) {
				return;
			}
		}

		// Try to use WooCommerce logger if available.
		if ( function_exists( 'wc_get_logger' ) ) {
			try {
				$logger = wc_get_logger();
				$logger->log(
					$level,
					$message,
					array_merge(
						array( 'source' => 'woocommerce-stock-manager' ),
						$context
					)
				);
			} catch ( Exception $e ) {
				// Fallback to error_log if WC logger fails.
				self::fallback_log( $message, $level, $context );
			}
		} else {
			// Fallback to error_log if WooCommerce is not available.
			self::fallback_log( $message, $level, $context );
		}
	}

	/**
	 * Fallback logging method using PHP error_log
	 *
	 * @param string $message The message to log.
	 * @param string $level   The log level.
	 * @param array  $context Additional context data.
	 *
	 * @return void
	 */
	private static function fallback_log( $message, $level, $context = array() ) {
		$log_message = sprintf(
			'[WSM][%s] %s',
			strtoupper( $level ),
			$message
		);

		if ( ! empty( $context ) ) {
			$log_message .= ' ' . wp_json_encode( $context );
		}

		error_log( $log_message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	/**
	 * Log debug message
	 *
	 * @param string $message The message to log.
	 * @param array  $context Additional context data.
	 *
	 * @return void
	 */
	public static function debug( $message, $context = array() ) {
		self::log( $message, 'debug', $context );
	}

	/**
	 * Log info message
	 *
	 * @param string $message The message to log.
	 * @param array  $context Additional context data.
	 *
	 * @return void
	 */
	public static function info( $message, $context = array() ) {
		self::log( $message, 'info', $context );
	}

	/**
	 * Log warning message
	 *
	 * @param string $message The message to log.
	 * @param array  $context Additional context data.
	 *
	 * @return void
	 */
	public static function warning( $message, $context = array() ) {
		self::log( $message, 'warning', $context );
	}

	/**
	 * Log error message
	 *
	 * @param string $message The message to log.
	 * @param array  $context Additional context data.
	 *
	 * @return void
	 */
	public static function error( $message, $context = array() ) {
		self::log( $message, 'error', $context );
	}

	/**
	 * Log critical message
	 *
	 * @param string $message The message to log.
	 * @param array  $context Additional context data.
	 *
	 * @return void
	 */
	public static function critical( $message, $context = array() ) {
		self::log( $message, 'critical', $context );
	}

	/**
	 * Log database error
	 *
	 * @param string $query The SQL query that failed.
	 * @param string $error The database error message.
	 *
	 * @return void
	 */
	public static function database_error( $query, $error ) {
		global $wpdb;

		$message = sprintf(
			'Database error: %s. Query: %s',
			$error,
			$query
		);

		self::error(
			$message,
			array(
				'query'      => $query,
				'error'      => $error,
				'last_query' => $wpdb->last_query,
			)
		);
	}

	/**
	 * Log file operation error
	 *
	 * @param string $operation The file operation (read|write|delete|upload).
	 * @param string $file_path The file path that failed.
	 * @param string $error     The error message.
	 *
	 * @return void
	 */
	public static function file_error( $operation, $file_path, $error ) {
		$message = sprintf(
			'File operation failed (%s): %s. Error: %s',
			$operation,
			$file_path,
			$error
		);

		self::error(
			$message,
			array(
				'operation' => $operation,
				'file_path' => $file_path,
				'error'     => $error,
			)
		);
	}

}//end class
