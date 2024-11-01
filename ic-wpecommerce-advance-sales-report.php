<?php
/*
Plugin Name: WP E-Commerce Advance Sales Report Lite
Plugin URI: http://plugins.infosofttech.com/
Author: Infosoft Consultants
Description: WP E-Commerce Sales Reporter shows you all key sales information in one main Dashboard in very intuitive, easy to understand format which gives a quick overview of your business and helps make smart decisions
License: A  "Slug" license name e.g. GPL2
Version: 1.0 
Author URI: http://www.infosofttech.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( in_array('wp-e-commerce/wp-shopping-cart.php', apply_filters('active_plugins', get_option('active_plugins'))) ) :
	if(!class_exists('WP_eCommerce_Sales_Report')):
		class WP_eCommerce_Sales_Report
		{
				public function __construct() {
					if(is_admin()){
						/*include class*/
						include_once("wpec-ic-functions.php");
						include_once("wpec-ic-clsReport.php");
						/*Add Menu in Word Press Panel*/
						add_action('admin_menu', array($this, 'ic_cr_add_page'));	
						
						/*For Ajax*/
						add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));	
						add_action('wp_ajax_ic_cr_action_comman', array($this, 'ic_cr_action_comman'));
						
						if(isset($_GET['page']) && $_GET['page'] == "ic_cr_page"){
							/*Set The default Constant*/
							$this->define_constant();
							add_action('admin_footer',  array( &$this, 'admin_footer'));
						}
					}				
			 }
			 function ic_cr_action_comman() {
			 $objReport = new clsReport();
				if(isset($_POST['action']) && $_POST['action'] == "ic_cr_action_comman"){
					
					if(isset($_POST['graph_by_type']) && $_POST['graph_by_type'] == "top_product"){
						$objReport->ic_cr_top_product_chart();					
					}
					//
					if(isset($_POST['graph_by_type']) && $_POST['graph_by_type'] == "today_order_count"){
						$objReport->ic_cr_today_order_count();					
					}
					if(isset($_POST['graph_by_type']) && $_POST['graph_by_type'] == "Last_7_days_sales_order_amount"){
						$objReport->ic_cr_Last_7_days_sales_amount();					
					}
				}
				die(); // this is required to return a proper result
				exit;
			}
			 function admin_enqueue_scripts($hook){
				if(isset($_GET['page']) && $_GET['page'] == "ic_cr_page"){}else{ return false;}
					wp_enqueue_script('ic_cr_ajax_script', plugins_url( '/assets/graph/scripts/graph.js', __FILE__ ), array('jquery'));
					wp_localize_script('ic_cr_ajax_script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php' ))); // setting ajaxurl		
			 }
			 function ic_cr_add_page(){
				$main_page = add_menu_page("WP E-Commerce Advance Sales Report Lite", 'WP E-Commerce Report', 'manage_options', 'ic_cr_page', array($this, 'ic_cr_report_init'), plugins_url( 'ic-wpecommerce-advance-sales-report/assets/images/menu_icons.png' ), '77.5' );
			 }
			 function admin_footer() {
				
				wp_enqueue_style( 'ic_cr_admin_styles', WPEC_IC_URL . '/assets/css/admin.css' );
				/*Graph Style Sheet*/
				wp_enqueue_style( 'ic_cr_admin_graph_css', WPEC_IC_URL . '/assets/graph/css/jquery.jqplot.min.css');
				/*Don't Touch This JqPlot Lib*/
				wp_enqueue_script( 'ic_cr_admin_graph_script_pie_lib', WPEC_IC_URL . '/assets/graph/scripts/jquery.jqplot.min.js');
				
				/*Don't Touch This (Pie Chart Lib)*/
				wp_enqueue_script( 'ic_cr_admin_graph_script_pie_chart', WPEC_IC_URL . '/assets/graph/scripts/jqplot.pieRenderer.min.js');	
				
				/*Don't Touch This (Meter Gauge Chart Lib)*/
				wp_enqueue_script( 'ic_cr_admin_graph_script_meter_gauge', WPEC_IC_URL . '/assets/graph/scripts/jqplot.meterGaugeRenderer.min.js');	
				
				
				/*Don't Touch This (point Labels)*/
				wp_enqueue_script( 'ic_cr_admin_graph_script_line_chart_1', WPEC_IC_URL . '/assets/graph/scripts/jqplot.pointLabels.min.js');	
				
				/*Don't Touch This (Date Lib)*/
				wp_enqueue_script( 'ic_cr_admin_graph_script_pointLabels', WPEC_IC_URL . '/assets/graph/scripts/jqplot.dateAxisRenderer.min.js');	
			 }
			 function ic_cr_report_init3()
			 {
			 
			 }
			 function ic_cr_report_init()
			 {
				$objReport = new clsReport();
				
				$total_orders 		=	$objReport->ic_cr_total_order();
				$total_amount  		=	$objReport->ic_cr_total_amount();			
				$total_customer  	=	$objReport->ic_cr_total_customer();
				$total_products  	=	$objReport->ic_cr_total_product();
				$total_categories  	=	$objReport->ic_cr_total_categories();
				
				?>
					 <div class="wrap ic_mis_report ic_cr_wrap">
						<div class="icon32" id="icon-options-general"><br /></div>
						<h2><?php _e('Dashboard','wcismis') ?></h2>
							  <div id="poststuff" class="woo_cr-reports-wrap">
									<div class="woo_cr-reports-top">					
										<div class="postbox left">
											<h3><span><?php _e( 'Total Orders', 'wcismis' ); ?></span></h3>
											<div class="inside">
											  <p class="stat"><?php echo $total_orders; ?></p>
											</div>
										</div>
										
										<div class="postbox left">
											<h3><span><?php _e( 'Total Sales', 'wcismis' ); ?></span></h3>
											<div class="inside">
												<p class="stat"><?php echo wpsc_currency_display($total_amount); ?></p>
											</div>
										</div>
										
										<div class="postbox left">
											<h3><span><?php _e( 'Total Customers', 'wcismis' ); ?></span></h3>
											<div class="inside">
												<p class="stat"><?php echo $total_customer; ?></p>
											</div>
										</div>
										
										<div class="postbox left">
											<h3><span><?php _e( 'Total Products', 'wcismis' ); ?></span></h3>
											<div class="inside">
												<p class="stat"><?php echo $total_products; ?></p>
											</div>
										</div>
										
										<div class="postbox left">
											<h3><span><?php _e( 'Total Categories', 'wcismis' ); ?></span></h3>
											<div class="inside">
												<p class="stat"><?php echo $total_categories; ?></p>
											</div>
										</div>
										
										
										<div class="clearfix"></div>
									</div>
									
									 <div class="ThreeCol_Boxes">
										<div class="postbox">
											<h3>
												<span><?php _e( 'Today Order Count', 'wcismis' ); ?></span>
											</h3>
											<div class="inside">
												 <div id="today_order_count_meter_gauge" class="example-chart"></div>
											</div>
										</div>
									</div>
									 <div class="ThreeCol_Boxes">
										<div class="postbox">
											<h3>
												<span><?php _e( 'Top Products', 'wcismis' ); ?></span>
											</h3>
											<div class="inside">
												 <div id="top_product_pie_chart" class="example-chart"></div>	
											</div>
										</div>
									</div>
									 <div class="ThreeCol_Boxes LastBox_Margin">
										<div class="postbox">
											<h3>
												<span><?php _e( 'Last 7 days Sales Amount', 'wcismis' ); ?></span>
											</h3>
											<div class="inside">
												 <div id="last_7_days_sales_order_amount" class="example-chart" style="width:100%"></div>	
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="postbox">
										<h3><span><?php _e( 'Sales Order Summary', 'wcismis' ); ?></span></h3>
										<div class="inside">
											<?php $objReport->ic_cr_sales_order_count_value();?>
										</div>
									</div>
									
									 <div class="postbox">
										<h3><span><?php _e( 'Recent Orders', 'wcismis' ); ?></span></h3>
										<div class="inside">                            
											<?php $objReport->ic_cr_recent_orders();?>
										</div>
									</div>  
									
									 <div class="postbox">
										<h3><span><?php _e( 'Top Billing Countries', 'wcismis' ); ?></span></h3>
										<div class="inside">
											 <?php $objReport->ic_cr_top_billing_country();?>
										</div>
									</div> 
									<div class="postbox">
										<h3><span><?php _e( 'Top Customers', 'wcismis' ); ?></span></h3>
										<div class="inside">
											<?php $objReport->ic_cr_top_customer_list();?>
										</div>
									</div>
							  </div>
					 </div>
				<?php
				$objReport=null;
				
			 }
			 function define_constant(){
				if(!defined('WPEC_IC_FILE_PATH'))   define( 'WPEC_IC_FILE_PATH', dirname( __FILE__ ) );
				if(!defined('WPEC_IC_DIR_NAME')) 	define( 'WPEC_IC_DIR_NAME', basename( WPEC_IC_FILE_PATH ) );
				if(!defined('WPEC_IC_FOLDER')) 	    define( 'WPEC_IC_FOLDER', dirname( plugin_basename( __FILE__ ) ) );
				if(!defined('WPEC_IC_NAME')) 		define(	'WPEC_IC_NAME', plugin_basename(__FILE__) );
				if(!defined('WPEC_IC_URL')) 		define( 'WPEC_IC_URL', WP_CONTENT_URL . '/plugins/' . WPEC_IC_FOLDER );
			} 
		}
	endif;//End Class Exists	
	$wpec = new WP_eCommerce_Sales_Report();
	
else :
	 add_action('admin_notices', 'ic_cr_sales_report_error_notice');
	 function ic_cr_sales_report_error_notice(){
        global $current_screen;
        if($current_screen->parent_base == 'plugins'){
            echo '<div class="error"><p>'.__('<strong>Please activate WP e-Commerce</a> first.').'</p></div>';
        }
    }
endif;


?>
