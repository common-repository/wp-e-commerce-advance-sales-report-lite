<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!function_exists('get_results')){
	function get_results($where='', $order='', $limit ='', $output_type='OBJECT') {
		global $wpdb;
		$table_name = $wpdb->prefix . "woo_compare_categories";
		if (trim($where) != '')
			$where = " WHERE {$where} ";
		if (trim($order) != '')
			$order = " ORDER BY {$order} ";
		if (trim($limit) != '')
			$limit = " LIMIT {$limit} ";
		$result = $wpdb->get_results("SELECT * FROM {$table_name} {$where} {$order} {$limit}", $output_type);
		return $result;
	}
}

if(!function_exists('print_array')){
	function print_array($ar = NULL,$display = true){
			if($ar){
				$output = "<pre>";
				$output .= print_r($ar,true);
				$output .= "</pre>";
				
				if($display){
					echo $output;
				}else{
					return $output;
				}
			}
	}
}


if(!function_exists('ic_cr_price')){	
	function ic_cr_price($vlaue){
		return $vlaue;
	}
}

?>