<?php
/*
* Plugin Name: Change Order Status
* Plugin URI: http://www.llt-group.com
* Description: Change Order Status by Date
* Version: 1.0
* Author: Marcos Robles
* Author URI: http://www.llt-group.com
* Text Domain: _llt_slmdgrp
*/

if( !defined( 'ABSPATH' ) ) exit;

function llt_hook_to_function() {
    global $wpdb;

	$order_status = 'processing';
	$status = 'wc-' . str_replace( 'wc-', '', $order_status );
	$sql = "SELECT * FROM ".$wpdb->prefix."posts WHERE post_status = '".$status."' AND post_type = 'shop_order'";
	$orders = $wpdb->get_results( $sql, ARRAY_A );

	foreach( $orders as $order ):

		$order_id = intval( $order['ID'] );
		$current_date = date( 'd-m-Y' );
		$field = get_field( 'item_end_date', $order['ID'] );
		$full_end_date = ( new DateTime( $field ) )->format( 'd-m-Y' );

		/**
		 * Change order status function
		 **/ 
		$gets = "SELECT * FROM " . $wpdb->prefix . "custom_orders WHERE order_id = '" .$order_id. "'";
		$orders_v = $wpdb->get_results( $gets, ARRAY_A );

		$validate = false;
		foreach( $orders_v as $mv ):
			if( !empty( mv['order_id'] ) ):
				$validate = true;
			endif;
		endforeach;

		if( !$validate ):
			$_order = new WC_Order( $order_id );
			if( !empty( $_order ) ):
				if( strtotime( $full_end_date ) <= strtotime( "today" ) ):
					$_order->update_status( 'completed' );
					$wpdb->insert(
						$wpdb->prefix.'custom_orders', array(
							'order_id'     => $order_id,
							'order_status' => 'true'
						)
					);
				endif;
			endif;
		endif;
	endforeach;		
}
