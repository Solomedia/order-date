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

if(!defined('ABSPATH'))exit;
	/* get all order processing status */
function llt_hook_to_function() {
	global $wpdb;
	// We add 'wc-' prefix when is missing from order staus
	$order_status = 'processing';
	$status = 'wc-' . str_replace('wc-', '', $order_status);
	$sql = "SELECT * FROM ".$wpdb->prefix."posts WHERE post_status = '".$status."' AND post_type = 'shop_order'";
	$orders = $wpdb->get_results($sql, ARRAY_A);

	//get all orders	
	foreach($orders as $order):

		$order_id = intval($order['ID']);
		$current_date = date('d-m-Y');
		$field = get_field('item_end_date', $order['ID']);
		$full_end_date = (new DateTime($field))->format('d-m-Y');

		/**
		 * Change order status function
		 **/ 

		$_order = new WC_Order($order_id);
		if(!empty($_order)) {
			global $woocommerce;
			if(strtotime($full_end_date) == strtotime("today")) {
				$_order->update_status('completed');
			} else if(strtotime($full_end_date) < strtotime("today")) {
				$_order->update_status('completed');
			}
		}
	endforeach;		
}//end cron function

add_action('_llt_next_event_2', 'llt_hook_to_function');
add_filter('cron_schedules', function($schedules) {
	$schedules['every-minute'] = array(
	   'interval' => 1 * MINUTE_IN_SECONDS,
	   'display'  => __('Every minute')
	);
	return $schedules;
});

if(!wp_next_scheduled('_llt_next_event_2')) {
	wp_schedule_event(time(), 'every-minute', '_llt_next_event_2');
}
?>