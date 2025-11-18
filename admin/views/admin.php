<?php
/**
 * Admin page to mount Stock Manager.
 *
 * @package  woocommerce-stock-manager/admin/views
 * @version  2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stock = $this->stock();

/**
 * Save all data.
 */
$product_id = ( ! empty( $_POST['product_id'] ) ) ? wc_clean( wp_unslash( $_POST['product_id'] ) ) : 0; // phpcs:ignore
$nonce      = ( ! empty( $_POST['_wpnonce'] ) ) ? wc_clean( wp_unslash( $_POST['_wpnonce'] ) ) : ''; // phpcs:ignore

if ( ! empty( $product_id ) ) {
	// Verify nonce for security.
	if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wsm_save_all' ) ) {
		wp_die(
			esc_html__( 'Security check failed. Please refresh the page and try again.', 'woocommerce-stock-manager' ),
			esc_html__( 'Security Error', 'woocommerce-stock-manager' ),
			array( 'response' => 403 )
		);
	}

	$product = ( ! empty( $_POST ) ) ? wc_clean( wp_unslash( $_POST ) ) : array(); // phpcs:ignore
	$stock->save_all( $product );
	// add redirect.
}

/**
 * Save display option.
 */
$page_filter_display = ( ! empty( $_POST['page-filter-display'] ) ) ? wc_clean( wp_unslash( $_POST['page-filter-display'] ) ) : ''; // phpcs:ignore
$nonce_display       = ( ! empty( $_POST['_wpnonce_display'] ) ) ? wc_clean( wp_unslash( $_POST['_wpnonce_display'] ) ) : ''; // phpcs:ignore

if ( ! empty( $page_filter_display ) ) {
	// Verify nonce for display option save.
	if ( empty( $nonce_display ) || ! wp_verify_nonce( $nonce_display, 'wsm_save_display' ) ) {
		wp_die(
			esc_html__( 'Security check failed. Please refresh the page and try again.', 'woocommerce-stock-manager' ),
			esc_html__( 'Security Error', 'woocommerce-stock-manager' ),
			array( 'response' => 403 )
		);
	}

	$product = ( ! empty( $_POST ) ) ? wc_clean( wp_unslash( $_POST ) ) : array(); // phpcs:ignore
	$stock->save_filter_display( $product );
}

?>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<?php class_exists( 'Stock_Manager_Admin' ) && is_callable( array( 'Stock_Manager_Admin', 'add_admin_notices' ) ) && Stock_Manager_Admin::add_admin_notices(); ?>
	<div id="woocommerce-stock-manager-app"></div>  
</div>
<?php
